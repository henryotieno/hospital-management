<?php
$user_object=new MJ_hmgt_user();
$edit = 0;
$obj_virtual_appointment = new MJ_hmgt_virtual_appointment;
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
{	
	$edit = 1;
	$appointment_id = $_REQUEST['appointment_id'];
	$result = $obj_appointment->MJ_hmgt_get_single_appointment($appointment_id);
	$meeting_data='';
	$meeting_data = $obj_virtual_appointment->MJ_hmgt_get_singal_meeting_data_in_zoom_with_appointment_id($_REQUEST['appointment_id']);
	$patient_timeArr[]  =$result->appointment_time;		
	$appointment_time=json_encode($result->appointment_time);
	$date=$result->appointment_date;	
			
	    $doctor_id=$result->doctor_id;
	    $patient_id=$result->patient_id;	
		$str_date = date('l', strtotime($date));
		$weekday = strtolower($str_date);
		
		global $wpdb;
	    $table_appointment_time = $wpdb->prefix. 'hmgt_apointment_time';
		$table_appointment = $wpdb->prefix. 'hmgt_appointment';
		
		if(!empty($patient_id) && !empty($doctor_id))
		{
		$result_appointment_time=$wpdb->get_results("SELECT apointment_time,apointment_startdate,apointment_enddate  FROM $table_appointment_time where day='".$weekday."' and '$date' between apointment_startdate and apointment_enddate  and user_id=".$doctor_id."");
		
		$result_allpatient_appointments=$wpdb->get_results("SELECT appointment_time  FROM $table_appointment where appointment_date='".$date."' and doctor_id=".$doctor_id."");
	
		}
		foreach($result_appointment_time as $time)
		 {
			 $timeArr[]  =$time->apointment_time; 
		 }
		$appointment_times=json_encode($timeArr);
		
		foreach($result_allpatient_appointments as $time)
		 {
			 $allpatient_timeArr[]  =$time->appointment_time; 
		 }
		 
		$appointment_times=json_encode($timeArr);
	
		$result_difference_appointment_time = array_diff($allpatient_timeArr, $patient_timeArr);
		$allpatient_appointment_times=json_encode($result_difference_appointment_time);
		?>
		<script type="text/javascript">
			jQuery(document).ready(function($) {
				//"use strict";
			    var appointment_times_array = <?php echo $appointment_times; ?>					
				
				$.each( appointment_times_array, function( i, val ) {
					  var new_val="";
					  new_val = val.replace(":","_");
					 
				      $('.selected_'+new_val).css("background","#4CAF50");
				       $('.selected_'+new_val).addClass("select"); 
				       $('.select .time').css("visibility","visible");
					
					 }); 		
					 
				//patient get appointment checked
				
			    var appointment_time_array = <?php echo $appointment_time; ?>					
				var time=appointment_time_array;				   
				var time = time.replace(":","_");
				
				$('.selected_'+time).addClass("checked"); 				
				$('.checked .time').prop('checked', true); 
				//booking appointments
				var allpatient_appointment_time_array = <?php echo $allpatient_appointment_times; ?>	
				$.each( allpatient_appointment_time_array, function( i, val ) {
				
				 new_val = val.replace(":","_");				 
				 $('.selected_'+new_val).css("background","#008CBA");
				 $('.selected_'+new_val).addClass("select_apointment"); 
				 $('.select_apointment .time').css("visibility","hidden");
				});				
	 			return true;
			});		
		</script>
	<?php
}
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
	var date = new Date();
		date.setDate(date.getDate()-0);
		$.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
		
		$('#appointment_date').datepicker({
		startDate: date,
		autoclose: true
	   }); 
	   $('#patient').select2();
		$("body").on("click", ".save_appointment", function()
		{	
		var patient_name = $("#patient");
		if (patient_name.val() == "") {
			alert("<?php esc_html_e('Please select a patient','hospital_mgt');?>");
			return false;
		}
		return true;
		});
		$("body").on("click", ".save_appointment", function()
		{		
		  if($('input[type=radio][name=realtime]:checked').length == 0)
		  {
			 alert("<?php esc_html_e('Please select the time slots','hospital_mgt');?>");
			 return false;
		  }
		   return true;
		});
		if($(".virtual_appointment_meeeting_option").prop("checked") == true)
		{
			$(".virtual_appointment_meeeting_div").show();
		}
		else if($(this).prop("checked") == false){
			$(".stripe_div").hide();
		}
		$('.virtual_appointment_meeeting_option').click(function(){
            if($(this).prop("checked") == true){
               $(".virtual_appointment_meeeting_div").show();
            }
            else if($(this).prop("checked") == false){
                $(".virtual_appointment_meeeting_div").hide();
            }
        });

} );
</script>
<?php 	
if($active_tab == 'addappointment')
{
	MJ_hmgt_browser_javascript_check();
	?>		
    <div class="panel-body"><!-- PANEL BODY DIV START-->
        <form name="patient_form" action="" method="post" class="form-horizontal" id="patient_form">
			<?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
			<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
			<input type="hidden" name="meeting_id" value="<?php if(isset($meeting_data->meeting_id)){echo $meeting_data->meeting_id;} ;?>">
			<input type="hidden" name="appointment_id" value="<?php if(isset($meeting_data->appointment_id)){echo $meeting_data->appointment_id;}?>">
			<input type="hidden" name="zoom_meeting_id" value="<?php if(isset($meeting_data->zoom_meeting_id)){ echo $meeting_data->zoom_meeting_id;}?>">
			<input type="hidden" name="uuid" value="<?php if(isset($meeting_data->uuid)){ echo $meeting_data->uuid;}?>">
			<input type="hidden" name="meeting_join_link" value="<?php if(isset($meeting_data->meeting_join_link)){ echo $meeting_data->meeting_join_link;}?>">
			<input type="hidden" name="meeting_start_link" value="<?php  if(isset($meeting_data->meeting_start_link)){ echo $meeting_data->meeting_start_link;}?>">
			<input type="hidden" name="appointment_id" value="<?php if(isset($_REQUEST['appointment_id'])) echo esc_attr($_REQUEST['appointment_id']);?>" />
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="middle_name"><?php esc_html_e('Select Patient','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-6">
						<select name="patient_id" id="patient" class="form-control">
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
					<label class="col-sm-2 control-label form-label" for="department_id"><?php esc_html_e('Select Department','hospital_mgt');?></label>
					<div class="col-sm-6">
						<select name="department_id" class="form-control max_width_100 department_id" id="department_id">
							<option value=""><?php esc_html_e('Select Department','hospital_mgt');?></option>
						<?php 
							$department_id=$result->department_id;
							$department_array = $user_object->MJ_hmgt_get_staff_department();
							if(!empty($department_array))
							{
								foreach ($department_array as $retrieved_data)
								{?>
									<option value="<?php echo esc_attr($retrieved_data->ID); ?>" <?php selected($department_id,$retrieved_data->ID);?>><?php echo $retrieved_data->post_title;?></option>
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
					<label class="col-sm-2 control-label form-label" for="middle_name"><?php esc_html_e('Select Doctor','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-6">
						<?php $doctors = MJ_hmgt_getuser_by_user_role('doctor');	?>
						<select name="doctor_id" class="form-control validate[required] max_width_100 doctor_by_dept" id="doctor">
							<option value=""><?php  esc_html_e('Select Doctor ','hospital_mgt');?></option>
							<?php 
								$doctory_data=$result->doctor_id;
								if(!empty($doctors))
								{
									foreach($doctors as $doctor)
									{							
									echo '<option value='.$doctor['id'].' '.selected($doctory_data,$doctor['id']).'>'.$doctor['first_name'].' - '.MJ_hmgt_doctor_specialization_title($doctor['id']).'</option>';
									}
								}
								?>
						</select>
					</div>
				</div>
			</div>
			<div class="apointment_time_reset form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="bed_number"><?php esc_html_e('Date','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-6">
						<input id="appointment_date" class="form-control validate[required] text-input appointment_date appointment_gatedate" 
						type="text" value="<?php if($edit){ echo date(MJ_hmgt_date_formate(),strtotime($result->appointment_date));}elseif(isset($_POST['appointment_date'])) echo esc_attr($_POST['appointment_date']);?>" 
						name="appointment_date" autocomplete="off">
						
						<input type="hidden" value="<?php if($edit){ echo date(MJ_hmgt_date_formate(),strtotime($result->appointment_date));}?>" id="hide_date_value">
						<input type="hidden" value="<?php if($edit){ echo esc_attr($result->appointment_time);}?>" id="hide_time_value">
					</div> 
				</div>					
			</div>	
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="Note"><?php esc_html_e('Note','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-6">
					<note>
						<p> <h3 class="green"><?php esc_html_e('Green box is available appointments', 'hospital_mgt' ); ?> </h3>
						</p>
						<p> <h3 class="blue"><?php esc_html_e('Blue box is already Booked appointments', 'hospital_mgt' );?></h3>
						</p>
					</note>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="time"><?php esc_html_e('Select Appointment Time','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">		
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 appointment_padding_border float_left div_float" align="center">		
							<?php esc_html_e('Morning', 'hospital_mgt' ); ?>
						</div>	
                    <div class="div_clere_flex">						
					<?php
					$morning_time=array("10:00"=>"10:00AM","10:15"=>"10:15AM","10:30"=>"10:30AM","10:45"=>"10:45AM","11:00"=>"11:00AM","11:15"=>"11:15AM ","11:30"=>"11:30AM","11:45"=>"11:45AM");
					$i = 0;
					foreach ($morning_time as $key => $value)
					{ 				
					  ?>	
						<div class="col-lg-2 col-md-3 col-sm-3 col-xs-3 appointment_padding_border col_xs_3_css float_left">	<span class="appointment_col_md_12 col-md-12 appoint_1 float_left">  
							<span class="appointment_col_md_12 col-md-12 time_font_size appoint_2 float_left"><?php echo MJ_hmgt_appoinment_time_language_translation(esc_attr($value)); ?></span>
							<span class="appointment_col_md_12 col-md-12 appoint_3 float_left"> <span class="appoint_4 removeselect selected_<?php print str_replace(":","_","$key")?>"> 
							<input type="radio" name="realtime" class="time appoiment_time_validation" value="<?php echo esc_attr($value);?>"></input>
							<input type="hidden" name="timeabc[<?php echo esc_attr($value);?>]" class="time" value="<?php echo esc_attr($value);?>"></input>
							<input type="hidden" name="time[<?php echo esc_attr($value);?>]" value="<?php echo esc_attr($key);?>  "></input> 
							</span>
							</span>
						</div>
						<?php  
						$i++; 
					}
					?> 
					</div>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 appointment_padding_border float_left div_clere_flex div_float" align="center">		
						<?php esc_html_e('Afternoon', 'hospital_mgt' ); ?>
					</div>
					<div class="div_clere_flex">
					<?php 
					$afternoon_time=array("12:00"=>"12:00PM","12:15"=>"12:15PM","12:30"=>"12:30PM","12:45"=>"12:45PM","01:00"=>"01:00PM","01:15"=>"01:15PM","01:30"=>"01:30PM","01:45"=>"01:45PM","02:00"=>"02:00PM","02:15"=>"02:15PM","02:30"=>"02:30PM","02:45"=>"02:45PM","03:00"=>"03:00PM","03:15"=>"03:15PM","03:30"=>"03:30PM","03:45"=>"03:45PM","04:00"=>"04:00PM","04:15"=>"04:15PM","04:30"=>"04:30PM","04:45"=>"04:45PM","05:00"=>"05:00PM","05:15"=>"05:15PM","05:30"=>"05:30PM","05:45"=>"05:45PM");
					 
					 $i = 0;
					foreach ($afternoon_time as $key => $value)
					{ 
					  ?>
						<div class="col-lg-2 col-md-3 col-sm-3 col-xs-3 appointment_padding_border col_xs_3_css float_left">	
							<span class="appointment_col_md_12 col-md-12 appoint_1 float_left">  
							<span class="appointment_col_md_12 col-md-12 time_font_size appoint_2 float_left"><?php echo MJ_hmgt_appoinment_time_language_translation(esc_attr($value)); ?></span>
							<span class="appointment_col_md_12 col-md-12 appoint_3 float_left"> <span class="appoint_4 removeselect selected_<?php print str_replace(":","_","$key")?> "> 
							<input type="radio" name="realtime" class="time appoiment_time_validation" value="<?php echo esc_attr($value);?>"></input>
							
							<input type="hidden" name="timeabc[<?php echo esc_attr($value);?>]" class="time" value="<?php echo esc_attr($value);?>"></input>
							<input type="hidden" name="time[<?php echo esc_attr($value);?>]" value="<?php echo esc_attr($key);?>  "></input> 
							</span>
							
							</span>
						</div>			
						<?php  
						$i++; 
					} 
					?>
					</div>
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 appointment_padding_border float_left div_clere_flex div_float" align="center">		
						<?php esc_html_e('Evening', 'hospital_mgt' ); ?>
					</div>
				   <div class="div_clere_flex">
					<?php 
					$evening_time=array("06:00"=>"06:00PM","06:15"=>"06:15PM","06:30"=>"06:30PM","06:45"=>"06:45PM","07:00"=>"07:00PM","07:15"=>"07:15PM","07:30"=>"07:30PM","07:45"=>"07:45PM","08:00"=>"08:00PM");
					 
					 $i = 0;
					foreach ($evening_time as $key => $value)
					{ 
					  ?>
						<div class="col-lg-2 col-md-3 col-sm-3 col-xs-3 appointment_padding_border col_xs_3_css float_left">	
							<span class="appointment_col_md_12 col-md-12 appoint_1 float_left">  
							<span  class="appointment_col_md_12 col-md-12 time_font_size appoint_2 float_left"><?php echo MJ_hmgt_appoinment_time_language_translation(esc_attr($value)); ?></span>
							<span class="appointment_col_md_12 col-md-12 appoint_3 float_left"> <span class="appoint_4 removeselect selected_<?php print str_replace(":","_","$key")?> " >
							<input type="radio" name="realtime" class="time appoiment_time_validation" value="<?php echo esc_attr($value);?>"></input>
							<input type="hidden" name="timeabc[<?php echo $value;?>]" class="time" value="<?php echo esc_attr($value);?>"></input>
							<input type="hidden" name="time[<?php echo esc_attr($value);?>]" value="<?php echo esc_attr($key);?>  "></input> </span>
							</span>
						</div>
						<?php  
						$i++; 
					} 
					?>
					</div>
					</div>
				</div>
			</div>
			<?php
			$virtual_meeting=get_option("hmgt_enable_virtual_appointment");
			if($virtual_meeting == "yes")
			{
			?>
				<div class="form-group margin_bottom_5px">
					<div class="mb-3 row">	
						<!-- <label class="col-sm-3 control-label form-label" for="enable"><?php esc_html_e('Virtual Appointment Meeting','hospital_mgt');?></label> -->
						<label class="col-sm-2 control-label form-label" for="time"><?php esc_html_e('Virtual Meeting','hospital_mgt');?></label>
						<div class="col-sm-8 margin_bottom_5px send_mail_checkbox">
							 <div class="checkbox">
								<label>
									<input id="virtual_appointment_meeeting_option" class="virtual_appointment_meeeting_option" type="checkbox" value="1" <?php if($edit) echo checked($result->virtual_appointment_meeeting_option,'1');?> name="virtual_appointment_meeeting_option">
								</label>
							</div>				 
						</div>
					</div>
				</div>	
				<div class="virtual_appointment_meeeting_div">
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="agenda"><?php _e('Topic','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-sm-8">
								<textarea name="agenda" class="form-control validate[required,custom[address_description_validation]]" placeholder="<?php esc_html_e('Enter Topic','hospital_mgt');?>" maxlength="250" id=""><?php if($edit){ echo esc_attr($meeting_data->agenda);}elseif(isset($_POST['agenda'])) echo esc_attr($_POST['agenda']);?></textarea>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="title"><?php _e('Password','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-sm-8">
								<input id="password" class="form-control validate[required,minSize[8],maxSize[12]]" type="password" value="<?php if($edit){ echo esc_attr($meeting_data->password);}elseif(isset($_POST['password'])) echo esc_attr($_POST['password']);?>" name="password">
							</div>
						</div>
					</div>
				</div>		
				
			<?php
			}
			?>
			<?php wp_nonce_field( 'save_appointment_nonce' ); ?>			
			<div class="form-group margin_bottom_5px">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="enable"><?php esc_html_e('Send SMS','hospital_mgt');?></label>
					<div class="col-sm-8 margin_bottom_5px send_mail_checkbox">
						 <div class="checkbox">
							<label>
								<input id="chk_sms_sent11" class="margin_top_10" type="checkbox" <?php $smgt_sms_service_enable = 0;if($smgt_sms_service_enable) echo "checked";?> value="1" name="hmgt_sms_service_enable">
							</label>
						</div>				 
					</div>
				</div>
			</div>			
			<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
				<input type="submit" value="<?php if($edit){ esc_html_e('Save','hospital_mgt'); }else{ esc_html_e('Save','hospital_mgt');}?>" name="save_appointment" class="btn btn-success save_appointment"/>
			</div>
		</form>
    </div> <!-- PANEL BODY DIV END-->  
<?php 
}
?>