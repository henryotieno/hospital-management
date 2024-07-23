<?php
class MJ_hmgt_appointment
{
	//Medicine Category
	public function MJ_hmgt_add_appointment($data)
	{
		
		$aa = $data['timeabc'];
		$time_with_ampm=$aa[$data['realtime']];
		$bb = $data['time'];
		$time=$bb[$data['realtime']];
		global $wpdb;
		$table_appointment = $wpdb->prefix. 'hmgt_appointment';
		//-------usersmeta table data--------------
		$appointmentdata['appointment_time_string']= MJ_hmgt_get_format_for_db($data['appointment_date'])." ".trim($time).":00";
		$appointmentdata['patient_id']=$data['patient_id'];
		$appointmentdata['doctor_id']=$data['doctor_id'];
		$appointmentdata['appointment_date']=MJ_hmgt_get_format_for_db($data['appointment_date']);
		$appointmentdata['appointment_time']=$time;
		$appointmentdata['appointment_time_with_a']=$time_with_ampm;
		$appointmentdata['appoint_create_date']=date("Y-m-d");
		$appointmentdata['appoint_create_by']=get_current_user_id();
		if(isset($_POST['department_id']))
		$appointmentdata['department_id']=$data['department_id'];
		if(!empty($data['virtual_appointment_meeeting_option']))
		{
			$appointmentdata['virtual_appointment_meeeting_option']=$data['virtual_appointment_meeeting_option'];
		}
		$user = wp_get_current_user();
		if($user->roles[0] == "doctor" || $user->roles[0] == "administrator")
		{
			$appointmentdata['status']= 1;
		}
		else
		{
			$appointmentdata['status']= 0;
		}
		
		if($data['action']=='edit')	
		{
			
			$appointment_id['appointment_id']=$data['appointment_id'];			
			$result=$wpdb->update( $table_appointment, $appointmentdata ,$appointment_id);
			
			if($result)
			{
				$patient=get_userdata($data['patient_id']);
				$patient_email=$patient->user_email;
				$patientname=$patient->display_name;;

				$doctor_id=get_userdata($data['doctor_id']);
				$doctor_name=$doctor_id->display_name;
				$doctor_email=$doctor_id->user_email;
				
			    $hospital_name = get_option('hmgt_hospital_name');
				$arr['{{Patient Name}}']=$patientname;			
				$arr['{{Doctor Name}}']=$doctor_name;			
				$arr['{{Appointment Time}}']=$time_with_ampm;			
				$arr['{{Appointment Date}}']=date(MJ_hmgt_date_formate(),strtotime($data['appointment_date']));			
				$arr['{{Hospital Name}}']=$hospital_name;
				$subject =get_option('MJ_hmgt_edit_appointment_patient_subject');
				
				$sub_arr['{{Doctor Name}}']=$doctor_name;
				$sub_arr['{{Appointment Time}}']=$time_with_ampm;			
				$sub_arr['{{Appointment Date}}']=date(MJ_hmgt_date_formate(),strtotime($data['appointment_date']));
				$sub_arr['{{Hospital Name}}']=$hospital_name;
				$subject = MJ_hmgt_subject_string_replacemnet($sub_arr,$subject);
				
				$message = get_option('MJ_hmgt_edit_appointment_patient_mail');
				$message_replacement = MJ_hmgt_string_replacemnet($arr,$message);	
				$to[]=$patient_email;
				//doctor mail
				$subject_doc =get_option('MJ_hmgt_edit_appointment_doctor_subject');
				$sub_doc_arr['{{Patient Name}}']=$patientname;
				$sub_doc_arr['{{Appointment Time}}']=$time_with_ampm;			
				$sub_doc_arr['{{Appointment Date}}']=date(MJ_hmgt_date_formate(),strtotime($data['appointment_date']));	
				$subject_doctor = MJ_hmgt_subject_string_replacemnet($sub_doc_arr,$subject_doc);
				
				$arr_doc['{{Doctor Name}}']=$doctor_name;
	            $arr_doc['{{Patient Name}}']=$patientname;			
				$arr_doc['{{Appointment Time}}']=$time_with_ampm;			
				$arr_doc['{{Appointment Date}}']=date(MJ_hmgt_date_formate(),strtotime($data['appointment_date']));			
				$arr_doc['{{Hospital Name}}']=$hospital_name;
				$message_doc = get_option('MJ_hmgt_edit_appointment_doctor_mail');
				$message_replacement_doc = MJ_hmgt_string_replacemnet($arr_doc,$message_doc);	
				$doctormail[]=$doctor_email;
				//patient mail
				 MJ_hmgt_send_mail($to,$subject,$message_replacement);
				//doctor mail
				MJ_hmgt_send_mail($doctormail,$subject_doctor,$message_replacement_doc);
				MJ_hmgt_append_audit_log(''.esc_html__('Update appointment','hospital_mgt').'',get_current_user_id());
				return $result;
			}
			//--------------- Zoom Integration -------------//
			$virtual_meeting=get_option("hmgt_enable_virtual_appointment");
			
			if($virtual_meeting == "yes")
			{
				if($data['virtual_appointment_meeeting_option'] == "1")
				{
					// var_dump("here");
					// var_dump($data);
					// die;
					if(empty($data['password']))
					{
						$password = wp_generate_password( 10, true, true );
					}
					else
					{
						$password = $data['password'];
					}
					$start_time = $data['appointment_date'].'T'.$data['appointment_time'].':'.'00';
					$end_time = $data['appointment_date'].'T'.$data['appointment_time'].':'.'00Z';
					$client = new GuzzleHttp\Client(['base_uri' => 'https://api.zoom.us']);
				    $arr_token = get_option('hmgt_virtual_appointment_access_token');
				   
					$token_decode = json_decode($arr_token);
				    $accessToken = $token_decode->access_token;
				    $topic = $data['agenda'];
				    // if(!empty($accessToken))
				    // {
				    try 
				    {
				    	if ($data['action']=='edit')
						{
							
							$meetingId = $data['zoom_meeting_id'];
							$response = $client->request('PATCH', "/v2/meetings/{$meetingId}", [
					            "headers" => [
					                "Authorization" => "Bearer $accessToken"
					            ],
					            "Query" => [
					                "occurrence_id" => "u+56LsDKSTmVXefuuMG8ug=="
					            ],
					            'json' => [
				                "topic" => "test",
				                "type" => 8,
				                "start_time" => $start_time,
				                "password" => $password,
				                "agenda" => $data['agenda'],
				                "recurrence" => [
								"type" => 2,
								//"weekly_days" => 2,
								"end_date_time" => $end_time,
								]
					            ],
					        ]);
						}
						
				    	$table_hmgt_zoom_meeting= $wpdb->prefix. 'hmgt_zoom_meeting';
				    	$meeting_data['title'] = "Appointment";
				    	$meeting_data['appointment_id'] = $data['appointment_id'];
				    	$meeting_data['doctor_id'] = $data['doctor_id'];
				    	$meeting_data['patient_id'] = $data['patient_id'];
				    	$meeting_data['agenda'] = $data['agenda'];
				    	$meeting_data['start_date'] = MJ_hmgt_get_format_for_db($data['appointment_date']);
				    	$meeting_data['end_date'] = MJ_hmgt_get_format_for_db($data['appointment_date']);
				    	// $meeting_data['duration'] = (int)$data['duration'];
				    	$meeting_data['password'] = $password;
				    	
				    	if($data['action']=='edit')
						{
							$meeting_data['zoom_meeting_id'] = $data['zoom_meeting_id'];
				    		$meeting_data['uuid'] = $data['uuid'];
							$meeting_data['meeting_join_link'] = $data['meeting_join_link'];
				    		$meeting_data['meeting_start_link'] = $data['meeting_start_link'];
							$meetingid['meeting_id']=sanitize_text_field($data['meeting_id']);
							$meeting_data['updated_date']=date("Y-m-d h:i:sa");
						    $meeting_data['updated_by']=get_current_user_id();
							$result=$wpdb->update( $table_hmgt_zoom_meeting, $meeting_data ,$meetingid);
							
						}
						if($result)
						{
							//---------- Virtual Appointment Invite MAIL Doctor---------//
							
							$hospital_name = get_option('hmgt_hospital_name');
		   
						    $subject =get_option('virtual_class_invite_mail_subject');
							
							$arr['{{topic}}']=$data['agenda'];
							$arr['{{date&time}}']=date(MJ_hmgt_date_formate(),strtotime($data['appointment_date'])).'( '.$time_with_ampm.')';
				            $arr['{{virtual_class_id}}']=$meeting_response->id;	
							$arr['{{password}}']=$password;			
							$arr['{{join_zoom_virtual_class}}']=$meeting_response->join_url;			
							$arr['{{start_zoom_virtual_class}}']=$meeting_response->start_url;			
							$arr['{{Hospital Name}}']=$hospital_name;
							$message = get_option('virtual_class_invite_mail_content');
							$message_replacement = MJ_hmgt_string_replacemnet($arr,$message);	
							$doctormail[]=$doctor_email;
							MJ_hmgt_send_mail($doctormail,$subject,$message_replacement);		


							//---------- Virtual Appointment Invite MAIL patient---------//
							
							$hospital_name = get_option('hmgt_hospital_name');
		   
						    $subject =get_option('virtual_class_invite_mail_subject');
							
							$arr['{{topic}}']=$data['agenda'];
							$arr['{{date&time}}']=date(MJ_hmgt_date_formate(),strtotime($data['appointment_date'])).'( '.$time_with_ampm.')';
				            $arr['{{virtual_class_id}}']=$meeting_response->id;	
							$arr['{{password}}']=$password;			
							$arr['{{join_zoom_virtual_class}}']=$meeting_response->join_url;			
							$arr['{{start_zoom_virtual_class}}']=$meeting_response->start_url;			
							$arr['{{Hospital Name}}']=$hospital_name;
							$message = get_option('virtual_class_invite_mail_content');
							$message_replacement = MJ_hmgt_string_replacemnet($arr,$message);	
							$doctormail[]=$patient_email;
							MJ_hmgt_send_mail($doctormail,$subject,$message_replacement);	
						}
						return $result;
					}
					catch(Exception $e) 
					{
					 	/*var_dump("here123");
						var_dump($e);
						die;*/
						if(401 == $e->getCode())
						{
							generate_access_token();
						}
						else
						{
							//wp_redirect ( admin_url().'admin.php?page=smgt_virtual_classroom&tab=meeting_list&message=5');
						}
					// }
					}
				}
			}
			
		}
		else
		{
			
			$result=$wpdb->insert( $table_appointment, $appointmentdata );
			
			$lastid = $wpdb->insert_id;
			$patient=get_userdata($data['patient_id']);
			$patient_email=$patient->user_email;
			$patientname=$patient->display_name;;
				
			$doctor_id=get_userdata($data['doctor_id']);
			$doctor_name=$doctor_id->display_name;
			$doctor_email=$doctor_id->user_email;
			
		    $hospital_name = get_option('hmgt_hospital_name');
			$arr['{{Patient Name}}']=$patientname;			
			$arr['{{Doctor Name}}']=$doctor_name;			
			$arr['{{Appointment Time}}']=$time_with_ampm;			
			$arr['{{Appointment Date}}']=date(MJ_hmgt_date_formate(),strtotime($data['appointment_date']));			
			$arr['{{Hospital Name}}']=$hospital_name;
			$subject =get_option('MJ_hmgt_appointment_booking_patient_mail_subject');
			
			$sub_arr['{{Doctor Name}}']=$doctor_name;
			$sub_arr['{{Appointment Time}}']=$time_with_ampm;			
			$sub_arr['{{Appointment Date}}']=date(MJ_hmgt_date_formate(),strtotime($data['appointment_date']));
			$sub_arr['{{Hospital Name}}']=$hospital_name;
			$subject = MJ_hmgt_subject_string_replacemnet($sub_arr,$subject);
			
			$message = get_option('MJ_hmgt_appointment_booking_patient_mail_template');
			$message_replacement = MJ_hmgt_string_replacemnet($arr,$message);	
			$to[]=$patient_email;
			MJ_hmgt_send_mail($to,$subject,$message_replacement);
			//patient mail template
		    $hospital_name = get_option('hmgt_hospital_name');
		   
		    $subject =get_option('MJ_hmgt_appointment_booking_doctor_mail_subject');
			$sub_arr['{{Patient Name}}']=$patientname;
			$sub_arr['{{Appointment Time}}']=$time_with_ampm;			
			$sub_arr['{{Appointment Date}}']=date(MJ_hmgt_date_formate(),strtotime($data['appointment_date']));	
			$subject = MJ_hmgt_subject_string_replacemnet($sub_arr,$subject);
			
			$arr['{{Doctor Name}}']=$doctor_name;
            $arr['{{Patient Name}}']=$patientname;			
			$arr['{{Appointment Time}}']=$time_with_ampm;			
			$arr['{{Appointment Date}}']=date(MJ_hmgt_date_formate(),strtotime($data['appointment_date']));			
			$arr['{{Hospital Name}}']=$hospital_name;
			$message = get_option('MJ_hmgt_appointment_booking_patient_mail_template');
			$message_replacement = MJ_hmgt_string_replacemnet($arr,$message);	
			$doctormail[]=$doctor_email;
			MJ_hmgt_send_mail($doctormail,$subject,$message_replacement);		
			
			MJ_hmgt_append_audit_log(''.esc_html__('Add new appointment ','hospital_mgt').'',get_current_user_id());

			//--------------- Zoom Integration -------------//
			$virtual_meeting=get_option("hmgt_enable_virtual_appointment");
			
			if($virtual_meeting == "yes")
			{
				if($data['virtual_appointment_meeeting_option'] == "1")
				{
					if(empty($data['password']))
					{
						$password = wp_generate_password( 10, true, true );
					}
					else
					{
						$password = $data['password'];
					}
					$start_time = $data['appointment_date'].'T'.$time.':'.'00';
					$end_time = $data['appointment_date'].'T'.$time.':'.'00Z';
					$client = new GuzzleHttp\Client(['base_uri' => 'https://api.zoom.us']);
				    $arr_token = get_option('hmgt_virtual_appointment_access_token');
				  
			    	$token_decode = json_decode($arr_token);
				    $accessToken = $token_decode->access_token;
				    $topic = $data['agenda'];
				    // if(!empty($accessToken))
				    // {
				    try 
				    {
						$response = $client->request('POST', '/v2/users/me/meetings', [
			            "headers" => [
			                "Authorization" => "Bearer $accessToken"
			            ],
			            'json' => [
			                "topic" => "test",
			                "type" => 8,
			                "start_time" => $start_time,
			                "password" => $password,
			                "agenda" => $data['agenda'],
			                "recurrence" => [
							"type" => 2,
							//"weekly_days" => 2,
							"end_date_time" => $end_time,
							]
				            ],
				        ]);
				       
				        $meeting_response = json_decode($response->getBody());
					  	
			        	$table_hmgt_zoom_meeting= $wpdb->prefix. 'hmgt_zoom_meeting';
			        	$meeting_data['title'] = "Appointment";
			        	$meeting_data['appointment_id'] = (int)$lastid;
			        	$meeting_data['doctor_id'] = $data['doctor_id'];
					    $meeting_data['patient_id'] = $data['patient_id'];
			        	$meeting_data['agenda'] = $data['agenda'];
			        	$meeting_data['start_date'] = MJ_hmgt_get_format_for_db($data['appointment_date']);
			        	$meeting_data['end_date'] = MJ_hmgt_get_format_for_db($data['appointment_date']);
			        	// $meeting_data['duration'] = (int)$data['duration'];
			        	$meeting_data['password'] = $password;
			        	
						if($meeting_response)
						{
							$meeting_data['zoom_meeting_id'] = $meeting_response->id;
			        		$meeting_data['uuid'] = $meeting_response->uuid;
							$meeting_data['meeting_join_link'] = $meeting_response->join_url;
			        		$meeting_data['meeting_start_link'] = $meeting_response->start_url;
							$meeting_data['created_by'] = get_current_user_id();
			        		$meeting_data['created_date'] = date("Y-m-d h:i:sa");
							$result=$wpdb->insert( $table_hmgt_zoom_meeting, $meeting_data );

						}
						
						if($result)
						{
							// var_dump($meeting_response->occurrences);
							//---------- Virtual Appointment Invite MAIL Doctor---------//
							
							$hospital_name = get_option('hmgt_hospital_name');
		   
						    $subject =get_option('virtual_class_invite_mail_subject');
							
							$arr['{{topic}}']=$data['agenda'];
							$arr['{{date&time}}']=date(MJ_hmgt_date_formate(),strtotime($data['appointment_date'])).'( '.$time_with_ampm.')';
				            $arr['{{virtual_class_id}}']=$meeting_response->id;	
							$arr['{{password}}']=$password;			
							$arr['{{join_zoom_virtual_class}}']=$meeting_response->join_url;			
							$arr['{{start_zoom_virtual_class}}']=$meeting_response->start_url;			
							$arr['{{Hospital Name}}']=$hospital_name;
							$message = get_option('virtual_class_invite_mail_content');
							$message_replacement = MJ_hmgt_string_replacemnet($arr,$message);	
							$doctormail[]=$doctor_email;
							MJ_hmgt_send_mail($doctormail,$subject,$message_replacement);		


							//---------- Virtual Appointment Invite MAIL patient---------//
							
							$hospital_name = get_option('hmgt_hospital_name');
		   
						    $subject =get_option('virtual_class_invite_mail_subject');
							
							$arr['{{topic}}']=$data['agenda'];
							$arr['{{date&time}}']=date(MJ_hmgt_date_formate(),strtotime($data['appointment_date'])).'( '.$time_with_ampm.')';
				            $arr['{{virtual_class_id}}']=$meeting_response->id;	
							$arr['{{password}}']=$password;			
							$arr['{{join_zoom_virtual_class}}']=$meeting_response->join_url;			
							$arr['{{start_zoom_virtual_class}}']=$meeting_response->start_url;			
							$arr['{{Hospital Name}}']=$hospital_name;
							$message = get_option('virtual_class_invite_mail_content');
							$message_replacement = MJ_hmgt_string_replacemnet($arr,$message);	
							$doctormail[]=$patient_email;
							MJ_hmgt_send_mail($doctormail,$subject,$message_replacement);	
						}
						return $result;
					}
					catch(Exception $e) 
					{
					 	/*var_dump("here123");
						var_dump($e);
						die;*/
						if(401 == $e->getCode())
						{
							generate_access_token();
						}
						else
						{
							//wp_redirect ( admin_url().'admin.php?page=smgt_virtual_classroom&tab=meeting_list&message=5');
						}
					// }
					}
				}
				
			}
		return $result;	
		}
	}
	
	//get all appointment
	public function MJ_hmgt_get_all_appointment()
	{
		$current_docter = wp_get_current_user();
		$id=$current_docter->ID;
		global $wpdb;
		$table_appointment = $wpdb->prefix. 'hmgt_appointment';		
		$result = $wpdb->get_results("SELECT * FROM $table_appointment ORDER BY appointment_date DESC");
		return $result;		
	}
	//get all appointment by created by
	public function MJ_hmgt_get_all_appointment_by_create_by()
	{
		$current_docter = wp_get_current_user();
		$id=$current_docter->ID;
		global $wpdb;
		$table_appointment = $wpdb->prefix. 'hmgt_appointment';		
		$result = $wpdb->get_results("SELECT * FROM $table_appointment where appoint_create_by=$id ORDER BY appointment_date DESC");
		return $result;		
	}
	//get doctor created all appointment 
	public function MJ_hmgt_get_doctor_all_appointment_by_create_by()
	{
		$current_docter = wp_get_current_user();
		$id=$current_docter->ID;
		global $wpdb;
		$array=MJ_hmgt_doctor_patientid_list(); 
		$array = implode("','",$array);
		$table_appointment = $wpdb->prefix. 'hmgt_appointment';		
		$result = $wpdb->get_results("SELECT * FROM $table_appointment where appoint_create_by=$id OR doctor_id=$id OR patient_id IN ('".$array."') ORDER BY appointment_date DESC");
		return $result;		
	}
	//get nurse crated all appointment
	public function MJ_hmgt_get_nurse_all_appointment_by_create_by()
	{
		$current_docter = wp_get_current_user();
		$id=$current_docter->ID;
		global $wpdb;
		$array=MJ_hmgt_nurse_patientid_list(); 
		
		$array = implode("','",$array);
		$table_appointment = $wpdb->prefix. 'hmgt_appointment';		
		$result = $wpdb->get_results("SELECT * FROM $table_appointment where appoint_create_by=$id OR patient_id IN ('".$array."') ORDER BY appointment_date DESC");
		return $result;		
	}
	//get patient all appointment
	public function MJ_hmgt_get_patient_all_appointment()
	{
		$current_patient = wp_get_current_user();
		$id=$current_patient->ID;
		global $wpdb;
		$table_appointment = $wpdb->prefix. 'hmgt_appointment';		
		$result = $wpdb->get_results("SELECT * FROM $table_appointment where patient_id=$id ORDER BY appointment_date DESC");
		
		return $result;		
	}
	public function MJ_hmgt_get_patient_all_appointment1($id)
	{
		global $wpdb;
		$table_appointment = $wpdb->prefix. 'hmgt_appointment';		
		$result = $wpdb->get_results("SELECT * FROM $table_appointment where patient_id=$id ORDER BY appointment_date DESC");
		
		return $result;		
	}
	//get tretment name
	public function MJ_hmgt_get_treatment_name($treatment_id)
	{
		global $wpdb;
		$table_treatment = $wpdb->prefix. 'hmgt_treatment';
		
		$result = $wpdb->get_var("SELECT treatment_name FROM $table_treatment where appointment_id= ".$treatment_id);
		return $result;
	}
	//get single appointment
	public function MJ_hmgt_get_single_appointment($appointment_id)
	{
		global $wpdb;
		$table_appointment = $wpdb->prefix. 'hmgt_appointment';
		$result = $wpdb->get_row("SELECT * FROM $table_appointment where appointment_id= ".$appointment_id);
		
		return $result;
	}
	//delete appointment
	public function delete_appointment($appointment_id)
	{
		global $wpdb;
		$table_appointment = $wpdb->prefix. 'hmgt_appointment';
		//patient mail
		$appointment_data=$this->MJ_hmgt_get_single_appointment($appointment_id);
		
		$patient=get_userdata($appointment_data->patient_id);
		$patient_email=$patient->user_email;
		$patientname=$patient->display_name;;

		$doctor_id=get_userdata($appointment_data->doctor_id);
		$doctor_name=$doctor_id->display_name;
		$doctor_email=$doctor_id->user_email;
		
		$hospital_name = get_option('hmgt_hospital_name');
		$arr['{{Patient Name}}']=$patientname;			
		$arr['{{Doctor Name}}']=$doctor_name;			
		$arr['{{Appointment Time}}']=$appointment_data->appointment_time_with_a;			
		$arr['{{Appointment Date}}']=$appointment_data->appointment_date;			
		$arr['{{Hospital Name}}']=$hospital_name;
		$subject =get_option('MJ_hmgt_cancel_appointment_patient_subject');
		
		$sub_arr['{{Doctor Name}}']=$doctor_name;
		$sub_arr['{{Appointment Time}}']=$appointment_data->appointment_time_with_a;			
		$sub_arr['{{Appointment Date}}']=$appointment_data->appointment_date;
		$sub_arr['{{Hospital Name}}']=$hospital_name;
		$subject = MJ_hmgt_subject_string_replacemnet($sub_arr,$subject);
		
		$message = get_option('MJ_hmgt_cancel_appointment_patient_mail');
		$message_replacement = MJ_hmgt_string_replacemnet($arr,$message);	
		$to[]=$patient_email;
		
		//doctor mail
		$subject_doc =get_option('MJ_hmgt_cancel_appointment_doctor_subject');
		$sub_doc_arr['{{Patient Name}}']=$patientname;
		$sub_doc_arr['{{Appointment Time}}']=$appointment_data->appointment_time_with_a;			
		$sub_doc_arr['{{Appointment Date}}']=$appointment_data->appointment_date;	
		$subject_doctor = MJ_hmgt_subject_string_replacemnet($sub_doc_arr,$subject_doc);
		
		$arr_doc['{{Doctor Name}}']=$doctor_name;
		$arr_doc['{{Patient Name}}']=$patientname;			
		$arr_doc['{{Appointment Time}}']=$appointment_data->appointment_time_with_a;			
		$arr_doc['{{Appointment Date}}']=$appointment_data->appointment_date;			
		$arr_doc['{{Hospital Name}}']=$hospital_name;
		$message_doc = get_option('MJ_hmgt_cancel_appointment_doctor_mail');
		$message_replacement_doc = MJ_hmgt_string_replacemnet($arr_doc,$message_doc);	
		$doctormail[]=$doctor_email;
		$result = $wpdb->query("DELETE FROM $table_appointment where appointment_id= ".$appointment_id);
		if(isset($result))
		{
			//patient mail
			MJ_hmgt_send_mail($to,$subject,$message_replacement);
		   //doctor mail
		   MJ_hmgt_send_mail($doctormail,$subject_doctor,$message_replacement_doc);
		   MJ_hmgt_append_audit_log(''.esc_html__('Delete appointment ','hospital_mgt').'',get_current_user_id());
		   return $result;  
		}	  
	}
	//add appointment time
	public function MJ_hmgt_add_appointment_time($data)
	{	
		global $wpdb;
		$table_appointment_time = $wpdb->prefix. 'hmgt_apointment_time';       
		
		$user_id = wp_get_current_user();
		$doctor_id=$user_id->ID;
		
		$date=MJ_hmgt_get_format_for_db($data['appointment_time_startdate']);
		$result=$wpdb->get_results("SELECT id FROM $table_appointment_time where '$date' between apointment_startdate and apointment_enddate and user_id=".$doctor_id."");
		
		foreach($result as $time)
		{	
			$result_delete=$wpdb->query("delete from $table_appointment_time where id=".$time->id);
		}			
		
		$time=$data['time'];
		 
		foreach ($time as $key => $value)
		{		
			foreach ($value as $key1 => $value1)
			{				
				$appointment_time_data['user_id']=get_current_user_id();
				$appointment_time_data['apointment_startdate']=MJ_hmgt_get_format_for_db($data['appointment_time_startdate']);
				$appointment_time_data['apointment_enddate']=MJ_hmgt_get_format_for_db($data['appointment_time_enddate']);
			
				$appointment_time_data['day']=$key1;		
				$appointment_time_data['apointment_time']=$value1; 
				$appointment_time_data['created_date']=date("Y-m-d");
				$appointment_time_data['created_by']=get_current_user_id();	
				
				$result_insert=$wpdb->insert( $table_appointment_time, $appointment_time_data);		
			}	
		 }
		return $result_insert;	
	}
	//admin dashboard appointment list
	public function MJ_hmgt_get_appointment_on_admin_dashboard()
	{
		global $wpdb;
		$table_appointment = $wpdb->prefix. 'hmgt_appointment';		
		$result = $wpdb->get_results("SELECT * FROM $table_appointment ORDER BY appointment_id DESC LIMIT 3");
		return $result;		
	}
	//fronted dashboard appointment list
	public function MJ_hmgt_get_appointment_on_fronted_dashboard()
	{
		global $wpdb;
		$table_appointment = $wpdb->prefix. 'hmgt_appointment';	
		
		$page='appointment';
		$data=MJ_hmgt_page_wise_view_data_on_fronted_dashboard($page);
		$role=MJ_hmgt_get_current_user_role();	
		$current_user = wp_get_current_user();
		$id=$current_user->ID;
		
		if($data==1)
		{	
			if($role == 'laboratorist' || $role == 'pharmacist' || $role == 'accountant' || $role == 'receptionist') 
			{
				$result = $wpdb->get_results("SELECT * FROM $table_appointment where appoint_create_by=$id ORDER BY appointment_id DESC LIMIT 3");			  
			}
			elseif($role == 'doctor') 
			{
				$array=MJ_hmgt_doctor_patientid_list(); 
				$array = implode("','",$array);
				$result = $wpdb->get_results("SELECT * FROM $table_appointment where doctor_id=$id OR appoint_create_by=$id OR patient_id IN ('".$array."') ORDER BY appointment_id DESC LIMIT 3");
			}
			elseif($role == 'nurse') 
			{				
				$array=MJ_hmgt_nurse_patientid_list(); 				
				$array = implode("','",$array);
					
				$result = $wpdb->get_results("SELECT * FROM $table_appointment where appoint_create_by=$id OR patient_id IN ('".$array."') ORDER BY appointment_id DESC LIMIT 3");
			}
			elseif($role == 'patient')
			{
				$result = $wpdb->get_results("SELECT * FROM $table_appointment where patient_id=$id ORDER BY appointment_id DESC LIMIT 3");				
			}			
		}
		else
		{
			$result = $wpdb->get_results("SELECT * FROM $table_appointment ORDER BY appointment_id DESC LIMIT 3");
		}
		return $result;		
	}
}
?>