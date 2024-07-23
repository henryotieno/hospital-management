<?php
class MJ_hmgt_medicine
{	
	//add Medicine Category
	public function MJ_hmgt_add_medicinecategory($data)
	{
		global $wpdb;
		$result = wp_insert_post( array(
						'post_status' => 'publish',
						'post_type' => 'medicine_category',
						'post_title' => $data['category_name']) );
		MJ_hmgt_append_audit_log(''.esc_html__('Add new medicine category ','hospital_mgt').'',get_current_user_id());
			return $result;			
	}
	//get all medicine category
	public function MJ_hmgt_get_all_category()
	{
		$args= array('post_type'=> 'medicine_category','posts_per_page'=>-1,'orderby'=>'post_title','order'=>'Asc');
					$result = get_posts( $args );
		
		return $result;
		
	}
	//get medicine category name
	public function MJ_hmgt_get_medicine_categoryname($cat_id)
	{
		$result = get_post( $cat_id );		
		if(!empty($result))	
		return $result->post_title;
		else 
			return "";
	}
	//get single medicine category data
	public function MJ_hmgt_get_single_category($cat_id)
	{
		global $wpdb;
		$table_medicine_category = $wpdb->prefix. 'hmgt_medicine_category';
		$result = $wpdb->get_row("SELECT * FROM $table_medicine_category where med_cat_id= ".$cat_id);
		return $result;
	}
	//delete medicine category
	public function MJ_hmgt_delete_medicine_category($cat_id)
	{
		$result=wp_delete_post($cat_id);
		MJ_hmgt_append_audit_log(''.esc_html__('Delete medicine category ','hospital_mgt').'',get_current_user_id());		
		return $result;
	}
	
	//Add Medicine  data
	public function MJ_hmgt_add_medicine($data)
	{			
		global $wpdb;
		$table_medicine_category = $wpdb->prefix. 'hmgt_medicine';		
		
		if($data['action']=='edit')
		{
			
			$medicinedata['medicine_name']=MJ_hmgt_strip_tags_and_stripslashes($data['medicine_name']);
			$medicinedata['med_cat_id']=$data['medicine_category'];
			$medicinedata['medicine_price']=$data['med_price'];
			$medicinedata['batch_number']=MJ_hmgt_strip_tags_and_stripslashes($data['batch_number']);
			$medicinedata['med_quantity']=$data['med_quantity'];
			$medicinedata['med_uniqueid']=$data['med_uniqueid'];
			if(!empty($data['med_tax']))
			{
				$medicinedata['med_tax']=implode(",",$data['med_tax']);
			}
			$medicinedata['medicine_menufacture']=MJ_hmgt_strip_tags_and_stripslashes($data['mfg_cmp_name']);
			$medicinedata['manufactured_date']=MJ_hmgt_get_format_for_db($data['manufactured_date']);
			$medicinedata['medicine_expiry_date']=MJ_hmgt_get_format_for_db($data['expiry_date']);
			$medicinedata['medicine_description']=MJ_hmgt_strip_tags_and_stripslashes($data['description']);
			$medicinedata['note']=MJ_hmgt_strip_tags_and_stripslashes($data['note']);
			$medicinedata['med_discount']=$data['med_discount'];
			$medicinedata['med_discount_in']=$data['med_discount_in'];
			
			if($data['med_quantity']>0)
			{
				$medicine_stock='In';
			}
			else
			{
				$medicine_stock='Out';
			}
			
			$medicinedata['medicine_stock']=$medicine_stock;
			
			$medicinid['medicine_id']=$data['medicine_id'];
			$result=$wpdb->update( $table_medicine_category, $medicinedata ,$medicinid);
			
			MJ_hmgt_append_audit_log(''.esc_html__('Update medicine ','hospital_mgt').'',get_current_user_id());
			return $result;
		}
		else
		{		
				if(!empty($data['medicine_name']))
				{
					foreach ($data['medicine_name'] as $key=>$value)
					{						
						$medicinedata['medicine_name']=MJ_hmgt_strip_tags_and_stripslashes($value);
						$medicinedata['med_cat_id']=$data['medicine_category'];
						$medicinedata['medicine_price']=$data['med_price'][$key];
						$medicinedata['batch_number']=MJ_hmgt_strip_tags_and_stripslashes($data['batch_number'][$key]);
						$medicinedata['med_quantity']=$data['med_quantity'][$key];
						$medicinedata['med_uniqueid']=$data['med_uniqueid'][$key];
						if(!empty($data['med_tax']))
						{
							$medicinedata['med_tax']=implode(",",$data['med_tax'][$key]);
						}
						$medicinedata['medicine_menufacture']=MJ_hmgt_strip_tags_and_stripslashes($data['mfg_cmp_name'][$key]);
						$medicinedata['manufactured_date']=MJ_hmgt_get_format_for_db($data['manufactured_date'][$key]);
						$medicinedata['medicine_expiry_date']=MJ_hmgt_get_format_for_db($data['expiry_date'][$key]);
						$medicinedata['medicine_description']=MJ_hmgt_strip_tags_and_stripslashes($data['description'][$key]);
						$medicinedata['note']=MJ_hmgt_strip_tags_and_stripslashes($data['note'][$key]);
						$medicinedata['med_discount']=$data['med_discount'][$key];
						$medicinedata['med_discount_in']=$data['med_discount_in'][$key];
						
						if($data['med_quantity']>0)
						{
							$medicine_stock='In';
						}
						else
						{
							$medicine_stock='Out';
						}	
						$medicinedata['medicine_stock']=$medicine_stock;
						
						$medicinedata['med_create_date']=date("Y-m-d");
						$medicinedata['med_create_by']=get_current_user_id(); 
						
						$result=$wpdb->insert( $table_medicine_category, $medicinedata );
					}
					MJ_hmgt_append_audit_log(''.esc_html__('Add new medicine ','hospital_mgt').'',get_current_user_id());
				}					
			return $result;
		}
	}
	
	//Add Dispath  Medicine data
	public function MJ_hmgt_add_dispatch_medicine($data)
	{		
		global $wpdb;
		$table_dispatch_medicine = $wpdb->prefix. 'hmgt_dispatch_medicine';	
		$table_medicine_category = $wpdb->prefix. 'hmgt_medicine';		
		$hmgt_patient_transation = $wpdb->prefix. 'hmgt_patient_transation';
		
		$madicine_title =$data['madicine_title'];
		$qty =$data['qty'];
		$price =$data['price'];	
		$madicine_id= $data['madicine_id'];
		$discount_amount= $data['discount_amount'];
		$tax_amount= $data['tax_amount'];		
		$discount_value= $data['discount_value'];
		$med_discount_in= $data['med_discount_in'];
		
		if(!empty($madicine_title))
		{			
			foreach($madicine_title as $key=>$value)
			{
				$madicine['name']=$value;
				$madicine['qty']=$qty[$key];
				$madicine['price']=$price[$key];
				$madicine['discount_amount']=$discount_amount[$key];
				$madicine['tax_amount']=$tax_amount[$key];
				$madicine['madicine_id']=$madicine_id[$key];
				$madicine['discount_value']=$discount_value[$key];
				$madicine['med_discount_in']=$med_discount_in[$key];
				
				$medicine_result[] = $madicine;
				
				//stock update
				$singgle_madicine =$this->MJ_hmgt_get_single_medicine($madicine_id[$key]);
				
				$medicinid['medicine_id']=$madicine_id[$key];
				if($data['action']=='edit')
				{	
					$old_quantity= $data['old_quantity'];
					$oldquantity=$old_quantity[$key];
					
					$medicinedata['med_quantity']=$singgle_madicine->med_quantity+$oldquantity-$qty[$key];
				}
				else	
				{				
					$medicinedata['med_quantity']=$singgle_madicine->med_quantity-$qty[$key];
				}	
				
				if($medicinedata['med_quantity']>0)
				{
					$medicine_stock='In';
				}
				else
				{
					$medicine_stock='Out';			
				}	
				
				$medicinedata['medicine_stock']=$medicine_stock;	
				
				$result=$wpdb->update( $table_medicine_category, $medicinedata ,$medicinid);
			}
		}
		$medicine = json_encode($medicine_result);		
		
		$dispatchmedicinedata['madicine']=$medicine;
		$dispatchmedicinedata['patient']=$data['patient'];
		$dispatchmedicinedata['prescription_id']=$data['prescription_id'];
		$dispatchmedicinedata['med_price']=$data['med_price'];
		$dispatchmedicinedata['total_tax_amount']=$data['total_tax_amount'];				
		$dispatchmedicinedata['sub_total']=$data['sub_total'];
		$dispatchmedicinedata['discount']=$data['discount'];
		
		$dispatchmedicinedata['description']=MJ_hmgt_strip_tags_and_stripslashes($data['description']);
		
		$dispatchmedicinedata['med_create_date']=date("Y-m-d");
		$dispatchmedicinedata['med_create_by']=get_current_user_id();			
		
		//invoice patient transaction entry
		$feesdata['patient_id'] = $data['patient'];
		$feesdata['type_id'] = "Medicine";					
		$feesdata['type_value'] = $data['med_price'];
		$feesdata['type_discount'] =$data['discount'];
		$feesdata['type_tax'] =$data['total_tax_amount'];
		$feesdata['type_total_value'] =$data['sub_total'];
		$feesdata['status'] ="Unpaid";
		$feesdata['type'] ="Medicine Charges";
		$feesdata['date'] =date("Y-m-d");
		
		if($data['action']=='edit')
		{
			$dispatchmedicinid['id']=$data['dispatch_id'];
			$result=$wpdb->update( $table_dispatch_medicine, $dispatchmedicinedata ,$dispatchmedicinid);
			MJ_hmgt_append_audit_log(''.esc_html__('Update Despatch  medicine ','hospital_mgt').'',get_current_user_id());
			
			//delete patient transaction
			$delete_ambulance_charge = $wpdb->query("DELETE FROM $hmgt_patient_transation where type='Medicine Charges' AND refer_id= ".$data['dispatch_id']);
			
			//insert patient transaction
			$feesdata['refer_id'] = $data['dispatch_id'];		
			$wpdb->insert($hmgt_patient_transation,$feesdata);
			return $result;
		}
		else
		{
			$result=$wpdb->insert( $table_dispatch_medicine, $dispatchmedicinedata );
			$pre_id = $wpdb->insert_id;
			
			MJ_hmgt_append_audit_log(''.esc_html__('Add new Dispath medicine ','hospital_mgt').'',get_current_user_id());
			
			//insert patient transaction
			$feesdata['refer_id'] = $pre_id;
			$invoice = $wpdb->insert($hmgt_patient_transation,$feesdata);
			return $result;
		}
	
	}
	//get all medicine data
	public function MJ_hmgt_get_all_medicine()
	{
		global $wpdb;
		$table_medicine_category = $wpdb->prefix. 'hmgt_medicine';
	
		$result = $wpdb->get_results("SELECT * FROM $table_medicine_category");
		return $result;
	
	}
	//get all medicine by created user data
	public function MJ_hmgt_get_all_medicine_by_created_user()
	{
		global $wpdb;
		$table_medicine_category = $wpdb->prefix. 'hmgt_medicine';
		$current_user_id=get_current_user_id();
		$result = $wpdb->get_results("SELECT * FROM $table_medicine_category where med_create_by=$current_user_id");
		return $result;
	
	}
	//get all medicine in stock data
	public function MJ_hmgt_get_all_medicine_in_stock()
	{
		global $wpdb;
		$table_medicine_category = $wpdb->prefix. 'hmgt_medicine';
	
		$result = $wpdb->get_results("SELECT * FROM $table_medicine_category where medicine_stock='in'");
		return $result;
	
	}
	//get all dispatch medicine data
	public function MJ_hmgt_get_all_dispatch_medicine()
	{
		global $wpdb;
		$table_dispatch_medicine = $wpdb->prefix. 'hmgt_dispatch_medicine';
	
		$result = $wpdb->get_results("SELECT * FROM $table_dispatch_medicine");
		return $result;
	
	}	
	//get all dispatch medicne by created by data
	public function MJ_hmgt_get_all_dispatch_medicine_by_created_by()
	{
		global $wpdb;
		$table_dispatch_medicine = $wpdb->prefix. 'hmgt_dispatch_medicine';
		$user_id=get_current_user_id();		
		$result = $wpdb->get_results("SELECT * FROM $table_dispatch_medicine where med_create_by=$user_id");
		return $result;
	
	}
	//get doctor created all dispatch medicine data
	public function MJ_hmgt_get_doctor_all_dispatch_medicine_by_created_by()
	{
		global $wpdb;
		$table_dispatch_medicine = $wpdb->prefix. 'hmgt_dispatch_medicine';
		$user_id=get_current_user_id();		
		$array=MJ_hmgt_doctor_patientid_list(); 
		$array = implode("','",$array);
		$result = $wpdb->get_results("SELECT * FROM $table_dispatch_medicine where med_create_by=$user_id  OR patient IN ('".$array."')");
		return $result;
	
	}
	//get nurse created all dispatch medicine data
	public function MJ_hmgt_get_nurse_all_dispatch_medicine_by_created_by()
	{
		global $wpdb;
		$table_dispatch_medicine = $wpdb->prefix. 'hmgt_dispatch_medicine';
		$user_id=get_current_user_id();		
		$array=MJ_hmgt_nurse_patientid_list(); 
		
		$array = implode("','",$array);
		$result = $wpdb->get_results("SELECT * FROM $table_dispatch_medicine where med_create_by=$user_id  OR patient IN ('".$array."')");
		return $result;
	
	}
	//get patient all dispatch medicine data
	public function MJ_hmgt_get_all_dispatch_medicine_by_patient($user_id)
	{
		global $wpdb;
		$table_dispatch_medicine = $wpdb->prefix. 'hmgt_dispatch_medicine';
	
		$result = $wpdb->get_results("SELECT * FROM $table_dispatch_medicine where patient=$user_id");
		return $result;
	
	}
	//get single medicine data
	public function MJ_hmgt_get_single_medicine($cat_id)
	{
		global $wpdb;
		$table_medicine_category = $wpdb->prefix. 'hmgt_medicine';
		$result = $wpdb->get_row("SELECT * FROM $table_medicine_category where medicine_id= ".$cat_id);
		return $result;
	}
	//get signle dispatch medicine data
	public function MJ_hmgt_get_single_dispatch_medicine($dispatch_med_id)
	{
		global $wpdb;
		$table_dispatch_medicine = $wpdb->prefix. 'hmgt_dispatch_medicine';
		$result = $wpdb->get_row("SELECT * FROM $table_dispatch_medicine where id= ".$dispatch_med_id);
		return $result;
	}
	//delete medicine data
	public function MJ_hmgt_delete_medicine($cat_id)
	{
		global $wpdb;
		$table_medicine_category = $wpdb->prefix. 'hmgt_medicine';
		$result = $wpdb->query("DELETE FROM $table_medicine_category where medicine_id= ".$cat_id);
		MJ_hmgt_append_audit_log(''.esc_html__('Delete medicine ','hospital_mgt').'',get_current_user_id());
		return $result;
	}
	//delete dispatch medicine data
	public function MJ_hmgt_delete_dispatch_medicine($dispatch_id)
	{
			global $wpdb;
		$table_dispatch_medicine = $wpdb->prefix. 'hmgt_dispatch_medicine';
		$result = $wpdb->query("DELETE FROM $table_dispatch_medicine where id= ".$dispatch_id);
		MJ_hmgt_append_audit_log(''.esc_html__('Delete Dispath medicine ','hospital_mgt').'',get_current_user_id());
		return $result;
	}
	//get all medicine in stock data
	public function MJ_hmgt_get_all_medicine_out_stock()
	{
		global $wpdb;
		$table_medicine_category = $wpdb->prefix. 'hmgt_medicine';
	
		$result = $wpdb->get_results("SELECT * FROM $table_medicine_category where medicine_stock='Out'");
		return $result;
	
	}
}
?>