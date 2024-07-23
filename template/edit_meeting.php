<?php
$meeting_data = $obj_virtual_appointment->MJ_hmgt_get_singal_meeting_data_in_zoom($_REQUEST['meeting_id']);
$appointment_data = $obj_appointment->MJ_hmgt_get_single_appointment($meeting_data->appointment_id);
$patient_data =	MJ_hmgt_get_user_detail_byid($appointment_data->patient_id);
$doctor_data =	MJ_hmgt_get_user_detail_byid($appointment_data->doctor_id);
?>
<script type="text/javascript">
$(document).ready(function() {
	$('#meeting_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
	$.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
	$("#start_date").datepicker({
		startDate: '+0d',
		autoclose: true
	});
} );
</script>
<div class="panel-body">   
        <form name="route_form" action="" method="post" class="form-horizontal" id="meeting_form">
        <?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
		<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
		<input type="hidden" name="meeting_id" value="<?php echo $_REQUEST['meeting_id'];?>">
		<input type="hidden" name="appointment_id" value="<?php echo $meeting_data->appointment_id;?>">
		<input type="hidden" name="patient_id" value="<?php echo $appointment_data->patient_id;?>">
		<input type="hidden" name="doctor_id" value="<?php echo $appointment_data->doctor_id;?>">
		<input type="hidden" name="appointment_time" value="<?php echo $appointment_data->appointment_time;?>">
		<input type="hidden" name="appointment_time_with_a" value="<?php echo $appointment_data->appointment_time_with_a;?>">
		<input type="hidden" name="department_id" value="<?php echo $appointment_data->department_id;?>">
		<input type="hidden" name="zoom_meeting_id" value="<?php echo $meeting_data->zoom_meeting_id;?>">
		<input type="hidden" name="uuid" value="<?php echo $meeting_data->uuid;?>">
		<input type="hidden" name="meeting_join_link" value="<?php echo $meeting_data->meeting_join_link;?>">
		<input type="hidden" name="meeting_start_link" value="<?php echo $meeting_data->meeting_start_link;?>">
        <div class="form-group">
        	<div class="mb-3 row">	
				<label class="col-sm-2 control-label form-label" for="class_name"><?php _e('Patient Name','hospital_mgt');?></label>
				<div class="col-sm-8">
					<input id="class_name" class="form-control" maxlength="50" type="text" value="<?php echo esc_html($patient_data['first_name']." ".$patient_data['last_name']); ?>" name="patient_name" disabled>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="mb-3 row">	
				<label class="col-sm-2 control-label form-label" for="class_section"><?php _e('Doctor Name','hospital_mgt');?></label>
				<div class="col-sm-8">
					<input id="class_section" class="form-control" maxlength="50" type="text" value="<?php echo esc_html($doctor_data['first_name']." ".$doctor_data['last_name']); ?>" name="doctor_name" disabled>
				</div>
			</div>
		</div>		
		<div class="form-group">
			<div class="mb-3 row">	
				<label class="col-sm-2 control-label form-label" for="start_date"><?php _e('Date','hospital_mgt');?><span class="require-field">*</span></label>
				<div class="col-sm-8">
					<input id="start_date" class="form-control validate[required] text-input" type="text" placeholder="<?php esc_html_e('Enter Start Date','school-mgt');?>" name="appointment_date" value="<?php echo date(MJ_hmgt_date_formate(),strtotime($appointment_data->appointment_date)); ?>" readonly>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="mb-3 row">	
				<label class="col-sm-2 control-label form-label" for="agenda"><?php _e('Topic','hospital_mgt');?></label>
				<div class="col-sm-8">
					<textarea name="agenda" class="form-control validate[custom[address_description_validation]]" placeholder="<?php esc_html_e('Enter Agenda','hospital_mgt');?>" maxlength="250" id=""><?php echo $meeting_data->agenda; ?></textarea>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="mb-3 row">	
				<label class="col-sm-2 control-label form-label" for="title"><?php _e('Password','hospital_mgt');?></label>
				<div class="col-sm-8">
					<input id="password" class="form-control validate[minSize[8],maxSize[12]]" type="password" value="<?php echo $meeting_data->password; ?>" name="password">
				</div>
			</div>
		</div>
		<?php wp_nonce_field( 'edit_meeting_admin_nonce' ); ?>
		
		<div class="offset-sm-2 col-sm-8">        	
        	<input type="submit" value="<?php if($edit){ _e('Save Meeting','hospital_mgt'); }else{ _e('Create Meeting','hospital_mgt');}?>" name="edit_meeting" class="btn btn-success" />
        </div>        
     </form>
    </div>
    <?php
?>