<?php
class MJ_hmgt_treatment
{	

	//Add treatment Category
	public function MJ_hmgt_add_treatment($data)
	{
		global $wpdb;
		$table_treatment = $wpdb->prefix. 'hmgt_treatment';
		//-------usersmeta table data--------------
		$treatmentdata['treatment_name']=MJ_hmgt_strip_tags_and_stripslashes($data['treatment_name']);
		$treatmentdata['treatment_price']=$_POST['treatment_price'];
		$treatmentdata['treat_create_Date']=date("Y-m-d");
		$treatmentdata['treat_create_by']=get_current_user_id();
		if(!empty($data['tax']))
		{		
			$treatmentdata['tax']=implode(",",$data['tax']);
		}
		else
		{
			$treatmentdata['tax']=null;
		}
		
		if($data['action']=='edit')	
		{
			$treatmentid['treatment_id']=$data['treatment_id'];			
			$result=$wpdb->update( $table_treatment, $treatmentdata ,$treatmentid);
			MJ_hmgt_append_audit_log(''.esc_html__('Update treatment ','hospital_mgt').'',get_current_user_id());
			return $result;
		}
		else
		{
			$result=$wpdb->insert( $table_treatment, $treatmentdata );	
			MJ_hmgt_append_audit_log(''.esc_html__('Add new treatment ','hospital_mgt').'',get_current_user_id());
			return $result;			
		}
		
	}
	//get all treatment data
	public function MJ_hmgt_get_all_treatment()
	{
		global $wpdb;
		$table_treatment = $wpdb->prefix. 'hmgt_treatment';
		
		$result = $wpdb->get_results("SELECT * FROM $table_treatment");
		return $result;
		
	}
	//get all treatment by id
	public function MJ_hmgt_get_all_treatment_by_id()
	{
		global $wpdb;
		$table_treatment = $wpdb->prefix. 'hmgt_treatment';
		$user_id=get_current_user_id();
		$result = $wpdb->get_results("SELECT * FROM $table_treatment where treat_create_by=$user_id");
		return $result;
	}
	//get tretment name
	public function MJ_hmgt_get_treatment_name($treatment_id)
	{
		global $wpdb;
		$table_treatment = $wpdb->prefix. 'hmgt_treatment';
		
		$result = $wpdb->get_var("SELECT treatment_name FROM $table_treatment where treatment_id= ".$treatment_id);
		return $result;
	}
	//get single treatment data
	public function MJ_hmgt_get_single_treatment($treatment_id)
	{
		global $wpdb;
		$table_treatment = $wpdb->prefix. 'hmgt_treatment';
		$result = $wpdb->get_row("SELECT * FROM $table_treatment where treatment_id= ".$treatment_id);
		return $result;
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
}
?>