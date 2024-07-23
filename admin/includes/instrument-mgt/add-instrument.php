<?php
MJ_hmgt_browser_javascript_check(); 
//Add bed
$obj_instrument = new MJ_hmgt_Instrumentmanage();
$edit = 0;
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
{
	$edit = 1;
	$instumrnt_id = $_REQUEST['instumrnt_id'];
	$result = $obj_instrument->MJ_hmgt_get_single_instrument($instumrnt_id);
}
?>
<script>
jQuery(document).ready(function($)
{
	"use strict";
	<?php
	if (is_rtl())
		{
		?>	
			$('#instrument_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#instrument_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>
	$('#tax_charge').multiselect(
	{
		nonSelectedText :'<?php esc_html_e('Select Tax','hospital_mgt');?>',
		includeSelectAllOption: true,
	    selectAllText : '<?php esc_html_e('Select all','hospital_mgt'); ?>',
		templates: {
            button: '<button class="multiselect btn btn-default dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="multiselect-selected-text"></span><b class="caret"></b></button>',
        },
		buttonContainer: '<div class="dropdown" />'
	});
});
</script>
<div class="panel-body"><!-- PANEL BODY DIV START -->		
	<form name="bed_form" action="" method="post" class="form-horizontal" id="instrument_form">
			<?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
			<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
			<input type="hidden" name="instrument_id" value="<?php if(isset($_REQUEST['instumrnt_id'])) echo esc_attr($_REQUEST['instumrnt_id']);?>"  />
			<div class="col-sm-6 min_height_400 float_left">
				<fieldset>
					<legend><?php esc_html_e('Instrument Info:','hospital_mgt'); ?></legend>
						<div class="form-group padding_top_20_saf">
							<div class="mb-3 row">	
								<label class="col-sm-3 control-label form-label" for="instrument_code"><?php esc_html_e('Instrument Code','hospital_mgt');?><span class="require-field">*</span></label>
								<div class="col-sm-8">
									<input id="instrument_code" class="form-control validate[required] text-input" min="0" type="number" onKeyPress="if(this.value.length==8) return false;" 
									value="<?php if($edit){ echo esc_attr($result->instrument_code);}elseif(isset($_POST['instrument_code'])) echo esc_attr($_POST['instrument_code']);?>" name="instrument_code">
								</div>
							</div>
						</div>
			
						<div class="form-group">
							<div class="mb-3 row">
								<label class="col-sm-3 control-label form-label" for="instrument_name"><?php esc_html_e('Instrument Name','hospital_mgt');?><span class="require-field">*</span></label>
								<div class="col-sm-8">
									<input id="instrument_name" class="form-control  validate[required,custom[popup_category_validation]]" type="text"  maxlength="50"
									value="<?php if($edit){ echo esc_attr($result->instrument_name);}elseif(isset($_POST['instrument_name'])) echo esc_attr($_POST['instrument_name']);?>" name="instrument_name">
								</div>
							</div>
						</div>	
			
						<div class="form-group">
							<div class="mb-3 row">
								<label class="col-sm-3 control-label form-label" for="charge_type"><?php esc_html_e('Charges Type','hospital_mgt');?><span class="require-field">*</span></label>
								<div class="col-sm-8">
								<?php $charge_type = "Daily"; if($edit){ $charge_type=$result->charge_type; }elseif(isset($_POST['charge_type'])) {$charge_type=$_POST['charge_type'];}?>
									<label class="radio-inline">
									 <input type="radio" value="Daily" class="tog" name="charge_type"  <?php  checked( 'Daily', $charge_type);  ?>/><?php esc_html_e('Daily','hospital_mgt');?>
									</label>
									<label class="radio-inline">
									  <input type="radio" value="Hourly" class="tog" name="charge_type"  <?php  checked( 'Hourly', $charge_type);  ?>/><?php esc_html_e('Hourly','hospital_mgt');?> 
									</label>
								</div>
							</div>
						</div>
			
					<div class="form-group">
						<div class="mb-3 row">
							<label class="col-sm-3 control-label form-label" for="instrument_charge"><?php esc_html_e('Instrument charge','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' )); ?>)<span class="require-field">*</span></label>
							<div class="col-sm-8">
								<input id="instrument_charge" class="form-control validate[required] " min="0" type="number" onKeyPress="if(this.value.length==8) return false;"  step="0.01"
								value="<?php if($edit){ echo $result->instrument_charge;}elseif(isset($_POST['instrument_charge'])) echo $_POST['instrument_charge'];?>" name="instrument_charge">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">
							<label class="col-sm-3 control-label form-label" for=""><?php esc_html_e('Tax','hospital_mgt');?></label>
							<div class="col-sm-8">
								<select  class="form-control" id="tax_charge" name="tax[]" multiple="multiple">					
									<?php					
								if($edit)
								{
									$tax_id=explode(',',$result->tax);
								}
								else
								{	
									$tax_id[]='';
								}
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
							<label class="col-sm-3 control-label form-label" for="instrument_description"><?php esc_html_e('Description','hospital_mgt');?></label>
							<div class="col-sm-8">
								<textarea id="instrument_description" class="form-control validate[custom[address_description_validation]]" maxlength="150"  name="instrument_description"><?php if($edit){ echo esc_textarea($result->instrument_description);}elseif(isset($_POST['instrument_description'])) echo esc_textarea($_POST['instrument_description']);?></textarea>
							</div>
						</div>
					</div>		
		        </fieldset>
	        </div>
			<div class="col-sm-6 min_height_400 float_left">
			    <fieldset>
					<legend class="legend"><?php esc_html_e('Firm Info:','hospital_mgt'); ?></legend>
						<div class="form-group padding_top_20_saf">
							<div class="mb-3 row">
								<label class="col-sm-3 control-label form-label" for="code"><?php esc_html_e('Code','hospital_mgt');?></label>
								<div class="col-sm-8">
									<input id="code" class="form-control text-input" min="0" type="number" onKeyPress="if(this.value.length==8) return false;" 
									value="<?php if($edit){ echo $result->code;}elseif(isset($_POST['code'])) echo esc_attr($_POST['code']);?>" name="code">
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<div class="mb-3 row">
								<label class="col-sm-3 control-label form-label" for="name"><?php esc_html_e('Name','hospital_mgt');?></label>
								<div class="col-sm-8">
									<input id="name" class="form-control text-input validate[custom[popup_category_validation]]" type="text" maxlength="30"
									value="<?php if($edit){ echo esc_attr($result->name);}elseif(isset($_POST['name'])) echo esc_attr($_POST['name']);?>" name="name">
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<div class="mb-3 row">
								<label class="col-sm-3 control-label form-label" for="address"><?php esc_html_e('Address','hospital_mgt');?></label>
								<div class="col-sm-8">
									<textarea id="address" class="form-control validate[custom[address_description_validation]]" maxlength="150" name="address" cols="29"><?php if($edit) print esc_textarea($result->address); ?></textarea>
								</div>
							</div>
						</div>	
						
						<div class="form-group">
							<div class="mb-3 row">
								<label class="col-sm-3 control-label form-label" for="contact"><?php esc_html_e('Contact','hospital_mgt');?></label>
								<div class="col-sm-8">
									<input id="contact" class="form-control validate[custom[phone_number]] text-input" minlength="6" maxlength="15" type="text" value="<?php if($edit){ echo esc_attr($result->contact);}elseif(isset($_POST['contact'])) echo esc_attr($_POST['contact']);?>" name="contact">									
								</div>
							</div>
						</div>			
				</fieldset>
			</div>
	        <div class="col-sm-6 min_height_280 float_right rtl_res_design_set">
	            <fieldset>
					<legend class="legend_asset"><?php esc_html_e('Asset Info:','hospital_mgt'); ?></legend>
					<div class="form-group padding_top_20_saf padding_left">
						<div class="mb-3 row asset_info_sub_div_padding">
							<label class="col-sm-3 control-label form-label" for="description"><?php esc_html_e('Description','hospital_mgt');?></label>
							<div class="col-sm-8 marging_right_instrument">
								<textarea name="description"  maxlength="150" class="form-control validate[custom[address_description_validation]]"  ><?php if($edit) print esc_textarea($result->description); ?></textarea>
							</div>
						</div>
					</div>	
					
					<div class="form-group padding_left">
						<div class="mb-3 row asset_info_sub_div_padding">
							<label class="col-sm-3 control-label form-label" for="quantity"><?php esc_html_e('Quantity','hospital_mgt');?></label>
							<div class="col-sm-8 marging_right_instrument">
								<input id="quantity" class="form-control  text-input" min="0"  type="number" onKeyPress="if(this.value.length==4) return false;" 
								value="<?php if($edit){ echo esc_attr($result->quantity);}elseif(isset($_POST['quantity'])) echo esc_attr($_POST['quantity']);?>" name="quantity">
							</div>
						</div>
					</div>
					
					<div class="form-group padding_left">
						<div class="mb-3 row asset_info_sub_div_padding">
							<label class="col-sm-3 control-label form-label" for="price"><?php esc_html_e('Price','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' )); ?>)</label>
							<div class="col-sm-8 marging_right_instrument">
								<input id="price" class="form-control text-input" min="0" type="number" onKeyPress="if(this.value.length==8) return false;" step="0.01" value="<?php if($edit){ echo esc_attr($result->price);}elseif(isset($_POST['price'])) echo esc_attr($_POST['price']);?>" name="price">
							</div>
						</div>
					</div>
					
					<div class="form-group padding_left">
						<div class="mb-3 row asset_info_sub_div_padding">
							<label class="col-sm-3 control-label form-label" for="class"><?php esc_html_e('Class','hospital_mgt');?></label>
							<div class="col-sm-8 marging_right_instrument">
								<input id="class" class="form-control text-input validate[custom[popup_category_validation]]" type="text" maxlength="30"
								value="<?php if($edit){ echo esc_attr($result->class);}elseif(isset($_POST['class'])) echo esc_attr($_POST['class']);?>" name="class">
							</div>
						</div>
					</div>			
				</fieldset>
	        </div>
			<?php wp_nonce_field( 'save_instrument_nonce' ); ?>
	        <div class="col-sm-6 min_height_280 float_left">
	            <fieldset>
					<legend class="legend"><?php esc_html_e('Invoice Info:','hospital_mgt');?></legend>
					<div class="form-group padding_top_20_saf ">
						<div class="mb-3 row">
							<label class="col-sm-3 control-label form-label" for="serial"><?php esc_html_e('Serial','hospital_mgt');?></label>
							<div class="col-sm-8 marging_left_right_instrument">
								<input id="serial" class="form-control  text-input" min="0" type="number" onKeyPress="if(this.value.length==6) return false;" value="<?php if($edit){ echo esc_attr($result->serial);}elseif(isset($_POST['serial'])) echo esc_attr($_POST['serial']);?>" name="serial">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">
							<label class="col-sm-3 control-label form-label" for="acquire"><?php esc_html_e('Acquire','hospital_mgt');?></label>
							<div class="col-sm-8 marging_left_right_instrument">
								<input id="acquire" class="form-control  text-input" min="0" type="number" onKeyPress="if(this.value.length==6) return false;" value="<?php if($edit){ echo esc_attr($result->acquire);}elseif(isset($_POST['acquire'])) echo esc_attr($_POST['acquire']);?>" name="acquire">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">
							<label class="col-sm-3 control-label form-label" for="asset_id"><?php esc_html_e('Asset ID','hospital_mgt');?></label>
							<div class="col-sm-8 marging_left_right_instrument">
								<input id="asset_id" class="form-control  text-input" min="0" type="number" onKeyPress="if(this.value.length==6) return false;" value="<?php if($edit){ echo esc_attr($result->asset_id);}elseif(isset($_POST['asset_id'])) echo esc_attr($_POST['asset_id']);?>" name="asset_id">
							</div>
						</div>
					</div>				
	            </fieldset>
	        </div>
			<div class="offset-sm-1 col-lg-2 col-md-2 col-sm-6 col-xs-6">
				<input type="submit" value="<?php if($edit){ esc_html_e('Save ','hospital_mgt'); }else{ esc_html_e('Save','hospital_mgt');}?>" name="save_instrument" class="btn btn-success patient_btn1 save_instrument"/>
			</div>
	</form>
</div><!-- PANEL BODY DIV END -->		