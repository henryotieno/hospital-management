<?php
class MJ_hmgt_bloodbank
{
	//add blood donor
	public function MJ_hmgt_add_blood_donor($data)
	{
		global $wpdb;
		$table_blooddonor=$wpdb->prefix. 'hmgt_bld_donor';
		$table_bloodbank=$wpdb->prefix. 'hmgt_blood_bank';
		
		$donordata['donor_name']=MJ_hmgt_strip_tags_and_stripslashes($data['bool_dodnor_name']);
		$donordata['donor_gender']=$data['gender'];
		$donordata['donor_age']=$data['dodnor_age'];
		$donordata['donor_phone']=MJ_hmgt_strip_tags_and_stripslashes($data['phone']);
		$donordata['donor_email']=MJ_hmgt_strip_tags_and_stripslashes($data['email']);
		$donordata['blood_group']=MJ_hmgt_strip_tags_and_stripslashes($data['blood_group']);		
		$donordata['last_donet_date']=MJ_hmgt_get_format_for_db($data['last_donate_date']);
		$donordata['donor_create_date']=date("Y-m-d");
		$donordata['donor_create_by']=get_current_user_id();
		$donordata['blood_status']=$data['blood_status'];	
			
		
		if($data['action']=='edit')
		{
			$donor_dataid['bld_donor_id']=$data['blooddonor_id'];
			$result=$wpdb->update( $table_blooddonor, $donordata ,$donor_dataid);	
			
			//update blood status
			$old_blood_group=MJ_hmgt_strip_tags_and_stripslashes($data['old_blood_group']);		
			$result_old_blood_group = $wpdb->get_row("SELECT * FROM $table_bloodbank where blood_group='$old_blood_group'");
			$oldblood_status=$result_old_blood_group->blood_status;
			$newblood_status=$oldblood_status-$data['old_blood_status'];
			
			$blood_dataid['blood_id']=$result_old_blood_group->blood_id;
			$blooddata['blood_status']=$newblood_status;
			$update_blood_status=$wpdb->update( $table_bloodbank, $blooddata ,$blood_dataid);		
			
			$blood_group=MJ_hmgt_strip_tags_and_stripslashes($data['blood_group']);			
			$result_blood_group = $wpdb->get_row("SELECT * FROM $table_bloodbank where blood_group='$blood_group'");
			
			if(empty($result_blood_group))
			{			
				$blooddata['blood_group']=MJ_hmgt_strip_tags_and_stripslashes($data['blood_group']);
				$blooddata['blood_status']=$data['blood_status'];
				$blooddata['blood_create_date']=date("Y-m-d");
				$blooddata['blood_create_by']= get_current_user_id();
				
				$result=$wpdb->insert( $table_bloodbank,$blooddata);
			}
			else
			{
				$old_blood_status=$result_blood_group->blood_status;
				$new_blood_status=$old_blood_status+$data['blood_status'];
				
				$blood_dataid['blood_id']=$result_blood_group->blood_id;
				$blooddata['blood_status']=$new_blood_status;			
				$update_blood_status=$wpdb->update( $table_bloodbank, $blooddata ,$blood_dataid);
			}
			
			MJ_hmgt_append_audit_log(''.esc_html__('Update boold doner ','hospital_mgt').'',get_current_user_id());
			return $result;
		}
		else
		{
			$result=$wpdb->insert( $table_blooddonor,$donordata);
			
			//update blood status
			$blood_group=MJ_hmgt_strip_tags_and_stripslashes($data['blood_group']);			
			$result_blood_group = $wpdb->get_row("SELECT * FROM $table_bloodbank where blood_group='$blood_group'");
			if(empty($result_blood_group))
			{			
				$blooddata['blood_group']=MJ_hmgt_strip_tags_and_stripslashes($data['blood_group']);
				$blooddata['blood_status']=$data['blood_status'];
				$blooddata['blood_create_date']=date("Y-m-d");
				$blooddata['blood_create_by']= get_current_user_id();
				
				$result=$wpdb->insert( $table_bloodbank,$blooddata);
			}
			else
			{	
				$old_blood_status=$result_blood_group->blood_status;
				$new_blood_status=$old_blood_status+$data['blood_status'];
			
				$blood_dataid['blood_id']=$result_blood_group->blood_id;
				$blooddata['blood_status']=$new_blood_status;
				$update_blood_status=$wpdb->update( $table_bloodbank, $blooddata ,$blood_dataid);
			}
			MJ_hmgt_append_audit_log(''.esc_html__('Add new blood doner ','hospital_mgt').'',get_current_user_id());
			return $result;
		}
	}
	//add dispatch blood
	public function MJ_hmgt_add_dispatch_blood($data)
	{
		global $wpdb;
		$table_hmgt_dispatch_blood = $wpdb->prefix . 'hmgt_dispatch_blood';
		$table_bloodbank=$wpdb->prefix. 'hmgt_blood_bank';
		$hmgt_patient_transation = $wpdb->prefix. 'hmgt_patient_transation';
		
		$dispatchblooddata['patient_id']=$data['patient_id'];
		$dispatchblooddata['blood_group']=MJ_hmgt_strip_tags_and_stripslashes($data['blood_group']);		
		$dispatchblooddata['blood_charge']=$data['blood_charge'];		
		$dispatchblooddata['blood_status']=$data['blood_status'];		
		$dispatchblooddata['date']=MJ_hmgt_get_format_for_db($data['date']);
		$dispatchblooddata['dispatch_blood_create_date']=date("Y-m-d");
		$dispatchblooddata['dispatch_blood_create_by']=get_current_user_id();
		if(!empty($data['tax']))
		{		
			$dispatchblooddata['tax']=implode(",",$data['tax']);
		}
		else
		{
			$dispatchblooddata['tax']=null;
		}
		$tax_array=$data['tax'];
			
		$blood_charge=$data['blood_charge'];	
		
		if(!empty($tax_array))
		{
			$total_tax=0;
			foreach($tax_array as $tax_id)
			{
				$tax_percentage=MJ_hmgt_tax_percentage_by_tax_id($tax_id);
				$tax_amount=$blood_charge * $tax_percentage / 100;
				
				$total_tax=$total_tax + $tax_amount;				
			}
			
			$total_blood_charge=$blood_charge+$total_tax;
		}
		else
		{
			$total_tax=0;
			$total_blood_charge=$blood_charge;
		}	
		$dispatchblooddata['total_tax']=$total_tax;
		$dispatchblooddata['total_blood_charge']=$total_blood_charge;
		
		//invoice patient transaction entry
		$feesdata['patient_id'] = $data['patient_id'];
		$feesdata['type_id'] = MJ_hmgt_strip_tags_and_stripslashes($data['blood_group']);
		$feesdata['type_value'] =$blood_charge;
		$feesdata['type_tax'] =$total_tax;
		$feesdata['type_total_value'] =$total_blood_charge;
		$feesdata['status'] ="Unpaid";
		$feesdata['unit'] =$data['blood_status'].' Bags';	
		$feesdata['type'] ="Blood Charges";
		$feesdata['date'] =date("Y-m-d");
		
		if($data['action']=='edit')
		{
			$dispatchblood_id['dispatchblood_id']=$data['dispatchblood_id'];
			$result=$wpdb->update( $table_hmgt_dispatch_blood, $dispatchblooddata ,$dispatchblood_id);		
			
			//update blood status
			$old_blood_group=MJ_hmgt_strip_tags_and_stripslashes($data['old_blood_group']);		
			$result_old_blood_group = $wpdb->get_row("SELECT * FROM $table_bloodbank where blood_group='$old_blood_group'");
			$oldblood_status=$result_old_blood_group->blood_status;
			$newblood_status=$oldblood_status+$data['old_blood_status'];
			
			$blood_dataid['blood_id']=$result_old_blood_group->blood_id;
			$blooddata['blood_status']=$newblood_status;
			$update_blood_status=$wpdb->update( $table_bloodbank, $blooddata ,$blood_dataid);		
			
			$blood_group=MJ_hmgt_strip_tags_and_stripslashes($data['blood_group']);			
			$result_blood_group = $wpdb->get_row("SELECT * FROM $table_bloodbank where blood_group='$blood_group'");
			
			if(!empty($result_blood_group))
			{						
				$old_blood_status=$result_blood_group->blood_status;
				$new_blood_status=$old_blood_status-$data['blood_status'];
				
				$blood_dataid['blood_id']=$result_blood_group->blood_id;
				$blooddata['blood_status']=$new_blood_status;			
				$update_blood_status=$wpdb->update( $table_bloodbank, $blooddata ,$blood_dataid);
			}			
					
			MJ_hmgt_append_audit_log(''.esc_html__('Add Dispatch blood ','hospital_mgt').'',get_current_user_id());
			
			//delete patient transaction
			$delete_blood_charge = $wpdb->query("DELETE FROM $hmgt_patient_transation where type='Blood Charges' AND refer_id= ".$data['dispatchblood_id']);
			
			//insert patient transaction
			$feesdata['refer_id'] = $data['dispatchblood_id'];		
			$wpdb->insert($hmgt_patient_transation,$feesdata);
				
			return $result;
		}
		else
		{
			$result=$wpdb->insert( $table_hmgt_dispatch_blood,$dispatchblooddata);
			$pre_id = $wpdb->insert_id;
			
			//update blood status
			$blood_group=MJ_hmgt_strip_tags_and_stripslashes($data['blood_group']);			
			$result_blood_group = $wpdb->get_row("SELECT * FROM $table_bloodbank where blood_group='$blood_group'");
			
			if(!empty($result_blood_group))
			{							
				$old_blood_status=$result_blood_group->blood_status;
				$new_blood_status=$old_blood_status-$data['blood_status'];
			
				$blood_dataid['blood_id']=$result_blood_group->blood_id;
				$blooddata['blood_status']=$new_blood_status;
				$update_blood_status=$wpdb->update( $table_bloodbank, $blooddata ,$blood_dataid);
			}
			
			MJ_hmgt_append_audit_log(''.esc_html__('Add Dispatch blood ','hospital_mgt').'',get_current_user_id());
			
			//insert patient transaction
			$feesdata['refer_id'] = $pre_id;
			$invoice = $wpdb->insert($hmgt_patient_transation,$feesdata);				
				
			return $result;
		}
	}
	//get all blooddonors
	public function MJ_hmgt_get_all_blooddonors()
	{
		global $wpdb;
		$table_blooddonor=$wpdb->prefix. 'hmgt_bld_donor';
		
		$result = $wpdb->get_results("SELECT * FROM $table_blooddonor");
		return $result;
		
	}
	//get all dispatch blood data
	public function MJ_hmgt_get_all_dispatch_blood_data()
	{
		global $wpdb;
		$table_hmgt_dispatch_blood = $wpdb->prefix . 'hmgt_dispatch_blood';
		
		$result = $wpdb->get_results("SELECT * FROM $table_hmgt_dispatch_blood");
		return $result;
		
	}
	public function MJ_hmgt_get_petient_dispatch_blood_data()
	{
		global $wpdb;
		$table_hmgt_dispatch_blood = $wpdb->prefix . 'hmgt_dispatch_blood';
		$user_id=get_current_user_id();	
		$result = $wpdb->get_results("SELECT * FROM $table_hmgt_dispatch_blood where patient_id=$user_id");
		return $result;
		
	}
	//get all dispatch blood data by created by 
	public function MJ_hmgt_get_all_dispatch_blood_data_created_by()
	{
		global $wpdb;
		$table_hmgt_dispatch_blood = $wpdb->prefix . 'hmgt_dispatch_blood';
		$user_id=get_current_user_id();	
		$result = $wpdb->get_results("SELECT * FROM $table_hmgt_dispatch_blood where dispatch_blood_create_by=$user_id");
		return $result;		
	}
	//get doctor created all dispatch blood data
	public function MJ_hmgt_get_doctor_all_dispatch_blood_data_created_by()
	{
		global $wpdb;
		$table_hmgt_dispatch_blood = $wpdb->prefix . 'hmgt_dispatch_blood';
		$user_id=get_current_user_id();	
		$array=MJ_hmgt_doctor_patientid_list(); 
		$array = implode("','",$array);
		$result = $wpdb->get_results("SELECT * FROM $table_hmgt_dispatch_blood where dispatch_blood_create_by=$user_id OR patient_id IN ('".$array."')");
		return $result;
		
	}
	//get nurse created all dispatch blood data
	public function MJ_hmgt_get_nurse_all_dispatch_blood_data_created_by()
	{
		global $wpdb;
		$table_hmgt_dispatch_blood = $wpdb->prefix . 'hmgt_dispatch_blood';
		$user_id=get_current_user_id();	
		$array=MJ_hmgt_nurse_patientid_list(); 
		
		$array = implode("','",$array);
		$result = $wpdb->get_results("SELECT * FROM $table_hmgt_dispatch_blood where dispatch_blood_create_by=$user_id OR patient_id IN ('".$array."')");
		return $result;
		
	}
	//get all blood donors by created by
	public function MJ_hmgt_get_all_blooddonors_by_donor_create_by()
	{
		global $wpdb;
		$table_blooddonor=$wpdb->prefix. 'hmgt_bld_donor';
		$user_id=get_current_user_id();	
		$result = $wpdb->get_results("SELECT * FROM $table_blooddonor where donor_create_by=$user_id");
		return $result;
		
	}
	//delete blood donor data
	public function MJ_hmgt_delete_blooddonor($blooddonor_id)
	{
		global $wpdb;
		$table_blooddonor=$wpdb->prefix. 'hmgt_bld_donor';
		$result = $wpdb->query("DELETE FROM $table_blooddonor where bld_donor_id= ".$blooddonor_id);
		return $result;
	}
	//get single blood donor data
	public function MJ_hmgt_get_single_blooddonor($donor_id)
	{
		global $wpdb;
		$table_blooddonor=$wpdb->prefix. 'hmgt_bld_donor';
	
		$result = $wpdb->get_row("SELECT * FROM $table_blooddonor where bld_donor_id= ".$donor_id);
		return $result;
	}
	//get single dispatch blood data
	public function MJ_hmgt_get_single_dispatch_blood_data($dispatchblood_id)
	{
		global $wpdb;
		$table_hmgt_dispatch_blood = $wpdb->prefix . 'hmgt_dispatch_blood';
	
		$result = $wpdb->get_row("SELECT * FROM $table_hmgt_dispatch_blood where dispatchblood_id= ".$dispatchblood_id);
		return $result;
	}
	//add blood group data
	public function MJ_hmgt_add_blood_group($data)
	{
		global $wpdb;
		$table_bloodbank=$wpdb->prefix. 'hmgt_blood_bank';
		$blooddata['blood_group']=MJ_hmgt_strip_tags_and_stripslashes($data['blood_group']);
		$blooddata['blood_status']=$data['blood_status'];
		$blooddata['blood_create_date']=date("Y-m-d");
		$blooddata['blood_create_by']= get_current_user_id();
		
		if($data['action']=='edit')		
		{
			$blood_dataid['blood_id']=$data['bloodgroup_id'];
			$result=$wpdb->update( $table_bloodbank, $blooddata ,$blood_dataid);
			MJ_hmgt_append_audit_log(''.esc_html__('Update boold group detail ','hospital_mgt').'',get_current_user_id());
			return $result;
		}
		else
		{
			$result=$wpdb->insert( $table_bloodbank,$blooddata);
			MJ_hmgt_append_audit_log(''.esc_html__('Add new blood group ','hospital_mgt').'',get_current_user_id());
			return $result;
		}
		
	}
	// get single blood group data
	public function MJ_hmgt_get_single_bloodgroup($blood_id)
	{
		global $wpdb;
		$table_bloodbank=$wpdb->prefix. 'hmgt_blood_bank';
	
		$result = $wpdb->get_row("SELECT * FROM $table_bloodbank where blood_id= ".$blood_id);
		return $result;
	}
	//delete nlood group data
	public function MJ_hmgt_delete_bloodgroup($blood_id)
	{
		global $wpdb;
		$table_bloodbank=$wpdb->prefix. 'hmgt_blood_bank';
		$result = $wpdb->query("DELETE FROM $table_bloodbank where blood_id= ".$blood_id);
		MJ_hmgt_append_audit_log(''.esc_html__('Delete blood group ','hospital_mgt').'',get_current_user_id());
		return $result;
	}
	//delete dispatch blood data
	public function MJ_hmgt_delete_dispatchblood_data($dispatchblood_id)
	{
		global $wpdb;
		$table_hmgt_dispatch_blood = $wpdb->prefix . 'hmgt_dispatch_blood';
		$result = $wpdb->query("DELETE FROM $table_hmgt_dispatch_blood where dispatchblood_id= ".$dispatchblood_id);
		MJ_hmgt_append_audit_log(''.esc_html__('Delete Dispatch blood ','hospital_mgt').'',get_current_user_id());
		return $result;
	}
	//get all blood group
	public function MJ_hmgt_get_all_bloodgroups()
	{
		global $wpdb;
		$table_bloodbank=$wpdb->prefix. 'hmgt_blood_bank';
		
		$result = $wpdb->get_results("SELECT * FROM $table_bloodbank");
		return $result;		
	}
	public function MJ_hmgt_get_single_dispatch_blood_group($blood_group)
	{
		global $wpdb;
		$table_hmgt_dispatch_blood = $wpdb->prefix . 'hmgt_dispatch_blood';
	
		$result = $wpdb->get_row("SELECT * FROM $table_hmgt_dispatch_blood where blood_group= '$blood_group'");
		return $result;
	}
}
?>