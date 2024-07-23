<?php
MJ_hmgt_browser_javascript_check();
//Add Treatment
$edit=0;
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
{
	$edit = 1;
	$treatment_id = $_REQUEST['treatment_id'];
	$result = $obj_treatment->MJ_hmgt_get_single_treatment($treatment_id);
}
?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
	"use strict";
	<?php
	if (is_rtl())
		{
		?>	
			$('#treatment_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#treatment_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
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
} );
</script>
   <!--PANEL BODY START-->
    <div class="panel-body">
		<form name="treatment_form" action="" method="post" class="form-horizontal" id="treatment_form">
			 <?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
			<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
			<input type="hidden" name="treatment_id" value="<?php if(isset($_REQUEST['treatment_id'])) echo esc_attr($_REQUEST['treatment_id']);?>"  />
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="med_category_name"><?php esc_html_e('Treatment Name','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="treatment_name" class="form-control validate[required,custom[popup_category_validation]]  text-input" maxlength="50" type="text" 
						value="<?php if($edit){ echo esc_attr($result->treatment_name);}elseif(isset($_POST['treatment_name'])) echo esc_attr($_POST['treatment_name']);?>" name="treatment_name">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="treatment_price"><?php esc_html_e('Treatment Price','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' )); ?>)</label>
					<div class="col-sm-8">
						<input id="treatment_price" class="form-control" type="number" min="0" onKeyPress="if(this.value.length==10) return false;" step="0.01"  value="<?php if($edit){ echo esc_attr($result->treatment_price);}elseif(isset($_POST['treatment_price'])) echo esc_attr($_POST['treatment_price']);?>" name="treatment_price">
					</div>
				</div>
			</div>
			<?php wp_nonce_field( 'save_treatment_nonce' ); ?>
			<div class="form-group margin_bottom_5px">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="visiting_fees"><?php esc_html_e('Tax','hospital_mgt');?></label>
					<div class="col-sm-2">
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
			<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
				<input type="submit" value="<?php if($edit){ esc_html_e('Save','hospital_mgt'); }else{ esc_html_e('Save','hospital_mgt');}?>" name="save_treatment" class="btn btn-success"/>
			</div>
		</form>
    </div> <!-- END PANEL BODY-->