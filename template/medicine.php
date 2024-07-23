<?php
MJ_hmgt_browser_javascript_check(); 
$obj_medicine = new MJ_hmgt_medicine();
//access right
$user_access=MJ_hmgt_get_userrole_wise_access_right_array();

if (isset ( $_REQUEST ['page'] ))
{	
	if($user_access['view']=='0')
	{	
		MJ_hmgt_access_right_page_not_access_message();
		die;
	}
	if(!empty($_REQUEST['action']))
	{
		if (isset ( $_REQUEST ['page'] ) && $_REQUEST ['page'] == $user_access['page_link'] && ($_REQUEST['action']=='edit'))
		{
			if($user_access['edit']=='0')
			{	
				MJ_hmgt_access_right_page_not_access_message();
				die;
			}			
		}
		if (isset ( $_REQUEST ['page'] ) && $_REQUEST ['page'] == $user_access['page_link'] && ($_REQUEST['action']=='delete'))
		{
			if($user_access['delete']=='0')
			{	
				MJ_hmgt_access_right_page_not_access_message();
				die;
			}	
		}
		if (isset ( $_REQUEST ['page'] ) && $_REQUEST ['page'] == $user_access['page_link'] && ($_REQUEST['action']=='insert'))
		{
			if($user_access['add']=='0')
			{	
				MJ_hmgt_access_right_page_not_access_message();
				die;
			}	
		} 
	}
}
//SAVE Medicine CATEGORY
if(isset($_REQUEST['save_category'])){
	$result = $obj_medicine->MJ_hmgt_add_medicinecategory($_POST);
	if($result)	{ ?>
		<div id="message" class="updated below-h2">	<?php 	esc_html_e('Record inserted successfully','hospital_mgt');?></div>
	<?php 	}	
}
//SAVE Medicine DATA
if(isset($_REQUEST['save_medicine']))
{
	$nonce = $_POST['_wpnonce'];
	if (wp_verify_nonce( $nonce, 'save_medicine_nonce' ) )
	{	
		if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'insert' || $_REQUEST['action'] == 'edit')){
			/* var_dump($_POST);
			die; */
			$result = $obj_medicine->MJ_hmgt_add_medicine($_POST);
			if($result)	
			{
				if($_REQUEST['action'] == 'edit')
				{				
					wp_redirect ( home_url() . '?dashboard=user&page=medicine&tab=medicinelist&message=2');
				}
				else 
				{
					wp_redirect ( home_url() . '?dashboard=user&page=medicine&tab=medicinelist&message=1');
				}
			}
		}
	}
}
//SAVE Dispatch Medicine DATA
if(isset($_REQUEST['save_dispatch_medicine']))
{
	$nonce = $_POST['_wpnonce'];
	if (wp_verify_nonce( $nonce, 'save_dispatch_medicine_nonce' ) )
	{	
		if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'insert' || $_REQUEST['action'] == 'edit'))
		{			
			$result = $obj_medicine->MJ_hmgt_add_dispatch_medicine($_POST);
			if($result)
			{
				if($_REQUEST['action'] == 'edit')
				{
					wp_redirect ( home_url().'?dashboard=user&page=medicine&tab=dismedicinelist&message=2'); }
				else 
				{
					wp_redirect ( home_url().'?dashboard=user&page=medicine&tab=dismedicinelist&message=1');
				}
			}
		}
	}
}
	
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete')
{
	if(isset($_REQUEST['medicine_id'])){
		$result = $obj_medicine->MJ_hmgt_delete_medicine(MJ_hmgt_id_decrypt($_REQUEST['medicine_id']));
		if($result)
		{
			wp_redirect ( home_url() . '?dashboard=user&page=medicine&tab=medicinelist&message=3');
		}
	}
	else{
		$result = $obj_medicine->MJ_hmgt_delete_dispatch_medicine(MJ_hmgt_id_decrypt($_REQUEST['dispatch_id']));
		if($result)
		{
			wp_redirect ( home_url() . '?dashboard=user&page=medicine&tab=dismedicinelist&message=3');
		}
	}
} 
if(isset($_REQUEST['message'])){
$message =$_REQUEST['message'];
if($message == 1){ ?>
	<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
		</button><?php esc_html_e('Record inserted successfully','hospital_mgt');?></p></div>
<?php 
}
elseif($message == 2){?>
	<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
		</button><p><?php esc_html_e("Record updated successfully",'hospital_mgt');?></p></div>
<?php }
elseif($message == 3) { ?>
	<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
		</button><p><?php esc_html_e('Record deleted successfully','hospital_mgt');?></div></p>
<?php	}
}
$user_role=MJ_hmgt_get_current_user_role();
if($user_role=='patient')
{		
	$active_tab = isset($_REQUEST['tab'])?$_REQUEST['tab']:'dismedicinelist';
}
else
{
	$active_tab = isset($_REQUEST['tab'])?$_REQUEST['tab']:'medicinelist';
}	
?>
<!-- POP up code -->
<div class="popup-bg">
    <div class="overlay-content">
		<div class="modal-content">
			<div class="category_list"></div>    
		</div>
    </div>     
</div>
<!-- End POP-UP Code -->
<script type="text/javascript">
jQuery(document).ready(function($) 
{
	"use strict";
	function MJ_hmgt_initMultiSelect()
	{
		$('.tax_dropdawn').multiselect(
		{
			nonSelectedText :'<?php esc_html_e('Select Tax','hospital_mgt');?>',
			includeSelectAllOption: true,
		    selectAllText : '<?php esc_html_e('Select all','hospital_mgt'); ?>',
			templates: {
		            button: '<button class="multiselect btn btn-default dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="multiselect-selected-text"></span><b class="caret"></b></button>',
		        },
				buttonContainer: '<div class="dropdown" />'
		});
    }
    $('.demo').on("click",function(){
		MJ_hmgt_initMultiSelect();
	}) 
	jQuery('#medicine_list').DataTable({
		"responsive": true,
		"aoColumns":[
	                  {"bSortable": true},
					  {"bSortable": true},
					  {"bSortable": true},
					  {"bSortable": true},
					  {"bSortable": true},
					  {"bSortable": true},
					  {"bSortable": true},
					  {"bSortable": true},
					  {"bSortable": true}            	                 
					  <?php  
						if($user_access['edit']=='1' || $user_access['delete']=='1')
						{
							?>
						  ,{"bSortable": false}
						 <?php  
						 } 
						 ?> 	
						],
			language:<?php echo MJ_hmgt_datatable_multi_language();?>
		});
	$.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
	  $('#manufactured_date').datepicker({
		endDate: '+0d',
		autoclose: true
   }); 	
   var date = new Date();
   date.setDate(date.getDate()-0);
	$.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
	$('#expiry_date').datepicker({ 
		startDate: date,
		autoclose: true
	});
	$('.tax_charge').multiselect(
	{
		nonSelectedText :'<?php esc_html_e('Select Tax','hospital_mgt');?>',
		includeSelectAllOption: true,
	    selectAllText : '<?php esc_html_e('Select all','hospital_mgt'); ?>',
		templates: {
	            button: '<button class="multiselect btn btn-default dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="multiselect-selected-text"></span><b class="caret"></b></button>',
	        },
			buttonContainer: '<div class="dropdown" />'
	});
} );
</script>

<div class="panel-body panel-white"><!-- START PANEL BODY DIV-->
    <ul class="nav nav-tabs panel_tabs" role="tablist">
    	<?php
		$user_role=MJ_hmgt_get_current_user_role();
		if($user_role!='patient')
		{			
		?>	
			<li class="<?php if($active_tab == 'medicinelist') echo "active";?>">
			  <a href="?dashboard=user&page=medicine&tab=medicinelist">
				 <i class="fa fa-align-justify"></i> <?php esc_html_e('Medicine List', 'hospital_mgt'); ?></a>
			  </a>
		    </li>	
		<?php if(isset($_REQUEST['action']) && $_REQUEST['action'] =='edit' && isset($_REQUEST['medicine_id'])) { ?> 
			<li class="<?php if($active_tab == 'addmedicine') echo "active";?>">
				<a href="?dashboard=user&page=medicine&tab=addmedicine&action=edit&medicine_id=<?php echo $_REQUEST['medicine_id'];?>">
					<i class="fa fa-plus-circle"></i> <?php esc_html_e('Edit Medicine', 'hospital_mgt'); ?>
				</a> 
			</li>
		<?php } 
			else
			{
				if($user_access['add']=='1')
				{
				?> 		
					<li class="<?php if($active_tab == 'addmedicine') echo "active";?>">
						<a href="?dashboard=user&page=medicine&tab=addmedicine&&action=insert">
							<i class="fa fa-plus-circle"></i> <?php esc_html_e('Add Medicine', 'hospital_mgt'); ?>
						</a> 
					</li>
	       <?php }
		    }
	    }
	?>
	<li class="<?php if($active_tab == 'dismedicinelist') echo "active";?>">
          <a href="?dashboard=user&page=medicine&tab=dismedicinelist">
             <i class="fa fa-align-justify"></i> <?php esc_html_e('Dispatched Medicine List', 'hospital_mgt'); ?></a>
         </a>
    </li>	
	  
	<?php if(isset($_REQUEST['action']) && $_REQUEST['action'] =='edit' && isset($_REQUEST['dispatch_id'])) { ?> 
	<li class="<?php if($active_tab == 'adddismedicine') echo "active";?>">
		<a href="?dashboard=user&page=medicine&tab=adddismedicine&action=edit&dispatch_id=<?php echo $_REQUEST['dispatch_id'];?>">
			<i class="fa fa-plus-circle"></i> <?php esc_html_e('Edit Dispatch Medicine', 'hospital_mgt'); ?>
		</a> 
	</li>
	<?php } 
		else
		{
			if($user_access['add']=='1')
			{			
			?> 
				<li class="<?php if($active_tab == 'adddismedicine') echo "active";?>">
					<a href="?dashboard=user&page=medicine&tab=adddismedicine&&action=insert">
						<i class="fa fa-plus-circle"></i> <?php esc_html_e('Dispatch  Medicine', 'hospital_mgt'); ?>
					</a> 
				</li>
	<?php 	}
		}
		?>
    </ul>
	<div class="tab-content opacity_div"><!-- START TAB CONTENT DIV-->
        <div class="tab-pane <?php if($active_tab == 'medicinelist') echo "fade active in";?>" id="appointmentlist"><!-- START TAB PANE DIV-->
            <div class="panel-body"><!-- START PANEL BODY DIV-->
                <div class="table-responsive"><!-- START TABLE RESPONSIVE DIV-->
				    <table id="medicine_list" class="display dataTable" cellspacing="0" width="100%"><!-- START Medicine LIST TABLE-->
						<thead>
							<tr>
								<th><?php esc_html_e( 'Medicine Name', 'hospital_mgt' ) ;?></th>
								 <th><?php esc_html_e( 'Category', 'hospital_mgt' ) ;?></th>
								 <th><?php esc_html_e( 'Batch Number', 'hospital_mgt' ) ;?></th>
								 <th><?php esc_html_e( 'Quantity', 'hospital_mgt' ) ;?></th>
								   <th><?php esc_html_e( 'Price', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th> 
								   <th><?php esc_html_e( 'Discount', 'hospital_mgt' ) ;?></th> 
								   <th><?php esc_html_e( 'Tax', 'hospital_mgt' ) ;?></th> 
								   <th><?php esc_html_e( 'Expiry Date', 'hospital_mgt' ) ;?></th> 
									<th><?php esc_html_e( 'Stock', 'hospital_mgt' ) ;?></th> 
									<?php  
									if($user_access['edit']=='1' || $user_access['delete']=='1')
									{ ?>
										<th> <?php esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
									<?php
									}
									?>		
							</tr>
					    </thead>
						<tfoot>
							<tr>
								<th><?php esc_html_e( 'Medicine Name', 'hospital_mgt' ) ;?></th>
								 <th><?php esc_html_e( 'Category', 'hospital_mgt' ) ;?></th>
								 <th><?php esc_html_e( 'Batch Number', 'hospital_mgt' ) ;?></th>
								 <th><?php esc_html_e( 'Quantity', 'hospital_mgt' ) ;?></th>
								   <th><?php esc_html_e( 'Price', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th> 
								   <th><?php esc_html_e( 'Discount', 'hospital_mgt' ) ;?></th> 
								   <th><?php esc_html_e( 'Tax', 'hospital_mgt' ) ;?></th> 
								   <th><?php esc_html_e( 'Expiry Date', 'hospital_mgt' ) ;?></th> 
									<th><?php esc_html_e( 'Stock', 'hospital_mgt' ) ;?></th> 
									<?php  
									if($user_access['edit']=='1' || $user_access['delete']=='1')
									{ ?>
										<th> <?php esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
									<?php
									}
									?>		
							</tr>
						</tfoot>
						<tbody>
						 <?php 
						if($obj_hospital->role == 'doctor' || $obj_hospital->role == 'nurse' || $obj_hospital->role == 'laboratorist' || $obj_hospital->role == 'pharmacist' || $obj_hospital->role == 'accountant' || $obj_hospital->role == 'receptionist') 
						{
							$own_data=$user_access['own_data'];
							if($own_data == '1')
							{
								$medicinedata=$obj_medicine->MJ_hmgt_get_all_medicine_by_created_user();
							}
							else
							{
								$medicinedata=$obj_medicine->MJ_hmgt_get_all_medicine();
							}
						}						
						else
						{
							$medicinedata=$obj_medicine->MJ_hmgt_get_all_medicine();
						}	
						
						if(!empty($medicinedata))
						{
							foreach ($medicinedata as $retrieved_data)
							{ 
							?>
							<tr>
								<td class="medicine_name"><?php	echo esc_html($retrieved_data->medicine_name);	?></td>
								<td class="category"><?php echo $obj_medicine->MJ_hmgt_get_medicine_categoryname($retrieved_data->med_cat_id);?></td>
								<td class=""><?php	echo esc_html($retrieved_data->batch_number);?></td>
								<td class=""><?php	echo esc_html($retrieved_data->med_quantity);?></td>
								<td class="price"><?php  echo esc_html($retrieved_data->medicine_price);	?></td>
								<td class="price">
								<?php 
								if(!empty($retrieved_data->med_discount))
								{	
									echo $retrieved_data->med_discount; 
									if($retrieved_data->med_discount_in == 'percentage')
									{
										?>
										(%)
										<?php
									}	
									else
									{
										?>
										(<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)
										<?php 	
									}
								}
								else
								{
									echo '-';	
								}	
								?>	
								</td>
								<td class="">
								<?php
								if(!empty($retrieved_data->med_tax))
								{								
									echo MJ_hmgt_tax_name_array_by_tax_id_array($retrieved_data->med_tax);
								}
								else
								{
									echo '-';	
								}	
								?></td>
								<td class="price"><?php  echo  date(MJ_hmgt_date_formate(),strtotime($retrieved_data->medicine_expiry_date));	?></td>
								<td class="medicine_qty"><?php echo esc_html__("$retrieved_data->medicine_stock","hospital_mgt");?></td>
								<?php  
								if($user_access['edit']=='1' || $user_access['delete']=='1')
								{ ?>	
									<td class="action"> 
									<?php
									if($user_access['edit']=='1')
									{
									?>
										<a href="?dashboard=user&page=medicine&tab=addmedicine&action=edit&medicine_id=<?php echo esc_attr($retrieved_data->medicine_id);?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
								   <?php
									}
									if($user_access['delete']=='1')
									{
									?>	
										<a href="?dashboard=user&page=medicine&tab=medicinelist&action=delete&medicine_id=<?php echo MJ_hmgt_id_encrypt(esc_attr($retrieved_data->medicine_id));?>" class="btn btn-danger" 
										onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this record?','hospital_mgt');?>');">
										<?php esc_html_e( 'Delete', 'hospital_mgt' ) ;?> </a>
								   <?php
									}
									?>
								   </td>
								<?php
								}
								?>
							</tr>
							<?php 
							} 
						}?>
						</tbody>
					</table><!-- END Medicine LIST TABLE-->
 		        </div><!-- END TABLE RESPONSIVE DIV-->
		    </div><!-- END PANEL BODY DIV-->
	    </div><!-- END TAB PANE DIV-->
		<?php 
		//Add Medicine
		$edit = 0;
		if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && isset($_REQUEST['medicine_id']))
		{
			$edit = 1;
			$medcategory_id = $_REQUEST['medicine_id'];
			$result = $obj_medicine->MJ_hmgt_get_single_medicine($medcategory_id);
		}	
		?>
		<script type="text/javascript">
		$(document).ready(function() {
			"use strict";
			<?php
			if (is_rtl())
				{
				?>	
					$('#medicine_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
				<?php
				}
				else{
					?>
					$('#medicine_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
					<?php
				}
			?>
			$('.tax_charge').multiselect(
			{
				nonSelectedText :'<?php esc_html_e('Select Tax','hospital_mgt');?>',
				includeSelectAllOption: true,
			    selectAllText : '<?php esc_html_e('Select all','hospital_mgt'); ?>',
				templates: {
			            button: '<button class="multiselect btn btn-default dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="multiselect-selected-text"></span><b class="caret"></b></button>',
			        },
					buttonContainer: '<div class="dropdown" />'
			});
		} );
		</script>
	<div class="tab-pane <?php if($active_tab == 'addmedicine') echo "fade active in";?>"><!-- START TAB PANE DIV-->
        <div class="panel-body"><!-- START PANEL BODY DIV-->
			<form name="medicine_form" action="" method="post" class="form-horizontal" id="medicine_form"><!-- START Medicine FORM-->
			<?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
			<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
			<input type="hidden" class="medicine_id" name="medicine_id" value="<?php if(isset($_REQUEST['medicine_id'])) echo esc_attr($_REQUEST['medicine_id']);?>"  />			
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="medicine_category"><?php esc_html_e('Category Name','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8 margin_bottom_5px">			
						<select class="form-control validate[required] max_width_100" name="medicine_category" id="medicine">
						<option value=""><?php esc_html_e('Select Category','hospital_mgt');?></option>
						<?php 
						$medicine_category = $obj_medicine->MJ_hmgt_get_all_category();
						if(isset($_REQUEST['medicine_category']))
							$category =$_REQUEST['medicine_category'];  
						elseif($edit)
							$category =$result->med_cat_id;
						else 
							$category = "";
						
						if(!empty($medicine_category))
						{
							foreach ($medicine_category as $retrive_data)
							{
								echo '<option value="'.$retrive_data->ID.'" '.selected($category,$retrive_data->ID).'>'.$retrive_data->post_title.'</option>';
							}
						}
						?>
						</select>
					</div>
					<div class="col-sm-2"><button id="addremove" model="medicine"><?php esc_html_e('Add Or Remove','hospital_mgt');?></button></div>
				</div>
		</div>
		<?php
		if($edit)
		{
		?>
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="medicine_name"><?php esc_html_e('Medicine','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-3 margin_bottom_5px">
						<input id="medicine_name" class="form-control validate[required,custom[popup_category_validation]] text-input edit_medicine_name" maxlength="50" type="text" placeholder="<?php esc_html_e('Medicine Name','hospital_mgt');?>"
						value="<?php if($edit){ echo esc_attr($result->medicine_name);}elseif(isset($_POST['medicine_name'])) echo esc_attr($_POST['medicine_name']);?>" name="medicine_name">
					</div>
					<div class="col-sm-6 margin_bottom_5px">				
						<textarea rows="1"  name="description"  class="form-control validate[custom[address_description_validation]]" id="" maxlength="150" placeholder="<?php esc_html_e('Description','hospital_mgt');?>"><?php if($edit){ echo trim($result->medicine_description);}elseif(isset($_POST['description'])) echo esc_textarea($_POST['description']);?></textarea>
					</div>	
				</div>			
			</div>
			
			<div class="form-group">
				<div class="mb-3 row">
					<div class="offset-sm-2 col-sm-3 margin_bottom_5px">
						<input class="form-control validate[custom[popup_category_validation]]" type="text" maxlength="20" placeholder="<?php esc_html_e('Batch Number','hospital_mgt');?>" value="<?php if($edit){ echo esc_attr($result->batch_number);}elseif(isset($_POST['batch_number'])) echo esc_attr($_POST['batch_number']);?>" name="batch_number">
					</div>	
					<div class="col-sm-2 margin_bottom_5px">
						<input  class="form-control validate[required] text-input" min="0" type="number" onKeyPress="if(this.value.length==6) return false;"  placeholder="<?php esc_html_e('Quantity','hospital_mgt');?>"
						value="<?php if($edit){ echo esc_attr($result->med_quantity);}elseif(isset($_POST['med_quantity'])) echo esc_attr($_POST['med_quantity']);?>" name="med_quantity">
					</div>		
					<div class="col-sm-2 margin_bottom_5px">
						<input id="med_price" class="form-control validate[required] text-input" min="1" type="number" onKeyPress="if(this.value.length==8) return false;" step="0.01"  placeholder="<?php esc_html_e('Price','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' )); ?>)"
						value="<?php if($edit){ echo esc_attr($result->medicine_price);}elseif(isset($_POST['med_price'])) echo esc_attr($_POST['med_price']);?>" name="med_price">
					</div>
					<div class="col-sm-2">
						<input id="" class="form-control validate[custom[popup_category_validation]] text-input edit_med_uniqueid" maxlength="10" type="text" placeholder="<?php esc_html_e('Medicine ID','hospital_mgt');?>"	value="<?php if($edit){ echo esc_attr($result->med_uniqueid);}elseif(isset($_POST['med_uniqueid'])) echo esc_attr($_POST['med_uniqueid']);?>" name="med_uniqueid">
					</div>
				</div>				
			</div>	
			<div class="form-group">	
				<div class="mb-3 row">			
					<div class="offset-sm-2 col-sm-3 margin_bottom_5px">				
						<textarea rows="1"  name="note"  class="form-control validate[custom[address_description_validation]]"  maxlength="150" placeholder="<?php esc_html_e('Note','hospital_mgt');?>"><?php if($edit){ echo trim($result->note);}elseif(isset($_POST['note'])) echo esc_textarea($_POST['note']);?></textarea>
					</div>
					<div class="col-sm-2 margin_bottom_5px">
						<input id="med_discount" class="form-control text-input" type="number" onKeyPress="if(this.value.length==10) return false;" step="0.01"  placeholder="<?php esc_html_e('Discount','hospital_mgt');?>" value="<?php if($edit){ echo esc_attr($result->med_discount);}elseif(isset($_POST['med_discount'])) echo esc_attr($_POST['med_discount']);?>" name="med_discount">
					</div>	
					<div class="col-sm-2 margin_bottom_5px">
						<select class="form-control" name="med_discount_in">
							<option value="flat" <?php selected($result->med_discount_in,'flat'); ?>><?php esc_html_e('Flat','hospital_mgt');?></option>
							<option value="percentage" <?php selected($result->med_discount_in,'percentage'); ?>><?php esc_html_e('Percentage','hospital_mgt');?></option>
						</select>
					</div>	
					<div class="col-sm-2 margin_bottom_5px">					
						<select  class="form-control tax_charge"  name="med_tax[]" multiple="multiple">					
						<?php
						$tax_id=explode(',',$result->med_tax);
						
						$obj_invoice= new MJ_hmgt_invoice();
						$hmgt_taxs=$obj_invoice->MJ_hmgt_get_all_tax_data();	
						
						if(!empty($hmgt_taxs))
						{
							foreach($hmgt_taxs as $entry)
							{
								$selected = "";
								if(in_array($entry->tax_id,$tax_id))
									$selected = "selected";
								?>
								<option value="<?php echo esc_attr($entry->tax_id); ?>" <?php echo esc_attr($selected); ?> ><?php echo esc_html($entry->tax_title);?>-<?php echo esc_html($entry->tax_value);?></option>
							<?php 
							}
						}
						?>
						</select>		
					</div>
				</div>					
			</div>
			<div class="form-group">	
				<div class="mb-3 row">			
					<div class="offset-sm-2 col-sm-3 margin_bottom_5px">
						<input id="mfg_cmp_name" class="form-control validate[custom[popup_category_validation]]" type="text" maxlength="50" placeholder="<?php esc_html_e('Manufacturer Company Name','hospital_mgt');?>"
						value="<?php if($edit){ echo esc_attr($result->medicine_menufacture);}elseif(isset($_POST['mfg_cmp_name'])) echo esc_attr($_POST['mfg_cmp_name']);?>" name="mfg_cmp_name">
					</div>
					<div class="col-sm-2 margin_bottom_5px">
						<input id="manufactured_date" class="form-control validate[required]" type="text"  name="manufactured_date" placeholder="<?php esc_html_e('Manufactured Date','hospital_mgt');?>" value="<?php if($edit){ echo date(MJ_hmgt_date_formate(),strtotime($result->manufactured_date));}elseif(isset($_POST['manufactured_date'])) echo esc_attr($_POST['manufactured_date']);?>" readonly>
					</div>	
					<div class="col-sm-2 margin_bottom_5px">
						<input id="expiry_date" class="form-control validate[required]" type="text"  name="expiry_date" 
						placeholder="<?php esc_html_e('Expiry Date','hospital_mgt');?>"	value="<?php if($edit){ echo date(MJ_hmgt_date_formate(),strtotime($result->medicine_expiry_date));}elseif(isset($_POST['expiry_date'])) echo esc_attr($_POST['expiry_date']);?>" readonly>
					</div>
				</div>					
			</div>			
		<?php	
		}
		else
		{	
		?>			
			<div class="main_medicine_div">				
				<div class="medicine_div">
					<div class="form-group">
						<div class="mb-3 row">
							<label class="col-sm-2 control-label form-label" for="medicine_name"><?php esc_html_e('Medicine','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-sm-3 margin_bottom_5px">
								<input id="medicine_name" class="form-control validate[required,custom[popup_category_validation]] text-input medicine_name" maxlength="50" type="text" placeholder="<?php esc_html_e('Medicine Name','hospital_mgt');?>" value="" name="medicine_name[]">
							</div>
							<div class="col-sm-6 margin_bottom_5px">				
								<textarea rows="1"  name="description[]"  class="form-control validate[custom[address_description_validation]]" id="description" maxlength="150" placeholder="<?php esc_html_e('Description','hospital_mgt');?>"></textarea>
							</div>	
						</div>					
					</div>
					<div class="form-group">
						<div class="mb-3 row">
							<div class="offset-sm-2 col-sm-3 margin_bottom_5px">
								<input class="form-control validate[custom[popup_category_validation]]" type="text" maxlength="20" placeholder="<?php esc_html_e('Batch Number','hospital_mgt');?>" value="" name="batch_number[]">
							</div>						
							<div class="col-sm-2 margin_bottom_5px">
								<input  class="form-control validate[required] text-input" min="0" type="number" onKeyPress="if(this.value.length==6) return false;"  placeholder="<?php esc_html_e('Quantity','hospital_mgt');?>" value="" name="med_quantity[]">
							</div>
							<div class="col-sm-2 margin_bottom_5px">
								<input id="med_price" class="form-control validate[required] text-input" min="1" type="number" onKeyPress="if(this.value.length==8) return false;" step="0.01"  placeholder="<?php esc_html_e('Price','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' )); ?>)" value="" name="med_price[]">
							</div>	
							<div class="col-sm-2 margin_bottom_5px">
								<input id="" class="form-control validate[custom[popup_category_validation]] text-input med_uniqueid" maxlength="10" type="text" placeholder="<?php esc_html_e('Medicine ID','hospital_mgt');?>"	value="" name="med_uniqueid[]">
							</div>
						</div>													
					</div>
					<div class="form-group">
						<div class="mb-3 row">						
							<div class="offset-sm-2 col-sm-3 margin_bottom_5px">				
								<textarea rows="1"  name="note[]"  class="form-control validate[custom[address_description_validation]]"  maxlength="150" placeholder="<?php esc_html_e('Note','hospital_mgt');?>"></textarea>
							</div>
							<div class="col-sm-2 margin_bottom_5px">
								<input id="med_discount" class="form-control text-input" type="number" onKeyPress="if(this.value.length==10) return false;" step="0.01"  placeholder="<?php esc_html_e('Discount','hospital_mgt');?> " value="" name="med_discount[]">
							</div>		
							<div class="col-sm-2 margin_bottom_5px">
								<select class="form-control" name="med_discount_in[]">
									<option value="flat"><?php esc_html_e('Flat','hospital_mgt');?></option>
									<option value="percentage"><?php esc_html_e('Percentage','hospital_mgt');?></option>
								</select>
							</div>	
													
							<div class="col-sm-2 margin_bottom_5px">						
								<select  class="form-control tax_charge"  name="med_tax[0][]" multiple="multiple">					
									<?php	
									$obj_invoice= new MJ_hmgt_invoice();
									$hmgt_taxs=$obj_invoice->MJ_hmgt_get_all_tax_data();	
									
									if(!empty($hmgt_taxs))
									{
										foreach($hmgt_taxs as $entry)
										{										
											?>
											<option value="<?php echo esc_attr($entry->tax_id); ?>"><?php echo esc_html($entry->tax_title);?>-<?php echo esc_html($entry->tax_value);?></option>
										<?php 
										}
									}
									?>
								</select>		
							</div>
						</div>		
					</div> 
					<div class="form-group">
						<div class="mb-3 row">
							<div class="offset-sm-2 col-sm-3 margin_bottom_5px">
								<input id="mfg_cmp_name" class="form-control validate[custom[popup_category_validation]]" type="text" maxlength="50" placeholder="<?php esc_html_e('Manufacturer Company Name','hospital_mgt');?>"
								value="" name="mfg_cmp_name[]">
							</div>
							<div class="col-sm-2 margin_bottom_5px">
								<input id="manufactured_date" class="form-control validate[required]" type="text"  name="manufactured_date[]" placeholder="<?php esc_html_e('Manufactured Date','hospital_mgt');?>"
								value="" readonly>
							</div>	
							<div class="col-sm-2 margin_bottom_5px">
								<input id="expiry_date" class="form-control validate[required]" type="text"  name="expiry_date[]" placeholder="<?php esc_html_e('Expiry Date','hospital_mgt');?>" value="" readonly>
							</div>	
						</div>
					</div>
					
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="expense_entry"></label>
					<div class="col-sm-3 margin_bottom_5px">				
						<button id="add_new_entry" class="btn btn-default btn-sm btn-icon icon-left demo" type="button"   name="add_new_entry" onclick="add_entry()"><?php esc_html_e('Add More Medicine','hospital_mgt'); ?>
						</button>
					</div>	
				</div>			
			</div>
		<?php
		}
		?>
		<?php wp_nonce_field( 'save_medicine_nonce' ); ?>
			<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
				<input type="submit" value="<?php if($edit){ esc_html_e('Save','hospital_mgt'); }else{ esc_html_e('Save','hospital_mgt');}?>" name="save_medicine" class="btn btn-success"/>
			</div>
			</form><!-- END Medicine FORM-->
        </div><!-- END PANEL BODY DIV-->
    </div><!-- END TAB PANE DIV-->
	<script>
	var key=0;
   function add_entry()
	{
		key++;
		$(".main_medicine_div").append('<div class="medicine_div"><div class="form-group"><div class="mb-3 row"><label class="col-sm-2 control-label form-label" for="medicine_name"><?php esc_html_e('Medicine','hospital_mgt');?><span class="require-field">*</span></label><div class="col-sm-3 margin_bottom_5px"><input id="medicine_name" class="form-control validate[required,custom[popup_category_validation]] text-input  medicine_name" maxlength="50" type="text" placeholder="<?php esc_html_e('Medicine Name','hospital_mgt');?>" value="" name="medicine_name[]"></div><div class="col-sm-6"><textarea rows="1"  name="description[]"  class="form-control validate[custom[address_description_validation]]" id="description" maxlength="150" placeholder="<?php esc_html_e('Description','hospital_mgt');?>"></textarea></div></div></div><div class="form-group"><div class="mb-3 row"><div class="offset-sm-2 col-sm-3 margin_bottom_5px"><input class="form-control validate[custom[popup_category_validation]]" type="text" maxlength="20" placeholder="<?php esc_html_e('Batch Number','hospital_mgt');?>" value="" name="batch_number[]"></div><div class="col-sm-2 margin_bottom_5px"><input  class="form-control validate[required] text-input" min="0" type="number" onKeyPress="if(this.value.length==6) return false;"  placeholder="<?php esc_html_e('Quantity','hospital_mgt');?>" value="" name="med_quantity[]"></div><div class="col-sm-2 margin_bottom_5px"><input id="med_price" class="form-control validate[required] text-input" min="1" step="0.01" type="number" onKeyPress="if(this.value.length==8) return false;"  placeholder="<?php esc_html_e('Price','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' )); ?>)" value="" name="med_price[]"></div><div class="col-sm-2 margin_bottom_5px"><input id="" class="form-control validate[] text-input med_uniqueid validate[custom[popup_category_validation]]" maxlength="10" type="text" placeholder="<?php esc_html_e('Medicine ID','hospital_mgt');?>"value="" name="med_uniqueid[]"></div></div><div class="form-group"><div class="mb-3 row"><div class="offset-sm-2 col-sm-3 margin_bottom_5px"><textarea rows="1"  name="note[]"  class="form-control validate[custom[address_description_validation]]"  maxlength="150" placeholder="<?php esc_html_e('Note','hospital_mgt');?>"></textarea></div><div class="col-sm-2 margin_bottom_5px"><input id="med_discount" class="form-control text-input" type="number" onKeyPress="if(this.value.length==10) return false;" step="0.01"  placeholder="<?php esc_html_e('Discount','hospital_mgt');?>" value="" name="med_discount[]"></div><div class="col-sm-2 margin_bottom_5px"><select class="form-control" name="med_discount_in[]"><option value="flat"><?php esc_html_e('Flat','hospital_mgt');?></option><option value="percentage"><?php esc_html_e('Percentage','hospital_mgt');?></option></select></div>	<div class="col-sm-2 margin_bottom_5px"><select  class="form-control tax_charge tax_dropdawn"  name="med_tax['+key+'][]" multiple="multiple"><?php $obj_invoice= new MJ_hmgt_invoice(); $hmgt_taxs=$obj_invoice->MJ_hmgt_get_all_tax_data(); if(!empty($hmgt_taxs)){	foreach($hmgt_taxs as $entry){ ?> <option value="<?php echo $entry->tax_id; ?>"><?php echo $entry->tax_title;?>-<?php echo $entry->tax_value;?></option> <?php } }	?></select></div></div></div><div class="form-group"><div class="mb-3 row"><div class="offset-sm-2 col-sm-3 margin_bottom_5px"><input id="mfg_cmp_name" class="form-control validate[custom[popup_category_validation]]" type="text" maxlength="50" placeholder="<?php esc_html_e('Manufacturer Company Name','hospital_mgt');?>" value="" name="mfg_cmp_name[]"></div><div class="col-sm-2 margin_bottom_5px"><input id="" class="form-control validate[required] medicine_manufactured_date" type="text"  name="manufactured_date[]" placeholder="<?php esc_html_e('Manufactured Date','hospital_mgt');?>" value="" readonly></div><div class="col-sm-2 margin_bottom_5px"><input id="" class="form-control validate[required] medicine_expiry_date" type="text"  name="expiry_date[]" placeholder="<?php esc_html_e('Expiry Date','hospital_mgt');?>" value="" readonly></div><div class="offset-sm-2 col-sm-1"><button type="button" class="btn btn-default delete_medicine_div"><i class="entypo-trash"><?php esc_html_e('Delete','hospital_mgt');?></i></button></div></div></div></div>');
	}
    </script> 
<?php 
if($active_tab=="dismedicinelist")
{
	$obj = new MJ_hmgt_prescription();
	 ?>
	<script type="text/javascript">
	jQuery(document).ready(function() {
		"use strict";
		jQuery('#dispatchlist').DataTable({
			"responsive": true,
			"aoColumns":[
					  {"bSortable": true},
					  {"bSortable": true},
					  {"bSortable": true},
					  {"bSortable": true},
					  {"bSortable": true},
					  {"bSortable": true},  
					  <?php  
						if($user_access['edit']=='1' || $user_access['delete']=='1')
						{
						?>
					  {"bSortable": false}
					  <?php 
						}
						?>
					  ],
			language:<?php echo MJ_hmgt_datatable_multi_language();?>			  
			});
	} );
	</script>
    <form name="dispatchlist" action="" method="post"><!-- START Dispatch MEDICINE LIST FORM-->
		<div class="panel-body"><!-- START PANEL BODY DIV-->
				<div class="table-responsive"><!-- START TABLE RESPONSIVE DIV-->
					<table id="dispatchlist" class="display" cellspacing="0" width="100%"><!-- START Dispatch Medicine LIST TABLE-->
						<thead>
							<tr>
								<th><?php esc_html_e( 'Patient Name', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Prescription', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Medicine Price', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th> 
								<th><?php esc_html_e( 'Discount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th> 
								<th><?php esc_html_e( 'Tax Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th> 
								<th><?php esc_html_e( 'Sub Total', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th> 
								<?php  
								if($user_access['edit']=='1' || $user_access['delete']=='1')
								{
								?>
								<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
								<?php
								}
								?>
							</tr>
						</thead>
						<tfoot>
							 <tr>
								<th><?php esc_html_e( 'Patient Name', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Prescription', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Medicine Price', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th> 
								<th><?php esc_html_e( 'Discount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th> 
								<th><?php esc_html_e( 'Tax Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th> 
								<th><?php esc_html_e( 'Sub Total', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th> 
								<?php  
								if($user_access['edit']=='1' || $user_access['delete']=='1')
								{
								?>
								<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
								<?php
								}
								?>
							</tr>
						</tfoot>
						<tbody>
						 <?php
							if($obj_hospital->role == 'laboratorist' || $obj_hospital->role == 'pharmacist' || $obj_hospital->role == 'accountant' || $obj_hospital->role == 'receptionist') 
							{
								$own_data=$user_access['own_data'];
								if($own_data == '1')
								{
								  $medicinedata=$obj_medicine->MJ_hmgt_get_all_dispatch_medicine_by_created_by();
								}
								else
								{
									 $medicinedata=$obj_medicine->MJ_hmgt_get_all_dispatch_medicine();
								}
							}
							elseif($obj_hospital->role == 'doctor') 
							{
								$own_data=$user_access['own_data'];
								if($own_data == '1')
								{
								  $medicinedata=$obj_medicine->MJ_hmgt_get_doctor_all_dispatch_medicine_by_created_by();
								}
								else
								{
									 $medicinedata=$obj_medicine->MJ_hmgt_get_all_dispatch_medicine();
								}
							}
							elseif($obj_hospital->role == 'nurse') 
							{
								$own_data=$user_access['own_data'];
								if($own_data == '1')
								{
								 	$medicinedata=$obj_medicine->MJ_hmgt_get_nurse_all_dispatch_medicine_by_created_by();
								 
								}
								else
								{
									$medicinedata=$obj_medicine->MJ_hmgt_get_all_dispatch_medicine();
								}
								
							}
							elseif($obj_hospital->role == 'patient')
							{
								$own_data=$user_access['own_data'];
								if($own_data == '1')
								{
									$userid=get_current_user_id();			
									$medicinedata=$obj_medicine->MJ_hmgt_get_all_dispatch_medicine_by_patient($userid);
								}
								else
								{
									$medicinedata=$obj_medicine->MJ_hmgt_get_all_dispatch_medicine();
								}
							}
							
						 if(!empty($medicinedata)) {
							foreach ($medicinedata as $retrieved_data){
								$prescriptiondata = $obj->MJ_hmgt_get_prescription_data($retrieved_data->prescription_id);
							?>
							<tr>
								<td class=""><?php	echo MJ_hmgt_get_display_name($retrieved_data->patient);	?></td>
								<td class=""><?php echo MJ_hmgt_get_display_name($prescriptiondata->patient_id) .' - '.$prescriptiondata->pris_create_date; ?></td>
								<td class=""><?php  echo esc_html($retrieved_data->med_price);	?></td>
								<td class=""><?php  echo esc_html($retrieved_data->discount);	?></td>
								<td class=""><?php  echo esc_html($retrieved_data->total_tax_amount);	?></td>
								<td class=""><?php echo esc_html( $retrieved_data->sub_total);?></td>
								<?php  
								if($user_access['edit']=='1' || $user_access['delete']=='1')
								{
								?>                
								<td class="action">
								<?php
								if($user_access['edit']=='1')
								{
								?>
									<a href="?dashboard=user&page=medicine&tab=adddismedicine&action=edit&dispatch_id=<?php echo MJ_hmgt_id_encrypt(esc_attr($retrieved_data->id));?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
								<?php
								}
								if($user_access['delete']=='1')
								{
								?>	
									<a href="?dashboard=user&page=medicine&tab=medicinelist&action=delete&dispatch_id=<?php echo MJ_hmgt_id_encrypt(esc_attr($retrieved_data->id));?>" class="btn btn-danger" 
									onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this record?','hospital_mgt');?>');">
									<?php esc_html_e( 'Delete', 'hospital_mgt' ) ;?> </a>                
								<?php
								}
								?>	
							   </td>  
								<?php
								}
								?>
							</tr>
							<?php } }?>     
						</tbody>        
					</table><!-- END Dispatch Medicine LIST TABLE-->
				</div><!-- END TABLE RESPONSIVE DIV-->
		</div><!-- END PANEL BODY DIV-->
    </form><!-- END Dispatch MEDICINE LIST FORM-->
	<?php } 
	if($active_tab=="adddismedicine")
	{ 
			$edit = 0;
			if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
			{
			$edit = 1;
			 $medcategory_id = MJ_hmgt_id_decrypt($_REQUEST['dispatch_id']);	
			$result = $obj_medicine->MJ_hmgt_get_single_dispatch_medicine($medcategory_id);
			
			}	
			?>
			<script type="text/javascript">
			$(document).ready(function() {
				"use strict";
				<?php
				if (is_rtl())
				{
				?>	
					$('#dipatch_medicine_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
				<?php
				}
				else{
					?>
					$('#dipatch_medicine_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
					<?php
				}
			?>
			});
			</script>
			<div class="panel-body"><!-- START PANEL BODY DIV-->
				<form name="medicine_form" action="" method="post" class="form-horizontal" id="dipatch_medicine_form"><!-- START Dispatch Medicine FORM-->
					<?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
					<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
					<input type="hidden" name="dispatch_id" value="<?php if(isset($_REQUEST['dispatch_id'])) echo MJ_hmgt_id_decrypt($_REQUEST['dispatch_id']);?>"/>
					
					<div class="form-group">
						<div class="mb-3 row">
							<label class="col-sm-2 control-label form-label" for="patient"><?php esc_html_e('Patient','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-sm-8">				
								<?php if($edit){ $patient_id1=$result->patient; }elseif(isset($_POST['patient'])){$patient_id1=$_POST['patient'];}else{ $patient_id1="";}?>
								<select name="patient" class="form-control" id="patient_id" <?php if($edit) print 'disabled="disabled"'; ?> >
								<option value=""><?php esc_html_e('Select Patient','hospital_mgt');?></option>
								<?php 
								$own_data=$user_access['own_data'];
								if($own_data == '1')
								{ 
									$patients =$obj_hospital->patient;
									if(!empty($patients))
									{
										foreach($patients as $patient)
										{
											$patient_id = get_user_meta($patient->ID,'patient_id',true);
											
											echo '<option value="'.$patient->ID.'" '.selected($patient_id1,$patient->ID).'>'.$patient_id.' - '.$patient->display_name.'</option>';
										
										}
									}
								}
								else
								{					
									$patients = MJ_hmgt_patientid_list();
									if(!empty($patients)){
									foreach($patients as $patient){
										echo '<option value="'.$patient['id'].'" '.selected($patient_id1,$patient['id']).'>'.$patient['patient_id'].' - '.$patient['first_name'].' '.$patient['last_name'].'</option>';
									} } 
								}?>
								</select>
							<?php if($edit) print '<input type="hidden" name="patient" value="'.$patient_id1=$result->patient.'">'; ?>
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<div class="mb-3 row">
							<label class="col-sm-2 control-label form-label" for="patient"><?php esc_html_e('Prescription','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-sm-8">					
								<select name="prescription_id" class="form-control" id="prescription"  <?php if($edit) print 'disabled="disabled"'; ?>  >
									<option><?php esc_html_e('Select Prescription', 'hospital_mgt');?></option>	
									<?php 
										if($edit){
											$obj_prescription = new MJ_hmgt_prescription();
											$prescriptiondata = $obj_prescription->MJ_hmgt_get_all_prescription();
											if(!empty($prescriptiondata)){
												foreach($prescriptiondata as $key=>$value)
												{
													echo '<option value="'.$value->priscription_id.'" '.selected($result->prescription_id,$value->priscription_id).'>'.MJ_hmgt_get_display_name($value->patient_id) .' - '.$value->pris_create_date.'</option>';
												} 
											}
										}
									?>
								<?php if($edit) print '<input type="hidden" name="prescription_id" value="'.$result->prescription_id.'">'; ?>
								</select>
							</div>	
						</div>			
					</div>
					
					<div id="madicinedata"></div>
						<?php 
						if($edit)
						{ 
							$obj_madicine = new MJ_hmgt_medicine();
							$medication = json_decode($result->madicine);
							$i=1;
							?>
							<div class="form-group">
								<div class="mb-3 row">
									<div class="col-sm-2"></div>
									 <div class="col-sm-2"><?php esc_html_e('Medicine','hospital_mgt');?></div>
									 <div class="col-sm-1"><?php esc_html_e('Quantity','hospital_mgt');?></div>
									 <div class="col-sm-1"><?php esc_html_e('Price','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' )); ?>)</div>
									  <div class="col-sm-2"><?php esc_html_e('Discount','hospital_mgt');?></div>
									  <div class="col-sm-1"><?php esc_html_e('Discount Amount','hospital_mgt');?></div>
									 <div class="col-sm-1"><?php esc_html_e('Tax','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' )); ?>)</div>
									 <div class="col-sm-2"></div>
								</div>
							 </div>
							<?php
							foreach($medication as $key=>$val)
							{							
								$singgle_madicine = $obj_madicine->MJ_hmgt_get_single_medicine($val->madicine_id);
								
							?>
							<div id="invoice_entry">
								<div class="form-group">
									<div class="mb-3 row">									
										<div class="col-sm-2 margin_bottom_5px"></div>
										<input type="hidden"  name="madicine_id[]" value="<?php print $singgle_madicine->medicine_id ?>">
										<input type="hidden"  class="madicine_quantity_<?php print $i;?>" name="madicine_quantity" value="<?php print $singgle_madicine->med_quantity+$val->qty ?>">
										<input type="hidden"  class="" name="old_quantity[]" value="<?php print $val->qty ?>">
										<div class="col-sm-2 margin_bottom_5px">
											<input type="text" name="madicine_title[]" class="form-control" value="<?php print $singgle_madicine->medicine_name; ?> " readonly>
										</div>
										<div class="col-sm-1 margin_bottom_5px">
											<input id="qty_<?php print $i;?>" class="days form-control validate[required] medicineqty_<?php print $singgle_madicine->medicine_id ?>" dataid="<?php print $singgle_madicine->medicine_id ?>" counter="<?php print $i;?>" class="form-control" type="number" min="0" value="<?php print $val->qty ?>" name="qty[]">
										</div>
										<div class="col-sm-1 margin_bottom_5px">
											<input id="price_<?php print $i;?>"  class="med_price form-control" type="text" value="<?php  print $val->price; ?>" name="price[]" readonly>
										</div>	
										<div class="col-sm-1 margin_bottom_5px">
											<input id="discount_value_<?php print $i;?>" dataid="<?php print $singgle_madicine->medicine_id ?>" onKeyPress="if(this.value.length==10) return false;" step="0.01" class="med_discount_value form-control" type="number" value="<?php print $val->discount_value ?>" name="discount_value[]" counter="<?php print $i;?>">
										</div>	
										<div class="col-sm-1 margin_bottom_5px">
											<select class="form-control" id="med_discount_in_<?php print $i;?>" name="med_discountin[]" disabled>
												<option value="flat" <?php selected($val->med_discount_in,'flat'); ?>><?php esc_html_e('Flat','hospital_mgt');?></option>
												<option value="percentage" <?php selected($val->med_discount_in,'percentage'); ?>>%</option>
											</select>
											<input type="hidden" name="med_discount_in[]" value="<?php echo $val->med_discount_in; ?>">
										</div>
										<div class="col-sm-1 margin_bottom_5px">
											<input id="discount_<?php print $i;?>"  class="med_discount form-control" type="text" value="<?php  print $val->discount_amount; ?>" name="discount_amount[]" readonly>
										</div>	
										<div class="col-sm-1">
											<input id="tax_<?php print $i;?>"  class="tax_amount form-control" type="text" value="<?php  print $val->tax_amount; ?>" name="tax_amount[]" readonly>
										</div>		
										<div class="col-sm-2"></div>
									</div>
								</div>
							</div>
							<?php  $i++;
							}	
						}
					?>
					
					<div class="form-group">
						<div class="mb-3 row">
							<label class="col-sm-2 control-label form-label" for="med_price"><?php esc_html_e('Total Price Amount','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' )); ?>)<span class="require-field">*</span></label>
							<div class="col-sm-8">
								<input id="dispatch_medicine_price" class="form-control validate[required] text-input" type="text" 
								value="<?php if($edit) print esc_attr($result->med_price); ?>" name="med_price" readonly>
							</div>
						</div>
					</div>			
					<div class="form-group">
						<div class="mb-3 row">
							<label class="col-sm-2 control-label form-label" for="discount"><?php esc_html_e('Total Discount Amount','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' )); ?>)</label>
							<div class="col-sm-8">
								<input id="discount" class="form-control discount text-input" min="0" type="number" onKeyPress="if(this.value.length==8) return false;" step="0.01" value="<?php  if($edit) print esc_attr($result->discount); ?>" name="discount" readonly>
							</div>
						</div>
					</div>
					<?php wp_nonce_field( 'save_dispatch_medicine_nonce' ); ?>
					<div class="form-group">
						<div class="mb-3 row">
							<label class="col-sm-2 control-label form-label" for="med_price"><?php esc_html_e('Total Tax Amount','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' )); ?>)</label>
							<div class="col-sm-8">
								<input id="med_tax" class="form-control text-input" type="text" 
								value="<?php if($edit) print esc_attr($result->total_tax_amount); ?>" name="total_tax_amount" readonly>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">
							<label class="col-sm-2 control-label form-label" for="sub_total"><?php esc_html_e('Sub Total','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' )); ?>)</label>
							<div class="col-sm-8">
								<input id="sub_total" class="form-control  text-input" type="text" value="<?php  if($edit) print esc_attr($result->sub_total); ?>" name="sub_total" readonly>
							</div>
						</div>
					</div>
					<div class="form-group margin_bottom_5px">
						<div class="mb-3 row">
							<label class="col-sm-2 control-label form-label" for="description"><?php esc_html_e('Description','hospital_mgt');?></label>
							<div class="col-sm-8">
								<textarea name="description" id="description" maxlength="150" class="form-control validate[custom[address_description_validation]]"><?php if($edit){ echo trim($result->description);}elseif(isset($_POST['description'])) echo esc_textarea($_POST['description']);?></textarea>					
							</div>
						</div>
					</div>			
							
					<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
						<input type="submit" value="<?php if($edit){ esc_html_e('Save','hospital_mgt'); }else{ esc_html_e('Save','hospital_mgt');}?>" name="save_dispatch_medicine" class="btn btn-success"/>
					</div>
				</form><!-- END Dispatch Medicine FORM-->
			</div>	<!-- END PANEL BODY DIV-->
    <?php } ?>
    </div><!-- END TAB CONTEMT DIV-->
</div><!-- END PANEL BODY DIV-->