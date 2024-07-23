<?php
class MJ_hmgt_Instrumentmanage
{		
	//add instrument data //
	public function MJ_hmgt_hmgt_add_instrument($data)
	{		
		global $wpdb;		
		$table_intrument = $wpdb->prefix .'hmgt_instrument';
		$instrumentdata['instrument_code'] = $data['instrument_code'];
		$instrumentdata['instrument_name'] = MJ_hmgt_strip_tags_and_stripslashes($data['instrument_name']);
		$instrumentdata['charge_type'] = $data['charge_type'];
		$instrumentdata['instrument_charge'] = $data['instrument_charge'];
		$instrumentdata['instrument_description'] = MJ_hmgt_strip_tags_and_stripslashes($data['instrument_description']);
		$instrumentdata['code'] =MJ_hmgt_strip_tags_and_stripslashes($data['code']);
		$instrumentdata['name'] = MJ_hmgt_strip_tags_and_stripslashes($data['name']);
		$instrumentdata['address'] = MJ_hmgt_strip_tags_and_stripslashes($data['address']);
		$instrumentdata['contact'] = MJ_hmgt_strip_tags_and_stripslashes($data['contact']);
		$instrumentdata['description'] = MJ_hmgt_strip_tags_and_stripslashes($data['description']);
		$instrumentdata['quantity'] = $data['quantity'];
		$instrumentdata['price'] = $data['price'];
		$instrumentdata['class'] = MJ_hmgt_strip_tags_and_stripslashes($data['class']);
		$instrumentdata['serial'] = MJ_hmgt_strip_tags_and_stripslashes($data['serial']);
		$instrumentdata['acquire'] = MJ_hmgt_strip_tags_and_stripslashes($data['acquire']);
		$instrumentdata['asset_id'] = $data['asset_id'];
		
		$instrumentdata['created_at'] = date("Y-m-d");
		$instrumentdata['created_by'] =get_current_user_id();
		if(!empty($data['tax']))
		{		
			$instrumentdata['tax']=implode(",",$data['tax']);
		}
		else
		{
			$instrumentdata['tax']=null;
		}
		
		if(isset($data['action']) && $data['action']=="edit")
		{
			$whereid['id']= $data['instrument_id'];
			$result = $wpdb->update($table_intrument,$instrumentdata,$whereid);
		}
		else
		{
			$result = $wpdb->insert($table_intrument,$instrumentdata);			
		}
		return $result;
	}
	//get all instrumnet data
	public function MJ_hmgt_get_all_instrument()
	{
		global $wpdb;		
		$table_intrument = $wpdb->prefix .'hmgt_instrument';
		$result = $wpdb->get_results("SELECT * FROM $table_intrument");
		return $result;		
	}
	//get all instrumnet data created by
	public function MJ_hmgt_get_all_instrument_created_by()
	{
		global $wpdb;		
		$table_intrument = $wpdb->prefix .'hmgt_instrument';
		$current_user_id=get_current_user_id();
		$result = $wpdb->get_results("SELECT * FROM $table_intrument where created_by=$current_user_id");
		return $result;
		
	}
	//get all instrumnet data created by
	public function MJ_hmgt_get_all_instrument_by_created_by()
	{
		global $wpdb;		
		$table_intrument = $wpdb->prefix .'hmgt_instrument';
		$current_user_id=get_current_user_id();
		$result = $wpdb->get_results("SELECT * FROM $table_intrument where created_by=$current_user_id");
		return $result;
		
	}
	//delete  instrument data
	public function MJ_hmgt_delete_instrument($instrument_id)
	{
		global $wpdb;		
		$table_intrument = $wpdb->prefix .'hmgt_instrument';
		$result = $wpdb->query("DELETE FROM $table_intrument where id= ".$instrument_id);		
		return $result;
	}
	//get single instrument data
	public function MJ_hmgt_get_single_instrument($instrument_id)
	{
		global $wpdb;		
		$table_intrument = $wpdb->prefix .'hmgt_instrument';
		
		$result = $wpdb->get_row("SELECT * FROM $table_intrument where id= ".$instrument_id);
		return $result;
	}
	//get assined instrument data
	public function MJ_hmgt_assign_instrument($data)
	{		
		global $wpdb;	
		$units = 0;
		$charges_amount=0;
		$units_value=0;
		$table_hmgt_patient_transation = $wpdb->prefix. 'hmgt_patient_transation';		
		$table_hmgt_assign_instrument = $wpdb->prefix .'hmgt_assign_instrument';
		
		$assigninstrumentdata['patient_id'] = $data['patient_id'];
		$assigninstrumentdata['instrument_id'] = $data['instrument_id'];
		$assigninstrumentdata['charge_type'] = $data['charge_type'];
		if(isset($data['start_date']))
			$assigninstrumentdata['start_date']=MJ_hmgt_get_format_for_db($data['start_date']);
		if(isset($data['end_date']))
			$assigninstrumentdata['end_date'] = MJ_hmgt_get_format_for_db($data['end_date']);
		if(isset($data['start_time']))
			$assigninstrumentdata['start_time'] = $data['start_time'];
		if(isset($data['end_time']))
			$assigninstrumentdata['end_time'] = $data['end_time'];
		
		$assigninstrumentdata['description'] = MJ_hmgt_strip_tags_and_stripslashes($data['description']);
		$assigninstrumentdata['created_at'] = date("Y-m-d");
		$assigninstrumentdata['created_by'] =get_current_user_id();
		if(isset($data['end_date']))
		{
			$units=MJ_hmgt_get_day_between_date($assigninstrumentdata['start_date'],$assigninstrumentdata['end_date']);
			$units_value = $units.' Days';
		}
		elseif(isset($data['start_time']) && isset($data['end_time']))
		{
			$start_time=date("Y-m-d H:i:s", strtotime($data['start_time']));
			$end_time=date("Y-m-d H:i:s", strtotime($data['end_time']));
			$seconds  = strtotime($end_time) - strtotime($start_time);
			$hours = floor($seconds / 3600);
			$units = $hours;
			$units_value = $units.' Hours';
		}
		$charges_amount=MJ_hmgt_get_instrument_total_amount($units,$data['instrument_id']); 
		$assigninstrumentdata['charges_amount'] = $charges_amount;
		
		$instument_data=$this->MJ_hmgt_get_single_instrument($data['instrument_id']);
		$tax=$instument_data->tax;
			
		$tax_array=explode(',',$tax);		
		
		if(!empty($tax_array))
		{
			$total_tax=0;
			foreach($tax_array as $tax_id)
			{
				$tax_percentage=MJ_hmgt_tax_percentage_by_tax_id($tax_id);
				$tax_amount=$charges_amount * $tax_percentage / 100;
				
				$total_tax=$total_tax + $tax_amount;				
			}
			
			$total_charge_amount=$charges_amount+$total_tax;
		}
		else
		{
			$total_tax=0;
			$total_charge_amount=$charges_amount;
		}	
		
		$assigninstrumentdata['total_tax'] = $total_tax;
		$assigninstrumentdata['total_charge_amount'] = $total_charge_amount;
		
		if(isset($data['action']) && $data['action']=="edit")
		{
			$whereid['id']= $data['assign_instrument_id'];
			$result = $wpdb->update($table_hmgt_assign_instrument,$assigninstrumentdata,$whereid);
			
			$trasationdata['type'] ="Instrument Charges"; 
			$trasationdata['type_id'] = $data['instrument_id']; 
			$trasationdata['status'] ="Unpaid"; 
			$trasationdata['patient_id'] =$data['patient_id']; 
			$trasationdata['date'] =date("Y-m-d"); 
			$trasationdata['unit'] = $units_value; 
			$trasationdata['type_value'] = $charges_amount;
			$trasationdata['type_tax'] = $total_tax;
			$trasationdata['type_total_value'] = $total_charge_amount;
			$trasationdata['refer_id'] = $data['assign_instrument_id'];
			$refer_id['refer_id'] = $data['assign_instrument_id'];
			
			$wpdb->update( $table_hmgt_patient_transation, $trasationdata,$refer_id);
		}
		else
		{
			$result = $wpdb->insert($table_hmgt_assign_instrument,$assigninstrumentdata);
			$lastid = $wpdb->insert_id;
			//for  patient bed trasation table....			
			$trasationdata['type'] ="Instrument Charges"; 
			$trasationdata['type_id'] = $data['instrument_id']; 
			$trasationdata['status'] ="Unpaid"; 
			$trasationdata['patient_id'] =$data['patient_id']; 
			$trasationdata['date'] =date("Y-m-d"); 
			$trasationdata['unit'] = $units_value; 			
			$trasationdata['type_value'] = $charges_amount;
			$trasationdata['type_tax'] = $total_tax;
			$trasationdata['type_total_value'] = $total_charge_amount;
			$trasationdata['refer_id'] = $lastid;
						
			$wpdb->insert( $table_hmgt_patient_transation, $trasationdata );
		}
		return $result;
	}
	//get all assigned instrument
	public function MJ_hmgt_get_all_assigned_instrument()
	{
		global $wpdb;		
		$table_assign_instrument = $wpdb->prefix .'hmgt_assign_instrument';
		$result = $wpdb->get_results("SELECT * FROM $table_assign_instrument");
		return $result;
	}
	//get all assigned instrument by created by
	public function MJ_hmgt_get_all_assigned_instrument_by_created_by()
	{
		global $wpdb;		
		$table_assign_instrument = $wpdb->prefix .'hmgt_assign_instrument';
		$current_user_id=get_current_user_id();
		$result = $wpdb->get_results("SELECT * FROM $table_assign_instrument where created_by=$current_user_id");
		return $result;
	}
	//get doctor created all assigned instrument list
	public function MJ_hmgt_get_doctor_all_assigned_instrument_by_created_by()
	{
		global $wpdb;		
		$table_assign_instrument = $wpdb->prefix .'hmgt_assign_instrument';
		$current_user_id=get_current_user_id();
		$array=MJ_hmgt_doctor_patientid_list(); 
		$array = implode("','",$array);
		$result = $wpdb->get_results("SELECT * FROM $table_assign_instrument where created_by=$current_user_id OR patient_id IN ('".$array."')");
		return $result;
	}
	//get nurse created all assigned instrument list
	public function MJ_hmgt_get_nurse_all_assigned_instrument_by_created_by()
	{
		global $wpdb;		
		$table_assign_instrument = $wpdb->prefix .'hmgt_assign_instrument';
		$current_user_id=get_current_user_id();
		$array=MJ_hmgt_nurse_patientid_list(); 
		
		$array = implode("','",$array);
		$result = $wpdb->get_results("SELECT * FROM $table_assign_instrument where created_by=$current_user_id OR patient_id IN ('".$array."')");
		return $result;
	}
	//get patient all assigned instrument list
	public function MJ_hmgt_get_all_assigned_instrument_by_patient()
	{
		global $wpdb;		
		$table_assign_instrument = $wpdb->prefix .'hmgt_assign_instrument';
		$current_user_id=get_current_user_id();
		$result = $wpdb->get_results("SELECT * FROM $table_assign_instrument where patient_id=$current_user_id");
		return $result;
	}
	//get single assigned instrument data
	public function MJ_hmgt_get_single_assigned_instrument($assigned_instrument_id)
	{
		global $wpdb;		
		$table_assign_instrument = $wpdb->prefix .'hmgt_assign_instrument';
		$result = $wpdb->get_row("SELECT * FROM $table_assign_instrument where id= ".$assigned_instrument_id);
		return $result;
	}
	public function MJ_hmgt_get_single_assigned_instrument_by_patient_id($patient_id)
	{
		global $wpdb;		
		$table_assign_instrument = $wpdb->prefix .'hmgt_assign_instrument';
		$result = $wpdb->get_row("SELECT * FROM $table_assign_instrument where patient_id= ".$patient_id);
		return $result;
	}
	//delete assigned instrument data
	public function MJ_hmgt_delete_assigned_instrument($assign_instrument_id)
	{
		global $wpdb;		
		$table_assign_instrument = $wpdb->prefix .'hmgt_assign_instrument';
		$result = $wpdb->query("DELETE FROM $table_assign_instrument where id= ".$assign_instrument_id);		
		return $result;
	}
}
?>