<?php
class MJ_hmgt_invoice_genrate
{		
	//get bad by patient id
	public function MJ_hmgt_get_bad_by_patient_id($patient_id)
	{			
		global $wpdb;
		$table_hms_bad_allowtment = $wpdb->prefix ."hmgt_bed_allotment";
		$sql= "SELECT * FROM $table_hms_bad_allowtment WHERE patient_id=$patient_id";
		$results = $wpdb->get_results($sql);
		return $results;
	}
	//get all bad by patient id
	public function MJ_hmgt_get_all_by_patient_id($patient_id)
	{	
		global $wpdb;
		$table_hmgt_patient_transation = $wpdb->prefix. 'hmgt_patient_transation';
		$sql=  "SELECT * FROM $table_hmgt_patient_transation WHERE patient_id=$patient_id AND status= 'Unpaid'";
		$result = $wpdb->get_results($sql);
		return $result;
	}
	//get all treatment
	public function MJ_hmgt_get_all_treatment()
	{
		global $wpdb;
		$table_treatment = $wpdb->prefix. 'hmgt_treatment';
		
		$result = $wpdb->get_results("SELECT * FROM $table_treatment");
		return $result;
		
	}
	//get treatment name
	public function MJ_hmgt_get_treatment_name($treatment_id)
	{
		global $wpdb;
		$table_treatment = $wpdb->prefix. 'hmgt_treatment';
		
		$result = $wpdb->get_var("SELECT treatment_name FROM $table_treatment where treatment_id= ".$treatment_id);
		return $result;
	}
	//get single treatment
	public function MJ_hmgt_get_single_treatment($treatment_id)
	{
		global $wpdb;
		$table_treatment = $wpdb->prefix. 'hmgt_treatment';
		$result = $wpdb->get_row("SELECT * FROM $table_treatment where treatment_id= ".$treatment_id);
		return $result;
	}
	
	//delete treatment
	public function MJ_hmgt_delete_treatment($treatment_id)
	{
		global $wpdb;
		$table_treatment = $wpdb->prefix. 'hmgt_treatment';
		$result = $wpdb->query("DELETE FROM $table_treatment where treatment_id= ".$treatment_id);
		MJ_hmgt_append_audit_log(''.esc_html__('Delete treatment ','hospital_mgt').'',get_current_user_id());
		return $result;
	}
}
?>