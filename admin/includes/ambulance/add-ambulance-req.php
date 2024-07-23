<?php
	MJ_hmgt_browser_javascript_check();
	$role='patient';
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	"use strict";
	<?php
	if (is_rtl())
		{
		?>	
			$('#patient_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#patient_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>
	$('#request_time').timepicki(
	{
		show_meridian:false,
		min_hour_value:0,
		max_hour_value:23,
		step_size_minutes:15,
		overflow_minutes:true,
		increase_direction:'up',
		disable_keyboard_mobile: true}
		);
	$('#dispatch_time').timepicki(
	{
		show_meridian:false,
		min_hour_value:0,
		max_hour_value:23,
		step_size_minutes:15,
		overflow_minutes:true,
		increase_direction:'up',
		disable_keyboard_mobile: true}
		);
	
   var date = new Date();
         date.setDate(date.getDate()-0);
        $.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
         $('.request_date').datepicker({
        startDate: date,
         autoclose: true
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
		$('#patient_id').select2();
} );
</script>
    <?php 	
	if($active_tab == 'add_ambulance_req')	
	{
		$edit=0;
		if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
		{			
			$edit=1;
			$result= $obj_ambulance->MJ_hmgt_get_single_ambulance_req($_REQUEST['amb_req_id']);	
		}
		?>	
        <div class="panel-body"><!-- PANEL BODY DIV START-->
			<form name="patient_form" action="" method="post" class="form-horizontal" id="patient_form">
				 <?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
				<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
				<input type="hidden" name="amb_req_id" value="<?php if(isset($_REQUEST['amb_req_id']))echo esc_attr($_REQUEST['amb_req_id']);?>"  />
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-sm-2 control-label form-label" for="ambulance_id"><?php esc_html_e('Ambulance','hospital_mgt');?><span class="require-field">*</span></label>
						<div class="col-sm-8">
							<select name="ambulance_id" class="form-control validate[required] max_width_100" id="ambulance_id">
								<option value=""><?php esc_html_e('Select Ambulance','hospital_mgt');?></option>
								<?php 
								if($edit)
								{
									$amb_id = $result->ambulance_id;
								}
								elseif(isset($_REQUEST['ambulance_id']))
								{
									$amb_id = $_REQUEST['ambulance_id'];
								}
								else 
								{								
									$amb_id = "";
								}
									$ambulance_data=$obj_ambulance->MJ_hmgt_get_all_ambulance();
									if(!empty($ambulance_data))
									{
										foreach ($ambulance_data as $retrieved_data)
										{ 
											echo '<option value = '.$retrieved_data->amb_id.' '.selected($amb_id,$retrieved_data->amb_id).'>'.$retrieved_data->ambulance_id.'</option>';
										}
									}						
								 ?>
							</select>						
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-sm-2 control-label form-label" for="patient_id"><?php esc_html_e('Patient','hospital_mgt');?></label>
						<div class="col-sm-8">
							<select name="patient_id" id="patient_id" class="form-control patient_address max_width_100">
								<option><?php esc_html_e('Select Patient','hospital_mgt');?></option>
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
								$patients = MJ_hmgt_patientid_list();
								
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
						<label class="col-sm-2 control-label form-label" for="address"><?php esc_html_e('Address','hospital_mgt');?><span class="require-field">*</span></label>
						<div class="col-sm-8">
							<textarea name = "address" id="address" maxlength="150" class="form-control validate[required,custom[address_description_validation]]"><?php if($edit){ echo esc_textarea($result->address);}elseif(isset($_POST['address'])) echo esc_textarea($_POST['address']);?></textarea>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-sm-2 control-label form-label" for="charges"><?php esc_html_e('Charges','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' )); ?>)<span class="require-field">*</span></label>
						<div class="col-sm-8">
							<input id="charges" class="form-control validate[required]" min="0" type="number" onKeyPress="if(this.value.length==8) return false;" step="0.01"  value="<?php if($edit){ echo esc_attr($result->charge);}elseif(isset($_POST['charge'])) echo esc_attr($_POST['charge']);?>" name="charge">
						</div>
					</div>
				</div>	
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
										<option value="<?php echo esc_attr($entry->tax_id); ?>" <?php echo esc_attr($selected); ?> ><?php echo esc_html($entry->tax_title);?>-<?php echo esc_html($entry->tax_value);?></option>
									<?php 
									}
								}
								?>
							</select>		
						</div>
					</div>
				</div>		
				<?php wp_nonce_field( 'save_ambulance_request_nonce' ); ?>
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-sm-2 control-label form-label" for="request_date"><?php esc_html_e('Request Date','hospital_mgt');?><span class="require-field">*</span></label>
						<div class="col-sm-8">
							<input id="request_date" class="form-control validate[required] request_date" type="text"   value="<?php if($edit){ echo date(MJ_hmgt_date_formate(),strtotime($result->request_date)) ;}elseif(isset($_POST['request_date'])) echo esc_attr($_POST['request_date']);?>" name="request_date">
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-sm-2 control-label form-label" for="request_time"><?php esc_html_e('Request Time','hospital_mgt');?></label>
						<div class="col-sm-8">
							<input id="request_time" class="form-control request_time" 
							type="text"  value="<?php if($edit){ echo esc_attr($result->request_time);}elseif(isset($_POST['request_time'])) echo esc_attr($_POST['request_time']);?>" name="request_time">
						</div>
					</div>
				</div>
				<div class="form-group margin_bottom_5px">
					<div class="mb-3 row">	
						<label class="col-sm-2 control-label form-label" for="dispatch_time"><?php esc_html_e('Dispatch Time','hospital_mgt');?></label>
						<div class="col-sm-8">
							<input id="dispatch_time" class="form-control dispatch_time"  data-default-time="02:25" type="text"  value="<?php if($edit){ echo esc_attr($result->dispatch_time);}elseif(isset($_POST['dispatch_time'])) echo esc_attr($_POST['dispatch_time']);?>" name="dispatch_time">
						</div>
					</div>
				</div>
				<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
					<input type="submit" value="<?php if($edit){ esc_html_e('Save','hospital_mgt'); }else{ esc_html_e('Save','hospital_mgt');}?>" name="save_ambulance_request" class="btn btn-success"/>
				</div>
			</form>
        </div><!-- PANEL BODY DIV END-->        
    <?php 
	}
	?>