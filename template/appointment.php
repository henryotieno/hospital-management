<?php
MJ_hmgt_browser_javascript_check();
$obj_appointment = new MJ_hmgt_appointment();
$obj_virtual_appointment = new MJ_hmgt_virtual_appointment;
$hospital_obj=new MJ_hmgt_Hospital_Management(get_current_user_id());
$user_object=new MJ_hmgt_user();
//access right
$user_access=MJ_hmgt_get_userrole_wise_access_right_array();

if (isset ( $_REQUEST ['page'] ))
{	
	if($user_access['view']=='0')
	{	
		MJ_hmgt_access_right_page_not_access_message();
		die;
	}
	if(!empty($_REQUEST['action']))
	{
		if (isset ( $_REQUEST ['page'] ) && $_REQUEST ['page'] == $user_access['page_link'] && ($_REQUEST['action']=='edit'))
		{
			if($user_access['edit']=='0')
			{	
				MJ_hmgt_access_right_page_not_access_message();
				die;
			}			
		} 
		if (isset ( $_REQUEST ['page'] ) && $_REQUEST ['page'] == $user_access['page_link'] && ($_REQUEST['action']=='delete'))
		{
			if($user_access['delete']=='0')
			{	
				MJ_hmgt_access_right_page_not_access_message();
				die;
			}	
		}
		if (isset ( $_REQUEST ['page'] ) && $_REQUEST ['page'] == $user_access['page_link'] && ($_REQUEST['action']=='insert'))
		{
			if($user_access['add']=='0')
			{	
				MJ_hmgt_access_right_page_not_access_message();
				die;
			}	
		} 
	}
}
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	<?php
	if (is_rtl())
		{
		?>	
			$('.apointment_times_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('.apointment_times_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>
		var date = new Date();
            date.setDate(date.getDate()-0);
	        $.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
          var phpdate_formate="<?php  echo MJ_hmgt_date_formate(); ?>";
		$('#appointment_date').datepicker({
	        startDate: date,
            autoclose: true
           });
      
		$('.appointmet_sdate').datepicker({
	        startDate: date,
            autoclose: true
           }).on('changeDate', function()
		   {
			   if(phpdate_formate == 'd/m/Y')
			   {				   
				   var selected_date = $(this).val();
				    
				   var converted_date_array = selected_date.split('/');
				   var converted_date = converted_date_array[1]+'/'+converted_date_array[0]+'/'+converted_date_array[2];
					
			   }
			   else
			   {				    
				    var converted_date = $(this).val();
			   }
			   //alert(converted_date);
			//$('#appointment_time_enddate').datepicker('setStartDate', new Date(converted_date));
			$('#appointment_time_enddate').datepicker('setStartDate', converted_date);
		}); 
		
		$('#appointment_time_enddate').datepicker({
	        startDate: date,
            autoclose: true
           }).on('changeDate', function(){
			   if(phpdate_formate == 'd/m/Y')
			   {				   
				   var selected_date = $(this).val();
				  
				   var converted_date_array = selected_date.split('/');
				   var converted_date = converted_date_array[1]+'/'+converted_date_array[0]+'/'+converted_date_array[2];
					
			   }
			   else
			   {				    
				    var converted_date = $(this).val();
			   }
		});
		
		$("body").on("change", "#appointment_time_startdate", function()
		{			
			
			var apointment_date  = $('#appointment_time_startdate').val();
			
			if(apointment_date=="")
			{
				var already_appointment_set_time =$('#already_appointment_set_time').val();
				
				var apointment_date=$('#appointment_time_startdate').val(already_appointment_set_time);
			}
			else
			{
				$('.checked .avilable_time').prop('checked', false);
				$('.checked').removeClass('checked');	
				$('.appointment_note').css("display", "none");				

				  var curr_data = {
							 action: 'MJ_hmgt_onchage_gate_apointment_time_avilability',
							 apointment_date: apointment_date,
							 dataType: 'json'
							 };
							 
						$.post(hmgt.ajax, curr_data, function(response) {
						
						var json_obj = $.parseJSON(response);	
						
						var dateformate_value=json_obj['dateformate'];
						
						if(dateformate_value == 'Y-m-d')
						{						
							var dateformate='YYYY-MM-DD';
						}
						if(dateformate_value == 'm/d/Y')
						{						
							var dateformate='MM/DD/YYYY';
						}	
						if(dateformate_value == 'd/m/Y')
						{						
							var dateformate='DD/MM/YYYY';
						}				
						if(dateformate_value == 'F j, Y')
						{					
							var dateformate='MMMM DD,YYYY';				
						}
						
						var result=json_obj['result'];
						
						if(result!="")
						{	
							
							var date=json_obj['date'];
							var sdate = moment(date.apointment_startdate).format(dateformate);			
							var appstartdate=sdate;
							var edate = moment(date.apointment_enddate).format(dateformate);
							var enddate=edate;	
							$('.appointment_note').css("display", "block");
							$('.appointment_msg').html('You have Allready Appointment Time Set '+appstartdate+' to '+edate+' You Want To Edit It...');
							
							$('#already_appointment_set_time').val('');
							$('#already_appointment_set_time').val(appstartdate);
							
							var apointment_date  = $('#appointment_time_startdate');
							apointment_date.datepicker('setEndDate',edate );
							var enddate = $('#appointment_time_enddate');
							enddate.datepicker('option', 'minDate', appstartdate);	
							enddate.datepicker('setDate', edate);
							
							for (var i in result) 
							{
							   var apointment_startdate=result[i].apointment_startdate;
							   var apointment_enddate=result[i].apointment_enddate;
							   var time=result[i].apointment_time;
							   var day=result[i].day;
								var apointment_sdate = moment(apointment_startdate).format(dateformate);
								$('#appointment_time_startdate').val(apointment_sdate);
								var apointment_edate = moment(apointment_enddate).format(dateformate);
								$('#appointment_time_enddate').val(apointment_edate); 
							   var time = time.replace(":","_");					 
							   var day = day;
							   
							   $('.selected_'+time+'_'+day).addClass("checked"); 				 
							
								$('.checked .avilable_time').prop('checked', true); 
								
							}					
							return true;					
						}
						else
						{	
							
							$('.appointment_note').css("display", "none");
							
							var enddate = $('#appointment_time_enddate');
							var startDate = $('.appointmet_sdate').datepicker('getDate');
													
							startDate.setDate(startDate.getDate() + 7);
							enddate.datepicker('option', 'minDate', startDate);	
							enddate.datepicker('setDate', startDate); 
							
						}	
					}); 
			}	
		}); 
		$("body").on("change", "#appointment_date", function()
		{
			   $('.removeselect').css("background","#FFFFFF");			    
			   $('.removeselect').removeClass("select"); 
			   $('.removeselect .time').css('visibility', 'hidden');			   
			   $('.removeselect').removeClass("checked"); 
			   $('.removeselect .time').prop('checked', false); 
			 
			 var apointment_date  = $('#appointment_date').val() ;
			 var doctor_id =$('#doctor').val();
			 var patient_id =$('#patient').val();
			
			var date1 = $('#appointment_date').datepicker('getDate');
			var day = date1.getDay();	
				if (day == 1)
				{
					var dayofweek="monday";
				}
				if (day == 2){
					var dayofweek="tuesday";
				}
				if (day == 3)
				{
					var dayofweek="wednesday";
				}
				if (day == 4){
					var dayofweek="thursday";
				}
				if (day == 5)
				{
					var dayofweek="friday";
				}
				if (day == 6){
					var dayofweek="saturday";
				}
				if (day == 0){
					var dayofweek="sunday";
				}
	 		  var curr_data = {
	 					 action: 'MJ_hmgt_onchage_gate_apointment_time',
	 					 apointment_date: apointment_date,			
						 doctor_id: doctor_id,	
	 					 patient_id: patient_id,	
						 dayofweek: dayofweek,					 
	 					 dataType: 'json'
	 					 };
						 
	 				$.post(hmgt.ajax, curr_data, function(response) {
					
					var json_obj = $.parseJSON(response);
										
					 var new_val ="";
					 $.each( json_obj, function( i, val ) {
						
						
					  new_val = val.replace(":","_");
					 
					 
				      $('.selected_'+new_val).css("background","#4CAF50");
				      $('.selected_'+new_val).addClass("select"); 
				      $('.select .time').css("visibility","visible");
					
					 }); 					
	 			 return true;				 
	 			 });
		    }); 
	
		$("body").on("change", ".appointment_date", function()
		{
			  $('.removeselect').css("background","#FFFFFF");			    
			  $('.removeselect').removeClass("select_apointment"); 
			  $('.removeselect .time').css('visibility', 'hidden');
			 
			 var apointment_date  = $('#appointment_date').val();
			 var edit_apointment_date  = $('#hide_date_value').val();
			 var edit_apointment_time  = $('#hide_time_value').val();
			 var doctor_id =$('#doctor').val() ;
			 var patient_id =$('#patient').val() ;
			
			 var curr_data = {
	 					action: 'MJ_hmgt_onchage_gate_apointment',
	 					apointment_date: apointment_date,			
	 					doctor_id: doctor_id,						
	 					edit_apointment_date: edit_apointment_date,						
	 					edit_apointment_time: edit_apointment_time,						
	 					patient_id: patient_id,						
	 					dataType: 'json'
	 					};
				    $.post(hmgt.ajax, curr_data, function(response) {
						
					var json_obj = jQuery.parseJSON(response);	
					var new_val ="";
					$.each(json_obj['book_appointment_time'], function( i, val ) {
					new_val = val.replace(":","_");
				    $('.selected_'+new_val).css("background","#008CBA");
				    $('.selected_'+new_val).addClass("select_apointment"); 
				    $('.select_apointment .time').css("visibility","hidden");
					});
					$.each(json_obj['edit_appointment_time'], function( i, val ) {
					
						time = val.replace(":","_");
						
					   $('.selected_'+time).css("background","#4CAF50");
				       $('.selected_'+time).addClass("edited_select"); 
				       $('.edited_select .time').css("visibility","visible");					
					   $('.selected_'+time).addClass("checked"); 				
				       $('.checked .time').prop('checked', true); 
					});
	 			return true;
	 			});
		 }); 
 $('#patient').select2();	
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
});
</script>

<?php 
//SAVE Appointment TIME DATA
 if(isset($_REQUEST['save_appointment_time']))
{	
	$nonce = $_POST['_wpnonce'];
	if (wp_verify_nonce( $nonce, 'save_appointment_time_nonce' ) )
	{
		$result = $obj_appointment->MJ_hmgt_add_appointment_time($_POST);
		
		if($result)
		{
			wp_redirect ( home_url() . '?dashboard=user&page=appointment&tab=appointmentlist&message=4');
		}
	}		
}
// SAVE Appointment DATA	
if(isset($_REQUEST['save_appointment']))
{
	$nonce = $_POST['_wpnonce'];
	if (wp_verify_nonce( $nonce, 'save_appointment_nonce' ) )
	{
		if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'insert' || $_REQUEST['action'] == 'edit'))
		{

			   global $wpdb;
			   $doctor_id=$_POST['doctor_id'];
			   $apointment_dates=$_POST['appointment_date'];
			   $apointment_time=$_POST['time'];
			   $bb = $_POST['time'];
			   $apointment_time=$bb[$_POST['realtime']]; 
			   
			  $aa = $_POST['timeabc'];
			  $time_with_ampm=$aa[$_POST['realtime']];
			
			   $table_appointment_time = $wpdb->prefix. 'hmgt_apointment_time';
			   $table_appointment_time_data= $wpdb->get_row("SELECT * FROM $table_appointment_time where apointment_time='".$apointment_time."'
			   and user_id=".$doctor_id);
			   if(!empty($table_appointment_time_data))
			   {
				   global $wpdb;
				   $table_appointment = $wpdb->prefix. 'hmgt_appointment';
				   $table_appointment_data= $wpdb->get_row("SELECT * FROM $table_appointment where apointment_time='".$apointment_time."'
				   and doctor_id=".$doctor_id);
				   if(empty($table_appointment_data))
				   {
						$result = $obj_appointment->MJ_hmgt_add_appointment($_POST);
						
					   if($result)
					   {
						   
							$hmgt_sms_service_enable=0;
							if(isset($_POST['hmgt_sms_service_enable']))
								$hmgt_sms_service_enable = $_POST['hmgt_sms_service_enable'];
							if($hmgt_sms_service_enable)
							{
								if(!empty(get_user_meta($_REQUEST['doctor_id'], 'phonecode',true))){ $phone_code_doctor=get_user_meta($_REQUEST['doctor_id'], 'phonecode',true); }else{ $phone_code_doctor='+'.MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }	
								
								$doctor_number = $phone_code_doctor.get_user_meta($_REQUEST['doctor_id'], 'mobile',true);
								
								if(!empty(get_user_meta($_REQUEST['patient_id'], 'phonecode',true))){ $phone_code_patient=get_user_meta($_REQUEST['patient_id'], 'phonecode',true); }else{ $phone_code_patient='+'.MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }
								
								$patient_number = $phone_code_patient.get_user_meta($_REQUEST['patient_id'], 'mobile',true);								
								$doctor_name = MJ_hmgt_get_display_name($_REQUEST['doctor_id']);
								$patient_name = MJ_hmgt_get_display_name($_REQUEST['patient_id']);
								$message1 = "The Appointment has been booked for $patient_name with Dr. $doctor_name on DATE : ".$_REQUEST['appointment_date']." TIME : ".$time_with_ampm;
								//$message = str_replace(" ","%20",$message1);
								include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
								//------------------- SEND MSG ------------------//
								if(is_plugin_active('sms-pack/sms-pack.php'))
								{
									$mobile_number=array($doctor_number,$patient_number);
									$current_sms_service 	= get_option( 'smgt_sms_service');
									$args = array();
									$args['mobile']=$mobile_number;
									$args['message_from']="Appointment";
									$args['message']=$message1;					
									if($current_sms_service=='telerivet' || $current_sms_service ="MSG91" || $current_sms_service=='bulksmsgateway.in' || $current_sms_service=='textlocal.in' || $current_sms_service=='bulksmsnigeria' || $current_sms_service=='africastalking' || $current_sms_service == 'clickatell')
									{				
										$send = send_sms($args);							
									}
								}
								$current_sms_service = get_option( 'hmgt_sms_service');
								if($current_sms_service == 'clickatell')
								{				 
								
									$clickatell=get_option('hmgt_clickatell_sms_service');
									$username = urlencode($clickatell['username']);
									$password = urlencode($clickatell['password']);
									$api_id = urlencode($clickatell['api_key']);
									$to1 = $doctor_number;
									$to2 = $patient_number;
									$message = urlencode($message1);
									$send=file_get_contents("https://api.clickatell.com/http/sendmsg". "?user=$username&password=$password&api_id=$api_id&to=$to1,$to2&text=$message");
								}
								if($current_sms_service == 'twillo')
								{
									//Twilio lib
									require_once HMS_PLUGIN_DIR. '/lib/twilio/Services/Twilio.php';
									$twilio=get_option( 'hmgt_twillo_sms_service');
									$account_sid = $twilio['account_sid']; //Twilio SID
									$auth_token = $twilio['auth_token']; // Twilio token
									$from_number = $twilio['from_number'];//My number
									
									//twilio object
									$client = new Services_Twilio($account_sid, $auth_token);
									$message_sent = $client->account->messages->sendMessage(
											$from_number, // From a valid Twilio number
											$doctor_number, // Text this number
											$message
									);
									$message_sent = $client->account->messages->sendMessage(
											$from_number, // From a valid Twilio number
											$patient_number, // Text this number
											$message
									);
									
								}
								if($current_sms_service == 'msg91')
								{
									//MSG91
									$mobile_number=$patient_number;
									$country_code="+".MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' ));
									$message = $message1; // Message Text
									MJ_hmgt_msg91_send_mail_function($mobile_number,$message,$country_code);
								}		
							}
							if($_REQUEST['action'] == 'edit')
							{
								wp_redirect ( home_url() . '?dashboard=user&page=appointment&tab=appointmentlist&message=2');
							}
							else 
							{
								wp_redirect ( home_url() . '?dashboard=user&page=appointment&tab=appointmentlist&message=1');
							}	

						}
				   }
				   else
				   {
						wp_redirect ( home_url() . '?dashboard=user&page=appointment&tab=appointmentlist&message=6');
				   }
			
			   }
			   else
			   {
				   wp_redirect ( home_url() . '?dashboard=user&page=appointment&tab=appointmentlist&message=5');
				}
		}
	}
}
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete')
{
	$result = $obj_appointment->delete_appointment(MJ_hmgt_id_decrypt($_REQUEST['appointment_id']));
	if($result)
	{
		wp_redirect ( home_url() . '?dashboard=user&page=appointment&tab=appointmentlist&message=3');
	}
} 
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'Approved_appointment')
{

	$appointment_id = $_REQUEST['appointment_id'];
	$patient_id = MJ_hmgt_get_patient_id($appointment_id);
	$doctor_id = MJ_hmgt_get_doctor_id($appointment_id);
	$app_time = MJ_hmgt_get_time_id($appointment_id);
	$app_date = MJ_hmgt_get_date_id($appointment_id);
	
	//-------------------- SEND Mail ---------------------//
	
	$patient=get_userdata($patient_id);
	$patient_email=$patient->user_email;
	$patientname=$patient->display_name;;

	$doctor_id=get_userdata($doctor_id);
	$doctor_name=$doctor_id->display_name;
	$doctor_email=$doctor_id->user_email;
	
	$hospital_name = get_option('hmgt_hospital_name');
	$arr['{{Patient Name}}']=$patientname;			
	$arr['{{Doctor Name}}']=$doctor_name;			
	$arr['{{Appointment Time}}']=$app_time;			
	$arr['{{Appointment Date}}']= $app_date;			
	$arr['{{Hospital Name}}']=$hospital_name;
	$subject =get_option('MJ_hmgt_appointment_approve_patient_mail_subject');
	
	$sub_arr['{{Doctor Name}}']=$doctor_name;
	$sub_arr['{{Appointment Time}}']= $app_time;			
	$sub_arr['{{Appointment Date}}']= $app_date;
	$sub_arr['{{Hospital Name}}']=$hospital_name;
	$subject = MJ_hmgt_subject_string_replacemnet($sub_arr,$subject);
	
	$message = get_option('MJ_hmgt_appointment_approve_patient_mail_template');
	$message_replacement = MJ_hmgt_string_replacemnet($arr,$message);	
	$to[]=$patient_email;
	
	global $wpdb;
	$table_appointment = $wpdb->prefix. 'hmgt_appointment';
	$result = $wpdb->query("UPDATE $table_appointment SET status='1' where appointment_id= ".$appointment_id);
	if($result)
	{
	MJ_hmgt_send_mail($to,$subject,$message_replacement);
	wp_redirect ( home_url() .'?dashboard=user&page=appointment&tab=appointmentlist&message=7');
	}
}
if(isset($_REQUEST['message']))
{
	$message =$_REQUEST['message'];
	if($message == 1)
	{ ?>
		<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
		</button>
		<p>
		<?php 
			esc_html_e('Appointment booked successfully','hospital_mgt');
		?></p></div>
		<?php 
	}
	elseif($message == 2)
	{?><div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
		</button><p><?php
			esc_html_e("Appointment updated successfully.",'hospital_mgt');
			?></p>
			</div>
		<?php 
	}
	elseif($message == 3) 
	{?>
		<div id="message" class="updated below-h2"><p>
		<?php 
			esc_html_e('Appointment deleted successfully','hospital_mgt');
		?></div></p><?php
	}
	
	elseif($message == 4) 
	{?>
		<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
		</button><p>
		<?php 
			esc_html_e('Appointment time inserted successfully','hospital_mgt');
		?></div></p><?php
	}
	elseif($message == 5) 
	{?>
		<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
		</button><p>
		<?php 
			esc_html_e('This time not available appointment','hospital_mgt');
		?></div></p><?php
	}
	elseif($message == 6) 
	{?>
		<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
		</button><p>
		<?php 
			esc_html_e('This time already appointment booking , select another time','hospital_mgt');
		?></div></p><?php
	}
	elseif($message == 7) 
		{?>
			<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
		</button><p>
			<?php 
				esc_html_e('Appointment Approved successfully','hospital_mgt');
			?>
			</p></div>
			<?php		
		}
}	

	$active_tab = isset($_REQUEST['tab'])?$_REQUEST['tab']:'appointmentlist';
	?>
	<?php
	if($obj_hospital->role == 'patient') 
	{		 
		?>
		<script type="text/javascript">
		$(document).ready(function()
		{
			jQuery('#appointment_list').DataTable({ 
			"responsive": true, 			
			"aoColumns":[
						  {"bSortable": true},
						  {"bSortable": true},
						  {"bSortable": true},
						 <?php	
						if($user_access['edit']=='1' || $user_access['delete']=='1')
						{
						?>
							{"bSortable": false},	
						<?php
						}
						?>						
					   ],
			language:<?php echo MJ_hmgt_datatable_multi_language();?>
			});
			
		} );
		</script>
		<?php	
	}
	elseif($obj_hospital->role == 'doctor')
	{
		?>
		<script type="text/javascript">
		$(document).ready(function() {
			jQuery('#appointment_list').DataTable({
				"responsive": true,
				"order": [[ 0, "desc" ]],
			language:<?php echo MJ_hmgt_datatable_multi_language();?>
			});
			
		} );
		</script>
		<?php
	}
	else
	{
		?>
		<script type="text/javascript">
		$(document).ready(function()
		{
			jQuery('#appointment_list').DataTable({ 
			"responsive": true, 
			"order": [[ 0, "desc" ]],
			"aoColumns":[
						  {"bSortable": true},
						  {"bSortable": true},
						  {"bSortable": true},
						  <?php	
						if($user_access['edit']=='1' || $user_access['delete']=='1')
						{
						?>
							{"bSortable": false},	
						<?php
						}
						?>						
					   ],
			language:<?php echo MJ_hmgt_datatable_multi_language();?>
			});
		});
		</script>
		<?php 
	}
	?>
<div class="panel-body panel-white"><!-- START PANEL BODY DIV -->
	<ul class="nav nav-tabs panel_tabs" role="tablist"><!-- START NAV TABS-->
		<li class="<?php if($active_tab == 'appointmentlist'){?>active<?php }?>">
		  
			  <a href="?dashboard=user&page=appointment&tab=appointmentlist">
				 <i class="fa fa-align-justify"></i> <?php esc_html_e('Appointment List', 'hospital_mgt'); ?></a>
			  </a>
		</li>     
		<li class="<?php if($active_tab=='addappoint'){?>active<?php }?>">
			<?php 
				if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
				{
					?>
					<a href="?dashboard=user&page=appointment&tab=addappoint&&action=edit&appointment_id=<?php echo $_REQUEST['appointment_id'];?>" class="tab <?php echo $active_tab == 'addappoint' ? 'active' : ''; ?>">
					<i class="fa fa"></i> <?php if($obj_hospital->role == 'patient') {  esc_html_e('Edit Request Appointment', 'hospital_mgt'); }else{ esc_html_e('Edit Appointment', 'hospital_mgt'); } ?></a>
				<?php 
				}
				else
				{
					if($user_access['add']=='1')
					{			
					?>				
						<a href="?dashboard=user&page=appointment&tab=addappoint&&action=insert" class="tab <?php echo $active_tab == 'addappoint' ? 'active' : ''; ?>">
						<i class="fa fa-plus-circle"></i> <?php if($obj_hospital->role == 'patient') {  esc_html_e('Request Appointment', 'hospital_mgt'); }else{ esc_html_e('Add Appointment', 'hospital_mgt'); } ?></a>
					<?php
					}
				}
				?>	  
		</li>
		  <?php
			if($obj_hospital->role == 'doctor')
			{
			?>
			  <li class="<?php if($active_tab == 'appointment_time'){?>active<?php }?>">
				  <a href="?dashboard=user&page=appointment&tab=appointment_time">
					 <i class="fa fa-align-justify"></i> <?php esc_html_e('Appointment Time', 'hospital_mgt'); ?></a>
				  </a>
			  </li>	  
			<?php
			}
			?>
	</ul><!-- END NAV TABS -->
	<div class="tab-content opacity_div"><!-- SRAER TAB CONTENT DIV -->
		<div class="tab-pane <?php if($active_tab == 'appointmentlist'){?>fade active in<?php }?>" id="appointmentlist"><!-- END TAB PANE DIV-->
			<div class="panel-body"><!-- STAR PANEL BODY DIV -->
				<div class="table-responsive"><!--TABLE RESPONSIVE DIV -->
			   <table id="appointment_list" class="display dataTable " cellspacing="0" width="100%"><!-- START Appointment LIST TABLE -->
					 <thead>
					<tr>
					<th><?php esc_html_e( 'Date', 'hospital_mgt' ) ;?></th>
					 <th><?php esc_html_e( 'Patient ', 'hospital_mgt' ) ;?></th>
					  <th><?php esc_html_e( 'Doctor', 'hospital_mgt' ) ;?></th>       
					     
						<?php
						if($user_access['edit']=='1' || $user_access['delete']=='1')
						{
						?>
							<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>			 
						<?php
						}
						?>
					</tr>
				</thead>
				<tfoot>
					<tr>
					<th><?php esc_html_e( 'Date', 'hospital_mgt' ) ;?></th>
					 <th><?php esc_html_e( 'Patient ', 'hospital_mgt' ) ;?></th>
					  <th><?php esc_html_e( 'Doctor', 'hospital_mgt' ) ;?></th>
					                
						<?php
						if($user_access['edit']=='1' || $user_access['delete']=='1')
						{
						?>
							<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>			 
						<?php
						}
						?>
					</tr>
				</tfoot>
			<tbody>
				 <?php 
				if($obj_hospital->role == 'laboratorist' || $obj_hospital->role == 'pharmacist' || $obj_hospital->role == 'accountant' || $obj_hospital->role == 'receptionist') 
				{
				   $own_data=$user_access['own_data'];
					if($own_data == '1')
					{ 
					   $appointment_data=$obj_appointment->MJ_hmgt_get_all_appointment_by_create_by();
					}
					else
					{
						$appointment_data=$obj_appointment->MJ_hmgt_get_all_appointment();
					}
				}
				elseif($obj_hospital->role == 'doctor') 
				{
				   $own_data=$user_access['own_data'];
					if($own_data == '1')
					{ 
					   $appointment_data=$obj_appointment->MJ_hmgt_get_doctor_all_appointment_by_create_by();
					  
					}
					else
					{
						$appointment_data=$obj_appointment->MJ_hmgt_get_all_appointment();
					}
				}
				elseif($obj_hospital->role == 'nurse') 
				{
				   $own_data=$user_access['own_data'];
					if($own_data == '1')
					{ 
					   $appointment_data=$obj_appointment->MJ_hmgt_get_nurse_all_appointment_by_create_by();
					}
					else
					{
						$appointment_data=$obj_appointment->MJ_hmgt_get_all_appointment();
					}
				}
				elseif($obj_hospital->role == 'patient')
				{
					$own_data=$user_access['own_data'];
					if($own_data == '1')
					{ 
					   $appointment_data=$obj_appointment->MJ_hmgt_get_patient_all_appointment();
					}
					else
					{
						$appointment_data=$obj_appointment->MJ_hmgt_get_all_appointment();
					}			
				}		
				
				if(!empty($appointment_data))
				{
					foreach ($appointment_data as $retrieved_data)
					{			
						?>
						<tr>
							<td class="appointment_time"><?php echo date(MJ_hmgt_date_formate(),strtotime($retrieved_data->appointment_date));?>(<?php echo MJ_hmgt_appoinment_time_language_translation($retrieved_data->appointment_time_with_a); ?>)</td>
							<td class="patient">
							<?php 
							$patient_data =	MJ_hmgt_get_user_detail_byid($retrieved_data->patient_id);
							echo esc_html($patient_data['first_name']." ".$patient_data['last_name']);?></td>     
							<td class="doctor">
							<?php $doctor_data =	MJ_hmgt_get_user_detail_byid($retrieved_data->doctor_id);
							echo esc_html($doctor_data['first_name']." ".$doctor_data['last_name']);?></td> 
							
							<?php
							if($user_access['edit']=='1' || $user_access['delete']=='1')
							{
								?>
								<td class="action"> 
								<?php
								if(($retrieved_data->status) == '0')
								{ 
									if($obj_hospital->role != 'patient')
									{
										?>
										<a  href="?dashboard=user&page=appointment&tab=appointmentlist&action=Approved_appointment&appointment_id=<?php echo esc_attr($retrieved_data->appointment_id);?>" class="btn btn-default" >
										<?php esc_html_e('Approve','hospital_mgt' ) ;?></a>
										<?php
									}
								}
								if($user_access['edit']=='1')
								{
								?>
									<a href="?dashboard=user&page=appointment&tab=addappoint&action=edit&appointment_id=<?php echo MJ_hmgt_id_encrypt(esc_attr($retrieved_data->appointment_id));?>" class="btn btn-info"> 
									<?php esc_html_e('Edit','hospital_mgt' ) ;?></a>
								<?php
								}
								
								if($user_access['delete']=='1')
								{
								?>
									<a href="?dashboard=user&page=appointment&tab=appointmentlist&action=delete&appointment_id=<?php echo MJ_hmgt_id_encrypt(esc_attr($retrieved_data->appointment_id));?>" class="btn btn-danger" 
									onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this record?','hospital_mgt');?>');">
									<?php esc_html_e( 'Delete', 'hospital_mgt' ) ;?> </a>
								<?php
								}
								?>
								</td>
								<?php
							}
							?>
						</tr>
						<?php 
					}					
				} ?>
				</tbody>
				
				</table><!-- END Appointment LIST TABLE -->
			</div><!-- END TABLE RESPONSIVE DIV -->
		</div><!-- END PANEL BODY DIV -->
	</div><!-- END PANE TAB DIV -->
	<?php 
	$edit = 0;
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
	{
		$edit = 1;
		$appointment_id = MJ_hmgt_id_decrypt($_REQUEST['appointment_id']);
		$result = $obj_appointment->MJ_hmgt_get_single_appointment($appointment_id);
		$meeting_data = $obj_virtual_appointment->MJ_hmgt_get_singal_meeting_data_in_zoom_with_appointment_id($appointment_id);
		//selected date avilable appointment
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
		
		if(!empty($patient_id))
		{			
			$result_appointment_time=$wpdb->get_results("SELECT apointment_time FROM $table_appointment_time where day='".$weekday."' and '$date' between apointment_startdate and apointment_enddate AND user_id=".$doctor_id."");
		
			$result_allpatient_appointments=$wpdb->get_results("SELECT appointment_time  FROM $table_appointment where appointment_date='".$date."' and doctor_id=".$doctor_id."");
		}	
		if(!empty($result_appointment_time))
		{
			foreach($result_appointment_time as $time)
			{
				 $timeArr[]  =$time->apointment_time; 
			}
			$appointment_times=json_encode($timeArr);
		}
		
		if(!empty($result_allpatient_appointments))
		{
			foreach($result_allpatient_appointments as $time)
			{
				 $allpatient_timeArr[]  =$time->appointment_time; 
			}
			$result_difference_appointment_time = array_diff($allpatient_timeArr, $patient_timeArr);
			$allpatient_appointment_times=json_encode($result_difference_appointment_time);
		 }
		?>
		<script type="text/javascript">
			$(document).ready(function() {
				 "use strict";
				//all avilable appointment
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
	$(document).ready(function() {
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
		$("body").on("click","#btnsavetime",function()
		{
			if (typeof $("#chktime:checked").val() === "undefined")
			 {
				alert("<?php esc_html_e('Please checke atleast one time slots','hospital_mgt');?>");
				return false;
			}
		});

		
	} );
	</script>
	<div class="tab-pane <?php if($active_tab == 'addappoint'){?>fade active in<?php }?>" id="add_appointment"><!-- STAR TAB PANE DIV -->
        <div class="panel-body"><!-- STAR PANEL BODY DIV -->
			<form name="patient_form" action="" method="post" class="form-horizontal " id="patient_form" autocomplete="off"><!-- STAR Appointment FORM-->
				 <?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
				<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
				<input type="hidden" name="meeting_id" value="<?php if(!empty($meeting_data->meeting_id)) { echo $meeting_data->meeting_id; } ?>">
				<input type="hidden" name="appointment_id" value="<?php if(!empty($meeting_data->appointment_id)) { echo $meeting_data->appointment_id; } ?>">
				<input type="hidden" name="zoom_meeting_id" value="<?php if(!empty($meeting_data->zoom_meeting_id)) { echo $meeting_data->zoom_meeting_id; } ?>">
				<input type="hidden" name="uuid" value="<?php if(!empty($meeting_data->uuid)) { echo $meeting_data->uuid; } ?>">
				<input type="hidden" name="meeting_join_link" value="<?php if(!empty($meeting_data->meeting_join_link)) { echo $meeting_data->meeting_join_link; } ?>">
				<input type="hidden" name="meeting_start_link" value="<?php if(!empty($meeting_data->meeting_start_link)) { echo $meeting_data->meeting_start_link; } ?>">
				<input type="hidden" name="appointment_id" value="<?php if(isset($_REQUEST['appointment_id'])) echo MJ_hmgt_id_decrypt(esc_attr($_REQUEST['appointment_id']));?>"  />
				
				<?php
				if($obj_hospital->role == 'nurse' || $obj_hospital->role == 'doctor' || $obj_hospital->role == 'receptionist')
				{
				?>
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-sm-2 col-md-2 control-label form-label" for="middle_name"><?php esc_html_e('Select Patient','hospital_mgt');?><span class="require-field">*</span></label>
						<div class="col-sm-8 col-md-8">
							<select name="patient_id" id="patient" class="form-control validate[required] width_530">
								<option value=""><?php esc_html_e('Select Patient','hospital_mgt');?></option>
								<?php 
								if($edit)
									$patient_id1 = $result->patient_id;
								elseif(isset($_REQUEST['patient_id']))
									$patient_id1 = $_REQUEST['patient_id'];
								else 
									$patient_id1 = "";
								$own_data=$user_access['own_data'];
								if($own_data == '1')
								{ 
									$patients =$obj_hospital->patient;
												
									if(!empty($patients))
									{
										foreach($patients as $patient)
										{
											$patient_id = get_user_meta($patient->ID,'patient_id',true);
											
											echo '<option value="'.$patient->ID.'" '.selected($patient_id1,$patient->ID).'>'.$patient_id.' - '.$patient->display_name.'</option>';
										
										}	
									}
								}
								else
								{
									$patients = MJ_hmgt_patientid_list();	
									
									if(!empty($patients))
									{
									foreach($patients as $patient)
									{
										echo '<option value="'.$patient['id'].'" '.selected($patient_id1,$patient['id']).'>'.$patient['patient_id'].' - '.$patient['first_name'].' '.$patient['last_name'].'</option>';
									}
									}
								}
								?>
							</select>
						</div>
					</div>
				</div>
				<?php 
				}
				elseif($obj_hospital->role == 'patient')
				{
					echo '<input type="hidden" name="patient_id" value="'.get_current_user_id().'">';
				}			
			
				if($obj_hospital->role == 'nurse' || $obj_hospital->role == 'patient' || $obj_hospital->role == 'receptionist' )
				{
				?>
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
							<?php 
							$doctors = MJ_hmgt_getuser_by_user_role('doctor');	
							?>
								
							<select name="doctor_id" class="form-control validate[required] doctor doctor_by_dept" id="doctor">
							<option value=""><?php  esc_html_e('Select Doctor','hospital_mgt');?></option>
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
				
				<?php 
					}
					elseif($obj_hospital->role == 'doctor')
					{
						echo '<input type="hidden" name="doctor_id" value="'.get_current_user_id().'">';
					}
				?>
				
				<div class="apointment_time_reset form-group">
					<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="bed_number"><?php esc_html_e('Date','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-6">
						<input id="appointment_date" class="form-control validate[required] text-input appointment_date" 
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
						<p> <h3 class="color_1"><?php esc_html_e( 'Green box is available appointments', 'hospital_mgt' ); ?> </h3>
						</p>
						<p> <h3 class="color_2"><?php esc_html_e( 'Blue box is already Booked appointments', 'hospital_mgt' ); ?> </h3>
						</p>
					</note>
				</div>
			</div>
			</div>		
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="time"><?php esc_html_e('Select Appointment Time','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">	
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 appointment_padding_border float_left" align="center">		
							<?php esc_html_e('Morning', 'hospital_mgt' ); ?>
						</div>
					<div class="div_clere_flex">
				<?php
			   $morning_time=array("10:00"=>"10:00AM","10:15"=>"10:15AM","10:30"=>"10:30AM","10:45"=>"10:45AM","11:00"=>"11:00AM","11:15"=>"11:15AM ","11:30"=>"11:30AM","11:45"=>"11:45AM");
				
				 $i = 0;
				foreach ($morning_time as $key => $value)
				{ 
				  ?>
				  <div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 appointment_padding_border col_xs_4_css float_left">	
						<span class="appointment_col_md_12 col-md-12 float_left appoin_span_1">  
						<span class="appointment_col_md_12 col-md-12 time_font_size float_left appoin_span_2">
						<?php echo MJ_hmgt_appoinment_time_language_translation(esc_attr($value)); ?></span>
						<span class="appointment_col_md_12 col-md-12 float_left appoin_span_3"> <span class="appoin_span_4 removeselect selected_<?php print str_replace(":","_","$key")?>">
						<input type="radio" name="realtime" class="time align_time"  value="<?php echo esc_attr($value);?>"></input>
						<input type="hidden" name="timeabc[<?php echo $value;?>]" class="time" value="<?php echo esc_attr($value);?>"></input>
						<input type="hidden" name="time[<?php echo $value;?>]" value="<?php echo esc_attr($key);?>"></input>
						</span>
						</span>
					</div> 
					<?php 
					$i++; 
				} 
				?>
				</div>
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 appointment_padding_border float_left" align="center">		
					<?php esc_html_e('Afternoon', 'hospital_mgt' ); ?>
				</div> 
				<div class="div_clere_flex">
				<?php 
				$afternoon_time=array("12:00"=>"12:00PM","12:15"=>"12:15PM","12:30"=>"12:30PM","12:45"=>"12:45PM","01:00"=>"01:00PM","01:15"=>"01:15PM","01:30"=>"01:30PM","01:45"=>"01:45PM","02:00"=>"02:00PM","02:15"=>"02:15PM","02:30"=>"02:30PM","02:45"=>"02:45PM","03:00"=>"03:00PM","03:15"=>"03:15PM","03:30"=>"03:30PM","03:45"=>"03:45PM","04:00"=>"04:00PM","04:15"=>"04:15PM","04:30"=>"04:30PM","04:45"=>"04:45PM","05:00"=>"05:00PM","05:15"=>"05:15PM","05:30"=>"05:30PM","05:45"=>"05:45PM");
				
				 $i = 0;
				foreach ($afternoon_time as $key => $value)
				{ 
				  ?>
					<div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 appointment_padding_border col_xs_4_css float_left">	
						<span class="appointment_col_md_12 col-md-12 float_left appoin_span_1">  
						<span class="appointment_col_md_12 col-md-12 time_font_size float_left appoin_span_2"><?php echo MJ_hmgt_appoinment_time_language_translation($value); ?></span>
						<span class="appointment_col_md_12 col-md-12 float_left appoin_span_3"> <span class="appoin_span_4 removeselect selected_<?php print str_replace(":","_","$key")?> ">
						<input type="radio" name="realtime" class="time" value="<?php echo esc_attr($value);?>"></input>
						<input type="hidden" name="timeabc[<?php echo $value;?>]" class="time" value="<?php echo esc_attr($value);?>"></input>
						<input type="hidden" name="time[<?php echo $value;?>]" value="<?php echo esc_attr($key);?>"></input></span>
						</span>
					</div> 
					<?php  
					$i++; 
				} ?>
                </div>
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 appointment_padding_border float_left" align="center">		
					<?php esc_html_e('Evening', 'hospital_mgt' ); ?>
				</div>
               <div class="div_clere_flex">				
				<?php 
				$evening_time=array("06:00"=>"06:00PM","06:15"=>"06:15PM","06:30"=>"06:30PM","06:45"=>"06:45PM","07:00"=>"07:00PM","07:15"=>"07:15PM","07:30"=>"07:30PM","07:45"=>"07:45PM","08:00"=>"08:00PM");
				 
				 $i = 0;
				foreach ($evening_time as $key => $value)
				{ 
				  ?>
					<div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 appointment_padding_border col_xs_4_css float_left">
						<span class="appointment_col_md_12 col-md-12 float_left appoin_span_1">  
						<span class="appointment_col_md_12 col-md-12 time_font_size float_left appoin_span_2"><?php echo MJ_hmgt_appoinment_time_language_translation($value); ?></span>
						<span class="appointment_col_md_12 col-md-12 appoin_span_3"> <span class="appoin_span_4 removeselect selected_<?php print str_replace(":","_","$key")?> ">
						<input type="radio" name="realtime" class="time" value="<?php echo esc_attr($value);?>"></input>
						<input type="hidden" name="timeabc[<?php echo $value;?>]" class="time" value="<?php echo esc_attr($value);?>"></input>
						<input type="hidden" name="time[<?php echo $value;?>]" value="<?php echo esc_attr($key);?>"></input>
						</span>
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
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-sm-2 control-label form-label" for="enable"><?php esc_html_e('Send SMS','hospital_mgt');?></label>
						<div class="col-sm-8">
							 <div class="checkbox">
								<label>
									<input id="chk_sms_sent11" class="margin_top_10 margin_left_min_15_res" type="checkbox" <?php $smgt_sms_service_enable = 0;if($smgt_sms_service_enable) echo "checked";?> value="1" name="hmgt_sms_service_enable">
								</label>
							</div>
							 
						</div>
					</div>
				</div>
				<?php wp_nonce_field( 'save_appointment_nonce' ); ?>
				<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
					<input type="submit" value="<?php esc_html_e('Save','hospital_mgt'); ?>" name="save_appointment" class="btn btn-success"/>
				</div>
			</form><!-- END Appointment FORM-->
        </div><!-- END PANEL BODY DIV -->
	</div><!-- END TAB PANE  DIV -->
		
	<!-- doctor side  -->
	
	<!---   start add time tab -->	
	<div class="tab-pane <?php if($active_tab == 'appointment_time'){?>fade active in<?php }?>" id="add_appointment"><!-- STAR TAB PANE DIV -->
         <div class="panel-body" ><!-- STAR PANEL BODY DIV -->
				<form name="apintment_time_form" action="" method="post" class="form-horizontal apointment_times_form" id="patient_form"><!--- START Appointment TIME FORM -->
				<div class="form-group appointment_note display_none">	
					<div class="mb-3 row">	
						<div class="col-sm-offset-2 col-sm-10">
							<note>
								<p>
								<h3 class="appointment_msg color_3"></h3>
								<input type="hidden" name="already_appointment_set_time" id="already_appointment_set_time" value="">	
								</p>				
							</note>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-sm-2 control-label" for="notice_content"><?php esc_html_e('Start Date','hospital_mgt');?><span class="require-field">*</span></label>
						
						<div class="col-sm-8">
						<input id="appointment_time_startdate" class="appointment_start_date appointmet_sdate datepicker form-control validate[required] text-input"  type="text" value="" name="appointment_time_startdate" readonly>
								
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-sm-2 control-label" for="notice_content"><?php esc_html_e('End Date','hospital_mgt');?><span class="require-field">*</span></label>
						
						<div class="col-sm-8">
						<input id="appointment_time_enddate" class="datepicker form-control validate[required] text-input"  type="text" value="" name="appointment_time_enddate" readonly>
							
						</div>
					</div>
				</div>	
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label" for="time"><?php esc_html_e('Add Appointment Time','hospital_mgt');?><span class="require-field">*</span></label>
				</div>
			</div>
			<?php 
				$days=array("monday"=>esc_html__('Monday','hospital_mgt'),"tuesday"=>esc_html__('Tuesday','hospital_mgt'),"wednesday"=>esc_html__('Wednesday','hospital_mgt'),"thursday"=>esc_html__('Thursday','hospital_mgt'),"friday"=>esc_html__('Friday','hospital_mgt'),"saturday"=>esc_html__('Saturday','hospital_mgt'),"sunday"=>esc_html__('Sunday','hospital_mgt'));
			?>
			<div class="form-group">
				<div class="mb-3 row">	
				<?php 
				foreach($days as $key => $value)
				{	
					$day=$key;	
					?>		
				
				<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
					<div class="panel-group accordion" id="accordionExample">
					<div class="panel panel-default accordion-item">
						<div class="panel-heading padding_0">
							<h4 class="accordion-header panel-title" id="heading_<?php echo $day;?>">
								<button class="accordion-button accordion-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_<?php echo $day;?>" aria-controls="collapse_<?php echo $day;?>">
								 <?php echo $value; ?>
							  </button>
							</h4>
					   </div>		
					   	<div id="collapse_<?php echo $day;?>" class="accordion-collapse collapse" aria-labelledby="heading_<?php echo $day;?>" data-bs-parent="#accordionExample">
							<div class="accordion-body panel-body">
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 appointment_padding_border float_left" align="center">		
								<?php esc_html_e('Morning', 'hospital_mgt' ); ?>
							</div> 
							<div class="div_clere_flex">
							<?php						
							 $morning_time=array("10:00"=>"10:00".esc_html__('AM','hospital_mgt'),"10:15"=>"10:15".esc_html__('AM','hospital_mgt'),"10:30"=>"10:30".esc_html__('AM','hospital_mgt'),"10:45"=>"10:45".esc_html__('AM','hospital_mgt'),"11:00"=>"11:00".esc_html__('AM','hospital_mgt'),"11:15"=>"11:15".esc_html__('AM','hospital_mgt'),"11:30"=>"11:30".esc_html__('AM','hospital_mgt'),"11:45"=>"11:45".esc_html__('AM','hospital_mgt'));
							
							$i = 0;
				
							foreach ($morning_time as $key => $value)
							{ 						
								?>					
								<div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 appointment_padding_border col_xs_4_css float_left">
									<span class="appointment_col_md_12 col-md-12 float_left appoin_span_1">  
									<span class="appointment_col_md_12 col-md-12 time_font_size float_left appoin_span_2"><?php echo esc_attr($value); ?></span>
									<span class="appointment_col_md_12 col-md-12 float_left appoin_span_3">
									<span class="appoin_span_4 selected_<?php print str_replace(":","_","$key")?>_<?php echo esc_attr($day); ?>">
									<input type="checkbox" class="avilable_time" id="chktime" name="time[<?php echo esc_attr($key);?>][<?php echo $day;?>]" value="<?php echo esc_attr($key);?>"> </input>
									</span>
									</span>
								</div>
								<?php 
								$i++; 
							}
							?>	
                            </div>						
							<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 appointment_padding_border float_left" align="center">		
								<?php esc_html_e('Afternoon', 'hospital_mgt' ); ?>
							</div>
							<div class="div_clere_flex">	
								<?php 
								$afternoon_time=array("12:00"=>"12:00".esc_html__('PM','hospital_mgt'),"12:15"=>"12:15".esc_html__('PM','hospital_mgt'),"12:30"=>"12:30".esc_html__('PM','hospital_mgt'),"12:45"=>"12:45".esc_html__('PM','hospital_mgt'),"01:00"=>"01:00".esc_html__('PM','hospital_mgt'),"01:15"=>"01:15".esc_html__('PM','hospital_mgt'),"01:30"=>"01:30".esc_html__('PM','hospital_mgt'),"01:45"=>"01:45".esc_html__('PM','hospital_mgt'),"02:00"=>"02:00".esc_html__('PM','hospital_mgt'),"02:15"=>"02:15".esc_html__('PM','hospital_mgt'),"02:30"=>"02:30".esc_html__('PM','hospital_mgt'),"02:45"=>"02:45".esc_html__('PM','hospital_mgt'),"03:00"=>"03:00".esc_html__('PM','hospital_mgt'),"03:15"=>"03:15".esc_html__('PM','hospital_mgt'),"03:30"=>"03:30".esc_html__('PM','hospital_mgt'),"03:45"=>"03:45".esc_html__('PM','hospital_mgt'),"04:00"=>"04:00".esc_html__('PM','hospital_mgt'),"04:15"=>"04:15".esc_html__('PM','hospital_mgt'),"04:30"=>"04:30".esc_html__('PM','hospital_mgt'),"04:45"=>"04:45".esc_html__('PM','hospital_mgt'),"05:00"=>"05:00".esc_html__('PM','hospital_mgt'),"05:15"=>"05:15".esc_html__('PM','hospital_mgt'),"05:30"=>"05:30".esc_html__('PM','hospital_mgt'),"05:45"=>"05:45".esc_html__('PM','hospital_mgt'));
								$i = 0;			
								foreach ($afternoon_time as $key => $value)
								{ 
								?>
									<div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 appointment_padding_border col_xs_4_css float_left">
										<span class="appointment_col_md_12 col-md-12 float_left appoin_span_1">  
										<span class="appointment_col_md_12 col-md-12 time_font_size float_left appoin_span_2"><?php echo esc_attr($value); ?></span>
										<span class="appointment_col_md_12 col-md-12 float_left appoin_span_3">
										<span class="appoin_span_4 selected_<?php print str_replace(":","_","$key")?>_<?php echo esc_attr($day); ?>">
										<input type="checkbox" class="avilable_time" id="chktime" name="time[<?php echo esc_attr($key);?>][<?php echo esc_attr($day);?>]" value="<?php echo esc_attr($key);?>"></input></span>
										</span>
									</div>	
									<?php 
									$i++; 
								} 
								?>	
                          </div>							
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 appointment_padding_border float_left" align="center">		
							<?php esc_html_e('Evening', 'hospital_mgt' ); ?>
						</div>
                       <div class="div_clere_flex">						
							<?php
							$evening_time=array("06:00"=>"06:00".esc_html__('PM','hospital_mgt'),"06:15"=>"06:15".esc_html__('PM','hospital_mgt'),"06:30"=>"06:30".esc_html__('PM','hospital_mgt'),"06:45"=>"06:45".esc_html__('PM','hospital_mgt'),"07:00"=>"07:00".esc_html__('PM','hospital_mgt'),"07:15"=>"07:15".esc_html__('PM','hospital_mgt'),"07:30"=>"07:30".esc_html__('PM','hospital_mgt'),"07:45"=>"07:45".esc_html__('PM','hospital_mgt'),"08:00"=>"08:00".esc_html__('PM','hospital_mgt'));
							$i = 0;		
							
							foreach ($evening_time as $key => $value)
							{ 
							?>
								<div class="col-lg-2 col-md-3 col-sm-3 col-xs-4 appointment_padding_border col_xs_4_css float_left">
									<span class="appointment_col_md_12 col-md-12 float_left appoin_span_1">  
									<span class="appointment_col_md_12 col-md-12 time_font_size float_left appoin_span_2"><?php echo esc_attr($value); ?></span>
									<span class="appointment_col_md_12 col-md-12 float_left appoin_span_3">
									<span class="appoin_span_4 selected_<?php print str_replace(":","_","$key")?>_<?php echo esc_attr($day); ?>">
									<input type="checkbox" class="avilable_time" id="chktime" name="time[<?php echo esc_attr($key);?>][<?php echo esc_attr($day);?>]" value="<?php echo esc_attr($key);?>"></input></span>
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
				</div>	
			</div>
			<?php } ?>
				</div>
			</div>
				<?php wp_nonce_field( 'save_appointment_time_nonce' ); ?>
				<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
					<input type="submit" id="btnsavetime" value="<?php esc_html_e('Save','hospital_mgt'); ?>" name="save_appointment_time" class="btn btn-success"/>
				</div>
			</form><!-- end appointment time form-->
		</div><!-- end PANEL BODY DIV-->
	</div><!-- end TAB PANE DIV-->
	</div><!-- end PANEL WHITE DIV-->
</div><!-- end PANEL BODY DIV-->