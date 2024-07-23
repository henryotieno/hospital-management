<?php
//Appointment
$obj_appointment = new MJ_hmgt_appointment();
$user_role=MJ_hmgt_get_roles(get_current_user_id());
if($user_role == 'administrator')
{
	$user_access_add=1;
	$user_access_edit=1;
	$user_access_delete=1;
	$user_access_view=1;
}
else
{
	$user_access=MJ_hmgt_get_access_right_for_management_user_page('appointment');
	$user_access_add=$user_access['add'];
	$user_access_edit=$user_access['edit'];
	$user_access_delete=$user_access['delete'];
	$user_access_view=$user_access['view'];
	
	
	if (isset ( $_REQUEST ['page'] ))
	{	
		if($user_access_view == '0')
		{	
			MJ_hmgt_access_right_page_not_access_message_admin();
			die;
		}
		if(!empty($_REQUEST['action']))
		{
			if (isset ( $_REQUEST ['page'] ) && 'appointment' == $user_access['page_link'] && ($_REQUEST['action']=='edit'))
			{
				if($user_access['edit']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}			
			}
			if (isset ( $_REQUEST ['page'] ) && 'appointment' == $user_access['page_link'] && ($_REQUEST['action']=='delete'))
			{
				if($user_access['delete']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}	
			}
			if (isset ( $_REQUEST ['page'] ) && 'appointment' == $user_access['page_link'] && ($_REQUEST['action']=='insert')) 
			{
				if($user_access['add']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}	
			} 
		}
	}
}
$active_tab = isset($_GET['tab'])?$_GET['tab']:'appointmentlist';
$current_sms_service = get_option( 'hmgt_sms_service');
?>
<div class="page-inner min_height_1631"><!-- PANEL INNER DIV START-->
	<div class="page-title"><!-- PANEL TITLE DIV START-->
		<h3><img src="<?php echo esc_url(get_option( 'hmgt_hospital_logo', 'hospital_mgt' )) ?>" class="img-circle head_logo" width="40" height="40" /><?php echo esc_html(get_option('hmgt_hospital_name','hospital_mgt'));?></h3>
	</div><!-- PANEL TITLE DIV END-->
	<?php 
	//------------------- SAVE APPOINTMENT -------------------//
	if(isset($_REQUEST['save_appointment']))
	{	
		$nonce = $_POST['_wpnonce'];
		if (wp_verify_nonce( $nonce, 'save_appointment_nonce' ) )
		{
			if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'insert' || $_REQUEST['action'] == 'edit'))
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
						$message_content = "The Appointment has been booked for $patient_name with Dr. $doctor_name on DATE : ".$_REQUEST['appointment_date']." TIME : ".$_REQUEST['realtime'];
						//-------------------- SEND MESSGAE ------------------//
						if(is_plugin_active('sms-pack/sms-pack.php'))
						{
							$mobile_number=array($doctor_number,$patient_number);
							$current_sms_service = get_option('smgt_sms_service');
							$args = array();
							$args['mobile']=$mobile_number;
							$args['message_from']="Appointment";
							$args['message']=$message_content;					
							if($current_sms_service=='telerivet' || $current_sms_service ="msg91" || $current_sms_service=='bulksmsgateway.in' || $current_sms_service=='textlocal.in' || $current_sms_service=='bulksmsnigeria' || $current_sms_service=='africastalking' || $current_sms_service == 'clickatell')
							{				
								$send = send_sms($args);							
							}
						}	
						if($current_sms_service == 'clickatell')
						{
							
							$clickatell=get_option('hmgt_clickatell_sms_service');
							$username = urlencode($clickatell['username']);
							$password = urlencode($clickatell['password']);
							$api_id = urlencode($clickatell['api_key']);
							$to1 = $doctor_number;
							$to2 = $patient_number;
							$message = urlencode($message_content);
							$doctor=file_get_contents("https://api.clickatell.com/http/sendmsg". "?user=$username&password=$password&api_id=$api_id&to=$to1,$to2&text=$message");
							
						}
						if($current_sms_service == 'twillo')
						{
							//Twilio lib
							require_once HMS_PLUGIN_DIR. '/lib/twilio/Services/Twilio.php';
							$twilio=get_option( 'hmgt_twillo_sms_service');
						
							$account_sid = $twilio['account_sid']; //Twilio SID
							$auth_token = $twilio['auth_token']; // Twilio token
							$from_number = $twilio['from_number'];//My number
							$receiver = $reciever_number; //Receiver Number
							
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
							$message = $message_content; // Message Text
							MJ_hmgt_msg91_send_mail_function($mobile_number,$message,$country_code);
						}		
					}
					if($_REQUEST['action'] == 'edit')
					{
						wp_redirect ( admin_url() . 'admin.php?page=hmgt_appointment&tab=appointmentlist&message=2');
					}
					else
					{
						wp_redirect ( admin_url() . 'admin.php?page=hmgt_appointment&tab=appointmentlist&message=1');
					}	
				}
			}
		}
	}
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete')
	{
		$result = $obj_appointment->delete_appointment($_REQUEST['appointment_id']);
		if($result)
		{
			wp_redirect ( admin_url() . 'admin.php?page=hmgt_appointment&tab=appointmentlist&message=3');
		}
	}
	if(isset($_REQUEST['delete_selected']))
	{		
		if(!empty($_REQUEST['selected_id']))
		{
			
			foreach($_REQUEST['selected_id'] as $id)
			{
				$result=$obj_appointment->delete_appointment($id);
			}
			if($result)
			{
				wp_redirect ( admin_url() . 'admin.php?page=hmgt_appointment&tab=appointmentlist&message=3');
			}
		}
		else
		{
			echo '<script language="javascript">';
			echo 'alert("'.esc_html__('Please select at least one record.','hospital_mgt').'")';
			echo '</script>';
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
			wp_redirect ( admin_url() . 'admin.php?page=hmgt_appointment&tab=appointmentlist&message=4');
		}
	}
	if(isset($_REQUEST['message']))
	{
		$message =$_REQUEST['message'];
		if($message == 1)
		{?>
			<div id="message" class="updated below-h2 notice is-dismissible">
			<p>
			<?php 
				esc_html_e('Record inserted successfully','hospital_mgt');
			?></p></div>
			<?php 
		}
		elseif($message == 2)
		{?>
			<div id="message" class="updated below-h2 notice is-dismissible"><p><?php
				esc_html_e("Record updated successfully.",'hospital_mgt');
				?></p>
				</div>
			<?php 
		}
		elseif($message == 3) 
		{?>
			<div id="message" class="updated below-h2 notice is-dismissible">
			<p>
			<?php 
				esc_html_e('Record deleted successfully','hospital_mgt');
			?>
			</p></div>
			<?php		
		}
		elseif($message == 4) 
		{?>
			<div id="message" class="updated below-h2 notice is-dismissible">
			<p>
			<?php 
				esc_html_e('Appointment Approved successfully','hospital_mgt');
			?>
			</p></div>
			<?php		
		}
	}
	?>
	<div id="main-wrapper"><!-- MAIN WRAPPER DIV START-->
		<div class="row"><!-- ROW DIV START-->
			<div class="col-md-12"> 
				<div class="panel panel-white"><!-- PANEL WHITE DIV START-->
					<div class="panel-body"><!-- PANEL BODY DIV START-->
						<h2 class="nav-tab-wrapper">
							<a href="?page=hmgt_appointment&tab=appointmentlist" class="nav-tab <?php echo $active_tab == 'appointmentlist' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-menu"></span>'.esc_html__('Appointment List', 'hospital_mgt'); ?></a>
							<?php  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
							{?>
							<a href="?page=hmgt_appointment&tab=addappointment&&action=edit&appointment_id=<?php echo $_REQUEST['appointment_id'];?>" class="nav-tab <?php echo $active_tab == 'addappointment' ? 'nav-tab-active' : ''; ?>">
							<?php esc_html_e('Edit Appointment', 'hospital_mgt'); ?></a>  
							<?php 
							}
							else
							{
								if($user_access_add == 1)
							{?>
								<a href="?page=hmgt_appointment&tab=addappointment" class="nav-tab <?php echo $active_tab == 'addappointment' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-plus-alt"></span> '.esc_html__('Add New Appointment', 'hospital_mgt'); ?></a>  
							<?php  
							} }?>						   
						</h2>
						<?php 
						if($active_tab == 'appointmentlist')
						{ 						
						?>	
							<script type="text/javascript">
							jQuery(document).ready(function($) {
								"use strict";
								jQuery('#appointment_list').DataTable({
									"responsive": true,
									 "order": [[ 1, "desc" ]],
									 "aoColumns":[
												  {"bSortable": false},
												  {"bSortable": true},
												  {"bSortable": true},
												  {"bSortable": true},	                                 
												  {"bSortable": false}
											   ],
									language:<?php echo MJ_hmgt_datatable_multi_language();?>
									});
								$('.select_all').on('click', function(e)
								{
									 if($(this).is(':checked',true))  
									 {
										$(".sub_chk").prop('checked', true);  
									 }  
									 else  
									 {  
										$(".sub_chk").prop('checked',false);  
									 } 
								});
							
								$('.sub_chk').on('change',function()
								{ 
									if(false == $(this).prop("checked"))
									{ 
										$(".select_all").prop('checked', false); 
									}
									if ($('.sub_chk:checked').length == $('.sub_chk').length )
									{
										$(".select_all").prop('checked', true);
									}
							  	});
							} );
							</script>
							<form name="wcwm_report" action="" method="post">						
								<div class="panel-body"><!-- PANEL BODY DIV START-->
									<div class="table-responsive"><!-- TABLE RESPONSIVE DIV START-->
										<table id="appointment_list" class="display" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th><input type="checkbox" class="select_all"></th>
												<th><?php esc_html_e( 'Date', 'hospital_mgt' ) ;?></th>
												 <th><?php esc_html_e( 'Patient ', 'hospital_mgt' ) ;?></th>
												  <th><?php esc_html_e( 'Doctor', 'hospital_mgt' ) ;?></th>
												  <th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
												</tr>
											</thead>
											<tfoot>
												<tr>
													<th></th>
												<th><?php esc_html_e( 'Date', 'hospital_mgt' ) ;?></th>
												 <th><?php esc_html_e( 'Patient ', 'hospital_mgt' ) ;?></th>
												  <th><?php esc_html_e( 'Doctor', 'hospital_mgt' ) ;?></th>
												  <th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
												</tr>
											</tfoot>								 
											<tbody>
											 <?php 
											$appointment_data=$obj_appointment->MJ_hmgt_get_all_appointment();
											if(!empty($appointment_data))
											{
												foreach ($appointment_data as $retrieved_data)
												{
												?>
												<tr>
													<td class="title"><input type="checkbox" name="selected_id[]" class="sub_chk" value="<?php echo esc_attr($retrieved_data->appointment_id); ?>"></td>													
													<td class="appointment_time"><?php echo date(MJ_hmgt_date_formate(),strtotime($retrieved_data->appointment_date));?>(<?php echo MJ_hmgt_appoinment_time_language_translation($retrieved_data->appointment_time_with_a); ?>)</td>
													
													<td class="patient">
													<?php 
													$patient_data =	MJ_hmgt_get_user_detail_byid($retrieved_data->patient_id);
													echo esc_html($patient_data['first_name']." ".$patient_data['last_name']);?></td>     
													
													<td class="doctor">
													 <?php $doctor_data =	MJ_hmgt_get_user_detail_byid($retrieved_data->doctor_id);
													echo esc_html($doctor_data['first_name']." ".$doctor_data['last_name']);?></td> 
													
													<td class="action"> 
													<?php
													if(($retrieved_data->status) == '0')		
													{ ?>
													<a  href="?page=hmgt_appointment&action=Approved_appointment&appointment_id=<?php echo esc_attr($retrieved_data->appointment_id);?>" class="btn btn-default" >
													<?php esc_html_e('Approve','hospital_mgt' ) ;?></a>
													<?php }?>
														<?php if($user_access_edit == 1)
													{?>
													<a href="?page=hmgt_appointment&tab=addappointment&action=edit&appointment_id=<?php echo esc_attr($retrieved_data->appointment_id);?>" class="btn btn-info"> 
													<?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
													<?php 
													} 
													?>
													<?php if($user_access_delete == 1)
													{?>	
													<a href="?page=hmgt_appointment&tab=appointmentlist&action=delete&appointment_id=<?php echo esc_attr($retrieved_data->appointment_id);?>" class="btn btn-danger" 
													onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this record?','hospital_mgt');?>');">
													<?php esc_html_e( 'Delete', 'hospital_mgt' ) ;?> </a>
												   <?php }?>
													</td>
												</tr>
												<?php 
												} 												
											}?>
											</tbody>										
										</table>
										<?php if($user_access_delete == 1)
										{?>	
										<div class="print-button pull-left">
											<input  type="submit" value="<?php esc_html_e('Delete Selected','hospital_mgt');?>" name="delete_selected" class="btn btn-danger delete_selected "/>
										</div>
										<?php }?>
									</div><!-- TABEL RESPONSIVEE DIV END-->
								</div><!-- PANEL BODY DIV END-->					   
							</form>
						 <?php 
						}						
						if($active_tab == 'addappointment')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/appointment/add-appointment.php';
						}
						?>
					</div>	<!-- PANEL BODY DIV END-->		
				</div><!-- PANEL WHITE DIV END-->
			</div>
		</div><!-- ROW DIV END-->
	</div><!-- ROW DIV END-->
</div><!-- ROW DIV END-->