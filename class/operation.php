<?php
class MJ_hmgt_operation
{	
	//Add Operation type
	public function MJ_hmgt_add_operationtype($data)
	{		
		global $wpdb;
		
		$category_name=MJ_hmgt_strip_tags_and_stripslashes($data['category_name']);
		$operation_cost=$data['operation_cost'];
		$operation_tax=$data['operation_tax'];
		$operation_description=MJ_hmgt_strip_tags_and_stripslashes($data['operation_description']);
				
		$operation_type_array=array("category_name"=>$category_name,"operation_cost"=>$operation_cost,"operation_tax"=>$operation_tax,"operation_description"=>$operation_description);
		
		$operation_type=json_encode($operation_type_array);
		
		$result = wp_insert_post( array(
				'post_status' => 'publish',
				'post_type' => 'operation_category',
				'post_title' => $operation_type) );
				
		MJ_hmgt_append_audit_log(''.esc_html__('Add new operation type ','hospital_mgt').'',get_current_user_id());
		return $result;
	}
	//get all operation type
	public function MJ_hmgt_get_all_operationtype()
	{
		$args= array('post_type'=> 'operation_category','posts_per_page'=>-1,'orderby'=>'post_title','order'=>'Asc');
					$result = get_posts( $args );
		return $result;
		
	}
	//get bed type name
	public function MJ_hmgt_get_bedtype_name($id)
	{		
		$result = get_post( $id );		
		if(!empty($result))	
		return $result->post_title;
		else 
			return "";
	}
	//delete treatment data
	public function MJ_hmgt_delete_treatment($treatment_id)
	{
		global $wpdb;
		$table_treatment = $wpdb->prefix. 'hmgt_treatment';
		$result = $wpdb->query("DELETE FROM $table_treatment where treatment_id= ".$treatment_id);
		MJ_hmgt_append_audit_log(''.esc_html__('Delete treatment ','hospital_mgt').'',get_current_user_id());
		return $result;
	}
	//delete oparation category
	public function MJ_hmgt_delete_operation_type($operation_id)
	{
		$result=wp_delete_post($operation_id);
		MJ_hmgt_append_audit_log(''.esc_html__('Delete operation type ','hospital_mgt').'',get_current_user_id());
		return $result;
	}
	//get single bed data
	public function MJ_hmgt_get_single_bed($bed_id)
	{
		global $wpdb;
		$table_bed = $wpdb->prefix. 'hmgt_bed';
	
		$result = $wpdb->get_row("SELECT * FROM $table_bed where bed_id= ".$bed_id);
		return $result;
	}
	//get bed number
	public function MJ_hmgt_get_bed_number($bed_id)
	{
		global $wpdb;
		$table_bed = $wpdb->prefix. 'hmgt_bed';
	
		$result = $wpdb->get_var("SELECT bed_number FROM $table_bed where bed_id= ".$bed_id);
		return $result;
	}
	//add operation data
	public function MJ_hmgt_add_operation_theater($data)
	{	
		global $wpdb;
		$table_ot = $wpdb->prefix. 'hmgt_ot';
		$table_hmgt_assign_type = $wpdb->prefix. 'hmgt_assign_type';
		$table_hmgt_charges = $wpdb->prefix. 'hmgt_charges';
		$table_hmgt_history = $wpdb->prefix. 'hmgt_history';
		$table_hmgt_guardian = $wpdb->prefix. 'hmgt_inpatient_guardian';
		
		$table_hmgt_patient_transation = $wpdb->prefix. 'hmgt_patient_transation';
				
		$ot_data['operation_title']=strip_tags($data['operation_title']);
		$ot_data['patient_id']=$data['patient_id'];
		$ot_data['patient_status']=$data['patient_status'];
		$ot_data['bed_type_id']=$data['bed_type_id'];
		$ot_data['bednumber']=$data['bed_number'];
		$ot_data['operation_date']=MJ_hmgt_get_format_for_db($data['operation_date']);				
		$ot_data['operation_time']=$data['operation_time'];		
		$ot_data['ot_description']=MJ_hmgt_strip_tags_and_stripslashes($data['ot_description']);
		$ot_data['operation_charge']=$data['operation_charge'];
		$ot_data['operation_status']=$data['operation_status'];
		
		if($data['operation_status'] == 'Completed')
		{			
			$ot_data['out_come_status']=$data['out_come_status'];
		}	
		else
		{
			$ot_data['out_come_status']=null;
		}
		$ot_data['ot_create_date']=date("Y-m-d");
		$ot_data['ot_create_by']=get_current_user_id();	
				
		$ot_data['ot_tax']=$data['ot_tax'];	
		$ot_data['ot_charge']=$data['ot_charge'];	
		
		$guardian = MJ_hmgt_get_guardian_name($_POST['patient_id']);
		$history['patient_id']=$_POST['patient_id'];
		$history['status']=$_POST['patient_status'];
		$history['bed_number']=$data['bednumber'];
		
		if(!empty($guardian))
			$history['guardian_name']=$guardian->first_name." ".$guardian->last_name;
		else
			$history['guardian_name']="";
		
		$history['history_type']="operation";
		$history['history_date']=date("Y-m-d H:i:s");
		$history['created_by']=get_current_user_id();
		$bed_type = $this->MJ_hmgt_get_bedtype_name($ot_data['bed_type_id']);
		$bed_number = $this->MJ_hmgt_get_bed_number($ot_data['bednumber']);
		$get_bedrow= $this->MJ_hmgt_get_single_bed($ot_data['bednumber']);
		$single_bed_charge= $get_bedrow->bed_charges;
		$total_bed_charge = $single_bed_charge +$data['operation_charge'];
		
		$charge_data['charge_label']= 'operation Charge';
		$charge_data['charge_type']= 'operation';
		$charge_data['room_number']=$bed_number;
		$charge_data['bed_type']=$bed_type;
		$charge_data['charges']=$total_bed_charge;
		$charge_data['patient_id']=$ot_data['patient_id'];
		$charge_data['status']=0;
		//$charge_data['refer_id']=1;
		$charge_data['created_date']=date("Y-m-d");
		$charge_data['created_by']=get_current_user_id();		
		
		$patient_satus['patient_status']=$_POST['patient_status'];
		$patient_id['patient_id']=$_POST['patient_id'];
		
		if(!empty($guardian))
			$result=$wpdb->update( $table_hmgt_guardian, $patient_satus,$patient_id);
		else
		{
			$wpdb->insert($table_hmgt_guardian, array(
					"patient_status" => $_POST['patient_status'],
					"patient_id" => $_POST['patient_id']
						
			));
		}
		
		//for oparation transtion table...		
		$transtiondata['type']="Operation Charges";
		$transtiondata['type_value']=$data['ot_charge'];
		$transtiondata['type_tax']=$data['ot_tax'];
		$transtiondata['type_total_value']=$data['operation_charge'];
		$transtiondata['status']="Unpaid";
		$transtiondata['date']=date("Y-m-d");
		$transtiondata['patient_id']=$data['patient_id'];
			
		if($data['action']=='edit')
		{
			$ot_id['operation_id']=$data['operation_id'];
			$charge_referid['refer_id']=$data['operation_id'];
			$charge_referid['charge_type']='operation';
			$result=$wpdb->update( $table_ot, $ot_data ,$ot_id);
			$this->MJ_hmgt_delete_assign_byparant($ot_id['operation_id']);
			if(!empty($data['doctor']))
			{
				foreach($data['doctor'] as $id)
				{
					$assign_data['child_id']=$id;
					$assign_data['parent_id']=$data['operation_id'];
					$assign_data['assign_type']='operation_theater';
					$assign_data['assign_date']=date("Y-m-d");
					$assign_data['assign_by']=get_current_user_id();
					$wpdb->insert( $table_hmgt_assign_type, $assign_data );
				}
			}
			$wpdb->update( $table_hmgt_charges, $charge_data,$charge_referid );
			MJ_hmgt_append_audit_log(''.esc_html__('Update operation detail ','hospital_mgt').'',get_current_user_id());
			
			$delete_trement_charge = $wpdb->query("DELETE FROM $table_hmgt_patient_transation where type='Operation Charges' AND refer_id= ".$data['operation_id']);
			
			$transtiondata['type_id']=$data['operation_id'];
			$transtiondata['refer_id'] = $data['operation_id'];
			$wpdb->insert( $table_hmgt_patient_transation, $transtiondata );
			
			return $result;
		}
		else
		{
			$result=$wpdb->insert( $table_ot, $ot_data );
			$ot_id = $wpdb->insert_id;			
			
			$transtiondata['type_id']=$ot_id;
			$transtiondata['refer_id'] = $ot_id;
			$wpdb->insert( $table_hmgt_patient_transation, $transtiondata );
			
			$charge_data['refer_id'] = $ot_id;
			if(!empty($data['doctor']))
			{
				foreach($data['doctor'] as $id)
				{
					$assign_data['child_id']=$id;
					$assign_data['parent_id']=$ot_id ;
					$assign_data['assign_type']='operation_theater';
					$assign_data['assign_date']=date("Y-m-d");
					$assign_data['assign_by']=get_current_user_id();
					$wpdb->insert( $table_hmgt_assign_type, $assign_data );
				}
			}
			MJ_hmgt_append_audit_log(''.esc_html__('Add new operation detail ','hospital_mgt').'',get_current_user_id());
			$wpdb->insert( $table_hmgt_charges, $charge_data );
			$wpdb->insert( $table_hmgt_history, $history );
			return $result;
		}
		
		return $result;
	}	
	//get all operation data
	public function MJ_hmgt_get_all_operation()
	{
		global $wpdb;
		$table_ot = $wpdb->prefix. 'hmgt_ot';
		
		$result = $wpdb->get_results("SELECT * FROM $table_ot");
		return $result;
	
	}
	//get operation by created_by
	public function MJ_hmgt_get_operation_by_created_by($userid)
	{
		global $wpdb;		
		$table_ot = $wpdb->prefix. 'hmgt_ot';			
		$current_user_id=get_current_user_id();
		
		$result = $wpdb->get_results("SELECT * FROM $table_ot WHERE ot_create_by=$current_user_id");	
		
		return $result;	
	}
	//get doctor all created operation data
	public function MJ_hmgt_get_doctor_operation_by_created_by($userid)
	{
		global $wpdb;		
		$table_ot = $wpdb->prefix. 'hmgt_ot';			
		$current_user_id=get_current_user_id();
		$array=MJ_hmgt_doctor_patientid_list(); 
		$array = implode("','",$array);
		$array1=MJ_hmgt_doctor_all_operation_array();		
		$array1 = implode("','",$array1);
		$result = $wpdb->get_results("SELECT * FROM $table_ot WHERE ot_create_by=$current_user_id OR patient_id IN ('".$array."') OR operation_id IN ('".$array1."')");	
		
		return $result;	
	}
	//get nurse all created operation data
	public function MJ_hmgt_get_nurse_operation_by_created_by($userid)
	{
		global $wpdb;		
		$table_ot = $wpdb->prefix. 'hmgt_ot';			
		$current_user_id=get_current_user_id();
		$array=MJ_hmgt_nurse_patientid_list(); 
		
		$array = implode("','",$array);
		$result = $wpdb->get_results("SELECT * FROM $table_ot WHERE ot_create_by=$current_user_id OR patient_id IN ('".$array."')");	
		
		return $result;	
	}
	//get patient all operation data
	public function MJ_hmgt_get_operation_by_patient($userid)
	{
		global $wpdb;
		$table_ot = $wpdb->prefix. 'hmgt_ot';	
			
		$result = $wpdb->get_results("SELECT * FROM $table_ot WHERE patient_id=$userid");	
		
		return $result;	
	}
	//get operation name
	public function MJ_hmgt_get_operation_name($id)
	{
		$result = get_post( $id );
		$operation_data=json_decode($result->post_title);
		return $operation_data->category_name;
	}
	//get doctor by operation id
	public function MJ_hmgt_get_doctor_by_oprationid($operation_id)
	{
		global $wpdb;		
		$table_hmgt_assign_type = $wpdb->prefix. 'hmgt_assign_type';		
		$result = $wpdb->get_results("SELECT * FROM $table_hmgt_assign_type WHERE parent_id = $operation_id AND assign_type = 'operation_theater' ");
		return $result;	
	}
	//get single oparation data
	public function MJ_hmgt_get_single_operation($id)
	{
		global $wpdb;
		$table_ot = $wpdb->prefix. 'hmgt_ot';	
		$result = $wpdb->get_row("SELECT * FROM $table_ot where operation_id = $id");
		return $result;	
	}
	//delete assiged by parent_id
	public function MJ_hmgt_delete_assign_byparant($parent_id)
	{
		global $wpdb;
		$table_hmgt_assign_type = $wpdb->prefix. 'hmgt_assign_type';		
		$result = $wpdb->query("DELETE FROM $table_hmgt_assign_type where parent_id= ".$parent_id. " AND assign_type = 'operation_theater'"  );
		return $result;
	}
	//delete operation data
	public function MJ_hmgt_delete_oprationtheater($ot_id)
	{
		global $wpdb;
		$table_ot = $wpdb->prefix. 'hmgt_ot';
		$result = $wpdb->query("DELETE FROM $table_ot  where operation_id = $ot_id");
		$table_hmgt_assign_type = $wpdb->prefix. 'hmgt_assign_type';
		$result = $wpdb->query("DELETE FROM $table_hmgt_assign_type where parent_id= ".$ot_id. " AND assign_type = 'operation_theater'"  );
		MJ_hmgt_append_audit_log(''.esc_html__('Delete operation ','hospital_mgt').'',get_current_user_id());
		return $result;
	}
	//get operation data on admin dashboard
	public function MJ_hmgt_get_operation_on_admin_dashboard()
	{
		global $wpdb;
		$table_ot = $wpdb->prefix. 'hmgt_ot';
		
		$result = $wpdb->get_results("SELECT * FROM $table_ot ORDER BY operation_id DESC LIMIT 3");
		return $result;
	
	}
	//get operation data on fronted dashboard
	public function MJ_hmgt_get_operation_on_fronted_dashboard()
	{
		global $wpdb;
		$table_ot = $wpdb->prefix. 'hmgt_ot';
		$page='operation';
		$data=MJ_hmgt_page_wise_view_data_on_fronted_dashboard($page);
		$role=MJ_hmgt_get_current_user_role();	
		$current_user_id=get_current_user_id();			 	
		if($data==1)
		{	
			if($role == 'laboratorist' || $role == 'pharmacist' || $role == 'accountant' || $role == 'receptionist') 
			{				
				$result = $wpdb->get_results("SELECT * FROM $table_ot WHERE ot_create_by=$current_user_id ORDER BY operation_id DESC LIMIT 3");					
			}
			elseif($role == 'doctor') 
			{
				$array=MJ_hmgt_doctor_patientid_list(); 
				$array = implode("','",$array);
				$array1=MJ_hmgt_doctor_all_operation_array();		
				$array1 = implode("','",$array1);
				$result = $wpdb->get_results("SELECT * FROM $table_ot WHERE ot_create_by=$current_user_id OR patient_id IN ('".$array."') OR operation_id IN ('".$array1."') ORDER BY operation_id DESC LIMIT 3");	
			}
			elseif($role == 'nurse') 
			{				
				$array=MJ_hmgt_nurse_patientid_list(); 
				
				$array = implode("','",$array);
				$result = $wpdb->get_results("SELECT * FROM $table_ot WHERE ot_create_by=$current_user_id OR patient_id IN ('".$array."')  ORDER BY operation_id DESC LIMIT 3");	
			}
			elseif($role == 'patient') 
			{
				$result = $wpdb->get_results("SELECT * FROM $table_ot WHERE patient_id=$current_user_id ORDER BY operation_id DESC LIMIT 3");	
			}			
		}
		else
		{
			$result = $wpdb->get_results("SELECT * FROM $table_ot ORDER BY operation_id DESC LIMIT 3");
		}
		return $result;		
	}
	//get single oparation data
	public function MJ_hmgt_get_single_operation_by_title_id($id)
	{
		global $wpdb;
		$table_ot = $wpdb->prefix. 'hmgt_ot';	
		$result = $wpdb->get_row("SELECT * FROM $table_ot where operation_title = $id");
		return $result;	
	}
}
?>