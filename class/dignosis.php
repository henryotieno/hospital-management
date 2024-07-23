<?php
class MJ_hmgt_dignosis
{	
	//add Report type
	public function MJ_hmgt_add_report_type($data)
	{
		global $wpdb;
		
		$category_name=MJ_hmgt_strip_tags_and_stripslashes($data['category_name']);
		$report_cost=$data['report_cost'];
		$diagnosis_tax=$data['diagnosis_tax'];
		$diagnosis_description=MJ_hmgt_strip_tags_and_stripslashes($data['diagnosis_description']);
				
		$report_type_array=array("category_name"=>$category_name,"report_cost"=>$report_cost,"diagnosis_tax"=>$diagnosis_tax,"diagnosis_description"=>$diagnosis_description);
		
		$report_type=json_encode($report_type_array);
		
		$result = wp_insert_post( array(
				'post_status' => 'publish',
				'post_type' => 'report_type_category',
				'post_title' => $report_type ));
		
		MJ_hmgt_append_audit_log(''.esc_html__('Add new report type ','hospital_mgt').'',get_current_user_id());
		return $result;
	}
	//get all report type
	public function MJ_hmgt_get_all_report_type()
	{
		$args= array('post_type'=> 'report_type_category','posts_per_page'=>-1,'orderby'=>'post_title','order'=>'Asc');
		$result = get_posts( $args );
		return $result;
	
	}
	//get report type name
	public function MJ_hmgt_get_report_type_name($id)
	{
		$result = get_post( $id );
		return $result->post_title;
	}
	//get report by id
	public function MJ_hmgt_get_report_by_id($id)
	{
		$result = get_post( $id );
		return $result->post_title;
	}
	//delete report type
	public function MJ_hmgt_delete_report_type($id)
	{
		$result=wp_delete_post($id);
		MJ_hmgt_append_audit_log(''.esc_html__('Delete report type ','hospital_mgt').'',get_current_user_id());
		return $result;
	}
	//add dignosis report
	public function MJ_hmgt_add_dignosis($data)
	{
		global $wpdb;
		$table_diagnosis = $wpdb->prefix. 'hmgt_diagnosis';
		$hmgt_patient_transation = $wpdb->prefix. 'hmgt_patient_transation';
		
		//-------usersmeta table data--------------
		$diagnosisdata['patient_id']=$data['patient_id'];
		$diagnosisdata['report_type']=implode(",",$data['report_type']);
		
		$diagnosisdata['diagno_description']=MJ_hmgt_strip_tags_and_stripslashes($data['diagno_description']);		
		$diagnosisdata['report_cost']=$data['cost'];	
		$diagnosisdata['diagno_create_by']=get_current_user_id();
		$diagnosisdata['total_tax'] =$data['report_tax'];	
		$diagnosisdata['total_cost'] =$data['report_cost'];	
		
		//invoice patient transaction entry
		$feesdata['patient_id'] = $data['patient_id'];
		$feesdata['type_id'] = implode(",",$data['report_type']);
		$feesdata['type_value'] = $data['cost'];
		$feesdata['type_tax'] = $data['report_tax'];	
		$feesdata['type_total_value'] = $data['report_cost'];
		$feesdata['status'] ="Unpaid";
		$feesdata['type'] ="Dignosis Report Charges";		
		$feesdata['date'] =date("Y-m-d");		
		
		if($data['action']=='edit')
		{	
			//multiple dignosis upload
			$upload_dignosis_array=array();
			$not_upload_dignosis_array=array();
			
			$upload_dignosis_id_array=array();
			$not_upload_dignosis_id_array=array();
			
			$upload_dignosis_amount_array=array();
			$not_upload_dignosis_amount_array=array();
			
			if(!empty($_FILES['document']['name']))
			{
				$count_array=count($_FILES['document']['name']);

				for($a=0;$a<$count_array;$a++)
				{			
					foreach($_FILES['document'] as $image_key=>$image_val)
					{	
						if($_FILES['document']['name'][$a]!='')
						{	
							$diagnosis_array[$a]=array(
							'name'=>$_FILES['document']['name'][$a],
							'type'=>$_FILES['document']['type'][$a],
							'tmp_name'=>$_FILES['document']['tmp_name'][$a],
							'error'=>$_FILES['document']['error'][$a],
							'size'=>$_FILES['document']['size'][$a]
							);							
						}
						else
						{
							if(!empty($data['hidden_attach_report'][$a]))
							{
								$not_upload_dignosis_array[$a]=$data['hidden_attach_report'][$a];
								$not_upload_dignosis_id_array[$a]=$data['report_id'][$a];
								$not_upload_dignosis_amount_array[$a]=$data['diagnosis_total_amount'][$a];
							}	
						}
					}
				}
				if(!empty($diagnosis_array))
				{
					foreach($diagnosis_array as $key=>$value)		
					{	
						$get_file_name=$diagnosis_array[$key]['name'];	
						
						$upload_dignosis_array[]=MJ_hmgt_load_multiple_documets($value,$value,$get_file_name);				
						$upload_dignosis_id_array[]=$data['report_id'][$key];			
						$upload_dignosis_amount_array[]=$data['diagnosis_total_amount'][$key];			
					} 	
				}					
			}
			
			$upload_array_merge=array_merge($upload_dignosis_array,$not_upload_dignosis_array);
			$upload_id_array_merge=array_merge($upload_dignosis_id_array,$not_upload_dignosis_id_array);
			$upload_amount_array_merge=array_merge($upload_dignosis_amount_array,$not_upload_dignosis_amount_array);
		
			$report_array=array();				
			if(!empty($upload_array_merge))
			{	
				foreach($upload_array_merge as $key=>$value)
				{
					$report_array[]=array("report_id"=>$upload_id_array_merge[$key],"attach_report"=>$value,"report_amount"=>$upload_amount_array_merge[$key]);
				}	
			}
			$attach_report_data=json_encode($report_array);
			$diagnosisdata['attach_report']=$attach_report_data;
			$dignosis_id['diagnosis_id']=$data['diagnosisid'];
			$result=$wpdb->update( $table_diagnosis, $diagnosisdata ,$dignosis_id);
			MJ_hmgt_append_audit_log(''.esc_html__('Update diagnosis report ','hospital_mgt').'',get_current_user_id());
			
			//delete patient transaction
			$delete_dignosis_charge = $wpdb->query("DELETE FROM $hmgt_patient_transation where type='Dignosis Report Charges' AND refer_id= ".$data['diagnosisid']);
			
			//insert patient transaction
			$feesdata['refer_id'] = $data['diagnosisid'];		
			$wpdb->insert($hmgt_patient_transation,$feesdata);
			
			
			if(isset($result))
			{	
				//Common details for patient and doctor mail template//
				$patient=get_userdata($data['patient_id']);
				$patient_email=$patient->user_email;
				$patientname=$patient->display_name;
				$hospital_name = get_option('hmgt_hospital_name');
				//-------------------- SEND SMS ---------------------//
		
				if(!empty(get_user_meta($data['patient_id'], 'phonecode',true))){ $phone_code=get_user_meta($data['patient_id'], 'phonecode',true); }else{ $phone_code='+'.MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }
									
				$user_number[] = $phone_code.get_user_meta($data['patient_id'], 'mobile',true);
				$user_number1 = $phone_code.get_user_meta($data['patient_id'], 'mobile',true);
				if(is_plugin_active('sms-pack/sms-pack.php'))
				{
					$curency=html_entity_decode(MJ_hmgt_get_currency_symbol());
					$hospital_name = get_option('hmgt_hospital_name');
					$message_content ="Your have a new diagnosis report ".$curency." ".$data['report_cost']." Amount will be charged to you for the diagnosis report.";
					$current_sms_service 	= get_option( 'smgt_sms_service');
					$args = array();
					$args['mobile']=$user_number;
					$args['message_from']="Dignosis Report";
					$args['message']=$message_content;					
					if($current_sms_service=='telerivet' || $current_sms_service ="MSG91" || $current_sms_service=='bulksmsgateway.in' || $current_sms_service=='textlocal.in' || $current_sms_service=='bulksmsnigeria' || $current_sms_service=='africastalking' || $current_sms_service == 'clickatell')
					{				
						$send = send_sms($args);							
					}
				}
				$current_sms_service = get_option('hmgt_sms_service');
				$message_content ="Your have a new diagnosis report ".$curency." ".$data['report_cost']." Amount will be charged to you for the diagnosis report.";
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
				$attachments=array();
				foreach($upload_dignosis_array as $upload_dignosis_name)
				{
					array_push($attachments,WP_CONTENT_DIR . '/uploads/hospital_assets/'.$upload_dignosis_name );
				}
				
				$curency=html_entity_decode(MJ_hmgt_get_currency_symbol());
				$headers="";
				$headers .= 'From: '.$hospital_name.' <noreplay@gmail.com>' . "\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/plain; charset=iso-8859-1\r\n";
				$enable_notofication=get_option('hospital_enable_notifications');
				//end common details for patient and doctor mail template//
				
				//Send diagnosis mail for notification for patient//
				if($data['hmgt_send_mail_to_patient'] == '1')
				{
					$subject =get_option('MJ_hmgt_add_diagnosis_report_subject');
					$sub_arr['{{Hospital Name}}']=$hospital_name;
					$subject = MJ_hmgt_subject_string_replacemnet($sub_arr,$subject);
					$arr['{{Patient Name}}']=$patientname;			
					$arr['{{Charges Amount}}']= $curency.' '.$data['report_cost'];			
					$arr['{{Hospital Name}}']=$hospital_name;
					$message = get_option('MJ_hmgt_add_diagnosis_report_template');
					$message_replacement = MJ_hmgt_string_replacemnet($arr,$message);	
					$to[]=$patient_email;
					
					if($enable_notofication=='yes')
					{
					   wp_mail($to,$subject,$message_replacement,$headers,$attachments);
					}
				}
				//Send diagnosis mail for notification for doctor//
				
				if($data['hmgt_send_mail_to_doctor'] == '1')
				{
					$doctordetail=MJ_hmgt_get_guardianby_patient($data['patient_id']);
					
					if(isset($doctordetail['doctor_id']))
					{
						$doctor = get_userdata($doctordetail['doctor_id']);
						$doctorid=$doctor->ID;
						$display_name=$doctor->display_name;
						$user_email[]=$doctor->user_email;
						$subject_2 =get_option('MJ_hmgt_add_diagnosis_report_subject_doctor');
						$sub_arr1['{{Hospital Name}}']=$hospital_name;
						$sub_arr1['{{Patient Name}}']=$patientname;
						$subject_doctor = MJ_hmgt_subject_string_replacemnet($sub_arr1,$subject_2);
						$arr1['{{Doctor Name}}']=$display_name;			
						$arr1['{{Hospital Name}}']=$hospital_name;
						$arr1['{{Patient Name}}']=$patientname;
						$message_doctor = get_option('MJ_hmgt_add_diagnosis_report_template_doctor');
						$message_replacement_doctor = MJ_hmgt_string_replacemnet($arr1,$message_doctor);
						if($enable_notofication=='yes')
						{
						   wp_mail($user_email,$subject_doctor,$message_replacement_doctor,$headers,$attachments);
						}					 
					}						
				}
				
			}
			
			return $result;
		}
		else
		{			
			$diagnosisdata['diagnosis_date']=date("Y-m-d");
			$result=$wpdb->insert( $table_diagnosis, $diagnosisdata );
			$dignosis_id['diagnosis_id']=$wpdb->insert_id;
			$pre_id = $wpdb->insert_id;
			MJ_hmgt_append_audit_log(''.esc_html__('Add new diagnosis report ','hospital_mgt').'',get_current_user_id());
			//insert patient transaction
			$feesdata['refer_id'] = $pre_id;
			$invoice = $wpdb->insert($hmgt_patient_transation,$feesdata);
			
			//---Multiple diagnosis report insert---//
			$upload_dignosis_array=array();			
			if(!empty($_FILES['document']['name']))
			{
				$count_array=count($_FILES['document']['name']);

				for($a=0;$a<$count_array;$a++)
				{			
					foreach($_FILES['document'] as $image_key=>$image_val)
					{	
						if($_FILES['document']['name'][$a]!='')
						{	
							$diagnosis_array[$a]=array(
							'name'=>$_FILES['document']['name'][$a],
							'type'=>$_FILES['document']['type'][$a],
							'tmp_name'=>$_FILES['document']['tmp_name'][$a],
							'error'=>$_FILES['document']['error'][$a],
							'size'=>$_FILES['document']['size'][$a]
							);							
						}	
					}
				}
				if(!empty($diagnosis_array))
				{
					foreach($diagnosis_array as $key=>$value)		
					{	
						$get_file_name=$diagnosis_array[$key]['name'];	
						
						$upload_dignosis_array[]=MJ_hmgt_load_multiple_documets($value,$value,$get_file_name);				
					} 	
				}
				$report_array=array();
				$report_id= $data['report_id'];	
				$report_amount= $data['diagnosis_total_amount'];	
				
				if(!empty($upload_dignosis_array))
				{	
					foreach($upload_dignosis_array as $key=>$value)
					{
						$report_array[]=array("report_id"=>$report_id[$key],"attach_report"=>$value,"report_amount"=>$report_amount[$key]);
					}	
				}
				$attach_report_data=json_encode($report_array);
				$diagnosisreportdata['attach_report']=$attach_report_data;
				$wpdb->update( $table_diagnosis, $diagnosisreportdata ,$dignosis_id);	
			}
			
			if(isset($result))
			{	
				//Common details for patient and doctor mail template//
				$patient=get_userdata($data['patient_id']);
				$patient_email=$patient->user_email;
				$patientname=$patient->display_name;
				$hospital_name = get_option('hmgt_hospital_name');
				//-------------------- SEND SMS ---------------------//
		
				if(!empty(get_user_meta($data['patient_id'], 'phonecode',true))){ $phone_code=get_user_meta($data['patient_id'], 'phonecode',true); }else{ $phone_code='+'.MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }
									
				$user_number[] = $phone_code.get_user_meta($data['patient_id'], 'mobile',true);
				$user_number1 = $phone_code.get_user_meta($data['patient_id'], 'mobile',true);
				$curency="";
				if(is_plugin_active('sms-pack/sms-pack.php'))
				{
					$curency=html_entity_decode(MJ_hmgt_get_currency_symbol());
					$hospital_name = get_option('hmgt_hospital_name');
					$message_content ="Your have a new diagnosis report ".$curency." ".$data['report_cost']." Amount will be charged to you for the diagnosis report.";
					$current_sms_service 	= get_option( 'smgt_sms_service');
					$args = array();
					$args['mobile']=$user_number;
					$args['message_from']="Dignosis Report";
					$args['message']=$message_content;					
					if($current_sms_service=='telerivet' || $current_sms_service ="MSG91" || $current_sms_service=='bulksmsgateway.in' || $current_sms_service=='textlocal.in' || $current_sms_service=='bulksmsnigeria' || $current_sms_service=='africastalking' || $current_sms_service == 'clickatell')
					{				
						$send = send_sms($args);							
					}
				}
				
				$current_sms_service = get_option('hmgt_sms_service');
				$message_content ="Your have a new diagnosis report ".$curency." ".$data['report_cost']." Amount will be charged to you for the diagnosis report.";
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
				$attachments=array();
				foreach($upload_dignosis_array as $upload_dignosis_name)
				{
					array_push($attachments,WP_CONTENT_DIR . '/uploads/hospital_assets/'.$upload_dignosis_name );
				}
				
				$curency=html_entity_decode(MJ_hmgt_get_currency_symbol());
				$headers="";
				$headers .= 'From: '.$hospital_name.' <noreplay@gmail.com>' . "\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/plain; charset=iso-8859-1\r\n";
				$enable_notofication=get_option('hospital_enable_notifications');
				//end common details for patient and doctor mail template//
			
				//Send diagnosis mail for notification for patient//
				if(!empty($data['hmgt_send_mail_to_patient']) &&  $data['hmgt_send_mail_to_patient'] == '1')
				{
					$subject =get_option('MJ_hmgt_add_diagnosis_report_subject');
					$sub_arr['{{Hospital Name}}']=$hospital_name;
					$subject = MJ_hmgt_subject_string_replacemnet($sub_arr,$subject);
					$arr['{{Patient Name}}']=$patientname;			
					$arr['{{Charges Amount}}']= $curency.' '.$data['report_cost'];			
					$arr['{{Hospital Name}}']=$hospital_name;
					$message = get_option('MJ_hmgt_add_diagnosis_report_template');
					$message_replacement = MJ_hmgt_string_replacemnet($arr,$message);	
					$to=$patient_email;
					if($enable_notofication=='yes')
					{
					   wp_mail($to,$subject,$message_replacement,$headers,$attachments);
					}
				}
				//Send diagnosis mail for notification for doctor//
				
				if($data['hmgt_send_mail_to_doctor'] == '1')
				{
					$doctordetail=MJ_hmgt_get_guardianby_patient($data['patient_id']);
					
					if(isset($doctordetail['doctor_id']))
					{
						$doctor = get_userdata($doctordetail['doctor_id']);
						if(!empty($doctor))
						{
							$doctorid=$doctor->ID;
							$display_name=$doctor->display_name;
							$user_email[]=$doctor->user_email;
							$subject_2 =get_option('MJ_hmgt_add_diagnosis_report_subject_doctor');
							$sub_arr1['{{Hospital Name}}']=$hospital_name;
							$sub_arr1['{{Patient Name}}']=$patientname;
							$subject_doctor = MJ_hmgt_subject_string_replacemnet($sub_arr1,$subject_2);
							$arr1['{{Doctor Name}}']=$display_name;			
							$arr1['{{Hospital Name}}']=$hospital_name;
							$arr1['{{Patient Name}}']=$patientname;
							$message_doctor = get_option('MJ_hmgt_add_diagnosis_report_template_doctor');
							$message_replacement_doctor = MJ_hmgt_string_replacemnet($arr1,$message_doctor);
							if($enable_notofication=='yes')
							{
							   wp_mail($user_email,$subject_doctor,$message_replacement_doctor,$headers,$attachments);
							}
						}	
					}						
				}
				
			}
			return $result;
		}
	
	}
	//get all dignosis report
	public function MJ_hmgt_get_all_dignosis_report()
	{
		global $wpdb;
		$table_diagnosis = $wpdb->prefix. 'hmgt_diagnosis';
	
		$result = $wpdb->get_results("SELECT * FROM $table_diagnosis");
		return $result;
	}
	//get single diagnosis report
	public function MJ_hmgt_get_single_dignosis_report($dignosis_reportid)
	{
		global $wpdb;
		$table_diagnosis = $wpdb->prefix. 'hmgt_diagnosis';
		$result = $wpdb->get_row("SELECT * FROM $table_diagnosis where diagnosis_id = ".$dignosis_reportid);
		return $result;
	}
	//delete dignosis report
	public function MJ_hmgt_delete_dignosis($dignosis_reportid)
	{
		global $wpdb;
		$table_diagnosis = $wpdb->prefix. 'hmgt_diagnosis';
		$result = $wpdb->query("DELETE FROM $table_diagnosis where diagnosis_id = ".$dignosis_reportid);
		MJ_hmgt_append_audit_log(''.esc_html__('Delete diagnosis report ','hospital_mgt').'',get_current_user_id());
		return $result;
	}
	//get dignosis report by patient
	public function MJ_hmgt_get_diagnosis_by_patient($patient_id)
	{
		global $wpdb;
		$table_diagnosis = $wpdb->prefix. 'hmgt_diagnosis';
		$result = $wpdb->get_results("SELECT *FROM $table_diagnosis where patient_id = ".$patient_id);
		return $result;
	}
	//get outpatient dignosis report
	public function MJ_hmgt_get_diagnosis_outpatient($patient_id)
	{
		global $wpdb;
		$table_diagnosis = $wpdb->prefix. 'hmgt_diagnosis';
		$result = $wpdb->get_results("SELECT *FROM $table_diagnosis where report_cost IS NULL AND patient_id = ".$patient_id);
		return $result;
	}
	//get last dignosis by patient
	public function MJ_hmgt_get_last_diagnosis_by_patient($patient_id)
	{
		global $wpdb;
		$table_diagnosis = $wpdb->prefix. 'hmgt_diagnosis';
		$result = $wpdb->get_row("SELECT *FROM $table_diagnosis where patient_id = ".$patient_id." ORDER BY 'diagnosis_id' DESC");
		return $result;
	}
	//get last dignsis by created by
	public function MJ_hmgt_get_last_diagnosis_created_by($user_id)
	{
		global $wpdb;
		$table_diagnosis = $wpdb->prefix. 'hmgt_diagnosis';
		$result = $wpdb->get_results("SELECT *FROM $table_diagnosis where diagno_create_by = ".$user_id." ORDER BY 'diagnosis_id' DESC");
		return $result;
	}
	//get doctor created last dignosis repot 
	public function MJ_hmgt_get_doctor_last_diagnosis_created_by($user_id)
	{
		global $wpdb;
		$table_diagnosis = $wpdb->prefix. 'hmgt_diagnosis';
		$array=MJ_hmgt_doctor_patientid_list(); 
		$array = implode("','",$array);
		$result = $wpdb->get_results("SELECT *FROM $table_diagnosis where diagno_create_by = ".$user_id." OR patient_id IN ('".$array."') ORDER BY 'diagnosis_id' DESC");
		return $result;
	}
	//get nurse created last diagnosis report
	public function MJ_hmgt_get_nurse_last_diagnosis_created_by($user_id)
	{
		global $wpdb;
		$table_diagnosis = $wpdb->prefix. 'hmgt_diagnosis';
		$array=MJ_hmgt_nurse_patientid_list(); 
		
		$array = implode("','",$array);
		$result = $wpdb->get_results("SELECT *FROM $table_diagnosis where diagno_create_by = ".$user_id." OR patient_id IN ('".$array."') ORDER BY 'diagnosis_id' DESC");
		return $result;
	}
}
?>