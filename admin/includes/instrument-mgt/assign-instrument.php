<?php
MJ_hmgt_browser_javascript_check();
//Add bed
$obj_instrument = new MJ_hmgt_Instrumentmanage();
$edit = 0;
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && $_REQUEST['tab']=='assigninstrument')
{
	$edit = 1;
	$assign_instument_id = $_REQUEST['assign_instument_id'];
	$result = $obj_instrument->MJ_hmgt_get_single_assigned_instrument($assign_instument_id);
	
}?>
<script type="text/javascript">
jQuery(document).ready(function()
 {
	"use strict";
	<?php
	if (is_rtl())
		{
		?>	
			jQuery('#assign_instrument_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			jQuery('#assign_instrument_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>
	jQuery("#patient_id").select2();		
	jQuery('.start_time').timepicki(
		{
			show_meridian:false,
			min_hour_value:0,
			max_hour_value:23,
			step_size_minutes:15,
			overflow_minutes:true,
			increase_direction:'up',
			disable_keyboard_mobile: true
		});	
		jQuery('.end_time').timepicki(
	    {
		show_meridian:false,
		min_hour_value:0,
		max_hour_value:23,
		step_size_minutes:15,
		overflow_minutes:true,
		increase_direction:'up',
		disable_keyboard_mobile: true
		});	
	  
		var start = new Date();
		var end = new Date(new Date().setYear(start.getFullYear()+1));
		jQuery.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
		jQuery('#start_date').datepicker({
			startDate : start,
			endDate   : end,
			autoclose: true
		}).on('changeDate', function (selected) {
        var minDate = new Date(selected.date.valueOf());
        jQuery('#end_date').datepicker('setStartDate', minDate);
		}); 
		jQuery.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
		jQuery('#end_date').datepicker({
			startDate : start,
			endDate   : end,
			autoclose: true
		}).on('changeDate', function (selected) {
            var maxDate = new Date(selected.date.valueOf());
            jQuery('#start_date').datepicker('setEndDate', maxDate);
        });
		
		jQuery("body").on("click", ".assigned_instrument_validation", function()
		{
            var patient_name = jQuery("#patient_id");
			
            if (patient_name.val() == "") {
                //If the "Please Select" option is selected display error.
                alert("<?php esc_html_e('Please select a patient','hospital_mgt');?>");
                return false;
            }
            return true;
		});

} );
</script>
<div class="panel-body"><!-- PANEL BODY DIV START -->		
    <form name="assign_instrument_form" action="" method="post" class="form-horizontal" id="assign_instrument_form">
        <?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
		<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
		<input type="hidden" name="assign_instrument_id" value="<?php if(isset($_REQUEST['assign_instument_id'])) echo esc_attr($_REQUEST['assign_instument_id']);?>"  />
		<div class="form-group">
			<div class="mb-3 row">
				<label class="col-sm-2 control-label form-label" for="patient"><?php esc_html_e('Patient','hospital_mgt');?><span class="require-field">*</span></label>
				<div class="col-sm-8">				
					<?php if($edit){ $patient_id1=$result->patient_id; }elseif(isset($_POST['patient'])){$patient_id1=$_POST['patient'];}else{ $patient_id1="";}?>
					<select name="patient_id" class="form-control max_width_100" id="patient_id">
					<option value=""><?php esc_html_e('Select Patient','hospital_mgt');?></option>
					<?php 					
						$patients = MJ_hmgt_patientid_list();
						//print_r($patient);
						if(!empty($patients))
						{
							foreach($patients as $patient)
							{
								echo '<option value="'.$patient['id'].'" '.selected($patient_id1,$patient['id']).'>'.$patient['patient_id'].' - '.$patient['first_name'].' '.$patient['last_name'].'</option>';
							} 
						} ?>
					</select>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="mb-3 row">
				<label class="col-sm-2 control-label form-label" for="patient"><?php esc_html_e('Instrument','hospital_mgt');?><span class="require-field">*</span></label>
				<div class="col-sm-8">				
					<?php if($edit){ $instrument_id=$result->instrument_id; }elseif(isset($_POST['instrument_id'])){$instrument_id=$_POST['instrument_id'];}else{ $instrument_id="";}?>
					<select name="instrument_id" class="form-control validate[required] max_width_100" id="instrument_id" <?php if($edit) print "disabled"; ?> >
					<option value=""><?php esc_html_e('Select Instrument','hospital_mgt');?></option>
					<?php 					
						$instrumentdata=$obj_instrument->MJ_hmgt_get_all_instrument();
						 if(!empty($instrumentdata))
						 {
							foreach ($instrumentdata as $retrieved_data)
							{ 
								echo '<option value="'.$retrieved_data->id.'" '.selected($instrument_id,$retrieved_data->id).'>'.$retrieved_data->instrument_code.' - '.$retrieved_data->instrument_name.'</option>';
							} 
						} ?>
					</select>
					<?php if($edit) print '<input type="hidden" name="instrument_id" value="'.$instrument_id.'">'; ?>
				</div>
			</div>
		</div>
		<?php if($edit==1 && $result->charge_type=='Daily'){ ?>
		<input id="charge_type"  type="hidden" value="Daily" name="charge_type">
		<div class="form-group">
			<div class="mb-3 row">
				<label class="col-sm-2 control-label form-label" for="facility_start_date">
				<?php esc_html_e('Start Date','hospital_mgt');?> <span class="require-field">*</span></label>
				<div class="col-sm-8">
					<input id="start_date" class="form-control validate[required] start" type="text"  
					value="<?php if($edit){ echo date(MJ_hmgt_date_formate(),strtotime($result->start_date));}elseif(isset($_POST['start_date'])) echo esc_attr($_POST['start_date']);?>"  name="start_date" autocomplete="off">
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="mb-3 row">
				<label class="col-sm-2 control-label form-label" for="end_date">
				<?php esc_html_e('Expected End Date','hospital_mgt');?> <span class="require-field">*</span></label>
				<div class="col-sm-8">
					<input id="end_date" class="form-control validate[required] end" type="text" value="<?php if($edit){ echo date(MJ_hmgt_date_formate(),strtotime($result->end_date));}elseif(isset($_POST['end_date'])) echo esc_attr($_POST['end_date']);?>"  name="end_date" autocomplete="off">
				</div>
			</div>
		</div>
		<?php } ?>
		<?php if($edit==1 && $result->charge_type=='Hourly'){ ?>
		<input id="charge_type"  type="hidden" value="Hourly" name="charge_type">
		<div class="form-group">
			<div class="mb-3 row">
				<label class="col-sm-2 control-label form-label" for="facility_start_date">
				<?php esc_html_e('Instrument Assign Date','hospital_mgt');?> <span class="require-field">*</span></label>
				<div class="col-sm-8">
					<input id="start_date" class="form-control validate[required] start_date" type="text"  
					
					value="<?php if($edit){ echo date(MJ_hmgt_date_formate(),strtotime($result->start_date));}elseif(isset($_POST['start_date'])) echo esc_attr($_POST['start_date']);?>"  name="start_date">
				</div>
			</div>
		</div>
		
		<div class="form-group">
			<div class="mb-3 row">
				<label class="col-sm-2 control-label form-label" for="facility_start_date">
				<?php esc_html_e('Start Time','hospital_mgt');?> <span class="require-field">*</span></label>
				<div class="col-sm-8">
					<input id="start_time" type="text" value="<?php if($edit){ echo esc_attr($result->start_time);}elseif(isset($_POST['start_time'])) echo esc_attr($_POST['start_time']);?>" class="form-control start_time validate[required]" name="start_time"/>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="mb-3 row">
				<label class="col-sm-2 control-label form-label" for="end_date">
				<?php esc_html_e('Expected End Time','hospital_mgt');?> <span class="require-field">*</span></label>
				<div class="col-sm-8">
					<input id="end_time" type="text" value="<?php if($edit){ echo esc_attr($result->end_time);}elseif(isset($_POST['end_time'])) echo esc_attr($_POST['end_time']);?>" class="form-control end_time  validate[required]" name="end_time"/>
				</div>
			</div>
		</div>
		<?php } ?>
		
		<div id="select_instrument_block">
		</div>
		<?php wp_nonce_field( 'assign_instrument_nonce' ); ?>
		<div class="form-group margin_bottom_5px">
			<div class="mb-3 row">
				<label class="col-sm-2 control-label form-label" for="description"><?php esc_html_e('Description','hospital_mgt');?></label>
				<div class="col-sm-8">
					<textarea id="description" maxlength="150" class="form-control validate[custom[address_description_validation]]"  name="description"><?php if($edit){ echo esc_textarea($result->description);}elseif(isset($_POST['description'])) echo esc_textarea($_POST['description']);?></textarea>
				</div>
			</div>
		</div>
		
		<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
        	<input type="submit" value="<?php if($edit){ esc_html_e('Save','hospital_mgt'); }else{ esc_html_e('Assign Instrument','hospital_mgt');}?>" name="assign_instrument" class="btn btn-success assigned_instrument_validation"/>
        </div>
      </form>
</div> <!-- PANEL BODY DIV END -->
<?php  ?>