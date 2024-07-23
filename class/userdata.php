<?php
class MJ_hmgt_user
{
	public $usermetadata=array();
	public $userdata = array();
	public function MJ_hmgt_add_user($data)
	{		
		//-------usersmeta table data--------------
		if(isset($data['middle_name']))
		$usermetadata['middle_name']=MJ_hmgt_strip_tags_and_stripslashes($data['middle_name']);
	
		if(isset($data['gender']))
		$usermetadata['gender']=MJ_hmgt_strip_tags_and_stripslashes($data['gender']);
	
		if(isset($data['birth_date']))
		$usermetadata['birth_date']=MJ_hmgt_get_format_for_db($data['birth_date']); 
		
		if(isset($data['address']))
		$usermetadata['address']=MJ_hmgt_strip_tags_and_stripslashes($data['address']);
		
		if(isset($data['city_name']))
		$usermetadata['city_name']=MJ_hmgt_strip_tags_and_stripslashes($data['city_name']);
		if(isset($data['state_name']))
		$usermetadata['state_name']=MJ_hmgt_strip_tags_and_stripslashes($data['state_name']);
		if(isset($data['country_name']))
		$usermetadata['country_name']=MJ_hmgt_strip_tags_and_stripslashes($data['country_name']);
		if(isset($data['zip_code']))
		$usermetadata['zip_code']=MJ_hmgt_strip_tags_and_stripslashes($data['zip_code']);
		if(isset($data['phonecode']))
		$usermetadata['phonecode']=MJ_hmgt_strip_tags_and_stripslashes($data['phonecode']);
		if(isset($data['mobile']))
		$usermetadata['mobile']=MJ_hmgt_strip_tags_and_stripslashes($data['mobile']);
		if(isset($data['phone']))
		$usermetadata['phone']=MJ_hmgt_strip_tags_and_stripslashes($data['phone']);
		if(isset($data['hmgt_user_avatar']))
		$usermetadata['hmgt_user_avatar']=$data['hmgt_user_avatar'];
		if($data['role']=='doctor')
		{
			if(isset($data['office_address']))
			$usermetadata['office_address']=MJ_hmgt_strip_tags_and_stripslashes($data['office_address']);
			if(isset($data['department']))
			$usermetadata['department']=MJ_hmgt_strip_tags_and_stripslashes($data['department']);
			if(isset($data['specialization']))
			$usermetadata['specialization']=MJ_hmgt_strip_tags_and_stripslashes($data['specialization']);
			if(isset($data['doc_degree']))
			$usermetadata['doctor_degree']=MJ_hmgt_strip_tags_and_stripslashes($data['doc_degree']);
			if(isset($data['visiting_fees']))
			$usermetadata['visiting_fees']=$data['visiting_fees'];
			if(isset($data['consulting_fees']))
			$usermetadata['consulting_fees']=$data['consulting_fees'];
			if(isset($data['home_city_name']))
			$usermetadata['home_city']=MJ_hmgt_strip_tags_and_stripslashes($data['home_city_name']);
			if(isset($data['home_state_name']))
			$usermetadata['home_state']=MJ_hmgt_strip_tags_and_stripslashes($data['home_state_name']);
			if(isset($data['home_country_name']))
			$usermetadata['home_country']=MJ_hmgt_strip_tags_and_stripslashes($data['home_country_name']);
		    if(isset($data['home_zip_code']))
			$usermetadata['home_zip_code']=MJ_hmgt_strip_tags_and_stripslashes($data['home_zip_code']);	
			if(isset($data['visiting_fees_tax']))
			$usermetadata['visiting_fees_tax']=implode(",",$data['visiting_fees_tax']);		
			if(isset($data['consulting_fees_tax']))
			$usermetadata['consulting_fees_tax']=implode(",",$data['consulting_fees_tax']);			
			
		}
		
		if($data['role']=='patient')
		{
			if(isset($data['patient_id']))
			$usermetadata['patient_id']=MJ_hmgt_strip_tags_and_stripslashes($data['patient_id']);
			if(isset($data['blood_group']))
			$usermetadata['blood_group']=MJ_hmgt_strip_tags_and_stripslashes($data['blood_group']);
		    if(isset($data['symptoms']))
				$usermetadata['symptoms']=implode(",",$data['symptoms']);
			if(isset($data['patient_type']))
			$usermetadata['patient_type']=MJ_hmgt_strip_tags_and_stripslashes($data['patient_type']);
			
		}
		if(isset($data['patient_type'])=='outpatient')
		{		
			if(isset($data['symptoms']))
				$usermetadata['symptoms']=implode(",",$data['symptoms']);
			if(isset($data['patient_convert']))
				$usermetadata['patient_type']='inpatient';
		}
		if($data['role']=='nurse' || $data['role']=='receptionist')
		{
			if(isset($data['department']))
			$usermetadata['department']=MJ_hmgt_strip_tags_and_stripslashes($data['department']);
			if(isset($data['charge']))
			$usermetadata['charge']=$data['charge'];
		}
				
		if($data['role']=='nurse')
		{
			if(!empty($data['tax']))
			{		
				$usermetadata['tax']=implode(",",$data['tax']);
			}
			else
			{
				$usermetadata['tax']=null;
			}	
		}
		
		//-------users table data-----------------
		if(isset($data['username']))
		$userdata['user_login']=MJ_hmgt_strip_tags_and_stripslashes($data['username']);
		else{$userdata['user_login']=MJ_hmgt_strip_tags_and_stripslashes($data['email']);}
		if(isset($data['email']))
		$userdata['user_email']=MJ_hmgt_strip_tags_and_stripslashes($data['email']);
		$userdata['user_nicename']=NULL;
		$userdata['user_url']=NULL;
		if(isset($data['first_name']))
		$userdata['display_name']=MJ_hmgt_strip_tags_and_stripslashes($data['first_name'])." ".MJ_hmgt_strip_tags_and_stripslashes($data['last_name']);
		if($data['password'] != "")
				$userdata['user_pass']=MJ_hmgt_password_validation($data['password']);
		
			
		if($data['action']=='edit')	
		{ 
			MJ_hmgt_append_audit_log(''.esc_html__('Update user detail ','hospital_mgt').'',get_current_user_id());
			$userdata['ID']=$data['user_id'];
			$user_id = wp_update_user($userdata);
			
	       if(isset($data['first_name']))
			$returnans=update_user_meta( $user_id, 'first_name', MJ_hmgt_strip_tags_and_stripslashes($data['first_name']) );
			if(isset($data['last_name']))
			$returnans=update_user_meta( $user_id, 'last_name',MJ_hmgt_strip_tags_and_stripslashes($data['last_name']) );
	
				foreach($usermetadata as $key=>$val)
				{
					$returnans=update_user_meta( $user_id, $key,$val );
				}
				return $user_id;
		}
		else
		{
			$usermetadata['created_by']=get_current_user_id();
			//----------insert code------------------
			$user_id = wp_insert_user( $userdata );
			MJ_hmgt_append_audit_log(''.esc_html__('Add new user ','hospital_mgt').'',get_current_user_id());
			//patient ragistation  send email  code start.
			if($data['role']=='patient')
			{
		    $login_link=home_url();
		    $hospital_name = get_option('hmgt_hospital_name');
			$subject =get_option('MJ_hmgt_patient_registration');
			 $arr['{{Patient Name}}']=$userdata['display_name'];			
			$arr['{{Patient ID}}']=MJ_hmgt_strip_tags_and_stripslashes($data['patient_id']);			
			$arr['{{Hospital Name}}']=$hospital_name;
			$arr['{{Login Link}}']=$login_link;	
			$sub_arr['{{Hospital Name}}']=$hospital_name;
			$subject = MJ_hmgt_subject_string_replacemnet($sub_arr,$subject);
			$message = get_option('MJ_hmgt_registration_email_template');	
			$message_replacement = MJ_hmgt_string_replacemnet($arr,$message);	
				$to[]=$data['email'];
					MJ_hmgt_send_mail($to,$subject,$message_replacement); 
			}
			//userragistation mail start
			 $role=$data['role'];
			$login_link=home_url();
			$hospital_name = get_option('hmgt_hospital_name');
			$arr['{{UserName}}']=$userdata['display_name'];
			$arr['{{User_Name}}']=MJ_hmgt_strip_tags_and_stripslashes($data['username']);
			$arr['{{Hospital Name}}']=$hospital_name;
			$arr['{{Role Name}}']=$role;
			$arr['{{Password}}']=MJ_hmgt_password_validation($data['password']);
			$arr['{{Login Link}}']=$login_link;
			$subject =get_option('MJ_hmgt_user_registration_subject');
			$sub_arr['{{Hospital Name}}']=$hospital_name;
			$subject = MJ_hmgt_subject_string_replacemnet($sub_arr,$subject);
			$message = get_option('MJ_hmgt_user_registration_email_template');	
			$message_replacement = MJ_hmgt_string_replacemnet($arr,$message);
				$to[]=$data['email'];
					MJ_hmgt_send_mail($to,$subject,$message_replacement);
			//userragistation mail end
			$user = new WP_User($user_id);
			$user->set_role($data['role']); 
						
			foreach($usermetadata as $key=>$val)
			{
				$returnans=add_user_meta( $user_id, $key,$val, true );
			}
			if(isset($data['first_name']))
			$returnans=update_user_meta( $user_id, 'first_name', MJ_hmgt_strip_tags_and_stripslashes($data['first_name']) );
			if(isset($data['last_name']))
			$returnans=update_user_meta( $user_id, 'last_name', MJ_hmgt_strip_tags_and_stripslashes($data['last_name']) );
			return $user_id;
		}
	}
	//delete user data
	public function MJ_hmgt_delete_usedata($record_id)
	{
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'usermeta';
		$result=$wpdb->query($wpdb->prepare("DELETE FROM $table_name WHERE user_id= %d",$record_id));
		$retuenval=wp_delete_user( $record_id );
		return $retuenval;
	}
	//update user data
	public function MJ_hmgt_update_user($data)
	{
		$user_id = wp_update_user();
		$returnval=update_user_meta( $user_id, 'first_name', $data['first_name'] );
		$returnval=update_user_meta( $user_id, 'last_name', $data['last_name'] );
		foreach($usermetadata as $key=>$val)
		{
			$returnans=update_user_meta( $user_id, $key,$val );
			if($returnans)
				$returnval=$returnans;
		}
	}
	//upload documents data
	public function MJ_hmgt_upload_documents($cv,$education,$experience,$user_id)
	{
		$usermetadata['doctor_cv']=$cv;
		$usermetadata['edu_certificate']=$education;
		$usermetadata['exp_certificate']=$experience;
		foreach($usermetadata as $key=>$val){
				$returnans=add_user_meta( $user_id, $key,$val, true );
		}
	}
	//update upload documents data
	public function MJ_hmgt_update_upload_documents($cv,$education,$experience,$user_id)
	{
		$usermetadata['doctor_cv']=$cv;
		$usermetadata['edu_certificate']=$education;
		$usermetadata['exp_certificate']=$experience;
		foreach($usermetadata as $key=>$val)
		{
			$returnans=update_user_meta( $user_id, $key,$val);
		}
	}
	//update diagnosis report data
	public function MJ_hmgt_update_diagnosis_report($user_id,$report,$diagno_id)
	{
		global $wpdb;
		$table_diagnosis = $wpdb->prefix. 'hmgt_diagnosis';		
		$diagnosisdata['patient_id']=$user_id;
		$diagnosisdata['attach_report']=$report;
		$diagnosisdata['diagnosis_date']=date("Y-m-d");
		$dignosis_id['diagnosis_id']=$diagno_id;
		$diagnosisdata['diagno_create_by']=get_current_user_id();
		$result=$wpdb->update( $table_diagnosis, $diagnosisdata ,$dignosis_id);
		return $result;
		
	}
	//upload diagnosis report data
	public function MJ_hmgt_upload_diagnosis_report($user_id,$report)
	{
		global $wpdb;
		$table_diagnosis = $wpdb->prefix. 'hmgt_diagnosis';		
		$diagnosisdata['patient_id']=$user_id;
		$diagnosisdata['attach_report']=$report;
		$diagnosisdata['diagnosis_date']=date("Y-m-d");
		$diagnosisdata['diagno_create_by']=get_current_user_id();
		$result=$wpdb->insert( $table_diagnosis, $diagnosisdata );
	}
	//upload multiple diagnosis report data
	public function MJ_hmgt_upload_multiple_diagnosis_report($user_id,$report)
	{
		global $wpdb;
		$table_diagnosis = $wpdb->prefix. 'hmgt_diagnosis';
		if(!empty($report))
		{	
			foreach($report as $filename)
			{
				$diagnosisdata['patient_id']=$user_id;
				$diagnosisdata['attach_report']= $filename;
				$diagnosisdata['diagnosis_date']=date("Y-m-d");
				$diagnosisdata['diagno_create_by']=get_current_user_id();
				$result=$wpdb->insert( $table_diagnosis, $diagnosisdata );
			}
		}
	}
	//get staff department data
	public function MJ_hmgt_get_staff_department()
	{
		$args= array('post_type'=> 'department','posts_per_page'=>-1,'orderby'=>'post_title','order'=>'Asc');
					$result = get_posts($args);
		return $result;
	}
	//add staff department
	public function MJ_hmgt_add_staff_department($data)
	{
		$result = wp_insert_post( array(
						'post_status' => 'publish',
						'post_type' => 'department',
						'post_title' => $data['category_name']) );
			return $result;		
	}
	//delete staff department data
	public function MJ_hmgt_delete_staff_department($department_id)
	{
		$result=wp_delete_post($department_id);
		return $result;
	}
	//get doctor specialization data
	public function MJ_hmgt_get_doctor_specilize()
	{
		$args= array('post_type'=> 'specialization','posts_per_page'=>-1,'orderby'=>'post_title','order'=>'Asc');
					$result = get_posts( $args );
		return $result;
	}
	//add doctor specialization data
	public function MJ_hmgt_add_doctor_specilize($data)
	{
		$result = wp_insert_post( array(
						'post_status' => 'publish',
						'post_type' => 'specialization',
						'post_title' => $data['category_name']) );
			return $result;		
	}
	//delete doctor specialization data
	public function MJ_hmgt_delete_doctor_specilize($specilize_id)
	{
		$result=wp_delete_post($specilize_id);
		return $result;
	}
	//------get patient SYMPTOMS------
	public function MJ_hmgt_getPatientSymptoms()
	{
		$args= array('post_type'=> 'symptoms','posts_per_page'=>-1,'orderby'=>'post_title','order'=>'Asc');
					$result = get_posts( $args );
		return $result;
	}
	//add symptoms data
	public function hmgtAddSymptoms($data)
	{
		$result = wp_insert_post( array(
						'post_status' => 'publish',
						'post_type' => 'symptoms',
						'post_title' => $data['category_name']) );
			return $result;		
	}
	//delete symptoms data
	public function hmgtDeleteSymptoms($symptoms_id)
	{
		$result=wp_delete_post($symptoms_id);
		return $result;
	}
	public function add_discharge_data($data)
	{
		global $wpdb;
		$table_discharge = $wpdb->prefix. 'hmgt_discharge';
		$dischargedata['patient_id']=$data['patient_id'];
		$dischargedata['discharge_medi']=$data['discharge_medi'];
		$dischargedata['discharge_date']=date("Y-m-d");
		$dischargedata['description']=$data['description'];
		$dischargedata['status']='Discharged';
		$guardian_data['patient_status']='Discharged';
		$result=MJ_hmgt_update_guardian($guardian_data,$_POST['patient_id']);
		$result=$wpdb->insert( $table_discharge, $dischargedata );	
		return $result;			
	}			
}
?>