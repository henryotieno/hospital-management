<?php
MJ_hmgt_browser_javascript_check();
//Add Medicine
$edit = 0;
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
{
	$edit = 1;
	$medcategory_id = $_REQUEST['dispatch_id'];	
	$result = $obj_medicine->MJ_hmgt_get_single_dispatch_medicine($medcategory_id);
}	
?>
<script type="text/javascript">
jQuery(document).ready(function($)
{
	"use strict";
	<?php
	if (is_rtl())
		{
		?>	
			$('#dispatch_medicine_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#dispatch_medicine_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>
	$('#expiry_date').datepicker({
	  	changeMonth: true,
        changeYear: true,
        yearRange:'-65:+0',
        onChangeMonthYear: function(year, month, inst) {
            $(this).val(month + "/" + year);
        }  
     }); 
	$("#patient_id").select2();			
} );
</script>
<div class="panel-body"><!--PANEL BODY DIV START-->
	<form name="dispatch_medicine_form" action="" method="post" class="form-horizontal" id="dispatch_medicine_form">
			 <?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
			<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
			<input type="hidden" name="dispatch_id" value="<?php if(isset($_REQUEST['dispatch_id'])) echo esc_attr($_REQUEST['dispatch_id']);?>"/>
			
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="patient"><?php esc_html_e('Patient','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">				
						<?php if($edit){ $patient_id1=$result->patient; }elseif(isset($_POST['patient'])){$patient_id1=$_POST['patient'];}else{ $patient_id1="";}?>
						<select name="patient" class="form-control" id="patient_id" <?php if($edit) print 'disabled="disabled"'; ?> >
						<option value=""><?php esc_html_e('Select Patient','hospital_mgt');?></option>
						<?php 					
							$patients = MJ_hmgt_patientid_list();
							if(!empty($patients)){
							foreach($patients as $patient){
								echo '<option value="'.$patient['id'].'" '.selected($patient_id1,$patient['id']).'>'.$patient['patient_id'].' - '.$patient['first_name'].' '.$patient['last_name'].'</option>';
							} } ?>
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
								<input type="hidden"  name="madicine_id[]" value="<?php print esc_attr($singgle_madicine->medicine_id) ?>">
								<input type="hidden"  class="madicine_quantity_<?php print esc_attr($i);?>" name="madicine_quantity" value="<?php print esc_attr($singgle_madicine->med_quantity+$val->qty) ?>">
								<input type="hidden"  class="" name="old_quantity[]" value="<?php print esc_attr($val->qty) ?>">
								<div class="col-sm-2 margin_bottom_5px">
									<input type="text" name="madicine_title[]" class="form-control" value="<?php print esc_attr($singgle_madicine->medicine_name); ?> " readonly>
								</div>
								<div class="col-sm-1 margin_bottom_5px">
									<input id="qty_<?php print esc_attr($i);?>" class="days form-control validate[required] medicineqty_<?php print esc_attr($singgle_madicine->medicine_id) ?>" dataid="<?php esc_attr(print $singgle_madicine->medicine_id) ?>" counter="<?php print esc_attr($i);?>" class="form-control" type="number" min="0" value="<?php print esc_attr($val->qty) ?>" name="qty[]">
								</div>
								<div class="col-sm-1 margin_bottom_5px">
									<input id="price_<?php print esc_attr($i);?>"  class="med_price form-control" type="text" value="<?php  print esc_attr($val->price); ?>" name="price[]" readonly>
								</div>	
								<div class="col-sm-1 margin_bottom_5px">
									<input id="discount_value_<?php print esc_attr($i);?>" dataid="<?php print esc_attr($singgle_madicine->medicine_id) ?>" onKeyPress="if(this.value.length==10) return false;" step="0.01" class="med_discount_value form-control" type="number" value="<?php print esc_attr($val->discount_value) ?>" name="discount_value[]" counter="<?php print esc_attr($i);?>">
								</div>	
								<div class="col-sm-1 margin_bottom_5px">
									<select class="form-control" id="med_discount_in_<?php print esc_attr($i);?>" name="med_discountin[]" disabled>
										<option value="flat" <?php selected($val->med_discount_in,'flat'); ?>><?php esc_html_e('Flat','hospital_mgt');?></option>
										<option value="percentage" <?php selected($val->med_discount_in,'percentage'); ?>>%</option>
									</select>
									<input type="hidden" name="med_discount_in[]" value="<?php echo $val->med_discount_in; ?>">
								</div>
								<div class="col-sm-1 margin_bottom_5px">
									<input id="discount_<?php print $i;?>"  class="med_discount form-control" type="text" value="<?php  print esc_attr($val->discount_amount); ?>" name="discount_amount[]" readonly>
								</div>	
								<div class="col-sm-1">
									<input id="tax_<?php print esc_attr($i);?>"  class="tax_amount form-control" type="text" value="<?php  print esc_attr($val->tax_amount); ?>" name="tax_amount[]" readonly>
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
						value="<?php if($edit) print $result->med_price; ?>" name="med_price" readonly>
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
						<input id="sub_total" class="form-control  text-input" type="text" value="<?php  if($edit) print $result->sub_total; ?>" name="sub_total" readonly>
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
	</form>
</div><!--PANEL BODY DIV END-->