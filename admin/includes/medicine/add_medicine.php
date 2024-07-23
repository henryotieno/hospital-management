<?php
MJ_hmgt_browser_javascript_check();
//Add Medicine
$edit = 0;
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
{
	$edit = 1;
	$medcategory_id = $_REQUEST['medicine_id'];
	$result = $obj_medicine->MJ_hmgt_get_single_medicine($medcategory_id);
}	
?>
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
		nonSelectedText :'<?php _e('Select Tax','hospital_mgt');?>',
		includeSelectAllOption: true,
	    selectAllText : '<?php esc_html_e('Select all','hospital_mgt'); ?>',
		templates: {
	            button: '<button class="multiselect btn btn-default dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="multiselect-selected-text"></span><b class="caret"></b></button>',
	        },
			buttonContainer: '<div class="dropdown" />'
	});
} );
</script>
<!-- PANEL BODY DIV START -->
<div class="panel-body">
    <form name="medicine_form" action="" method="post" class="form-horizontal" id="medicine_form">
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
						<input id="medicine_name" class="form-control validate[required] text-input edit_medicine_name" maxlength="50" type="text" placeholder="<?php esc_html_e('Medicine Name','hospital_mgt');?>"
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
								<input id="medicine_name" class="form-control validate[required] text-input medicine_name" maxlength="50" type="text" placeholder="<?php esc_html_e('Medicine Name','hospital_mgt');?>" value="" name="medicine_name[]">
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
    </form>
</div>
<script>
var key=0;
function add_entry()
{		
	key++;
	jQuery(".main_medicine_div").append('<div class="medicine_div"><div class="form-group"><div class="mb-3 row"><label class="col-sm-2 control-label form-label" for="medicine_name"><?php esc_html_e('Medicine','hospital_mgt');?><span class="require-field">*</span></label><div class="col-sm-3 margin_bottom_5px"><input id="medicine_name" class="form-control validate[required] text-input  medicine_name" maxlength="50" type="text" placeholder="<?php esc_html_e('Medicine Name','hospital_mgt');?>" value="" name="medicine_name[]"></div><div class="col-sm-6"><textarea rows="1"  name="description[]"  class="form-control validate[custom[address_description_validation]]" id="description" maxlength="150" placeholder="<?php esc_html_e('Description','hospital_mgt');?>"></textarea></div></div></div><div class="form-group"><div class="mb-3 row"><div class="offset-sm-2 col-sm-3 margin_bottom_5px"><input class="form-control validate[custom[popup_category_validation]]" type="text" maxlength="20" placeholder="<?php esc_html_e('Batch Number','hospital_mgt');?>" value="" name="batch_number[]"></div><div class="col-sm-2 margin_bottom_5px"><input  class="form-control validate[required] text-input" min="0" type="number" onKeyPress="if(this.value.length==6) return false;"  placeholder="<?php esc_html_e('Quantity','hospital_mgt');?>" value="" name="med_quantity[]"></div><div class="col-sm-2 margin_bottom_5px"><input id="med_price" class="form-control validate[required] text-input" min="1" step="0.01" type="number" onKeyPress="if(this.value.length==8) return false;"  placeholder="<?php esc_html_e('Price','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' )); ?>)" value="" name="med_price[]"></div><div class="col-sm-2 margin_bottom_5px"><input id="" class="form-control validate[] text-input med_uniqueid validate[custom[popup_category_validation]]" maxlength="10" type="text" placeholder="<?php esc_html_e('Medicine ID','hospital_mgt');?>"value="" name="med_uniqueid[]"></div></div><div class="form-group"><div class="mb-3 row"><div class="offset-sm-2 col-sm-3 margin_bottom_5px"><textarea rows="1"  name="note[]"  class="form-control validate[custom[address_description_validation]]"  maxlength="150" placeholder="<?php esc_html_e('Note','hospital_mgt');?>"></textarea></div><div class="col-sm-2 margin_bottom_5px"><input id="med_discount" class="form-control text-input" type="number" onKeyPress="if(this.value.length==10) return false;" step="0.01"  placeholder="<?php esc_html_e('Discount','hospital_mgt');?>" value="" name="med_discount[]"></div><div class="col-sm-2 margin_bottom_5px"><select class="form-control" name="med_discount_in[]"><option value="flat"><?php esc_html_e('Flat','hospital_mgt');?></option><option value="percentage"><?php esc_html_e('Percentage','hospital_mgt');?></option></select></div>	<div class="col-sm-2 margin_bottom_5px"><select  class="form-control tax_charge tax_dropdawn"  name="med_tax['+key+'][]" multiple="multiple"><?php $obj_invoice= new MJ_hmgt_invoice(); $hmgt_taxs=$obj_invoice->MJ_hmgt_get_all_tax_data(); if(!empty($hmgt_taxs)){	foreach($hmgt_taxs as $entry){ ?> <option value="<?php echo $entry->tax_id; ?>"><?php echo $entry->tax_title;?>-<?php echo $entry->tax_value;?></option> <?php } }	?></select></div></div></div><div class="form-group"><div class="mb-3 row"><div class="offset-sm-2 col-sm-3 margin_bottom_5px"><input id="mfg_cmp_name" class="form-control validate[custom[popup_category_validation]]" type="text" maxlength="50" placeholder="<?php esc_html_e('Manufacturer Company Name','hospital_mgt');?>" value="" name="mfg_cmp_name[]"></div><div class="col-sm-2 margin_bottom_5px"><input id="" class="form-control validate[required] medicine_manufactured_date" type="text"  name="manufactured_date[]" placeholder="<?php esc_html_e('Manufactured Date','hospital_mgt');?>" value="" readonly></div><div class="col-sm-2 margin_bottom_5px"><input id="" class="form-control validate[required] medicine_expiry_date" type="text"  name="expiry_date[]" placeholder="<?php esc_html_e('Expiry Date','hospital_mgt');?>" value="" readonly></div><div class="offset-sm-2 col-sm-1"><button type="button" class="btn btn-default delete_medicine_div"><i class="entypo-trash"><?php esc_html_e('Delete','hospital_mgt');?></i></button></div></div></div></div>');
}
</script> 