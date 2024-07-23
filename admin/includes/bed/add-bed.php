<?php
MJ_hmgt_browser_javascript_check();
//Add bed
$edit = 0;
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
{
	$edit = 1;
	$bed_type_id = $_REQUEST['bed_id'];
	$result = $obj_bed->MJ_hmgt_get_single_bed($bed_type_id);
}	
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	"use strict";
	<?php
	if (is_rtl())
		{
		?>	
			$('#bed_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#bed_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
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
<div class="panel-body"><!-- PANEL BODY DIV START-->
	<form name="bed_form" action="" method="post" class="form-horizontal" id="bed_form">
		<?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
		<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
		<input type="hidden" name="bed_id" value="<?php if(isset($_REQUEST['bed_id'])) echo esc_attr($_REQUEST['bed_id']);?>"  />
		<div class="form-group">
			<div class="mb-3 row">
				<label class="col-sm-2 control-label form-label" for="bed_type_id"><?php esc_html_e('Select Bed Category','hospital_mgt');?><span class="require-field">*</span></label>
				<div class="col-sm-8 margin_bottom_5px">
				<?php 
					if(isset($_REQUEST['bed_type_id']))
					{
						$bed_type1 = $_REQUEST['bed_type_id'];
					}
					elseif($edit)
					{
						$bed_type1 = $result->bed_type_id;
					}
					else
					{					
						$bed_type1 = "";
					}
					?>
					<select name="bed_type_id" class="form-control validate[required] max_width_100" id="bedtype">
						<option value = ""><?php esc_html_e('Select Bed Category','hospital_mgt');?></option>
						<?php 
						$bedtype_data=$obj_bed->MJ_hmgt_get_all_bedtype();
						if(!empty($bedtype_data))
						{
							foreach ($bedtype_data as $retrieved_data)
							{
								echo '<option value="'.$retrieved_data->ID.'" '.selected($bed_type1,$retrieved_data->ID).'>'.$retrieved_data->post_title.'</option>';
							}
						}
						?>
					</select>
				</div>
				<div class="col-sm-2"><button id="addremove" model="bedtype"><?php esc_html_e('Add Or Remove','hospital_mgt');?></button></div>
			</div>
		</div>
		<div class="form-group">
			<div class="mb-3 row">
				<label class="col-sm-2 control-label form-label" for="bed_number"><?php esc_html_e('Bed Number','hospital_mgt');?><span class="require-field">*</span></label>
				<div class="col-sm-8">
					<input id="bed_number" class="form-control validate[required,custom[popup_category_validation]] text-input" maxlength="10" type="text"  value="<?php if($edit){ echo esc_attr($result->bed_number);}elseif(isset($_POST['bed_number'])) echo esc_attr($_POST['bed_number']);?>" name="bed_number">
				</div>
			</div>
		</div>
		
		<div class="form-group">
			<div class="mb-3 row">
				<label class="col-sm-2 control-label form-label" for="bed_charges"><?php esc_html_e('Charges','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' )); ?>)<span class="require-field">*</span></label>
				<div class="col-sm-8">
					<input id="bed_charges" class="form-control validate[required]" min="0" type="number" onKeyPress="if(this.value.length==8) return false;" step="0.01"
					value="<?php if($edit){ echo esc_attr($result->bed_charges);}elseif(isset($_POST['bed_charges'])) echo esc_attr($_POST['bed_charges']);?>" name="bed_charges">
				</div>
				<div class="col-sm-2 padding_left_0 add_bed_1">
				<?php esc_html_e('/ Per Day','hospital_mgt');?>
				</div>
			</div>
		</div>
		<?php wp_nonce_field( 'save_bed_nonce' ); ?>
		<div class="form-group">
			<div class="mb-3 row">
				<label class="col-sm-2 control-label form-label" for="visiting_fees"><?php esc_html_e('Tax','hospital_mgt');?></label>
				<div class="col-sm-2">
					<select  class="form-control max_width_100" id="tax_charge" name="tax[]" multiple="multiple">			
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
								<option value="<?php echo esc_attr($entry->tax_id); ?>" <?php echo esc_attr($selected); ?> ><?php echo esc_attr($entry->tax_title);?>-<?php echo esc_attr($entry->tax_value);?></option>
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
				<label class="col-sm-2 control-label form-label" for="bed_location"><?php esc_html_e('Location','hospital_mgt');?></label>
				<div class="col-sm-8">
					<textarea id="bed_location" class="form-control validate[custom[address_description_validation]]"  maxlength="150" name="bed_location"><?php if($edit){ echo esc_attr($result->bed_location);}elseif(isset($_POST['bed_location'])) echo esc_attr($_POST['bed_location']);?></textarea>
				</div>
			</div>
		</div>
		<div class="form-group margin_bottom_5px">
			<div class="mb-3 row">
				<label class="col-sm-2 control-label form-label" for="bed_description"><?php esc_html_e('Description','hospital_mgt');?></label>
				<div class="col-sm-8">
					<textarea id="bed_description" class="form-control validate[custom[address_description_validation]]"  maxlength="150" name="bed_description"><?php if($edit){ echo esc_attr($result->bed_description);}elseif(isset($_POST['bed_description'])) echo esc_attr($_POST['bed_description']);?></textarea>
				</div>
			</div>
		</div>
		<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
			<input type="submit" value="<?php if($edit){ esc_html_e('Save','hospital_mgt'); }else{ esc_html_e('Save','hospital_mgt');}?>" name="save_bed" class="btn btn-success"/>
		</div>
	</form>
</div><!-- PANEL BODY DIV END-->
<?php 
?>