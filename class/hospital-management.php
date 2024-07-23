<?php
class MJ_hmgt_Hospital_Management
{
	public $patient;
	public $doctor;
	public $nurse;
	public $pharmacist;
	public $accountant;
	public $laboratorist;
	public $outpatient;
	public $medicine;
	public $prescription;
	public $role;
	public $ambulance;
	public $appointment;
	public $events;
	public $notice;
	public $birth_report;
	public $death_report;
	public $operation_report;
	public $all_events_notice;//Event tab
	public $diagnosis_report;
	
	
	function __construct($user_id = NULL)
	{
		if($user_id)
		{
			$this->role=$this->MJ_hmgt_get_current_user_role();
			if($this->role == 'doctor')
			{
				$this->patient = $this->MJ_hmgt_get_patient_list($user_id);		
				$this->appointment = $this->MJ_hmgt_get_appointment_list($user_id);	
				$this->events = $this->MJ_hmgt_get_event_notice_list($user_id,'doctor','hmgt_event');
				$this->notice = $this->MJ_hmgt_get_event_notice_list($user_id,'doctor','hmgt_notice');				
				$this->birth_report = $this->MJ_hmgt_all_patient_report('Birth');
				$this->death_report = $this->MJ_hmgt_all_patient_report('Death');
				$this->operation_report = $this->MJ_hmgt_all_patient_report('Operation');
				$this->prescription = $this->MJ_hmgt_get_doctor_prescription($user_id);
				$this->all_events_notice = $this->MJ_hmgt_get_all_event_notice_list($user_id,'doctor');
				$this->all_own_events_notice = $this->MJ_hmgt_get_all_own_event_notice_list($user_id,'doctor');
			}
			if($this->role == 'patient')
			{
				$this->patient = $this->MJ_hmgt_get_single_patient($user_id);
				$this->events = $this->MJ_hmgt_get_event_notice_list($user_id,'patient','hmgt_event');
				$this->notice = $this->MJ_hmgt_get_event_notice_list($user_id,'patient','hmgt_notice');
				$this->appointment = $this->MJ_hmgt_get_patient_appointment_list($user_id);
				
				$this->birth_report = $this->MJ_hmgt_patient_report($user_id,'Birth');
				$this->death_report = $this->MJ_hmgt_patient_report($user_id,'Death');
				$this->operation_report = $this->MJ_hmgt_patient_report($user_id,'Operation');
				$this->all_events_notice = $this->MJ_hmgt_get_all_event_notice_list($user_id,'patient');
				$this->all_own_events_notice = $this->MJ_hmgt_get_all_own_event_notice_list($user_id,'patient');
				$this->diagnosis_report = $this->MJ_hmgt_get_current_patint_diagnosis_report($user_id);
			}
			if($this->role == 'nurse')
			{
				
				$this->patient = $this->MJ_hmgt_get_nurse_side_patient_list();
				$this->events = $this->MJ_hmgt_get_event_notice_list($user_id,'nurse','hmgt_event');
				$this->notice = $this->MJ_hmgt_get_event_notice_list($user_id,'nurse','hmgt_notice');
				$this->birth_report = $this->MJ_hmgt_all_patient_report('Birth');
				$this->death_report = $this->MJ_hmgt_all_patient_report('Death');
				$this->appointment = $this->MJ_hmgt_get_allappointment_list();
				$this->operation_report = $this->MJ_hmgt_all_patient_report('Operation');
				$this->all_events_notice = $this->MJ_hmgt_get_all_event_notice_list($user_id,'nurse');
				$this->all_own_events_notice = $this->MJ_hmgt_get_all_own_event_notice_list($user_id,'nurse');
				$this->prescription = $this->MJ_hmgt_get_pharmacist_prescription();
			}
			if($this->role == 'receptionist')
			{
				
				$this->patient = $this->MJ_hmgt_get_nurse_side_patient_list();
				$this->events = $this->MJ_hmgt_get_event_notice_list($user_id,'receptionist','hmgt_event');
				$this->notice = $this->MJ_hmgt_get_event_notice_list($user_id,'receptionist','hmgt_notice');
				$this->birth_report = $this->MJ_hmgt_all_patient_report('Birth');
				$this->death_report = $this->MJ_hmgt_all_patient_report('Death');
				$this->operation_report = $this->MJ_hmgt_all_patient_report('Operation');
				$this->appointment = $this->MJ_hmgt_get_allappointment_list();
				$this->all_events_notice = $this->MJ_hmgt_get_all_event_notice_list($user_id,'receptionist');
				$this->all_own_events_notice = $this->MJ_hmgt_get_all_own_event_notice_list($user_id,'receptionist');
			}
			if($this->role == 'accountant')
			{
				$this->patient = $this->MJ_hmgt_get_nurse_side_patient_list();
				$this->events = $this->MJ_hmgt_get_event_notice_list($user_id,'accountant','hmgt_event');
				$this->notice = $this->MJ_hmgt_get_event_notice_list($user_id,'accountant','hmgt_notice');
				$this->birth_report = $this->MJ_hmgt_all_patient_report('Birth');
				$this->death_report = $this->MJ_hmgt_all_patient_report('Death');
				$this->operation_report = $this->MJ_hmgt_all_patient_report('Operation');
				$this->all_events_notice = $this->MJ_hmgt_get_all_event_notice_list($user_id,'accountant');
				$this->all_own_events_notice = $this->MJ_hmgt_get_all_own_event_notice_list($user_id,'accountant');
			}
			if($this->role == 'pharmacist')
			{
				$this->patient = $this->MJ_hmgt_get_nurse_side_patient_list();
				$this->events = $this->MJ_hmgt_get_event_notice_list($user_id,'pharmacist','hmgt_event');
				$this->notice = $this->MJ_hmgt_get_event_notice_list($user_id,'pharmacist','hmgt_notice');
				$this->birth_report = $this->MJ_hmgt_all_patient_report('Birth');
				$this->death_report = $this->MJ_hmgt_all_patient_report('Death');
				$this->operation_report = $this->MJ_hmgt_all_patient_report('Operation');
				$this->all_events_notice = $this->MJ_hmgt_get_all_event_notice_list($user_id,'pharmacist');
				$this->all_own_events_notice = $this->MJ_hmgt_get_all_own_event_notice_list($user_id,'pharmacist');
				$this->prescription = $this->MJ_hmgt_get_pharmacist_prescription();
			}
			if($this->role == 'laboratorist')
			{
				$this->patient = $this->MJ_hmgt_get_nurse_side_patient_list();
				$this->events = $this->MJ_hmgt_get_event_notice_list($user_id,'laboratorist','hmgt_event');
				$this->notice = $this->MJ_hmgt_get_event_notice_list($user_id,'laboratorist','hmgt_notice');
				$this->birth_report = $this->MJ_hmgt_all_patient_report('Birth');
				$this->death_report = $this->MJ_hmgt_all_patient_report('Death');
				$this->operation_report = $this->MJ_hmgt_all_patient_report('Operation');
				$this->all_events_notice = $this->MJ_hmgt_get_all_event_notice_list($user_id,'laboratorist');
				$this->all_own_events_notice = $this->MJ_hmgt_get_all_own_event_notice_list($user_id,'laboratorist');
			}
			
			
		}
	}
	//get current user role
	private function MJ_hmgt_get_current_user_role () {
		global $current_user;
		$user_roles = $current_user->roles;
		$user_role = array_shift($user_roles);
		return $user_role;
	}
	//get ptient list
	public function MJ_hmgt_get_patient_list($user_id)
	{		
		global $wpdb;
		$table_inpatient_guardian = $wpdb->prefix."hmgt_inpatient_guardian";
		$table_users = $wpdb->prefix."users";
		$table_usermeta = $wpdb->prefix."usermeta";
		
		$sql="SELECT u.* FROM $table_inpatient_guardian as gr,$table_users as u, $table_usermeta as um WHERE gr.doctor_id = $user_id AND gr.patient_id = u.id AND um.user_id= u.id AND um.meta_key= 'patient_type' AND um.meta_value= 'inpatient'";
		
		$patient=$wpdb->get_results($sql);
		return $patient;
	}
	//Patient see him/her own appointment
	public function MJ_hmgt_get_patient_appointment_list($user_id)
	{
		global $wpdb;
		$table_appointment = $wpdb->prefix. 'hmgt_appointment';	
		$table_users = $wpdb->prefix."users";
		$sql="SELECT * FROM $table_appointment as apnmt,$table_users as u  WHERE apnmt.patient_id = $user_id AND apnmt.patient_id = u.id ";
		$appointment=$wpdb->get_results($sql);
		return $appointment;
	}
	//Doctore appointment
	public function MJ_hmgt_get_appointment_list($user_id)
	{
		global $wpdb;
		$table_appointment = $wpdb->prefix. 'hmgt_appointment';
		$table_users = $wpdb->prefix."users";
		$sql="SELECT * FROM $table_appointment as apnmt,$table_users as u  WHERE apnmt.doctor_id = $user_id AND apnmt.patient_id = u.id ";
		$appointment=$wpdb->get_results($sql);
		return $appointment;
	}
	//Receptionist appointment
	public function MJ_hmgt_get_allappointment_list()
	{
		global $wpdb;
		$table_appointment = $wpdb->prefix. 'hmgt_appointment';
		$table_users = $wpdb->prefix."users";
		$sql="SELECT * FROM $table_appointment as apnmt,$table_users as u  WHERE apnmt.patient_id = u.id ";
		$appointment=$wpdb->get_results($sql);
		return $appointment;
	}
	//Display on dashboard
	public function MJ_hmgt_get_event_notice_list($user_id,$role,$type)
	{
		global $wpdb;
		$table_posts = $wpdb->prefix. 'posts';
		$table_postmeta = $wpdb->prefix."postmeta";
		$sql="SELECT * FROM $table_posts as post,$table_postmeta as pm  WHERE post.id = pm.post_id AND post.post_type = '$type' AND ((pm.meta_key = 'notice_for' AND pm.meta_value = '$role') OR (pm.meta_key = 'notice_for' AND pm.meta_value = 'all')) ";
		$evtn_notice=$wpdb->get_results($sql);
		return $evtn_notice;
	}
	//Display on event tab
	public function MJ_hmgt_get_all_event_notice_list($user_id,$role)
	{
		global $wpdb;
		$table_posts = $wpdb->prefix. 'posts';
		$table_postmeta = $wpdb->prefix."postmeta";		
		$sql="SELECT * FROM $table_posts as post,$table_postmeta as pm  WHERE post.id = pm.post_id AND (post.post_type = 'hmgt_event' || post.post_type = 'hmgt_notice') AND ((pm.meta_key = 'notice_for' AND (FIND_IN_SET('$role',pm.meta_value))))";
		$evtn_notice=$wpdb->get_results($sql);
		return $evtn_notice;
	}
	//own evtn_notice data
	public function MJ_hmgt_get_all_own_event_notice_list($user_id,$role)
	{
		global $wpdb;
		$table_posts = $wpdb->prefix. 'posts';
		$table_postmeta = $wpdb->prefix."postmeta";
		
		$sql="SELECT * FROM $table_posts as post,$table_postmeta as pm  WHERE post.id = pm.post_id AND (post.post_type = 'hmgt_event' || post.post_type = 'hmgt_notice') AND ((pm.meta_key = 'notice_for' AND (FIND_IN_SET('$role',pm.meta_value))) OR (pm.meta_key = 'created_by' AND pm.meta_value = '$user_id'))";
		$evtn_notice=$wpdb->get_results($sql);
		return $evtn_notice;
	}
	//current patient report
	public function MJ_hmgt_get_current_patint_diagnosis_report($user_id)
	{
		global $wpdb;
		$table_diagnosis = $wpdb->prefix. 'hmgt_diagnosis';
		
		$result = $wpdb->get_results("SELECT * FROM $table_diagnosis where patient_id = $user_id");
		return $result;
	}
	//get ambulance list
	public function MJ_hmgt_get_ambulance_list($user_id)
	{
		global $wpdb;
		$table_posts = $wpdb->prefix. 'posts';
		$table_postmeta = $wpdb->prefix."postmeta";
		$sql="SELECT * FROM $table_posts as post,$table_postmeta as pm  WHERE post.id = pm.post_id AND post.post_type = '$type' AND ((pm.meta_key = 'notice_for' AND pm.meta_value = '$role') OR (pm.meta_key = 'notice_for' AND pm.meta_value = 'all')) ";
		$evtn_notice=$wpdb->get_results($sql);
		return $evtn_notice;
	}
	//get single patinet data
	public function MJ_hmgt_get_single_patient($user_id)
	{
		global $wpdb;
		$table_inpatient_guardian = $wpdb->prefix."hmgt_inpatient_guardian";
		$table_users = $wpdb->prefix."users";
		$sql="SELECT u.* FROM $table_inpatient_guardian as gr,$table_users as u WHERE u.ID = $user_id AND gr.patient_id = $user_id";
		$patient=$wpdb->get_results($sql);
		return $patient;
	}
	//get nurse side patient list
	public function MJ_hmgt_get_nurse_side_patient_list()
	{
		global $wpdb;
		$table_inpatient_guardian = $wpdb->prefix."hmgt_inpatient_guardian";
		$table_users = $wpdb->prefix."users";
		$table_user_meta = $wpdb->prefix."usermeta";
		$sql="SELECT u.* FROM $table_users as u,$table_user_meta as um WHERE u.id = um.user_id AND (um.meta_key = 'patient_type' AND um.meta_value = 'inpatient')";
		$patient=$wpdb->get_results($sql);
		return $patient;
	}
	//get nurse notes
	public function MJ_hmgt_get_nurse_notes($patient_id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix."postmeta";
		$sql="SELECT post_id FROM $table_name  WHERE (meta_key = 'patient_id' AND meta_value = '$patient_id')";
		$patient=$wpdb->get_results($sql);
		return $patient;
	}
	//add nurse note
	public function MJ_hmgt_add_nurse_note($data)
	{
		global $wpdb;
		$post_id = wp_insert_post( array(
						'post_status' => 'publish',
						'post_type' => $data['note_by'].'_notes',
						'post_title' => esc_html__('patient_note','hospital_mgt'),
						'post_content' => $data['note']) );
			
		 $result=add_post_meta($post_id,'patient_id',$data['patient_id']);
		return $post_id;
	}
	
	//patinet Report
	public function MJ_hmgt_patient_report($user_id,$type)
	{
		global $wpdb;
		$table_hmgt_report = $wpdb->prefix. 'hmgt_report';
		$result = $wpdb->get_results("SELECT * FROM $table_hmgt_report where patient_id = $user_id AND report_type = '$type'");
		return $result;
	}
	//get patient report
	public function MJ_hmgt_all_patient_report($type)
	{
		global $wpdb;
		$table_hmgt_report = $wpdb->prefix. 'hmgt_report';
		$result = $wpdb->get_results("SELECT * FROM $table_hmgt_report where report_type = '$type'");
		return $result;
	}
	//delete nurse note
	public function MJ_hmgt_delete_nurse_note($note_id)
	{
		$result=wp_delete_post($note_id);
		return $result;
	}
	
	//get Doctor Prescription
	public function MJ_hmgt_get_doctor_prescription($user_id)
	{
		global $wpdb;
		$table_prescription = $wpdb->prefix. 'hmgt_priscription';
		$result = $wpdb->get_results("SELECT * FROM $table_prescription WHERE prescription_by = $user_id");
		return $result;
	} 
	
	//get Pharmacist prescription
	public function MJ_hmgt_get_pharmacist_prescription()
	{
		global $wpdb;
		$table_prescription = $wpdb->prefix. 'hmgt_priscription';
		$result = $wpdb->get_results("SELECT * FROM $table_prescription");
		return $result;
	}
}
?>