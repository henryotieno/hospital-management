<?php
class MJ_hmgt_report
{	
	//Add report Category
	public function MJ_hmgt_add_report($data)
	{
		global $wpdb;
		$table_hmgt_report = $wpdb->prefix. 'hmgt_report';
		//-------usersmeta table data--------------
		$reportdata['patient_id']=$data['patient_id'];
		$reportdata['report_type']=$data['report_type'];
		$reportdata['report_description']=MJ_hmgt_strip_tags_and_stripslashes($data['report_description']);
		$reportdata['report_date']=$data['report_date'];
		$reportdata['created_date']=date("Y-m-d");
		$reportdata['createdby']=get_current_user_id();
		
		
		if($data['action']=='edit')	
		{
			$reportdataid['rid']=$data['rid'];			
			$result=$wpdb->update( $table_hmgt_report, $reportdata ,$reportdataid);
			MJ_hmgt_append_audit_log(''.esc_html__('Add new report ','hospital_mgt').'',get_current_user_id());
			return $result;
		}
		else
		{
			$result=$wpdb->insert( $table_hmgt_report, $reportdata );			
			return $result;			
		}
		
	}
	//get all report data
	public function MJ_hmgt_get_all_report()
	{
		global $wpdb;
		$table_hmgt_report = $wpdb->prefix. 'hmgt_report';
		
		$result = $wpdb->get_results("SELECT * FROM $table_hmgt_report");
		return $result;
		
	}
	//get report by report type
	public function MJ_hmgt_get_report_byreport_type($type)
	{
		global $wpdb;
		$table_hmgt_report = $wpdb->prefix. 'hmgt_report';
	
		$result = $wpdb->get_results("SELECT * FROM $table_hmgt_report WHERE report_type = '$type'");
		return $result;
	
	}
	//get single report data
	public function MJ_hmgt_get_single_report($rid)
	{
		global $wpdb;
		$table_hmgt_report = $wpdb->prefix. 'hmgt_report';
		$result = $wpdb->get_row("SELECT * FROM $table_hmgt_report where rid= ".$rid);
		return $result;
	}
	//delete report data
	public function MJ_hmgt_delete_report($rid)
	{
		global $wpdb;
		$table_hmgt_report = $wpdb->prefix. 'hmgt_report';
		$result = $wpdb->query("DELETE FROM $table_hmgt_report where rid= ".$rid);
		MJ_hmgt_append_audit_log(''.esc_html__('Delete report ','hospital_mgt').'',get_current_user_id());
		return $result;
	}
	
}
?>