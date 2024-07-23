<?php
class MJ_hmgt_bedmanage
{	
	//add bed category
	public function MJ_hmgt_add_bedtype($data)
	{
		global $wpdb;
		$result = wp_insert_post( array(
			'post_status' => 'publish',
			'post_type' => 'bedtype_category',
			'post_title' => $data['category_name']) );
		MJ_hmgt_append_audit_log(''.esc_html__('Add new bed type ','hospital_mgt').'',get_current_user_id());
		return $result;
	}
	//get all bed category
	public function MJ_hmgt_get_all_bedtype()
	{
		$args= array('post_type'=> 'bedtype_category','posts_per_page'=>-1,'orderby'=>'post_title','order'=>'Asc');
		$result = get_posts( $args );
		return $result;		
	}
	//get bed category name
	public function MJ_hmgt_get_bedtype_name($bed_type_id)
	{		
		$result = get_post( $bed_type_id );		
		if(!empty($result))	
		return $result->post_title;
		else 
			return "";
	}
	//get single bed category
	public function MJ_hmgt_get_single_bedtype($bed_type_id)
	{
		global $wpdb;
		$table_bedtype = $wpdb->prefix. 'hmgt_bed_type';
		
		$result = $wpdb->get_row("SELECT * FROM $table_bedtype where bed_type_id= ".$bed_type_id);
		return $result;		
	
	}
	//delete bed category
	public function MJ_hmgt_delete_bedtype($bed_type_id)
	{
		global $wpdb;
		$table_bedtype = $wpdb->prefix. 'hmgt_bed_type';
		$result = $wpdb->query("DELETE FROM $table_bedtype where bed_type_id= ".$bed_type_id);
		MJ_hmgt_append_audit_log(''.esc_html__('Delete bed type ','hospital_mgt').'',get_current_user_id());
		return $result;
	}
	//add Bed data	
	public function MJ_hmgt_add_bed($data)
	{
		global $wpdb;
		$table_bed = $wpdb->prefix. 'hmgt_bed';
		//-------usersmeta table data--------------
		$beddata['bed_number']=$data['bed_number'];
		$beddata['bed_type_id']=$data['bed_type_id'];
		$beddata['bed_charges']=$data['bed_charges'];
		$beddata['bed_description']=MJ_hmgt_strip_tags_and_stripslashes($data['bed_description']);
		$beddata['bed_location']=MJ_hmgt_strip_tags_and_stripslashes($data['bed_location']);
		$beddata['bed_create_date']=date("Y-m-d");
		$beddata['bed_create_by']=get_current_user_id();	
		if(!empty($data['tax']))
		{		
			$beddata['tax']=implode(",",$data['tax']);		
		}
		else
		{
			$beddata['tax']=null;
		}	
		if($data['action']=='edit')
		{
			$beddataid['bed_id']=$data['bed_id'];
			$result=$wpdb->update( $table_bed, $beddata ,$beddataid);
			MJ_hmgt_append_audit_log(''.esc_html__('Update bed ','hospital_mgt').'',get_current_user_id());
			return $result;
		}
		else
		{
			$result=$wpdb->insert( $table_bed, $beddata );
			MJ_hmgt_append_audit_log(''.esc_html__('Add new bed ','hospital_mgt').'',get_current_user_id());
			return $result;
		}	
	}
	//get all bed
	public function MJ_hmgt_get_all_bed()
	{
		global $wpdb;
		$table_bed = $wpdb->prefix. 'hmgt_bed';
	
		$result = $wpdb->get_results("SELECT * FROM $table_bed");
		return $result;	
	}
	
	public function MJ_hmgt_get_all_bed_by_id($bed_type_id,$bed_number)
	{
		global $wpdb;
		$table_bed = $wpdb->prefix. 'hmgt_bed';
		$result = $wpdb->get_results("SELECT * FROM $table_bed where bed_type_id=$bed_type_id AND bed_number=$bed_number");
		return $result;	
	}
	//get bed number//
	public function MJ_hmgt_get_bed_number($bed_id)
	{
		global $wpdb;
		$table_bed = $wpdb->prefix. 'hmgt_bed';
	
		$result = $wpdb->get_var("SELECT bed_number FROM $table_bed where bed_id= ".$bed_id);
		return $result;
	}
	//get signle bed//
	public function MJ_hmgt_get_single_bed($bed_id)
	{
		global $wpdb;
		$table_bed = $wpdb->prefix. 'hmgt_bed';
		
		$result = $wpdb->get_row("SELECT * FROM $table_bed where bed_id= ".$bed_id);
		return $result;
	}
	//delete bed//
	public function MJ_hmgt_delete_bed($bed_id)
	{
		global $wpdb;
		$table_bed = $wpdb->prefix. 'hmgt_bed';
		$result = $wpdb->query("DELETE FROM $table_bed where bed_id= ".$bed_id);
		MJ_hmgt_append_audit_log(''.esc_html__('Delete bed ','hospital_mgt').'',get_current_user_id());
		return $result;
	}
	//delete bed category//
	public function MJ_hmgt_delete_bed_type($bed_id)
	{
		$result=wp_delete_post($bed_id);
		return $result;
	}
	//get bed by bed category
	public function MJ_hmgt_get_bed_by_bedtype($bed_type_id)
	{
		global $wpdb;
		$table_bed = $wpdb->prefix. 'hmgt_bed';
		$result = $wpdb->get_results("SELECT * FROM $table_bed where bed_type_id = ".$bed_type_id);
		return $result;
	}	
	// add bed allotment data
	public function MJ_hmgt_add_bed_allotment($data)
	{
		global $wpdb;			
		
		$table_bedallotment = $wpdb->prefix. 'hmgt_bed_allotment';
		$table_hmgt_assign_type = $wpdb->prefix. 'hmgt_assign_type';
		$table_hmgt_guardian = $wpdb->prefix. 'hmgt_inpatient_guardian';
		$table_hmgt_bed = $wpdb->prefix. 'hmgt_bed';
		$table_hmgt_history = $wpdb->prefix. 'hmgt_history';
		$table_hmgt_charges = $wpdb->prefix. 'hmgt_charges';
		$table_hmgt_patient_transation = $wpdb->prefix. 'hmgt_patient_transation';
		//-------usersmeta table data--------------
		$bedallotmnetdata['bed_number']=$data['bed_number'];
		$bedallotmnetdata['bed_type_id']=$data['bed_type_id'];
		$bedallotmnetdata['patient_id']=$data['patient_id'];
		$bedallotmnetdata['patient_status']=$data['patient_status'];	
		
	    $bedallotmnetdata['allotment_date']=MJ_hmgt_get_format_for_db($data['allotment_date']);
	    $bedallotmnetdata['discharge_time']=MJ_hmgt_get_format_for_db($data['discharge_time']);
		
		$bedallotmnetdata['allotment_description']=MJ_hmgt_strip_tags_and_stripslashes($data['allotment_description']);
		$bedallotmnetdata['created_date']=date("Y-m-d");
		$bedallotmnetdata['allotment_by']=get_current_user_id();
		
		$guardian = MJ_hmgt_get_guardian_name($data['patient_id']);
		$patient_satus['patient_status']=$data['patient_status'];
		$patient_id['patient_id']=$data['patient_id'];
		
		if(!empty($guardian))
		$result=$wpdb->update( $table_hmgt_guardian, $patient_satus,$patient_id);
		else 
		{
			$wpdb->insert($table_hmgt_guardian, array(
				"patient_status" => $data['patient_status'],
				"patient_id" => $data['patient_id']					
			));
		}		
		
		if($bedallotmnetdata['discharge_time'] >= date('Y-m-d'))
		{
			$bed['status']= 1;
			$bedid['bed_id']=$data['bed_number'];			
		}
		
		$history['patient_id']=$data['patient_id'];
		$history['status']=$data['patient_status'];
		$history['bed_number']=$data['bed_number'];
			
		if(!empty($guardian))
			$history['guardian_name']=$guardian->first_name." ".$guardian->last_name;
		else
			$history['guardian_name']="";
		$history['history_type']="bed_assign";
		
		$history['history_date']=date("Y-m-d H:i:s");
		$history['created_by']=get_current_user_id();		
		
		$bed_type = $this->MJ_hmgt_get_bedtype_name($bedallotmnetdata['bed_type_id']);
		$bed_number = $this->MJ_hmgt_get_bed_number($bedallotmnetdata['bed_number']);
		$charge_type = 'Bed charge';
		
		$numberDays = MJ_hmgt_get_day_between_date(MJ_hmgt_get_format_for_db($data['allotment_date']),MJ_hmgt_get_format_for_db($data['discharge_time'])); 
		
		$get_bedrow= $this->MJ_hmgt_get_single_bed($bedallotmnetdata['bed_number']);
		$single_bed_charge= $get_bedrow->bed_charges;
		$single_bed_tax= $get_bedrow->tax;
		
		$tax_array=explode(',',$single_bed_tax);
		
		$total_bed_charge = $single_bed_charge * $numberDays;
			
		if(!empty($tax_array))
		{
			$total_tax=0;
			foreach($tax_array as $tax_id)
			{
				$tax_percentage=MJ_hmgt_tax_percentage_by_tax_id($tax_id);
				$tax_amount=$total_bed_charge * $tax_percentage / 100;
				
				$total_tax=$total_tax + $tax_amount;
				
			}
			$bed_charge=$total_bed_charge;
			$bed_tax=$total_tax;
			$total_bed_charge_with_tax=$total_bed_charge+$total_tax;
		}
		else
		{
			$total_tax=0;
			$bed_charge=$total_bed_charge;
			$bed_tax=$total_tax;
			$total_bed_charge_with_tax=$total_bed_charge+$total_tax;
		}	
		
		$charge_data['charge_label']= 'Bed Charges';
		$charge_data['charge_type']= 'bed';
		$charge_data['room_number']=$bed_number;
		$charge_data['bed_type']=$bed_type;
		$charge_data['charges']=$total_bed_charge_with_tax;
		$charge_data['patient_id']=$bedallotmnetdata['patient_id'];
		$charge_data['status']=0;
		
		$charge_data['created_date']=date("Y-m-d");
		$charge_data['created_by']=get_current_user_id();
		
		//for  patient bed trasation table....							
			$trasationdata['type'] ="Bed Charges"; 
			$trasationdata['type_id'] = $data['bed_number']; 
			$trasationdata['status'] ="Unpaid"; 
			$trasationdata['patient_id'] =$data['patient_id']; 
			$trasationdata['date'] =date("Y-m-d"); 
			$trasationdata['invoice_id']=0;	
			$units=MJ_hmgt_get_day_between_date(MJ_hmgt_get_format_for_db($data['allotment_date']),MJ_hmgt_get_format_for_db($data['discharge_time'])); 
			$trasationdata['unit'] =$units.' Days';
			$trasationdata['type_value'] =$bed_charge; 
			$trasationdata['type_tax'] =$bed_tax;
			$trasationdata['type_total_value'] =$total_bed_charge_with_tax;
		if($data['action']=='edit')
		{
			$allotmentid['bed_allotment_id']=$data['allotment_id'];
			$charge_referid['refer_id']=$data['allotment_id'];
			$charge_referid['charge_type']='bed';
			$result=$wpdb->update( $table_bedallotment, $bedallotmnetdata ,$allotmentid);
			$this->MJ_hmgt_delete_assign_byparent($allotmentid['bed_allotment_id']);
			if(!empty($data['nurse']))
			{
				foreach($data['nurse'] as $id)
				{
					$assign_data['child_id']=$id;
					$assign_data['parent_id']=$allotmentid['bed_allotment_id'];
					$assign_data['assign_type']='nurse-bedallotment';
					$assign_data['assign_date']=date("Y-m-d");
					$assign_data['assign_by']=get_current_user_id();
					$wpdb->insert( $table_hmgt_assign_type, $assign_data );
				}
			}
			//for nurse transation table....
			if(!empty($data['nurse']))
			{
				$whereNurseTrasation['type'] ="Nurse Charges";
				foreach($data['nurse'] as $nurse_key=>$nurse_id)
				{
						
					if(!empty(get_user_meta($nurse_id, 'phonecode',true))){ $phone_code=get_user_meta($nurse_id, 'phonecode',true); }else{ $phone_code='+'.MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }
					if(!empty(get_user_meta($data['patient_id'], 'phonecode',true))){ $phone_code1=get_user_meta($data['patient_id'], 'phonecode',true); }else{ $phone_code1='+'.MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }
							
					$user_number[] = $phone_code.get_user_meta($nurse_id, 'mobile',true);
					$user_number1 = $phone_code.get_user_meta($nurse_id, 'mobile',true);
					$user_number[] = $phone_code1.get_user_meta($data['patient_id'], 'mobile',true);
					$user_number1 = $phone_code1.get_user_meta($data['patient_id'], 'mobile',true);
					//------------------ SEND SMS ---------------//
					if(is_plugin_active('sms-pack/sms-pack.php'))
					{
						$hospital_name = get_option('hmgt_hospital_name');
						$message_content = "You have been Assigned new Bed from $hospital_name";
						$current_sms_service 	= get_option( 'smgt_sms_service');
						$args = array();
						$args['mobile']=$user_number;
						$args['message_from']="AssignBed";
						$args['message']=$message_content;					
						if($current_sms_service=='telerivet' || $current_sms_service ="MSG91" || $current_sms_service=='bulksmsgateway.in' || $current_sms_service=='textlocal.in' || $current_sms_service=='bulksmsnigeria' || $current_sms_service=='africastalking' || $current_sms_service == 'clickatell')
						{				
							$send = send_sms($args);							
						}
					}
					$current_sms_service = get_option('hmgt_sms_service');
					$message_content ="You have been Assigned new Bed";
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
					$nurse_total_fees=MJ_hmgt_get_nurse_total_amount($units,$nurse_id); 
					$nurse_tax =MJ_hmgt_get_nurse_total_tax($nurse_total_fees,$nurse_id); 
					$nurse_total_fees_with_tax =$nurse_total_fees+$nurse_tax;
					
					$Nursetrasationdata['type_value'] =$nurse_total_fees;
					$Nursetrasationdata['type_tax'] =$nurse_tax;
					$Nursetrasationdata['type_total_value'] =$nurse_total_fees_with_tax;
					$Nursetrasationdata['unit'] =$units.' Days';
					$Nursetrasationdata['date'] =date("Y-m-d"); 
					$whereNurseTrasation['type_id'] =$nurse_id;
					$whereNurseTrasation['refer_id'] =$data['allotment_id'];
					$whereNurseTrasation['patient_id'] =$data['patient_id']; 					
					$wpdb->update( $table_hmgt_patient_transation, $Nursetrasationdata,$whereNurseTrasation);	
					$nursedata=get_userdata($nurse_id);
					$nurceemail=$nursedata->user_email;
					$nurcename=$nursedata->display_name;;
					$patient=get_userdata($data['patient_id']);
					$patient_name=$patient->display_name;
					$hospital_name = get_option('hmgt_hospital_name');
					$subject =get_option('MJ_hmgt_patient_assigned_to_nurse_subject');
					$sub_arr['{{Patient Name}}']=$patient_name;
					$subject = MJ_hmgt_subject_string_replacemnet($sub_arr,$subject);
					$arr['{{Nurse Name}}']=$nurcename;			
					$arr['{{Patient Name}}']=$patient_name;			
					$arr['{{Hospital Name}}']=$hospital_name;
					$message = get_option('MJ_hmgt_patient_assigned_to_nurse_template');
				    $message_replacement = MJ_hmgt_string_replacemnet($arr,$message);	
					$to=$nurceemail;
					MJ_hmgt_send_mail($to,$subject,$message_replacement);
					//end assign nurce mail			
				}				
			}
			$wheretrasation['refer_id'] = $data['allotment_id'];
			$wheretrasation['type'] ="Bed Charges";
			$wpdb->update($table_hmgt_patient_transation, $trasationdata,$wheretrasation);
			$wpdb->update( $table_hmgt_charges, $charge_data,$charge_referid );
			MJ_hmgt_append_audit_log(''.esc_html__('update bed allotment detail ','hospital_mgt').'',get_current_user_id());
			return $result;
		}
		else
		{
			$result=$wpdb->insert( $table_bedallotment, $bedallotmnetdata );
			$allotment_id = $wpdb->insert_id;
			MJ_hmgt_append_audit_log(''.esc_html__('Add new bed allotment ','hospital_mgt').'',get_current_user_id());
			if(!empty($data['nurse']))
			{
				foreach($data['nurse'] as $id)				
				{
					if(!empty(get_user_meta($id, 'phonecode',true))){ $phone_code=get_user_meta($id, 'phonecode',true); }else{ $phone_code='+'.MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }
					if(!empty(get_user_meta($data['patient_id'], 'phonecode',true))){ $phone_code1=get_user_meta($data['patient_id'], 'phonecode',true); }else{ $phone_code1='+'.MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }
							
					$user_number[] = $phone_code.get_user_meta($id, 'mobile',true);
					$user_number[] = $phone_code1.get_user_meta($data['patient_id'], 'mobile',true);
					$user_number1 = $phone_code1.get_user_meta($data['patient_id'], 'mobile',true);
					//------------------- SEND SMS ------------------------//
					if(is_plugin_active('sms-pack/sms-pack.php'))
					{
						$hospital_name = get_option('hmgt_hospital_name');
						$message_content = "You have been Assigned new Bed From $hospital_name";
						$current_sms_service 	= get_option( 'smgt_sms_service');
						$args = array();
						$args['mobile']=$user_number;
						$args['message_from']="AssignBed";
						$args['message']=$message_content;					
						if($current_sms_service=='telerivet' || $current_sms_service ="MSG91" || $current_sms_service=='bulksmsgateway.in' || $current_sms_service=='textlocal.in' || $current_sms_service=='bulksmsnigeria' || $current_sms_service=='africastalking' || $current_sms_service == 'clickatell')
						{				
							$send = send_sms($args);							
						}
					}		
					$current_sms_service = get_option('hmgt_sms_service');
					$message_content ="You have been Assigned new Bed";
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
					$assign_data['child_id']=$id;
					$assign_data['parent_id']=$allotment_id ;
					$assign_data['assign_type']='nurse-bedallotment';
					$assign_data['assign_date']=date("Y-m-d");
					$assign_data['assign_by']=get_current_user_id();
					$wpdb->insert( $table_hmgt_assign_type, $assign_data );
					$nursedata=get_userdata($id);
				$nurceemail=$nursedata->user_email;
				$nurcename=$nursedata->display_name;;
				
				$patient=get_userdata($data['patient_id']);
				$patient_name=$patient->display_name;;
				$hospital_name = get_option('hmgt_hospital_name');
				$subject =get_option('MJ_hmgt_patient_assigned_to_nurse_subject');
				
				$sub_arr['{{Patient Name}}']=$patient_name;
			    $subject = MJ_hmgt_subject_string_replacemnet($sub_arr,$subject);
			    $arr['{{Nurse Name}}']=$nurcename;			
			    $arr['{{Patient Name}}']=$patient_name;			
			    $arr['{{Hospital Name}}']=$hospital_name;
			    $message = get_option('MJ_hmgt_patient_assigned_to_nurse_template');
			   $message_replacement = MJ_hmgt_string_replacemnet($arr,$message);	
				$to[]=$nurceemail;
					MJ_hmgt_send_mail($to,$subject,$message_replacement);
					
				}
			}
		
			$charge_data['refer_id'] = $allotment_id;
			$history['parent_id']=$allotment_id;
			$wpdb->insert( $table_hmgt_charges, $charge_data );
			$wpdb->insert( $table_hmgt_history, $history );
			$trasationdata['refer_id'] =$allotment_id;
			$wpdb->insert( $table_hmgt_patient_transation, $trasationdata );
			//for nurse transation table....
			if(!empty($data['nurse']))
			{
				$trasationdata['type'] ="Nurse Charges";
				foreach($data['nurse'] as $nurse_key=>$nurse_id)
				{
					$nurse_total_fees=MJ_hmgt_get_nurse_total_amount($units,$nurse_id); 
					$nurse_tax =MJ_hmgt_get_nurse_total_tax($nurse_total_fees,$nurse_id); 
					$nurse_total_fees_with_tax =$nurse_total_fees+$nurse_tax;
										
					$trasationdata['type_id'] =$nurse_id;
					$trasationdata['type_value'] = $nurse_total_fees;
					$trasationdata['type_tax'] = $nurse_tax;
					$trasationdata['type_total_value'] = $nurse_total_fees_with_tax;
					
					$wpdb->insert( $table_hmgt_patient_transation, $trasationdata );					
				}				
			}
			//asign bed patienet mail messagee	
		   $bed_cat_tid= get_post($_POST['bed_type_id']);		
		    $bed_cat_name=$bed_cat_tid->post_title;
				$patient=get_userdata($data['patient_id']);
				$patient_name=$patient->display_name;
				$patine_email=$patient->user_email;
				$hospital_name = get_option('hmgt_hospital_name');
				$subject =get_option('MJ_hmgt_assign_bed_patient_subject');
				$sub_arr['{{Bed ID}}']=$data['bed_number'];
				$sub_arr['{{Bed Category}}']=$bed_cat_name;
			    $subject = MJ_hmgt_subject_string_replacemnet($sub_arr,$subject);
			$arr['{{Patient Name}}']=$patient_name;			
			$arr['{{Bed ID}}']=$data['bed_number'];			
			$arr['{{Bed Category}}']=$bed_cat_name;
			
			//convert HTML code to currency code
			$curency=html_entity_decode(MJ_hmgt_get_currency_symbol());
			
			$arr['{{Charges Amount}}']=$curency.' '.$total_bed_charge_with_tax;			
			$arr['{{Hospital Name}}']=$hospital_name;
			$message = get_option('MJ_hmgt_assign_bed_patient_template');
			$message_replacement = MJ_hmgt_string_replacemnet($arr,$message);	
				$to[]=$patine_email;
					MJ_hmgt_send_mail($to,$subject,$message_replacement);
          // end assign bed patient mail message		
			return $result;
		}
	}
	//get all bed allotment
	public function MJ_hmgt_get_all_bedallotment()
	{
		global $wpdb;
		$table_bedallotment = $wpdb->prefix. 'hmgt_bed_allotment';
		$date = date('Y-m-d');
		$result = $wpdb->get_results("SELECT * FROM $table_bedallotment");
		return $result;
	}
	//get all bedallotment by created by
	public function MJ_hmgt_get_all_bedallotment_by_allotment_by()
	{
		global $wpdb;
		$table_bedallotment = $wpdb->prefix. 'hmgt_bed_allotment';
		$date = date('Y-m-d');
		$user_id=get_current_user_id();	

		$result = $wpdb->get_results("SELECT * FROM $table_bedallotment where allotment_by=$user_id");
		return $result;
	}
	//get doctor created all bedallotment 
	public function MJ_hmgt_get_doctor_all_bedallotment_by_allotment_by()
	{
		global $wpdb;
		$table_bedallotment = $wpdb->prefix. 'hmgt_bed_allotment';
		$date = date('Y-m-d');
		$user_id=get_current_user_id();	
		$array=MJ_hmgt_doctor_patientid_list(); 
		$array = implode("','",$array);
		$result = $wpdb->get_results("SELECT * FROM $table_bedallotment where allotment_by=$user_id OR patient_id IN ('".$array."')");
		return $result;
	}
	//get nurse creared all bedallotment
	public function MJ_hmgt_get_nurse_all_bedallotment_by_allotment_by()
	{
		global $wpdb;
		$table_bedallotment = $wpdb->prefix. 'hmgt_bed_allotment';
		$date = date('Y-m-d');
		$user_id=get_current_user_id();	
		$array=MJ_hmgt_nurse_all_bedallotment_data_array();
		$array = implode("','",$array);
		
		$result = $wpdb->get_results("SELECT * FROM $table_bedallotment where allotment_by=$user_id OR bed_allotment_id IN ('".$array."')");
		return $result;
	}
	//get all bedallotment by patient
	public function MJ_hmgt_get_all_bedallotment_by_patient()
	{
		global $wpdb;
		$table_bedallotment = $wpdb->prefix. 'hmgt_bed_allotment';
		$date = date('Y-m-d');
		$user_id=get_current_user_id();	

		$result = $wpdb->get_results("SELECT * FROM $table_bedallotment where patient_id=$user_id");
		return $result;
	}
	//get single bedallotment
	public function MJ_hmgt_get_single_bedallotment($bed_allotment_id)
	{
		global $wpdb;
		$table_bedallotment = $wpdb->prefix. 'hmgt_bed_allotment';

		$result = $wpdb->get_row("SELECT * FROM $table_bedallotment where bed_allotment_id = ".$bed_allotment_id);
		return $result;
	}
	//get nurse by bedallotment id
	public function MJ_hmgt_get_nurse_by_bedallotment_id($allocate_id)
	{
		global $wpdb;
		$table_hmgt_assign_type = $wpdb->prefix. 'hmgt_assign_type';
		$result = $wpdb->get_results("SELECT * FROM $table_hmgt_assign_type WHERE parent_id = $allocate_id AND assign_type = 'nurse-bedallotment' ");
		return $result;
	}
	//get nurse by assign id
	public function MJ_hmgt_get_nurse_by_assignid($bed_assing_id)
	{
		global $wpdb;
		$table_hmgt_assign_type = $wpdb->prefix. 'hmgt_assign_type';
		$result = $wpdb->get_results("SELECT * FROM $table_hmgt_assign_type WHERE parent_id = $bed_assing_id AND assign_type = 'nurse-bedallotment' ");
		return $result;
	}
	//delete assigned by parent
	public function MJ_hmgt_delete_assign_byparent($parent_id)
	{
		global $wpdb;
		$table_hmgt_assign_type = $wpdb->prefix. 'hmgt_assign_type';
		$result = $wpdb->query("DELETE FROM $table_hmgt_assign_type where parent_id= ".$parent_id. " AND assign_type = 'nurse-bedallotment'"  );
		return $result;
	}
	//delete bedallotment record
	public function MJ_hmgt_delete_bedallocate_record($allocate_id)
	{
		global $wpdb;
		$table_bedallotment = $wpdb->prefix. 'hmgt_bed_allotment';
		$result = $wpdb->query("DELETE FROM $table_bedallotment  where bed_allotment_id = $allocate_id");
		$table_hmgt_assign_type = $wpdb->prefix. 'hmgt_assign_type';
		$result = $wpdb->query("DELETE FROM $table_hmgt_assign_type where parent_id= ".$allocate_id. " AND assign_type = 'nurse-bedallotment'"  );
		MJ_hmgt_append_audit_log(''.esc_html__('Delete bed allotment detail ','hospital_mgt').'',get_current_user_id());
		return $result;
	}
//patient bed transfar ....
public function MJ_hmgt_patient_bed_transfar($data)
{	
	global $wpdb;
	//table area ....
	$table_bedallotment = $wpdb->prefix. 'hmgt_bed_allotment';
	$table_hmgt_patient_transation = $wpdb->prefix. 'hmgt_patient_transation';
	$table_hmgt_assign_type = $wpdb->prefix. 'hmgt_assign_type';
	$table_hmgt_bed =$wpdb->prefix ."hmgt_bed";
	// update data allowment table ...
	$whereid['bed_allotment_id']=$data['transfar_allotment_id'];
	$updatadata['discharge_time']=date("Y-m-d");
	$updatadata['bed_type_id']=$data['old_bed_type_id'];
	$updatadata['bed_number']=$data['old_bednumber'];
	
	
	//insert data bed allowment  .....	
	$transfardata['patient_id']=$data['transfar_patient_id'];
	$transfardata['patient_status']=$data['transfar_patient_status'];
	$transfardata['bed_type_id']=$data['transfar_bed_type_id'];
	$transfardata['bed_number']=$data['transfar_bed_number'];
	$transfardata['allotment_date']=date("Y-m-d");
	$transfardata['allotment_description']=MJ_hmgt_strip_tags_and_stripslashes($data['allotment_description']);
	
	$transfardata['discharge_time']= MJ_hmgt_get_format_for_db($data['transfar_discharge_time']);
	$transfardata['created_date']=date("Y-m-d");
	$transfardata['allotment_by']=get_current_user_id();
	
	//update transation table ....
	$beddata = $wpdb->get_row("SELECT * FROM $table_hmgt_bed WHERE bed_id=".$data['old_bednumber']);
	
	$total_bed_charge = MJ_hmgt_get_bed_total_amount($upldate_trasationdata['unit'],$data['old_bednumber']);
	
	$single_bed_tax= $beddata->tax;
		
	$tax_array=explode(',',$single_bed_tax);
	
	if(!empty($tax_array))
	{
		$total_tax=0;
		foreach($tax_array as $tax_id)
		{
			$tax_percentage=MJ_hmgt_tax_percentage_by_tax_id($tax_id);
			$tax_amount=$total_bed_charge * $tax_percentage / 100;
			
			$total_tax=$total_tax + $tax_amount;
			
		}
		$bed_charge=$total_bed_charge;
		$bed_tax=$total_tax;
		$total_bed_charge_with_tax=$total_bed_charge+$total_tax;
	}
	else
	{
		$total_tax=0;
		$bed_charge=$total_bed_charge;
		$bed_tax=$total_tax;
		$total_bed_charge_with_tax=$total_bed_charge+$total_tax;
	}	
		
	$upldate_trasationdata['type_id'] = $data['old_bednumber'];

	$upldate_trasationdata['date'] =date("Y-m-d");
     $allotment_date_old=MJ_hmgt_get_format_for_db($data['allotment_date_old']);	

	$upldate_trasationdata['unit'] =MJ_hmgt_get_day_between_date($allotment_date_old,date("Y-m-d")).' Days'; 
	$upldate_trasationdata['type_value'] =$bed_charge;
	$upldate_trasationdata['type_tax'] =$bed_tax;
	$upldate_trasationdata['type_total_value'] =$total_bed_charge_with_tax;
	
	//Add Transtion data ......
	$beddata1 = $wpdb->get_row("SELECT * FROM $table_hmgt_bed WHERE bed_id=".$data['transfar_bed_number']);
	
	$total_bed_charge1 = MJ_hmgt_get_bed_total_amount($add_trasationdata['unit'],$data['transfar_bed_number']); 
	
	$single_bed_tax1= $beddata1->tax;
		
	$tax_array1=explode(',',$single_bed_tax1);
	
	if(!empty($tax_array1))
	{
		$total_tax1=0;
		foreach($tax_array1 as $tax_id)
		{
			$tax_percentage1=MJ_hmgt_tax_percentage_by_tax_id($tax_id);
			$tax_amount1=$total_bed_charge1 * $tax_percentage1 / 100;
			
			$total_tax1=$total_tax1 + $tax_amount1;
			
		}
		$bed_charge1=$total_bed_charge1;
		$bed_tax1=$total_tax1;
		$total_bed_charge_with_tax1=$total_bed_charge1+$total_tax1;
	}
	else
	{
		$total_tax1=0;
		$bed_charge1=$total_bed_charge1;
		$bed_tax1=$total_tax1;
		$total_bed_charge_with_tax1=$total_bed_charge1+$total_tax1;
	}
	
	$add_trasationdata['type'] ="Bed Charges"; 
	$add_trasationdata['type_id'] =$data['transfar_bed_number'];
	$add_trasationdata['status'] ="Unpaid"; 
	$add_trasationdata['patient_id'] =$data['transfar_patient_id'];
	$add_trasationdata['date'] =date("Y-m-d"); 		
	$add_trasationdata['unit'] =MJ_hmgt_get_day_between_date(date("Y-m-d"),MJ_hmgt_get_format_for_db($data['transfar_discharge_time'])).' Days'; 
	$add_trasationdata['type_value'] =$bed_charge1;
	$add_trasationdata['type_tax'] =$bed_tax1;
	$add_trasationdata['type_total_value'] =$total_bed_charge_with_tax1;
	
	$bedallotment_update = $wpdb->update($table_bedallotment,$updatadata,$whereid);	
	
	 if($bedallotment_update){	
	 
		$bedallotments_insert = $wpdb->insert($table_bedallotment,$transfardata);
		
		$add_trasationdata['refer_id'] = $wpdb->insert_id;
	
		
			if($bedallotments_insert){	
				$transationwhereid['refer_id']=$data['transfar_allotment_id'];
				$transationwhereid['type']="Bed Charges";
			$transation_update = $wpdb->update($table_hmgt_patient_transation,$upldate_trasationdata,$transationwhereid);
				if($transation_update){					
				 $result = $wpdb->insert($table_hmgt_patient_transation,$add_trasationdata);
				}
			} 
	} 
	if(!empty($data['nurse']))
	{	
		foreach($data['nurse'] as $nurse_id){	
			$cheak = MJ_hmgt_cheack_nurse_transfer($nurse_id,$data['transfar_allotment_id']);				
			$cheak_in_transition= MJ_hmgt_cheack_nurse_in_transition($nurse_id,$data['transfar_allotment_id'],'Nurse Charges');				
				$assign_data['child_id']=$nurse_id;
				$assign_data['parent_id']=$add_trasationdata['refer_id'];
				
				$assign_data['assign_type']='nurse-bedallotment';
				$assign_data['assign_date']=date("Y-m-d");
				$assign_data['assign_by']=get_current_user_id();
				$wpdb->insert( $table_hmgt_assign_type, $assign_data );
				
				if(empty($cheak_in_transition))
				{					
					$units =MJ_hmgt_get_day_between_date(date("Y-m-d"),MJ_hmgt_get_format_for_db($data['transfar_discharge_time']));
					  
					$nurse_total_fees=MJ_hmgt_get_nurse_total_amount($units,$nurse_id); 
					$nurse_tax =MJ_hmgt_get_nurse_total_tax($nurse_total_fees,$nurse_id); 
					$nurse_total_fees_with_tax =$nurse_total_fees+$nurse_tax;
															
					$nursedata['type_value'] =$nurse_total_fees;
					$nursedata['type_tax'] =$nurse_tax; 
					$nursedata['type_total_value'] =$nurse_total_fees_with_tax;
					$nursedata['unit'] =$units.' Days';
					$nursedata['type'] ="Nurse Charges";
					$nursedata['status'] ="Unpaid";
					$nursedata['date'] =date("Y-m-d"); 
					$nursedata['type_id'] =$nurse_id;
					$nursedata['refer_id'] =$add_trasationdata['refer_id'];
					$nursedata['patient_id'] =$data['transfar_patient_id'];							
					$result = $wpdb->insert( $table_hmgt_patient_transation, $nursedata);
					if($add_new_nurse){
						wp_redirect ( admin_url() . 'admin.php?page=hmgt_bedallotment&tab=allotedbedlist&message=4');
					}
				}			
		}
	}
	return $result;	
}	
	//get bedallotment list on admin dashboard
	public function MJ_hmgt_get_bedallotment_on_admin_dashboard()
	{
		global $wpdb;
		$table_bedallotment = $wpdb->prefix. 'hmgt_bed_allotment';
		
		$result = $wpdb->get_results("SELECT * FROM $table_bedallotment ORDER BY bed_allotment_id DESC LIMIT 3");
		return $result;
	}
	//get bedallotment list on fronted dashboard
	public function MJ_hmgt_get_bedallotment_on_fronted_dashboard()
	{
		global $wpdb;
		$table_bedallotment = $wpdb->prefix. 'hmgt_bed_allotment';
		$page='bedallotment';
		$data=MJ_hmgt_page_wise_view_data_on_fronted_dashboard($page);
		$role=MJ_hmgt_get_current_user_role();	
		$user_id=get_current_user_id();	
		
		if($data==1)
		{	
			if($role == 'laboratorist' || $role == 'pharmacist' || $role == 'accountant' || $role == 'receptionist') 
			{
				$result = $wpdb->get_results("SELECT * FROM $table_bedallotment where allotment_by=$user_id ORDER BY bed_allotment_id DESC LIMIT 3");				
			}
			elseif($role == 'doctor') 
			{	
				$array=MJ_hmgt_doctor_patientid_list(); 
				$array = implode("','",$array);
				$result = $wpdb->get_results("SELECT * FROM $table_bedallotment where allotment_by=$user_id OR patient_id IN ('".$array."') ORDER BY bed_allotment_id DESC LIMIT 3");
			}
			elseif($role == 'nurse') 
			{
				$array=MJ_hmgt_nurse_all_bedallotment_data_array();
				$array = implode("','",$array);
				
				$result = $wpdb->get_results("SELECT * FROM $table_bedallotment where allotment_by=$user_id OR bed_allotment_id IN ('".$array."') ORDER BY bed_allotment_id DESC LIMIT 3");
				
			}
			elseif($role == 'patient') 
			{
				$result = $wpdb->get_results("SELECT * FROM $table_bedallotment where patient_id=$user_id ORDER BY bed_allotment_id DESC LIMIT 3");
			}			
		}
		else
		{
			$result = $wpdb->get_results("SELECT * FROM $table_bedallotment ORDER BY bed_allotment_id DESC LIMIT 3");
		}
		
		return $result;
	}
}
?>