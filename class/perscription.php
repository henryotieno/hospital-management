<?php
class MJ_hmgt_prescription 
{
	//add prescription data
	public function MJ_hmgt_add_prescription($data)
	{	
		global $wpdb;	
		$table_prescription=$wpdb->prefix. 'hmgt_priscription';
		$table_hmgt_charges = $wpdb->prefix. 'hmgt_charges';
		$hmgt_patient_transation = $wpdb->prefix. 'hmgt_patient_transation';
		$table_treatment = $wpdb->prefix. 'hmgt_treatment';
		
		if($data['prescription_type'] == 'treatment')
		{			
			$entry_value=$this->MJ_hmgt_get_entry_records($data);
			$medication_value=$this->MJ_hmgt_get_medication_records($data);
			$prescriptiondata['patient_id']=$data['patient_id'];
			$prescriptiondata['prescription_type']=MJ_hmgt_strip_tags_and_stripslashes($data['prescription_type']);
			$prescriptiondata['report_description']='';
			$prescriptiondata['teratment_id']=$data['treatment_id'];
			$prescriptiondata['case_history']=MJ_hmgt_strip_tags_and_stripslashes($data['case_history']);
			$prescriptiondata['medication_list']=$medication_value;
			$prescriptiondata['treatment_note']=MJ_hmgt_strip_tags_and_stripslashes($data['note']);
			$prescriptiondata['pris_create_date']=date("Y-m-d");
			$prescriptiondata['prescription_by']=get_current_user_id();
			$prescriptiondata['custom_field']=$entry_value;		
			$prescriptiondata['doctor_id']=$data['doctor_id'];				
			$prescriptiondata['doctor_visiting_charge']=null;
			$prescriptiondata['doctor_consulting_charge']=null;
			$prescriptiondata['doctor_consulting_charge']=null;
			
			$treatment_data = $wpdb->get_row("SELECT * FROM $table_treatment where treatment_id= ".$data['treatment_id']);
			
			$treatment_price=$treatment_data->treatment_price;
			$treatment_tax=$treatment_data->tax;
			$tax_array=explode(',',$treatment_tax);
			if(isset($_FILES['attach']) && !empty($_FILES['attach']) && $_FILES['attach']['size'] !=0)
			{
			if($_FILES['attach']['size'] > 0)
				$esami_image=MJ_hmgt_load_documets($_FILES['attach'],'attach','prescription');	
				
			}
			else
			{
			if(isset($_REQUEST['hidden_esami_image']))
				$esami_image=$_REQUEST['hidden_esami_image'];
			
			}
			if(!empty($esami_image))
			{
				$prescriptiondata['attach'] = $esami_image;
			}
			if(!empty($tax_array))
			{
				$total_tax=0;
				foreach($tax_array as $tax_id)
				{
					$tax_percentage=MJ_hmgt_tax_percentage_by_tax_id($tax_id);
					$tax_amount=$treatment_price * $tax_percentage / 100;
					
					$total_tax=$total_tax + $tax_amount;
					
				}
				
				$treatment_tax=$total_tax;
				$total_treatment_price=$treatment_price+$total_tax;
			}
			else
			{
				$total_tax=0;
				$treatment_tax=$total_tax;
				$total_treatment_price=$treatment_price;
			}
			
			$feesdata['patient_id'] = $prescriptiondata['patient_id'];
			$feesdata['type_id'] = $data['treatment_id'];
			$feesdata['type_value'] = $treatment_price;
			$feesdata['type_tax'] =$treatment_tax;
			$feesdata['type_total_value'] =$total_treatment_price;
			$feesdata['status'] ="Unpaid";
			$feesdata['type'] ="Treatment Fees";
			$feesdata['date'] =date("Y-m-d");					
			
		}
		else
		{					
			$prescriptiondata['patient_id']=$data['patient_id'];
			$prescriptiondata['prescription_type']=MJ_hmgt_strip_tags_and_stripslashes($data['prescription_type']);
			$prescriptiondata['report_description']=MJ_hmgt_strip_tags_and_stripslashes($data['report_description']);
			$prescriptiondata['teratment_id']='';
			$prescriptiondata['case_history']='';
			$prescriptiondata['medication_list']='';
			$prescriptiondata['treatment_note']='';
			$prescriptiondata['pris_create_date']=date("Y-m-d");
			$prescriptiondata['prescription_by']=get_current_user_id();
			$prescriptiondata['doctor_id']=$data['doctor_id'];	
			$prescriptiondata['custom_field']='';
            $prescriptiondata['report_type']=implode(",",(array)$data['report_type']);			
            $prescriptiondata['status']='Pending';
			if(!empty($data['doctor_visiting_charge']))
			{
				$prescriptiondata['doctor_visiting_charge']=$data['doctor_visiting_charge'];
			}
			$prescriptiondata['doctor_consulting_charge']=$data['doctor_consulting_charge'];
			
			
			$visiting_fees = get_user_meta($prescriptiondata['doctor_id'],'visiting_fees',true);
			if(empty($visiting_fees))
			{
				$visiting_fees=0;
			}
			$consulting_fees = get_user_meta($prescriptiondata['doctor_id'],'consulting_fees',true);
			if(empty($consulting_fees))
			{
				$consulting_fees=0;
			}
			$visiting_fees_tax = get_user_meta($prescriptiondata['doctor_id'],'visiting_fees_tax',true);
			$consulting_fees_tax = get_user_meta($prescriptiondata['doctor_id'],'consulting_fees_tax',true);
			
			$visiting_fees_tax_array=explode(',',$visiting_fees_tax);
			$consulting_fees_tax_array=explode(',',$consulting_fees_tax);
			
			$fees=0;
			$total_tax_amount=0;
			$doctor_total_fees=0;
						
			$doctor_visiting_charge_check='0';
			if(isset($data['doctor_visiting_charge']))
				$doctor_visiting_charge_check = $data['doctor_visiting_charge'];
			
			$doctor_consulting_charge_check='0';
			if(isset($data['doctor_consulting_charge']))
				$doctor_consulting_charge_check = $data['doctor_consulting_charge'];
			
			if($doctor_visiting_charge_check == '1' && $doctor_consulting_charge_check == '1')
			{				
				//visiting fees tax calculate
				$total_visiting_fees_tax=0;
				
				if(!empty($visiting_fees_tax_array))
				{					
					foreach($visiting_fees_tax_array as $tax_id)
					{
						$tax_percentage=MJ_hmgt_tax_percentage_by_tax_id($tax_id);
						$tax_amount= $visiting_fees * $tax_percentage / 100;
						$total_visiting_fees_tax=$total_visiting_fees_tax + $tax_amount;					
					}
				}
				
				//consulting fees tax calculate
				$total_consulting_fees_tax=0;
				if(!empty($consulting_fees_tax_array))
				{					
					foreach($consulting_fees_tax_array as $tax_id)
					{
						$tax_percentage=MJ_hmgt_tax_percentage_by_tax_id($tax_id);
						$tax_amount= $consulting_fees * $tax_percentage / 100;
						
						$total_consulting_fees_tax=$total_consulting_fees_tax + $tax_amount;					
					}
				}
				
				$fees=$visiting_fees+$consulting_fees;
				$total_tax_amount=$total_visiting_fees_tax+$total_consulting_fees_tax;
				$doctor_total_fees=$fees+$total_tax_amount;
					
			}
			elseif($doctor_visiting_charge_check == '1')
			{				
				//visiting fees tax calculate
				$total_visiting_fees_tax=0;
				
				if(!empty($visiting_fees_tax_array))
				{					
					foreach($visiting_fees_tax_array as $tax_id)
					{
						$tax_percentage=MJ_hmgt_tax_percentage_by_tax_id($tax_id);
						$tax_amount= $visiting_fees * $tax_percentage / 100;
						
						$total_visiting_fees_tax=$total_visiting_fees_tax + $tax_amount;					
					}
				}
								
				$fees=$visiting_fees;
				$total_tax_amount=$total_visiting_fees_tax;
				$doctor_total_fees=$fees+$total_tax_amount;
			}
			elseif($doctor_consulting_charge_check == '1')
			{
								
				//consulting fees tax calculate
				$total_consulting_fees_tax=0;
				
				if(!empty($consulting_fees_tax_array))
				{					
					foreach($consulting_fees_tax_array as $tax_id)
					{
						$tax_percentage=MJ_hmgt_tax_percentage_by_tax_id($tax_id);
						$tax_amount= $consulting_fees * $tax_percentage / 100;
						
						$total_consulting_fees_tax=$total_consulting_fees_tax + $tax_amount;					
					}
				}
				
				$fees=$consulting_fees;
				$total_tax_amount=$total_consulting_fees_tax;
				$doctor_total_fees=$fees+$total_tax_amount;
			}		
			
			$feesdata['patient_id'] = $prescriptiondata['patient_id'];
			$feesdata['status'] ="Unpaid";
			$feesdata['date'] =date("Y-m-d");	
			
			$charge_data['charge_label']= 'Doctor Fees';
			$charge_data['charge_type']= 'doctorfees';
			$charge_data['room_number']="";
			$charge_data['bed_type']="";
			$charge_data['charges']=$doctor_total_fees;
			$charge_data['patient_id']=$prescriptiondata['patient_id'];
			$charge_data['status']=0;
			$charge_data['created_date']=date("Y-m-d");
			$charge_data['created_by']=get_current_user_id();
			
		}
		if(isset($data['patient_convert']))
		{
			update_user_meta($data['patient_id'],'patient_type',$data['patient_convert']);
		}
		
		if($data['action']=='edit')
		{
			$prescription_dataid['priscription_id']=$data['prescription_id'];
			$charge_referid['refer_id']=$data['prescription_id'];
			$charge_referid['charge_type']='doctorfees';
			
			if(isset($_FILES['attach']) && !empty($_FILES['attach']) && $_FILES['attach']['size'] !=0)
			{
				if($_FILES['attach']['size'] > 0)
					$document_name=MJ_hmgt_load_documets($_FILES['attach'],'attach','prescription');
			}
			else{
				if(isset($_REQUEST['edit_attach']))
					$document_name=$_REQUEST['edit_attach'];				
			}
			
			$prescriptiondata['attach'] = $document_name;
			
			$result=$wpdb->update( $table_prescription, $prescriptiondata ,$prescription_dataid);
			$wpdb->update( $table_hmgt_charges, $charge_data,$charge_referid );
			
			$delete_trement_charge = $wpdb->query("DELETE FROM $hmgt_patient_transation where type='Treatment Fees' AND refer_id= ".$data['prescription_id']);
			
			
			if($data['prescription_type'] == 'treatment')
			{		
				$feesdata['refer_id'] = $data['prescription_id'];		
				$wpdb->insert($hmgt_patient_transation,$feesdata);
			}		
			
			$delete_doctor_charge = $wpdb->query("DELETE FROM $hmgt_patient_transation where type='Doctor Fees' AND refer_id= ".$data['prescription_id']);
			
			if($data['prescription_type'] == 'report')
			{		
				if($doctor_visiting_charge_check == '1' || $doctor_consulting_charge_check == '1')
				{
					$feesdata['type'] ="Doctor Fees";
					$feesdata['type_id'] = $data['doctor_id'];	
					$feesdata['refer_id'] = $data['prescription_id'];
					$feesdata['type_value'] =$fees;
					$feesdata['type_tax'] =$total_tax_amount;
					$feesdata['type_total_value'] =$doctor_total_fees;
					$wpdb->insert($hmgt_patient_transation,$feesdata);
				}
			}
			
			MJ_hmgt_append_audit_log(''.esc_html__('Update prescription ','hospital_mgt').'',get_current_user_id());
			return $result;
		}
		else
		{		
			$result=$wpdb->insert( $table_prescription,$prescriptiondata);
			
				$pre_id = $wpdb->insert_id;
				$doctorid=get_current_user_id();
				$doctordata=get_userdata($doctorid);
				$doctorname=$doctordata->display_name;
				$patient=get_userdata($_POST['patient_id']);
				$patient_email=$patient->user_email;
				$patientname=$patient->display_name;
				$user_number=array();
				//-------------------- SEND SMS -----------------//
				if(!empty(get_user_meta($_POST['patient_id'], 'phonecode',true))){ $phone_code=get_user_meta($_POST['patient_id'], 'phonecode',true); }else{ $phone_code='+'.MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }
								
				$user_number[] = $phone_code.get_user_meta($_POST['patient_id'], 'mobile',true);
				$user_number1 = $phone_code.get_user_meta($_POST['patient_id'], 'mobile',true);
				if(is_plugin_active('sms-pack/sms-pack.php'))
				{
					$hospital_name = get_option('hmgt_hospital_name');
					$message_content ="Your Doctor has created  new prescription for you";
					$current_sms_service = get_option( 'smgt_sms_service');
					$args = array();
					$args['mobile']=$user_number;
					$args['message_from']="Prescription";
					$args['message']=$message_content;					
					if($current_sms_service=='telerivet' || $current_sms_service ="MSG91" || $current_sms_service=='bulksmsgateway.in' || $current_sms_service=='textlocal.in' || $current_sms_service=='bulksmsnigeria' || $current_sms_service=='africastalking' || $current_sms_service == 'clickatell')
					{				
						$send = send_sms($args);							
					}
				}
				$current_sms_service = get_option('hmgt_sms_service');$hospital_name = get_option('hmgt_hospital_name');
				$message_content ="Your Doctor has created new prescription for you";
				if($current_sms_service == 'clickatell')
				{
					$clickatell=get_option('hmgt_clickatell_sms_service');
					$username = urlencode($clickatell['username']);
					$password = urlencode($clickatell['password']);
					$api_id = urlencode($clickatell['api_key']);
					$to = $user_number1;
					$message = urlencode($message_content);
					$send=file_get_contents("https://api.clickatell.com/http/sendmsg". "?user=$username&password=$password&api_id=$api_id&to=$to&text=$message"); 
				}
				if($current_sms_service == 'msg91')
				{
					//MSG91
					$mobile_number=$user_number1;
					$country_code="+".MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' ));
					$message = $message_content; // Message Text
					MJ_hmgt_msg91_send_mail_function($mobile_number,$message,$country_code);
				}		
			if($data['prescription_type']=='treatment')
			{
				$hospital_name = get_option('hmgt_hospital_name');
				$subject =get_option('MJ_hmgt_add_prescription_subject');
				$sub_arr['{{Doctor Name}}']=$doctorname;
				$sub_arr['{{Hospital Name}}']=$hospital_name;
				$subject = MJ_hmgt_subject_string_replacemnet($sub_arr,$subject);

				$arr['{{Patient Name}}']=$patientname;			
				$arr['{{Doctor Name}}']=$doctorname;
				$arr['{{Hospital Name}}']=$hospital_name;
				$message = get_option('MJ_hmgt_add_prescription_template');
				$message_replacement = MJ_hmgt_string_replacemnet($arr,$message);

				$to=$patient_email;
				
				//send mail for for pdf file function
				MJ_hmgt_send_perscription_mail($to,$subject,$message_replacement,$pre_id);
			}
			
			if($doctor_visiting_charge_check == '1' || $doctor_consulting_charge_check == '1')
			{
				$charge_data['refer_id']=$pre_id;
				$wpdb->insert( $table_hmgt_charges, $charge_data );
			}
			
			MJ_hmgt_append_audit_log(''.esc_html__('Add new prescription ','hospital_mgt').'',get_current_user_id());
			
			if($data['prescription_type']=='treatment')
			{
				$feesdata['refer_id'] = $pre_id;
				$invoice = $wpdb->insert($hmgt_patient_transation,$feesdata);				           		
			}
			else
			{	
				if($doctor_visiting_charge_check == '1' || $doctor_consulting_charge_check == '1')
				{
					$feesdata['type'] ="Doctor Fees";
					$feesdata['type_id'] = $data['doctor_id'];	
					$feesdata['refer_id'] = $pre_id;
					$feesdata['type_value'] =$fees;
					$feesdata['type_tax'] =$total_tax_amount;
					$feesdata['type_total_value'] =$doctor_total_fees;
					$wpdb->insert($hmgt_patient_transation,$feesdata);
				}				
			}
			return $result;			
		}
	}
	//get custom entry
	public function MJ_hmgt_get_entry_records($data)
	{
		$all_value_entry=$data['custom_value'];
		$all_label=$data['custom_label'];
			
		$entry_data=array();
		$i=0;
		foreach($all_value_entry as $one_entry)
		{
			$entry_data[]= array('label'=>MJ_hmgt_strip_tags_and_stripslashes($all_label[$i]),'value'=>MJ_hmgt_strip_tags_and_stripslashes($one_entry));
			$i++;
		}
		return json_encode($entry_data);
	}
	//get medication record entry
	public function MJ_hmgt_get_medication_records($data)
	{
		$all_medication=$data['medication'];
		$medicationm_time=$data['times1'];
		$medication_per_day=$data['days'];
		$medication_takes_time=$data['takes_time'];
		$medication_time_period=$data['time_period'];
			
		$all_data=array();
		$i=0;
		foreach($all_medication as $one_record)
		{
			$all_data[]= array('medication_name'=>$one_record,'time'=>$medicationm_time[$i],'per_days'=>$medication_per_day[$i],'takes_time'=>$medication_takes_time[$i],'time_period'=>$medication_time_period[$i]);
			$i++;
		}
		return json_encode($all_data);
	}
	//delete presciption data
	public function MJ_hmgt_delete_prescription($prescription_id)
	{
		global $wpdb;
		$table_prescription = $wpdb->prefix. 'hmgt_priscription';
		$result = $wpdb->query("DELETE FROM $table_prescription where priscription_id= ".$prescription_id);
		MJ_hmgt_append_audit_log(''.esc_html__('Delete presciption ','hospital_mgt').'',get_current_user_id());
		return $result;
	}
	//get presciption data
	public function MJ_hmgt_get_prescription_data($prescription_id)
	{
		global $wpdb;
		$table_prescription = $wpdb->prefix. 'hmgt_priscription';
	
		$result = $wpdb->get_row("SELECT * FROM $table_prescription where priscription_id= ".$prescription_id);
		return $result;
	}
	//get all presciption data
	public function MJ_hmgt_get_all_prescription()
	{
		global $wpdb;
		$table_prescription = $wpdb->prefix. 'hmgt_priscription';
		
		$result = $wpdb->get_results("SELECT * FROM $table_prescription ORDER BY priscription_id DESC");
		return $result;
		
	}
	//get all diagnosis request
	public function MJ_hmgt_get_all_diagnosis_requst()
	{
		global $wpdb;
		$table_prescription = $wpdb->prefix. 'hmgt_priscription';
		
		$result = $wpdb->get_results("SELECT * FROM $table_prescription where prescription_type='report' AND status!='Completed'");
		return $result;	
	}
    //get all presciption by patinet id
	public function MJ_hmgt_get_all_prescription_patientid($patient_id)
	{
		global $wpdb;
		$table_prescription = $wpdb->prefix. 'hmgt_priscription';
		
		$result = $wpdb->get_results("SELECT * FROM $table_prescription where patient_id= $patient_id");
		return $result;		
	}
	//get all prescription report by patient id
	public function MJ_hmgt_get_all_report_prescription_patientid($patient_id)
	{
		global $wpdb;
		$table_prescription = $wpdb->prefix. 'hmgt_priscription';
		
		$result = $wpdb->get_results("SELECT * FROM $table_prescription where prescription_type='report' AND patient_id= $patient_id");
		return $result;
		
	}
	//get all presciption id
	public function MJ_hmgt_get_all_prescription_id($id)
	{
		global $wpdb;
		$table_prescription = $wpdb->prefix. 'hmgt_priscription';
		$result = $wpdb->get_results("SELECT * FROM $table_prescription where prescription_by= $id");
		return $result;
		
	}
	//get doctor all presciption id
	public function MJ_hmgt_get_doctor_all_prescription_id($id)
	{
		global $wpdb;
		$table_prescription = $wpdb->prefix. 'hmgt_priscription';
		$array=MJ_hmgt_doctor_patientid_list(); 
		$array = implode("','",$array);
		$result = $wpdb->get_results("SELECT * FROM $table_prescription where prescription_by= $id OR patient_id IN ('".$array."')");
		return $result;		
	}
	//get nurse all presciption id
	public function MJ_hmgt_get_nurse_all_prescription_id($id)
	{
		global $wpdb;
		$table_prescription = $wpdb->prefix. 'hmgt_priscription';
		$array=MJ_hmgt_nurse_patientid_list(); 
		
		$array = implode("','",$array);
		
		$result = $wpdb->get_results("SELECT * FROM $table_prescription where prescription_by= $id OR patient_id IN ('".$array."')");
		return $result;
		
	}
	//get presciption data on admin dashboard
	public function MJ_hmgt_get_prescription_on_admin_dashboard()
	{
		global $wpdb;
		$table_prescription = $wpdb->prefix. 'hmgt_priscription';
		
		$result = $wpdb->get_results("SELECT * FROM $table_prescription ORDER BY priscription_id DESC LIMIT 3");
		return $result;
		
	}
	//get presciption data on fronted dashboard
	public function MJ_hmgt_get_prescription_on_fronted_dashboard()
	{
		global $wpdb;
		$table_prescription = $wpdb->prefix. 'hmgt_priscription';
		
		$page='prescription';
		$data=MJ_hmgt_page_wise_view_data_on_fronted_dashboard($page);
		$role=MJ_hmgt_get_current_user_role();	
		$id=get_current_user_id();
					
		if($data==1)
		{			
			if($role == 'patient')
			{		
				$result = $wpdb->get_results("SELECT * FROM $table_prescription where patient_id= $id ORDER BY priscription_id DESC LIMIT 3");						
			}
			elseif($role == 'doctor') 
			{
				$array=MJ_hmgt_doctor_patientid_list(); 
				$array = implode("','",$array);
				$result = $wpdb->get_results("SELECT * FROM $table_prescription where prescription_by= $id OR patient_id IN ('".$array."') ORDER BY priscription_id DESC LIMIT 3");				
			} 
			elseif($role == 'nurse') 
			{
				$array=MJ_hmgt_nurse_patientid_list(); 
		
				$array = implode("','",$array);
		
				$result = $wpdb->get_results("SELECT * FROM $table_prescription where prescription_by= $id OR patient_id IN ('".$array."') ORDER BY priscription_id DESC LIMIT 3");				
			} 
			elseif($role == 'laboratorist' || $role == 'pharmacist' || $role == 'accountant' || $role == 'receptionist') 
			{
				$result = $wpdb->get_results("SELECT * FROM $table_prescription where prescription_by= $id ORDER BY priscription_id DESC LIMIT 3");				
			}			
		}
		else
		{
			$result = $wpdb->get_results("SELECT * FROM $table_prescription ORDER BY priscription_id DESC LIMIT 3");
		}
		return $result;		
	}
}
?>