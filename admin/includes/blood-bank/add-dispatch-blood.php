<?php
$obj_bloodbank=new MJ_hmgt_bloodbank();
?>
<script type="text/javascript">
jQuery(document).ready(function($)
{
	"use strict";
	<?php
	if (is_rtl())
		{
		?>	
			$('#dispatch_blood_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#dispatch_blood_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>
	$.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
    $('#date').datepicker({
		endDate: '+0d',
        autoclose: true,
	}); 
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

	$('#patient').select2();

	$("body").on("click", ".save_dispatch_blood", function()
	{
		var patient_name = $("#patient");
		if (patient_name.val() == "") {
			alert("<?php esc_html_e('Please select a patient','hospital_mgt');?>");
			return false;
		}
		return true;
	});
} );
</script>
    <?php 	
	if($active_tab == 'adddispatchblood')	
	{
		MJ_hmgt_browser_javascript_check();
		$edit=0;
		if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
		{
			$edit=1;
			$result = $obj_bloodbank->MJ_hmgt_get_single_dispatch_blood_data($_REQUEST['dispatchblood_id']);	
		}
		?>
    <div class="panel-body"><!-- PANEL BODY DIV START-->
        <form name="dispatch_blood_form" action="" method="post" class="form-horizontal" id="dispatch_blood_form">
			 <?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
			<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
			<input type="hidden" name="old_blood_group" value="<?php if($edit){ echo esc_attr($result->blood_group); } ?>">
			<input type="hidden" name="old_blood_status" value="<?php if($edit){ echo esc_attr($result->blood_status); } ?>">
			<input type="hidden" name="dispatchblood_id" value="<?php if(isset($_REQUEST['dispatchblood_id'])) echo esc_attr($_REQUEST['dispatchblood_id']);?>"  />		
			
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="patient"><?php esc_html_e('Patient','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<select name="patient_id" id="patient" class="form-control  max_width_100">
							<option value=""><?php esc_html_e('Select Patient','hospital_mgt');?></option>
							<?php 
							if($edit)
							{
								$patient_id1 = $result->patient_id;
							}							
							elseif(isset($_REQUEST['patient_id']))
							{
								$patient_id1 = $_REQUEST['patient_id'];
							}
							else 
							{
								$patient_id1 = "";
							}
							
							$patients = MJ_hmgt_inpatient_list();
							
							if(!empty($patients))
							{
								foreach($patients as $patient)
								{
									echo '<option value="'.$patient['id'].'" '.selected($patient_id1,$patient['id']).'>'.$patient['patient_id'].' - '.$patient['first_name'].' '.$patient['last_name'].'</option>';
								}
							}
							?>
						</select>
					</div>	
				</div>			
			</div>
			<div class="form-group">
				<div class="mb-3 row">	 
					<label class="col-sm-2 control-label form-label" for="bloodgruop"><?php esc_html_e('Blood Group','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<?php if($edit){ $userblood=$result->blood_group; }elseif(isset($_POST['blood_group'])){$userblood=$_POST['blood_group'];}else{$userblood='';}?>
						<select id="blood_group" class="form-control validate[required] max_width_100 selected_blood_group" name="blood_group">
						<option value=""><?php esc_html_e('Select Blood Group','hospital_mgt');?></option>
						<?php foreach(MJ_hmgt_blood_group() as $blood){ ?>
								<option value="<?php echo esc_attr($blood);?>" <?php selected($userblood,$blood);  ?>><?php echo esc_html($blood); ?> </option>
						<?php } ?>
						</select>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="blood_status"><?php esc_html_e('Number Of Bags','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="blood_status" class="form-control validate[required] text-input dispatch_blood_status_check" type="number" min="1" onKeyPress="if(this.value.length==1) return false;" value="<?php if($edit){ echo esc_attr($result->blood_status);}elseif(isset($_POST['blood_status'])) echo esc_attr($_POST['blood_status']);?>" name="blood_status">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for=""><?php esc_html_e('Charge','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' )); ?>)<span class="require-field">*</span></label>
					<div class="col-sm-8">				
						<input id="" class="form-control validate[required]" min="0" type="number" onKeyPress="if(this.value.length==8) return false;"  step="0.01" value="<?php if($edit){ echo esc_attr($result->blood_charge);}elseif(isset($_POST['blood_charge'])) echo esc_attr($_POST['blood_charge']);?>" name="blood_charge">				
					</div>
				</div>
			</div>
			<?php wp_nonce_field( 'save_dispatch_blood_nonce' ); ?>
			<div class="form-group">
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
			<div class="form-group margin_bottom_5px">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="last_donet_date"><?php esc_html_e('Date','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="date" class="form-control validate[required]" type="text"  value="<?php if($edit){ echo date(
				   MJ_hmgt_date_formate(),strtotime($result->date));}elseif(isset($_POST['date'])) echo esc_attr($_POST['date']);?>" name="date" autocomplete="off">
					</div>
				</div>
			</div>			
			<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
				<input type="submit" value="<?php if($edit){ esc_html_e('Save','hospital_mgt'); }else{ esc_html_e('Save','hospital_mgt');}?>" name="save_dispatch_blood" class="btn btn-success save_dispatch_blood"/>
			</div>
	    </form>
    </div><!-- PANEL BODY DIV END-->
<?php 
 }
?>