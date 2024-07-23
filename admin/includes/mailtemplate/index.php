<?php
$user_role=MJ_hmgt_get_roles(get_current_user_id());
if($user_role == 'administrator')
{
	$user_access_view=1;
	$user_access_add=1;
	$user_access_edit=1;
}
else
{
$user_access=MJ_hmgt_get_access_right_for_management_user_page('mail_template');
$user_access_view=$user_access['view'];
$user_access_add=$user_access['add'];
$user_access_edit=$user_access['edit'];
if (isset ( $_REQUEST ['page'] ))
{	
	if($user_access_view == '0')
	{	
		MJ_hmgt_access_right_page_not_access_message_admin();
		die;
	}
  }
}
?>
<script type="text/javascript">
$(document).ready(function() 
{
	"use strict";
	<?php
	if (is_rtl())
		{
		?>	
			$('#registration_email_template_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#registration_email_template_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>
});
</script>
<?php
if(isset($_REQUEST['Patient_Registration_Template'])){
	
	update_option('MJ_hmgt_patient_registration',$_REQUEST['MJ_hmgt_patient_registration']);
	update_option('MJ_hmgt_registration_email_template',$_REQUEST['MJ_hmgt_registration_email_template']);
} 
if(isset($_REQUEST['Save_Patient_Approved_Template'])){
	update_option('MJ_hmgt_patient_approved_subject',$_REQUEST['MJ_hmgt_patient_approved_subject']);
	update_option('MJ_hmgt_patient_approved_email_template',$_REQUEST['MJ_hmgt_patient_approved_email_template']);
} 

if(isset($_REQUEST['User_registration_email_template_save'])){
	update_option('MJ_hmgt_user_registration_subject',$_REQUEST['MJ_hmgt_user_registration_subject']);
	update_option('MJ_hmgt_user_registration_email_template',$_REQUEST['MJ_hmgt_user_registration_email_template']);	
}

if(isset($_REQUEST['Patient_Assigned_to_Doctor_Mail_Template_save'])){
	update_option('MJ_hmgt_patient_assigned_to_doctor_patient_email_subject',$_REQUEST['MJ_hmgt_patient_assigned_to_doctor_patient_email_subject']);
	update_option('MJ_hmgt_patient_assigned_to_doctor_patient_email_template',$_REQUEST['MJ_hmgt_patient_assigned_to_doctor_patient_email_template']);	
} 

if(isset($_REQUEST['Patient_Assigned_to_Doctor_doctor_Mail_Template_save'])){
	update_option('MJ_hmgt_patient_assigned_to_doctor_mail_subject',$_REQUEST['MJ_hmgt_patient_assigned_to_doctor_mail_subject']);
	update_option('MJ_hmgt_patient_assigned_to_doctor_mail_template',$_REQUEST['MJ_hmgt_patient_assigned_to_doctor_mail_template']);
} 

if(isset($_REQUEST['Patient_Assigned_to_Nurse_template_save'])){
	update_option('MJ_hmgt_patient_assigned_to_nurse_subject',$_REQUEST['MJ_hmgt_patient_assigned_to_nurse_subject']);
	update_option('MJ_hmgt_patient_assigned_to_nurse_template',$_REQUEST['MJ_hmgt_patient_assigned_to_nurse_template']);	
} 

if(isset($_REQUEST['Appointment_Booking_Patient_mail_template_save'])){
	update_option('MJ_hmgt_appointment_booking_patient_mail_subject',$_REQUEST['MJ_hmgt_appointment_booking_patient_mail_subject']);
	update_option('MJ_hmgt_appointment_booking_patient_mail_template',$_REQUEST['MJ_hmgt_appointment_booking_patient_mail_template']);		
} 

 if(isset($_REQUEST['Appointment_Booking_Patient_Doctor_mail_template_save'])){
	update_option('MJ_hmgt_appointment_booking_doctor_mail_subject',$_REQUEST['MJ_hmgt_appointment_booking_doctor_mail_subject']);
	update_option('MJ_hmgt_appointment_booking_patient_mail_template',$_REQUEST['MJ_hmgt_appointment_booking_patient_mail_template']);		
} 

if(isset($_REQUEST['Add_Prescription_save'])){
	update_option('MJ_hmgt_add_prescription_subject',$_REQUEST['MJ_hmgt_add_prescription_subject']);
	update_option('MJ_hmgt_add_prescription_template',$_REQUEST['MJ_hmgt_add_prescription_template']);	
} 

if(isset($_REQUEST['Payment_Received_against_Invoice_save'])){
	update_option('MJ_hmgt_payment_received_invoice_subject',$_REQUEST['MJ_hmgt_payment_received_invoice_subject']);
	update_option('MJ_hmgt_payment_received_invoice_template',$_REQUEST['MJ_hmgt_payment_received_invoice_template']);	
} 

  if(isset($_REQUEST['Generate_Invoice_Template_save'])){
	update_option('MJ_hmgt_generate_invoice_subject',$_REQUEST['MJ_hmgt_generate_invoice_subject']);
	update_option('MJ_hmgt_generate_invoice_template',$_REQUEST['MJ_hmgt_generate_invoice_template']);	
} 

 if(isset($_REQUEST['Assign_Bed_to_Patient_save'])){
	update_option('MJ_hmgt_assign_bed_patient_subject',$_REQUEST['MJ_hmgt_assign_bed_patient_subject']);
	update_option('MJ_hmgt_assign_bed_patient_template',$_REQUEST['MJ_hmgt_assign_bed_patient_template']);	
} 

if(isset($_REQUEST['Message_Received'])){
	update_option('MJ_hmgt_message_received_subject',$_REQUEST['MJ_hmgt_message_received_subject']);
	update_option('MJ_hmgt_message_received_template',$_REQUEST['MJ_hmgt_message_received_template']);	
} 

if(isset($_REQUEST['diagnosis_report_template'])){
	update_option('MJ_hmgt_add_diagnosis_report_subject',$_REQUEST['MJ_hmgt_add_diagnosis_report_subject']);
	update_option('MJ_hmgt_add_diagnosis_report_template',$_REQUEST['MJ_hmgt_add_diagnosis_report_template']);	
} 

if(isset($_REQUEST['diagnosis_report_template_doctor'])){
	update_option('MJ_hmgt_add_diagnosis_report_subject_doctor',$_REQUEST['MJ_hmgt_add_diagnosis_report_subject_doctor']);
	update_option('MJ_hmgt_add_diagnosis_report_template_doctor',$_REQUEST['MJ_hmgt_add_diagnosis_report_template_doctor']);	
} 

if(isset($_REQUEST['cancel_appointment_doctor'])){
	update_option('MJ_hmgt_cancel_appointment_doctor_subject',$_REQUEST['MJ_hmgt_cancel_appointment_doctor_subject']);
	update_option('MJ_hmgt_cancel_appointment_doctor_mail',$_REQUEST['MJ_hmgt_cancel_appointment_doctor_mail']);	
} 

if(isset($_REQUEST['cancel_appointment_patient'])){
	update_option('MJ_hmgt_cancel_appointment_patient_subject',$_REQUEST['MJ_hmgt_cancel_appointment_patient_subject']);
	update_option('MJ_hmgt_cancel_appointment_patient_mail',$_REQUEST['MJ_hmgt_cancel_appointment_patient_mail']);	
} 
   
if(isset($_REQUEST['edit_appointment_doctor'])){
	update_option('MJ_hmgt_edit_appointment_doctor_subject',$_REQUEST['MJ_hmgt_edit_appointment_doctor_subject']);
	update_option('MJ_hmgt_edit_appointment_doctor_mail',$_REQUEST['MJ_hmgt_edit_appointment_doctor_mail']);	
} 

if(isset($_REQUEST['edit_appointment_patient'])){
	update_option('MJ_hmgt_edit_appointment_patient_subject',$_REQUEST['MJ_hmgt_edit_appointment_patient_subject']);
	update_option('MJ_hmgt_edit_appointment_patient_mail',$_REQUEST['MJ_hmgt_edit_appointment_patient_mail']);	
}

if(isset($_REQUEST['Appointment_approve_Patient_mail_template_save'])){
	update_option('MJ_hmgt_appointment_approve_patient_mail_subject',$_REQUEST['MJ_hmgt_appointment_approve_patient_mail_subject']);
	update_option('MJ_hmgt_appointment_approve_patient_mail_template',$_REQUEST['MJ_hmgt_appointment_approve_patient_mail_template']);	
}

if(isset($_REQUEST['virtual_appointment_doctor_mail_template_save'])){
	update_option('virtual_appointment_doctor_reminder_mail_subject',$_REQUEST['virtual_appointment_doctor_reminder_mail_subject']);
	update_option('virtual_appointment_doctor_reminder_mail_content',$_REQUEST['virtual_appointment_doctor_reminder_mail_content']);	
}

if(isset($_REQUEST['virtual_appointment_patient_mail_template_save'])){
	update_option('virtual_appointment_patient_reminder_mail_subject',$_REQUEST['virtual_appointment_patient_reminder_mail_subject']);
	update_option('virtual_appointment_patient_reminder_mail_content',$_REQUEST['virtual_appointment_patient_reminder_mail_content']);	
}

if(isset($_REQUEST['virtual_class_invite_mail_template_save'])){
	update_option('virtual_class_invite_mail_subject',$_REQUEST['virtual_class_invite_mail_subject']);
	update_option('virtual_class_invite_mail_content',$_REQUEST['virtual_class_invite_mail_content']);	
}


if(isset($_REQUEST['medicine_out_of_stock_mail_template_save'])){
	update_option('MJ_hmgt_medicine_out_of_stock_email_subject',$_REQUEST['MJ_hmgt_medicine_out_of_stock_email_subject']);
	update_option('MJ_hmgt_medicine_out_of_stock_email_template',$_REQUEST['MJ_hmgt_medicine_out_of_stock_email_template']);	
}
?>

<div class="page-inner min_height_1088"> <!-- PAGE INNER DIV START--> 
	<div class="page-title"> <!-- PAGE TITLE DIV START--> 
			<h3><img src="<?php echo esc_url(get_option( 'hmgt_hospital_logo', 'hospital_mgt' )) ?>" class="img-circle head_logo" width="40" height="40" /><?php echo esc_html(get_option('hmgt_hospital_name','hospital_mgt'));?></h3>
			</h3>
	</div> <!-- PAGE TITLE DIV END--> 	
	<div id="main-wrapper"> <!-- MAIN WRAPPER DIV START--> 
		<div class="row"> <!-- ROW DIV START--> 
			<div class="col-md-12">
				<div class="panel panel-white mail_template_panel"> <!-- PANEL WHITE DIV START--> 
					<div class="panel-body"> <!-- PANEL BODY DIV START--> 
						<div class="panel-group accordion" id="accordionExample"><!-- PANEL GROUP DIV START--> 
								
								<div class="panel panel-default accordion-item"> <!-- PANEL DEFAULT DIV START--> 
								<div class="panel-heading padding_0">
									<h4 class="accordion-header panel-title" id="headingOne">
										<button class="accordion-button accordion-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
										<?php esc_html_e('Patient Registration Template ','hospital_mgt'); ?>
									  </button>
									</h4>
								</div>
								<div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
									<div class="accordion-body panel-body"><!-- PANEL BODY DIV START--> 
										<form id="registration_email_template_form" class="form-horizontal" method="post" action="" name="parent_form">
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Email Subject','hospital_mgt');?> </label>
												<div class="col-md-8">
													<input class="form-control validate[required]" name="MJ_hmgt_patient_registration" id="MJ_hmgt_patient_registration" placeholder="Enter email subject" value="<?php print get_option('MJ_hmgt_patient_registration'); ?>">
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Registration Email Template','hospital_mgt'); ?> </label>
												<div class="col-md-8">
													<textarea name="MJ_hmgt_registration_email_template" class="form-control validate[required] min_height_200"><?php print get_option('MJ_hmgt_registration_email_template'); ?></textarea>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">
													<label><?php esc_html_e('You can use following variables in the email template:','hospital_mgt');?></label><br>
													
													<label><strong>{{Patient Name}}</strong> <?php esc_html_e('User name of Patient','hospital_mgt'); ?></label><br>
													<label><strong>{{Patient ID}}</strong> <?php esc_html_e('Id of Patient','hospital_mgt'); ?></label><br>
													<label><strong>{{Hospital Name}}</strong> <?php esc_html_e('Name Of Hospital','hospital_mgt'); ?></label><br>
													<label><strong>{{Login Link}}</strong> <?php esc_html_e('Login Page  Link','hospital_mgt'); ?></label><br>	
												</div>
											</div>
										</div>
										<?php 
							              if($user_access_add == 1 OR $user_access_edit == 1 )
							             {
									     ?>
										<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
											<input value="<?php esc_html_e('Save','hospital_mgt'); ?>" name="Patient_Registration_Template" class="btn btn-success" type="submit">
										</div>
										 <?php } ?>
										</form>
									</div><!-- PANEL BODY DIV END--> 
								</div>
							</div><!-- PANEL DEFAULT DIV END-->
							<!---Member Approved by admin  -->
							<div class="accordion-item panel panel-default"><!-- PANEL DEFAULT DIV START--> 
								<div class="panel-heading padding_0">
									 <h4 class="accordion-header" id="headingtwentythree">
										<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwentyThree" aria-expanded="false" aria-controls="collapseTwentyThree">
											<?php esc_html_e('Patient Approved Template ','hospital_mgt'); ?>
										  </button>
									</h4>
								</div>
								<div id="collapseTwentyThree" class="accordion-collapse collapse" aria-labelledby="headingtwentythree" data-bs-parent="#accordionExample">
									<div class="accordion-body panel-body"><!-- PANEL BODY DIV START--> 
										<form id="email_template_form" class="form-horizontal" method="post" action="" name="parent_form">
											<div class="form-group">
												<div class="mb-3 row">	
													<label for="learner_complete_quiz_notification_title" class="col-sm-3 control-label form-label"><?php esc_html_e('Email Subject','hospital_mgt');?> <span class="require-field">*</span></label>
													<div class="col-md-8">
														<input id="MJ_hmgt_patient_approved_subject" class="form-control validate[required]" name="MJ_hmgt_patient_approved_subject" id="Patient_Approved_Subject" placeholder="Enter Email Subject" value="<?php echo get_option('MJ_hmgt_patient_approved_subject'); ?>">
													</div>
												</div>
											</div>
											<div class="form-group">
												<div class="mb-3 row">	
													<label for="learner_complete_quiz_notification_mailcontent" class="col-sm-3 control-label form-label"><?php esc_html_e('Patient Approved Template','hospital_mgt');?><span class="require-field">*</span></label>
													<div class="col-md-8">
														<textarea id="MJ_hmgt_patient_approved_email_template" name="MJ_hmgt_patient_approved_email_template" class="form-control validate[required]"><?php echo get_option('MJ_hmgt_patient_approved_email_template');?></textarea>
													</div>
												</div>
											</div>
											<div class="form-group">
												<div class="mb-3 row">	
													<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">
														<label><?php esc_html_e('You can use following variables in the email template:','hospital_mgt');?></label><br>				
														<label><strong>{{Patient Name}} - </strong><?php esc_html_e('The Patient name','hospital_mgt');?></label><br>
														<label><strong>{{Hospital Name}} - </strong><?php esc_html_e('Name Of Hospital','hospital_mgt');?></label><br>
														<label><strong>{{Login Link}} - </strong><?php esc_html_e('Login Link','hospital_mgt');?></label><br>
													</div>
												</div>
											</div>
											<?php 
							              if($user_access_add == 1 OR $user_access_edit == 1 )
							              {
									     ?>
											<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">
												<input type="submit" value="<?php  esc_html_e('Save','hospital_mgt')?>" name="Save_Patient_Approved_Template" class="btn btn-success"/>
											 </div>
										<?php } ?>
										</form>
									</div><!-- PANEL BODY DIV END--> 
								</div>
							</div>	<!-- PANEL DEFAULT DIV END--> 						
							<div class="accordion-item panel panel-default"><!-- PANEL DEFAULT DIV START--> 
							<div class="panel-heading padding_0">
							  <h4 class="panel-title">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsetwo" aria-expanded="false" aria-controls="collapsetwo">
										<?php _e('Add User in system Template ','hospital_mgt'); ?>
									  </button>
							  </h4>
							</div>
							<div id="collapsetwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
									<div class="accordion-body panel-body"><!-- PANEL BODY DIV START--> 
								<form id="registration_email_template_form" class="form-horizontal" method="post" action="" name="parent_form">	
									<div class="form-group">
										<div class="mb-3 row">	
											<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Email Subject','hospital_mgt');?> </label>
											<div class="col-md-8">
												<input class="form-control validate[required]" name="MJ_hmgt_user_registration_subject" id="MJ_hmgt_user_registration_subject" placeholder="Enter email subject" value="<?php print get_option('MJ_hmgt_user_registration_subject'); ?>">
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="mb-3 row">	
											<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Add User in system Template','hospital_mgt'); ?> </label>
											<div class="col-md-8">
												<textarea  name="MJ_hmgt_user_registration_email_template" class="form-control validate[required] min_height_200"><?php print get_option('MJ_hmgt_user_registration_email_template'); ?></textarea>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="mb-3 row">	
											<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">
												<label><?php esc_html_e('You can use following variables in the email template:','hospital_mgt');?></label><br>
												<label><strong>{{UserName}}</strong> <?php esc_html_e('Name Of User','hospital_mgt'); ?></label><br>
												<label><strong>{{Role Name}}</strong> <?php esc_html_e('Role Of User','hospital_mgt'); ?></label><br>
												<label><strong>{{User_Name}}</strong> <?php esc_html_e('User name','hospital_mgt'); ?></label><br>
												<label><strong>{{Password}}</strong> <?php esc_html_e('Password Of User','hospital_mgt'); ?></label><br>
												<label><strong>{{Hospital Name}}</strong> <?php esc_html_e('Name Of Hospital','hospital_mgt'); ?></label><br>
												<label><strong>{{Login Link}}</strong> <?php esc_html_e('Login Page  Link','hospital_mgt'); ?></label><br>
											</div>
										</div>
									</div>
									<?php 
							              if($user_access_add == 1 OR $user_access_edit == 1 )
							             {
									     ?>
									<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
										<input value="<?php esc_html_e('Save','hospital_mgt'); ?>" name="User_registration_email_template_save" class="btn btn-success" type="submit">
									</div>
									<?php } ?>
								</form>
							  </div><!-- PANEL BODY DIV END--> 
							</div>
						</div>	<!-- PANEL DEFAULT DIV END--> 						
						<div class="panel panel-default accordion-item"><!-- PANEL DEFAULT DIV START--> 
							<div class="panel-heading padding_0">
							  <h4 class="panel-title">
							  	<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
											 <?php esc_html_e('Doctor Assigned to Patient Mail Template','hospital_mgt'); ?>
										</button>
							  </h4>
							</div>
							<div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
									<div class="accordion-body panel-body"><!-- PANEL BODY DIV START--> 
								<form id="registration_email_template_form" class="form-horizontal" method="post" action="" name="parent_form">
									<div class="form-group">
										<div class="mb-3 row">	
											<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Email Subject','hospital_mgt');?> </label>
											<div class="col-md-8">
												<input class="form-control validate[required]" name="MJ_hmgt_patient_assigned_to_doctor_patient_email_subject" id="MJ_hmgt_patient_assigned_to_doctor_patient_email_subject" placeholder="Enter email subject" value="<?php print get_option('MJ_hmgt_patient_assigned_to_doctor_patient_email_subject'); ?>">
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="mb-3 row">	
											<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Patient Assigned to Doctor Patient Mail Template','hospital_mgt'); ?> </label>
											<div class="col-md-8">
												<textarea name="MJ_hmgt_patient_assigned_to_doctor_patient_email_template" class="form-control validate[required] min_height_200"><?php print get_option('MJ_hmgt_patient_assigned_to_doctor_patient_email_template'); ?></textarea>
											</div>
										</div>
									</div>
									<div class="form-group">
										<div class="mb-3 row">	
											<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">
												<label><?php esc_html_e('You can use following variables in the email template:','hospital_mgt');?></label><br>
												<label><strong>{{Patient Name}}</strong> <?php esc_html_e('Name of Patient','hospital_mgt'); ?></label><br>
												<label><strong>{{Doctor Name}}</strong> <?php esc_html_e('Name Of Dr Doctor','hospital_mgt'); ?></label><br>
												<label><strong>{{Department Name}}</strong> <?php esc_html_e('Name Of Department ','hospital_mgt'); ?></label><br>
												<label><strong>{{Hospital Name}}</strong> <?php esc_html_e('Name Of Hospital','hospital_mgt'); ?></label><br>
											</div>
										</div>
									</div>
									<?php 
							              if($user_access_add == 1 OR $user_access_edit == 1 )
							             {
									     ?>
									<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
										<input value="<?php esc_html_e('Save','hospital_mgt'); ?>" name="Patient_Assigned_to_Doctor_Mail_Template_save" class="btn btn-success" type="submit">
									</div>
									<?php } ?>
								</form>
							  </div><!-- PANEL BODY DIV END--> 
							</div>
						</div>	<!-- PANEL DEFAULT DIV END--> 						
						<div class="panel panel-default accordion-item"><!-- PANEL DEFAULT DIV START--> 
								<div class="panel-heading padding_0">
								  <h4 class="panel-title">
									<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
											<?php esc_html_e('Patient Assigned To Doctor Mail Template','hospital_mgt'); ?>
									</button>
								  </h4>
								</div>
								<div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
									<div class="accordion-body panel-body"><!-- PANEL BODY DIV START--> 
									<form id="registration_email_template_form" class="form-horizontal" method="post" action="" name="parent_form">
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Email Subject','hospital_mgt');?> </label>
												<div class="col-md-8">
													<input class="form-control validate[required]" name="MJ_hmgt_patient_assigned_to_doctor_mail_subject" id="MJ_hmgt_patient_assigned_to_doctor_mail_subject" placeholder="Enter email subject" value="<?php print get_option('MJ_hmgt_patient_assigned_to_doctor_mail_subject'); ?>">
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Patient Assign to Doctor Mail Template','hospital_mgt'); ?> </label>
												<div class="col-md-8">
													<textarea name="MJ_hmgt_patient_assigned_to_doctor_mail_template" class="form-control validate[required] min_height_200"><?php print get_option('MJ_hmgt_patient_assigned_to_doctor_mail_template'); ?></textarea>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">
													<label><?php esc_html_e('You can use following variables in the email template:','hospital_mgt');?></label><br>
													<label><strong>{{Patient Name}} </strong><?php esc_html_e('Name of Patient','hospital_mgt'); ?></label><br>
													<label><strong>{{Doctor Name}} </strong><?php esc_html_e('Name Of Dr Doctor','hospital_mgt'); ?></label><br>
													<label><strong>{{Hospital Name}} </strong><?php esc_html_e('Name Of Hospital','hospital_mgt'); ?></label><br>
												</div>
											</div>
										</div>
										<?php 
							              if($user_access_add == 1 OR $user_access_edit == 1 )
							             {
									     ?>
										<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
											<input value="<?php esc_html_e('Save','hospital_mgt'); ?>" name="Patient_Assigned_to_Doctor_doctor_Mail_Template_save" class="btn btn-success" type="submit">
										</div>
										<?php } ?>
									</form>
								  </div><!-- PANEL BODY DIV END--> 
								</div>
							</div>	<!-- PANEL DEFAULT DIV END--> 						
							<div class="panel panel-default accordion-item"><!-- PANEL DEFAULT DIV START--> 
								<div class="panel-heading padding_0">
								  <h4 class="panel-title">
								  	<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
											<?php esc_html_e('Patient Assigned to Nurse Mail Template','hospital_mgt'); ?>
										</button>
								  </h4>
								</div>
								<div id="collapseFive" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
									<div class="accordion-body panel-body"><!-- PANEL BODY DIV START--> 
									<form id="registration_email_template_form" class="form-horizontal" method="post" action="" name="parent_form">
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Email Subject','hospital_mgt');?> </label>
												<div class="col-md-8">
													<input class="form-control validate[required]" name="MJ_hmgt_patient_assigned_to_nurse_subject" id="MJ_hmgt_patient_assigned_to_nurse_subject" placeholder="Enter email subject" value="<?php print get_option('MJ_hmgt_patient_assigned_to_nurse_subject'); ?>">
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Patient Assigned to Nurse Mail Template','hospital_mgt'); ?> </label>
												<div class="col-md-8">
													<textarea  name="MJ_hmgt_patient_assigned_to_nurse_template" class="form-control validate[required] min_height_200"><?php print get_option('MJ_hmgt_patient_assigned_to_nurse_template'); ?></textarea>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">
													<label><?php esc_html_e('You can use following variables in the email template:','hospital_mgt');?></label><br>
													<label><strong>{{Nurse Name}}</strong> <?php esc_html_e('Name of Nurse','hospital_mgt'); ?></label><br>
													<label><strong>{{Patient Name}}</strong> <?php esc_html_e('Name Of Patient','hospital_mgt'); ?></label><br>
													
													<label><strong>{{Hospital Name}}</strong> <?php esc_html_e('Name Of Hospital','hospital_mgt'); ?></label><br>
												</div>
											</div>
										</div>
										<?php 
							              if($user_access_add == 1 OR $user_access_edit == 1 )
							             {
									     ?>
										<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
											<input value="<?php esc_html_e('Save','hospital_mgt'); ?>" name="Patient_Assigned_to_Nurse_template_save" class="btn btn-success" type="submit">
										</div>
										<?php } ?>
									</form>
								  </div><!-- PANEL BODY DIV END--> 
								</div>
							</div>	<!-- PANEL DEFAULT DIV END--> 						
							<div class="panel panel-default accordion-item"><!-- PANEL DEFAULT DIV START--> 
								<div class="panel-heading padding_0">
								  <h4 class="panel-title">
								  	<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
											<?php esc_html_e('Patient Appointment Booking Mail template','hospital_mgt'); ?>
										</button>
								  </h4>
								</div>
								<div id="collapseSix" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
									<div class="accordion-body panel-body"><!-- PANEL BODY DIV START--> 
									<form id="registration_email_template_form" class="form-horizontal" method="post" action="" name="parent_form">									
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Email Subject','hospital_mgt');?> </label>
												<div class="col-md-8">
													<input class="form-control validate[required]" name="MJ_hmgt_appointment_booking_patient_mail_subject" id="MJ_hmgt_appointment_booking_patient_mail_subject" placeholder="Enter email subject" value="<?php print get_option('MJ_hmgt_appointment_booking_patient_mail_subject'); ?>">
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Patient Appointment Booking Mail template','hospital_mgt'); ?> </label>
												<div class="col-md-8">
													<textarea  name="MJ_hmgt_appointment_booking_patient_mail_template" class="form-control validate[required] min_height_200"><?php print get_option('MJ_hmgt_appointment_booking_patient_mail_template'); ?></textarea>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">
													<label><?php esc_html_e('You can use following variables in the email template:','hospital_mgt');?></label><br>
													<label><strong>{{Patient Name}}</strong> <?php esc_html_e('Name Of Patient','hospital_mgt'); ?></label><br>
													<label><strong>{{Doctor Name}}</strong> <?php esc_html_e('Name Of Doctor','hospital_mgt'); ?></label><br>
													<label><strong>{{Appointment Time}}</strong> <?php esc_html_e('Appointment Time','hospital_mgt'); ?></label><br>
													<label><strong>{{Appointment Date}}</strong> <?php esc_html_e('Appointment Date','hospital_mgt'); ?></label><br>
													<label><strong>{{Hospital Name}}</strong> <?php esc_html_e('Name Of Hospital','hospital_mgt'); ?></label><br>
												</div>
											</div>
										</div>
										<?php 
							              if($user_access_add == 1 OR $user_access_edit == 1 )
							             {
									     ?>
										<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
											<input value="<?php esc_html_e('Save','hospital_mgt'); ?>" name="Appointment_Booking_Patient_mail_template_save" class="btn btn-success" type="submit">
										</div>
										<?php } ?>
									</form>
								  </div><!-- PANEL BODY DIV END--> 
								</div>
							</div>	<!-- PANEL DEFAULT DIV END--> 						
							<div class="panel panel-default accordion-item"><!-- PANEL DEFAULT DIV START--> 
								<div class="panel-heading padding_0">
								  <h4 class="panel-title">
								  	<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
											<?php esc_html_e(' Doctor Appointment Booking  Mail Template','hospital_mgt'); ?>
										</button>
								  </h4>
								</div>
								<div id="collapseSeven" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
									<div class="accordion-body panel-body"><!-- PANEL BODY DIV START--> 
									<form id="registration_email_template_form" class="form-horizontal" method="post" action="" name="parent_form">
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Email Subject','hospital_mgt');?> </label>
												<div class="col-md-8">
													<input class="form-control validate[required]" name="MJ_hmgt_appointment_booking_doctor_mail_subject" id="MJ_hmgt_appointment_booking_doctor_mail_subject" placeholder="Enter email subject" value="<?php print get_option('MJ_hmgt_appointment_booking_doctor_mail_subject'); ?>">
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Doctor Appointment Booking  Mail Template','hospital_mgt'); ?> </label>
												<div class="col-md-8">
													<textarea name="MJ_hmgt_appointment_booking_patient_mail_template" class="form-control validate[required] min_height_200"><?php print get_option('MJ_hmgt_appointment_booking_patient_mail_template'); ?></textarea>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">
													<label><?php esc_html_e('You can use following variables in the email template:','hospital_mgt');?></label><br>
													<label><strong>{{Doctor Name}}</strong> <?php esc_html_e('Name Of Doctor','hospital_mgt'); ?></label><br>
													<label><strong>{{Patient Name}}</strong> <?php esc_html_e('Name Of Patient','hospital_mgt'); ?></label><br>
													<label><strong>{{Appointment Time}}</strong> <?php esc_html_e('Appointment Time','hospital_mgt'); ?></label><br>
													<label><strong>{{Appointment Date}}</strong> <?php esc_html_e('Appointment Date','hospital_mgt'); ?></label><br>
													<label><strong>{{Hospital Name}}</strong> <?php esc_html_e('Name Of Hospital','hospital_mgt'); ?></label><br>
												</div>
											</div>
										</div>
										<?php 
							              if($user_access_add == 1 OR $user_access_edit == 1 )
							             {
									     ?>
										<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
											<input value="<?php esc_html_e('Save','hospital_mgt'); ?>" name="Appointment_Booking_Patient_Doctor_mail_template_save" class="btn btn-success" type="submit">
										</div>
										<?php } ?>
									</form>
								  </div><!-- PANEL BODY DIV END--> 
								</div>
							</div>	<!-- PANEL DEFAULT DIV END--> 						
							<div class="panel panel-default accordion-item"><!-- PANEL DEFAULT DIV START--> 
								<div class="panel-heading padding_0">
								  <h4 class="panel-title">
								  	<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseeight" aria-expanded="false" aria-controls="collapseeight">
											<?php esc_html_e('Add Prescription Template','hospital_mgt'); ?>
										</button>
								  </h4>
								</div>
								<div id="collapseeight" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
								<div class="accordion-body panel-body"><!-- PANEL BODY DIV START--> 
									<form id="registration_email_template_form" class="form-horizontal" method="post" action="" name="parent_form">
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Email Subject','hospital_mgt');?> </label>
												<div class="col-md-8">
													<input class="form-control validate[required]" name="MJ_hmgt_add_prescription_subject" id="MJ_hmgt_add_prescription_subject" placeholder="Enter email subject" value="<?php print get_option('MJ_hmgt_add_prescription_subject'); ?>">
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Add Prescription  Mail Template','hospital_mgt'); ?> </label>
												<div class="col-md-8">
													<textarea name="MJ_hmgt_add_prescription_template" class="form-control validate[required] min_height_200"><?php print get_option('MJ_hmgt_add_prescription_template'); ?></textarea>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">
													<label><?php esc_html_e('You can use following variables in the email template:','hospital_mgt');?></label><br>
													<label><strong>{{Doctor Name}}</strong> <?php esc_html_e('Name Of Doctor','hospital_mgt'); ?></label><br>
													<label><strong>{{Patient Name}}</strong> <?php esc_html_e('Name Of Patient','hospital_mgt'); ?></label><br>
													<label><strong>{{Hospital Name}}</strong> <?php esc_html_e('Name Of Hospital','hospital_mgt'); ?></label><br>
												</div>
											</div>
										</div>
										<?php 
							              if($user_access_add == 1 OR $user_access_edit == 1 )
							             {
									     ?>
										<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
											<input value="<?php esc_html_e('Save','hospital_mgt'); ?>" name="Add_Prescription_save" class="btn btn-success" type="submit">
										</div>
										<?php } ?>
									</form>
								  </div><!-- PANEL BODY DIV END--> 
								</div>
							</div>	<!-- PANEL DEFAULT DIV END--> 						
							<div class="panel panel-default accordion-item"><!-- PANEL DEFAULT DIV START--> 
								<div class="panel-heading padding_0">
								  <h4 class="panel-title">
								  	<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsenine" aria-expanded="false" aria-controls="collapsenine">
											<?php esc_html_e('Payment Received against Invoice Template','hospital_mgt'); ?>
									</button>
								  </h4>
								</div>
								<div id="collapsenine" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
									<div class="accordion-body panel-body"><!-- PANEL BODY DIV START--> 
									<form id="registration_email_template_form" class="form-horizontal" method="post" action="" name="parent_form">
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Email Subject','hospital_mgt');?> </label>
												<div class="col-md-8">
													<input class="form-control validate[required]" name="MJ_hmgt_payment_received_invoice_subject" id="MJ_hmgt_payment_received_invoice_subject" placeholder="Enter email subject" value="<?php print get_option('MJ_hmgt_payment_received_invoice_subject'); ?>">
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Payment Received against Invoice Template','hospital_mgt'); ?> </label>
												<div class="col-md-8">
													<textarea name="MJ_hmgt_payment_received_invoice_template" class="form-control validate[required] min_height_200"><?php print get_option('MJ_hmgt_payment_received_invoice_template'); ?></textarea>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">
													<label><?php esc_html_e('You can use following variables in the email template:','hospital_mgt');?></label><br>
													<label><strong>{{Patient Name}}</strong> <?php esc_html_e('Name Of Patient','hospital_mgt'); ?></label><br>
													<label><strong>{{InvoiceNo}}</strong> <?php esc_html_e('InvoiceNo','hospital_mgt'); ?></label><br>
													<label><strong>{{Hospital Name}}</strong> <?php esc_html_e('Name Of Hospital','hospital_mgt'); ?></label><br>
												</div>
											</div>
										</div>
										<?php 
							              if($user_access_add == 1 OR $user_access_edit == 1 )
							             {
									     ?>
										<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
											<input value="<?php esc_html_e('Save','hospital_mgt'); ?>" name="Payment_Received_against_Invoice_save" class="btn btn-success" type="submit">
										</div>
										<?php } ?>
									</form>
								  </div><!-- PANEL BODY DIV END--> 
								</div>
						    </div>	<!-- PANEL DEFAULT DIV END--> 						
							<div class="panel panel-default accordion-item"><!-- PANEL DEFAULT DIV START--> 
								<div class="panel-heading padding_0">
								  <h4 class="panel-title">
								 		<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTen" aria-expanded="false" aria-controls="collapseTen">
											<?php esc_html_e('Generate Invoice Template','hospital_mgt'); ?>
										</button>
								  </h4>
								</div>
								<div id="collapseTen" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
									<div class="accordion-body panel-body"><!-- PANEL BODY DIV START--> 
									<form id="registration_email_template_form" class="form-horizontal" method="post" action="" name="parent_form">
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label"><?php esc_html_e('Email Subject','hospital_mgt');?> </label>
												<div class="col-md-8">
													<input class="form-control validate[required]" name="MJ_hmgt_generate_invoice_subject" id="MJ_hmgt_generate_invoice_subject" placeholder="Enter email subject" value="<?php print get_option('MJ_hmgt_generate_invoice_subject'); ?>">
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	 
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Generate Invoice Template','hospital_mgt'); ?> </label>
												<div class="col-md-8">
													<textarea name="MJ_hmgt_generate_invoice_template" class="form-control validate[required] min_height_200"><?php print get_option('MJ_hmgt_generate_invoice_template'); ?></textarea>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">
													<label><?php esc_html_e('You can use following variables in the email template:','hospital_mgt');?></label><br>
													<label><strong>{{Patient Name}}</strong> <?php esc_html_e('Name Of Patient','hospital_mgt'); ?></label><br>
													<label><strong>{{Hospital Name}}</strong> <?php esc_html_e('Name Of Hospital','hospital_mgt'); ?></label><br>
												</div>
											</div>
										</div>
										<?php 
							              if($user_access_add == 1 OR $user_access_edit == 1 )
							             {
									     ?>
										<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
											<input value="<?php esc_html_e('Save','hospital_mgt'); ?>" name="Generate_Invoice_Template_save" class="btn btn-success" type="submit">
										</div>
										<?php } ?>
									</form>
								  </div><!-- PANEL BODY DIV END--> 
								</div>
							</div>	<!-- PANEL DEFAULT DIV END--> 						
							<div class="panel panel-default accordion-item"><!-- PANEL DEFAULT DIV START--> 
								<div class="panel-heading padding_0">
								  <h4 class="panel-title">
								  	<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseElevan" aria-expanded="false" aria-controls="collapseElevan">
											<?php esc_html_e('Assign Bed to Patient Template','hospital_mgt'); ?>
									</button>
								  </h4>
								</div>
								<div id="collapseElevan" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
									<div class="accordion-body panel-body"><!-- PANEL BODY DIV START--> 
									<form id="registration_email_template_form" class="form-horizontal" method="post" action="" name="parent_form">
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Email Subject','hospital_mgt');?> </label>
												<div class="col-md-8">
													<input class="form-control validate[required]" name="MJ_hmgt_assign_bed_patient_subject" id="MJ_hmgt_assign_bed_patient_subject" placeholder="Enter email subject" value="<?php print get_option('MJ_hmgt_assign_bed_patient_subject'); ?>">
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Assign Bed to Patient Template','hospital_mgt'); ?> </label>
												<div class="col-md-8">
													<textarea name="MJ_hmgt_assign_bed_patient_template" class="form-control validate[required] min_height_200"><?php print get_option('MJ_hmgt_assign_bed_patient_template'); ?></textarea>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">
													<label><?php esc_html_e('You can use following variables in the email template:','hospital_mgt');?></label><br>
													<label><strong>{{Patient Name}}</strong> <?php esc_html_e('Name Of Patient','hospital_mgt'); ?></label><br>
													<label><strong>{{Bed ID}}</strong> <?php esc_html_e('ID Of Bed','hospital_mgt'); ?></label><br>
													<label><strong>{{Bed Category}}</strong> <?php esc_html_e('Category Of Bed','hospital_mgt'); ?></label><br>
													<label><strong>{{Charges Amount}}</strong> <?php esc_html_e('Charges Amount Of Bed','hospital_mgt'); ?></label><br>
													<label><strong>{{Hospital Name}}</strong> <?php esc_html_e('Name Of Hospital','hospital_mgt'); ?></label><br>
												</div>
											</div>
										</div>
										<?php 
							              if($user_access_add == 1 OR $user_access_edit == 1 )
							             {
									     ?>
										<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
											<input value="<?php esc_html_e('Save','hospital_mgt'); ?>" name="Assign_Bed_to_Patient_save" class="btn btn-success" type="submit">
										</div>
										<?php } ?>
									</form>
								  </div><!-- PANEL BODY DIV END--> 
								</div>
							</div>	<!-- PANEL DEFAULT DIV END--> 						
							<div class="panel panel-default accordion-item"><!-- PANEL DEFAULT DIV START--> 
								<div class="panel-heading padding_0">
								  <h4 class="panel-title">
								  	<button class="accordion-button collapsed padding_top_mail" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwelve" aria-expanded="false" aria-controls="collapseTwelve">
											<?php esc_html_e('Message Received Template','hospital_mgt'); ?>
									</button>
								  </h4>
								</div>
								<div id="collapseTwelve" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
								  <div class="accordion-body panel-body"><!-- PANEL BODY DIV START--> 
									<form id="registration_email_template_form" class="form-horizontal" method="post" action="" name="parent_form">
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Email Subject','hospital_mgt');?> </label>
												<div class="col-md-8">
													<input class="form-control validate[required]" name="MJ_hmgt_message_received_subject" id="MJ_hmgt_message_received_subject" placeholder="Enter email subject" value="<?php print get_option('MJ_hmgt_message_received_subject'); ?>">
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Message Received Template','hospital_mgt'); ?> </label>
												<div class="col-md-8">
													<textarea name="MJ_hmgt_message_received_template" class="form-control validate[required] min_height_200"><?php print get_option('MJ_hmgt_message_received_template'); ?></textarea>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">
													<label><?php esc_html_e('You can use following variables in the email template:','hospital_mgt');?></label><br>
													<label><strong>{{Receiver Name}}</strong> <?php esc_html_e('Name Of Receiver','hospital_mgt'); ?></label><br>
													<label><strong>{{Sender Name}}</strong> <?php esc_html_e('Name Of Sender','hospital_mgt'); ?></label><br>
													<label><strong>{{Message Content}} </strong> <?php esc_html_e('Message Content','hospital_mgt'); ?></label><br>
													<label><strong>{{Message_Link}} </strong> <?php esc_html_e('Message Link ','hospital_mgt'); ?></label><br>
													<label><strong>{{Hospital Name}}</strong> <?php esc_html_e('Name Of Hospital','hospital_mgt'); ?></label><br>
												</div>
										</div>
										<?php 
							              if($user_access_add == 1 OR $user_access_edit == 1 )
							             {
									     ?>
										<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
											<input value="<?php esc_html_e('Save','hospital_mgt'); ?>" name="Message_Received" class="btn btn-success" type="submit">
										</div>
										<?php } ?>
									</form>
								  </div><!-- PANEL BODY DIV END--> 
								</div>
							</div>	<!-- PANEL DEFAULT DIV END--> 						
							<div class="panel panel-default accordion-item"><!-- PANEL DEFAULT DIV START--> 
								<div class="panel-heading padding_0">
								  <h4 class="panel-title">
								  	<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsethartin" aria-expanded="false" aria-controls="collapsethartin">
											<?php esc_html_e('Diagnosis Report Mail Template For Patient','hospital_mgt'); ?>
									</button>
								  </h4>
								</div>
								<div id="collapsethartin" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
								  <div class="accordion-body panel-body"><!-- PANEL BODY DIV START--> 
									<form id="registration_email_template_form" class="form-horizontal" method="post" action="" name="parent_form">
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Email Subject','hospital_mgt');?> </label>
												<div class="col-md-8">
													<input class="form-control validate[required]" name="MJ_hmgt_add_diagnosis_report_subject" id="MJ_hmgt_add_diagnosis_report_subject" placeholder="Enter email subject" value="<?php print get_option('MJ_hmgt_add_diagnosis_report_subject'); ?>">
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Diagnosis Report Mail Template For Patient','hospital_mgt'); ?> </label>
												<div class="col-md-8">
													<textarea name="MJ_hmgt_add_diagnosis_report_template" class="form-control validate[required] min_height_200"><?php print get_option('MJ_hmgt_add_diagnosis_report_template'); ?></textarea>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">
													<label><?php esc_html_e('You can use following variables in the email template:','hospital_mgt');?></label><br>
													<label><strong>{{Patient Name}}</strong> <?php esc_html_e('Name Of Patient','hospital_mgt'); ?></label><br>
													<label><strong>{{Charges Amount}}</strong> <?php esc_html_e('Charged amount for the diagnosis report.','hospital_mgt'); ?></label><br>
													<label><strong>{{Hospital Name}}</strong> <?php esc_html_e('Name Of Hospital','hospital_mgt'); ?></label><br>
												</div>
											</div>
										</div>
										<?php 
							              if($user_access_add == 1 OR $user_access_edit == 1 )
							             {
									     ?>
										<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
											<input value="<?php esc_html_e('Save','hospital_mgt'); ?>" name="diagnosis_report_template" class="btn btn-success" type="submit">
										</div>
										<?php } ?>
									</form>
								  </div><!-- PANEL BODY DIV END--> 
								</div>
							</div>	<!-- PANEL DEFAULT DIV END--> 						
							<div class="panel panel-default accordion-item"><!-- PANEL DEFAULT DIV START--> 
								<div class="panel-heading padding_0">
								  <h4 class="panel-title">
								  	<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsefourtin" aria-expanded="false" aria-controls="collapsefourtin">
											<?php esc_html_e('Diagnosis Report Mail Template For Doctor.','hospital_mgt'); ?>
										</button>
								  </h4>
								</div>
								<div id="collapsefourtin" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
									<div class="accordion-body panel-body"><!-- PANEL BODY DIV START--> 
									<form id="registration_email_template_form" class="form-horizontal" method="post" action="" name="parent_form">
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Email Subject','hospital_mgt');?> </label>
												<div class="col-md-8">
													<input class="form-control validate[required]" name="MJ_hmgt_add_diagnosis_report_subject_doctor" id="MJ_hmgt_add_diagnosis_report_subject_doctor" placeholder="Enter email subject" value="<?php print get_option('MJ_hmgt_add_diagnosis_report_subject_doctor'); ?>">
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Diagnosis Report Mail Template For Doctor','hospital_mgt'); ?> </label>
												<div class="col-md-8">
													<textarea name="MJ_hmgt_add_diagnosis_report_template_doctor" class="form-control validate[required] min_height_200"><?php print get_option('MJ_hmgt_add_diagnosis_report_template_doctor'); ?></textarea>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">
													<label><?php esc_html_e('You can use following variables in the email template:','hospital_mgt');?></label><br>
													<label><strong>{{Doctor Name}}</strong> <?php esc_html_e('Name Of Doctor','hospital_mgt'); ?></label><br>
													<label><strong>{{Patient Name}}</strong> <?php esc_html_e('Name Of Patient','hospital_mgt'); ?></label><br>
													<label><strong>{{Hospital Name}}</strong> <?php esc_html_e('Name Of Hospital','hospital_mgt'); ?></label><br>
												</div>
											</div>
										</div>
										<?php 
							              if($user_access_add == 1 OR $user_access_edit == 1 )
							             {
									     ?>
										<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
											<input value="<?php esc_html_e('Save','hospital_mgt'); ?>" name="diagnosis_report_template_doctor" class="btn btn-success" type="submit">
										</div>
										<?php } ?>
									</form>
								  </div><!-- PANEL BODY DIV END--> 
								</div>
							</div>	<!-- PANEL DEFAULT DIV END--> 						
						<div class="panel panel-default accordion-item"><!-- PANEL DEFAULT DIV START--> 
								<div class="panel-heading padding_0">
								  <h4 class="panel-title">
								  	<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFiftin" aria-expanded="false" aria-controls="collapseFiftin">
											<?php esc_html_e('Cancel Appointment Mail Template For Patient.','hospital_mgt'); ?>
										</button>
								  </h4>
								</div>
								<div id="collapseFiftin" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
									  <div class="accordion-body panel-body"><!-- PANEL BODY DIV START--> 
									<form id="registration_email_template_form" class="form-horizontal" method="post" action="" name="parent_form">
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Email Subject','hospital_mgt');?> </label>
												<div class="col-md-8">
													<input class="form-control validate[required]" name="MJ_hmgt_cancel_appointment_patient_subject" id="MJ_hmgt_cancel_appointment_patient_subject" placeholder="Enter email subject" value="<?php print get_option('MJ_hmgt_cancel_appointment_patient_subject'); ?>">
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Cancel Appointment Mail Template For Patient','hospital_mgt'); ?> </label>
												<div class="col-md-8">
													<textarea name="MJ_hmgt_cancel_appointment_patient_mail" class="form-control validate[required] min_height_200"><?php print get_option('MJ_hmgt_cancel_appointment_patient_mail'); ?></textarea>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">
												<label><?php esc_html_e('You can use following variables in the email template:','hospital_mgt');?></label><br>
												<label><strong>{{Patient Name}}</strong> <?php esc_html_e('Name Of Patient','hospital_mgt'); ?></label><br>
												<label><strong>{{Doctor Name}}</strong> <?php esc_html_e('Name Of Doctor','hospital_mgt'); ?></label><br>
												<label><strong>{{Appointment Time}}</strong> <?php esc_html_e('Appointment Time','hospital_mgt'); ?></label><br>
												<label><strong>{{Appointment Date}}</strong> <?php esc_html_e('Appointment Date','hospital_mgt'); ?></label><br>
												<label><strong>{{Hospital Name}}</strong> <?php esc_html_e('Name Of Hospital','hospital_mgt'); ?></label><br>
											</div>
										</div>
										<?php 
							              if($user_access_add == 1 OR $user_access_edit == 1 )
							             {
									     ?>
										<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
											<input value="<?php esc_html_e('Save','hospital_mgt'); ?>" name="cancel_appointment_patient" class="btn btn-success" type="submit">
										</div>
										<?php } ?>
									</form>
								  </div><!-- PANEL BODY DIV END--> 
								</div>
							</div>	<!-- PANEL DEFAULT DIV END--> 						
							<div class="panel panel-default accordion-item"><!-- PANEL DEFAULT DIV START--> 
								<div class="panel-heading padding_0">
								  <h4 class="panel-title">
								  		<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSixtine" aria-expanded="false" aria-controls="collapseSixtine">
											 <?php esc_html_e('Cancel Appointment Mail Template For Doctor.','hospital_mgt'); ?>
										</button>
								  </h4>
								</div>
								<div id="collapseSixtine" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
									  <div class="accordion-body panel-body"><!-- PANEL BODY DIV START--> 
									<form id="registration_email_template_form" class="form-horizontal" method="post" action="" name="parent_form">
									
										<div class="form-group">
											<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Email Subject','hospital_mgt');?> </label>
											<div class="col-md-8">
												<input class="form-control validate[required]" name="MJ_hmgt_cancel_appointment_doctor_subject" id="MJ_hmgt_cancel_appointment_doctor_subject" placeholder="Enter email subject" value="<?php print get_option('MJ_hmgt_cancel_appointment_doctor_subject'); ?>">
											</div>
										</div>
										<div class="form-group">
											<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Cancel Appointment Mail Template For Doctor','hospital_mgt'); ?> </label>
											<div class="col-md-8">
												<textarea name="MJ_hmgt_cancel_appointment_doctor_mail" class="form-control validate[required] min_height_200"><?php print get_option('MJ_hmgt_cancel_appointment_doctor_mail'); ?></textarea>
											</div>
										</div>
										
										<div class="form-group">
											<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">
												<label><?php esc_html_e('You can use following variables in the email template:','hospital_mgt');?></label><br>
												<label><strong>{{Patient Name}}</strong> <?php esc_html_e('Name Of Patient','hospital_mgt'); ?></label><br>
												<label><strong>{{Doctor Name}}</strong> <?php esc_html_e('Name Of Doctor','hospital_mgt'); ?></label><br>
												<label><strong>{{Appointment Time}}</strong> <?php esc_html_e('Appointment Time','hospital_mgt'); ?></label><br>
												<label><strong>{{Appointment Date}}</strong> <?php esc_html_e('Appointment Date','hospital_mgt'); ?></label><br>
												<label><strong>{{Hospital Name}}</strong> <?php esc_html_e('Name Of Hospital','hospital_mgt'); ?></label><br>
											</div>
										</div>
										<?php 
							              if($user_access_add == 1 OR $user_access_edit == 1 )
							             {
									     ?>
										<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
											<input value="<?php esc_html_e('Save','hospital_mgt'); ?>" name="cancel_appointment_doctor" class="btn btn-success" type="submit">
										</div>
										<?php } ?>
									</form>
								  </div><!-- PANEL BODY DIV END--> 
								</div>
							</div>	<!-- PANEL DEFAULT DIV END--> 						
							<div class="panel panel-default accordion-item"><!-- PANEL DEFAULT DIV START--> 
								<div class="panel-heading padding_0">
								  <h4 class="panel-title">
								  	<button class="accordion-button collapsed padding_top_mail" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSevantin" aria-expanded="false" aria-controls="collapseSevantin">
											 <?php esc_html_e('Edit Appointment Mail Template For Patient.','hospital_mgt'); ?>
										</button>
								  </h4>
								</div>
								<div id="collapseSevantin" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
									<div class="accordion-body panel-body">
								  <div class="panel-body"><!-- PANEL BODY DIV START--> 
									<form id="registration_email_template_form" class="form-horizontal" method="post" action="" name="parent_form">
									
										<div class="form-group">
											<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Email Subject','hospital_mgt');?> </label>
											<div class="col-md-8">
												<input class="form-control validate[required]" name="MJ_hmgt_edit_appointment_patient_subject" id="MJ_hmgt_edit_appointment_patient_subject" placeholder="Enter email subject" value="<?php print get_option('MJ_hmgt_edit_appointment_patient_subject'); ?>">
											</div>
										</div>
										<div class="form-group">
											<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Edit Appointment Mail Template For Patient','hospital_mgt'); ?> </label>
											<div class="col-md-8">
												<textarea name="MJ_hmgt_edit_appointment_patient_mail" class="form-control validate[required] min_height_200"><?php print get_option('MJ_hmgt_edit_appointment_patient_mail'); ?></textarea>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">
													<label><?php esc_html_e('You can use following variables in the email template:','hospital_mgt');?></label><br>
													<label><strong>{{Patient Name}}</strong> <?php esc_html_e('Name Of Patient','hospital_mgt'); ?></label><br>
													<label><strong>{{Doctor Name}}</strong> <?php esc_html_e('Name Of Doctor','hospital_mgt'); ?></label><br>
													<label><strong>{{Appointment Time}}</strong> <?php esc_html_e('Appointment Time','hospital_mgt'); ?></label><br>
													<label><strong>{{Appointment Date}}</strong> <?php esc_html_e('Appointment Date','hospital_mgt'); ?></label><br>
													<label><strong>{{Hospital Name}}</strong> <?php esc_html_e('Name Of Hospital','hospital_mgt'); ?></label><br>
												</div>
											</div>
										</div>
										<?php 
							              if($user_access_add == 1 OR $user_access_edit == 1 )
							             {
									     ?>
										<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
											<input value="<?php esc_html_e('Save','hospital_mgt'); ?>" name="edit_appointment_patient" class="btn btn-success" type="submit">
										</div>
										<?php } ?>
									</form>
								  </div><!-- PANEL BODY DIV END--> 
								</div>
							</div>	<!-- PANEL DEFAULT DIV END--> 						
							<div class="panel panel-default accordion-item"><!-- PANEL DEFAULT DIV START--> 
								<div class="panel-heading padding_0">
								  <h4 class="panel-title">
								  	<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEightin" aria-expanded="false" aria-controls="collapseEightin">
											 <?php esc_html_e('Edit Appointment Mail Template For Doctor.','hospital_mgt'); ?>
										</button>
								  </h4>
								</div>
								<div id="collapseEightin" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
									  <div class="accordion-body panel-body"><!-- PANEL BODY DIV START--> 
									<form id="registration_email_template_form" class="form-horizontal" method="post" action="" name="parent_form">
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Email Subject','hospital_mgt');?> </label>
												<div class="col-md-8">
													<input class="form-control validate[required]" name="MJ_hmgt_edit_appointment_doctor_subject" id="MJ_hmgt_edit_appointment_doctor_subject" placeholder="Enter email subject" value="<?php print get_option('MJ_hmgt_edit_appointment_doctor_subject'); ?>">
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Edit Appointment Mail Template For Doctor','hospital_mgt'); ?> </label>
												<div class="col-md-8">
													<textarea name="MJ_hmgt_edit_appointment_doctor_mail" class="form-control validate[required] min_height_200"><?php print get_option('MJ_hmgt_edit_appointment_doctor_mail'); ?></textarea>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">
													<label><?php esc_html_e('You can use following variables in the email template:','hospital_mgt');?></label><br>
													<label><strong>{{Patient Name}}</strong> <?php esc_html_e('Name Of Patient','hospital_mgt'); ?></label><br>
													<label><strong>{{Doctor Name}}</strong> <?php esc_html_e('Name Of Doctor','hospital_mgt'); ?></label><br>
													<label><strong>{{Appointment Time}}</strong> <?php esc_html_e('Appointment Time','hospital_mgt'); ?></label><br>
													<label><strong>{{Appointment Date}}</strong> <?php esc_html_e('Appointment Date','hospital_mgt'); ?></label><br>
													<label><strong>{{Hospital Name}}</strong> <?php esc_html_e('Name Of Hospital','hospital_mgt'); ?></label><br>
												</div>
											</div>
										</div>
										<?php 
							              if($user_access_add == 1 OR $user_access_edit == 1 )
							             {
									     ?>
										<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
											<input value="<?php esc_html_e('Save','hospital_mgt'); ?>" name="edit_appointment_doctor" class="btn btn-success" type="submit">
										</div>
										<?php } ?>
									</form>
								    </div>	<!-- PANEL BODY DIV END--> 							
								</div>
							</div><!-- PANEL DEFAULT DIV END--> 
							<div class="panel panel-default accordion-item"><!-- PANEL DEFAULT DIV START--> 
								<div class="panel-heading padding_0">
								  <h4 class="panel-title">
								  	<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsenintitn" aria-expanded="false" aria-controls="collapsenintitn">
											<?php esc_html_e('Patient Appointment Approve Mail template','hospital_mgt'); ?>
										  </button>
								  </h4>
								</div>
								<div id="collapsenintitn" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
									  <div class="accordion-body panel-body"><!-- PANEL BODY DIV START--> 
									<form id="registration_email_template_form" class="form-horizontal" method="post" action="" name="parent_form">									
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-labe form-labell"><?php esc_html_e('Email Subject','hospital_mgt');?> </label>
												<div class="col-md-8">
													<input class="form-control validate[required]" name="MJ_hmgt_appointment_approve_patient_mail_subject" id="MJ_hmgt_appointment_approve_patient_mail_subject" placeholder="Enter email subject" value="<?php print get_option('MJ_hmgt_appointment_approve_patient_mail_subject'); ?>">
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Patient Appointment Approve Mail template','hospital_mgt'); ?> </label>
												<div class="col-md-8">
													<textarea  name="MJ_hmgt_appointment_approve_patient_mail_template" class="form-control validate[required] min_height_200"><?php print get_option('MJ_hmgt_appointment_approve_patient_mail_template'); ?></textarea>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">
													<label><?php esc_html_e('You can use following variables in the email template:','hospital_mgt');?></label><br>
													<label><strong>{{Patient Name}}</strong> <?php esc_html_e('Name Of Patient','hospital_mgt'); ?></label><br>
													<label><strong>{{Doctor Name}}</strong> <?php esc_html_e('Name Of Doctor','hospital_mgt'); ?></label><br>
													<label><strong>{{Appointment Time}}</strong> <?php esc_html_e('Appointment Time','hospital_mgt'); ?></label><br>
													<label><strong>{{Appointment Date}}</strong> <?php esc_html_e('Appointment Date','hospital_mgt'); ?></label><br>
													<label><strong>{{Hospital Name}}</strong> <?php esc_html_e('Name Of Hospital','hospital_mgt'); ?></label><br>
												</div>
											</div>
										</div>
										<?php 
							              if($user_access_add == 1 OR $user_access_edit == 1 )
							             {
									     ?>
										<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
											<input value="<?php esc_html_e('Save','hospital_mgt'); ?>" name="Appointment_approve_Patient_mail_template_save" class="btn btn-success" type="submit">
										</div>
										<?php } ?>
									</form>
								  </div><!-- PANEL BODY DIV END--> 
								</div>
							</div>	<!-- PANEL DEFAULT DIV END--> 		


								<div class="panel panel-default accordion-item"><!-- PANEL DEFAULT DIV START--> 
								<div class="panel-heading padding_0">
								  <h4 class="panel-title">
								  	<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsenintitn12" aria-expanded="false" aria-controls="collapsenintitn12">
											<?php esc_html_e('Virtual Appointment Invite Template','hospital_mgt'); ?>
										  </button>
								  </h4>
								</div>
								<div id="collapsenintitn12" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
									  <div class="accordion-body panel-body"><!-- PANEL BODY DIV START--> 
									<form id="registration_email_template_form" class="form-horizontal" method="post" action="" name="parent_form">									
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-labe form-labell"><?php esc_html_e('Email Subject','hospital_mgt');?> </label>
												<div class="col-md-8">
													<input class="form-control validate[required]" name="virtual_class_invite_mail_subject" id="virtual_class_invite_mail_subject" placeholder="Enter email subject" value="<?php print get_option('virtual_class_invite_mail_subject'); ?>">
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Patient Appointment Approve Mail template','hospital_mgt'); ?> </label>
												<div class="col-md-8">
													<textarea  name="virtual_class_invite_mail_content" class="form-control validate[required] min_height_200"><?php print get_option('virtual_class_invite_mail_content'); ?></textarea>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">
													<label><?php esc_html_e('You can use following variables in the email template:','hospital_mgt');?></label><br>
													<label><strong>{{topic}}</strong> <?php esc_html_e('Topic','hospital_mgt'); ?></label><br>
													<label><strong>{{date&time}}</strong> <?php esc_html_e('Date And Time','hospital_mgt'); ?></label><br>
													<label><strong>{{virtual_class_id}}</strong> <?php esc_html_e('Virtual Appointment ID','hospital_mgt'); ?></label><br>
													<label><strong>{{password}}</strong> <?php esc_html_e('Password','hospital_mgt'); ?></label><br>
													<label><strong>{{join_zoom_virtual_class}} - </strong><?php esc_attr_e('Join Zoom Virtual Appointment','hospital_mgt');?></label><br>
													<label><strong>{{start_zoom_virtual_class}} - </strong><?php esc_attr_e('Start Zoom Virtual Appointment','hospital_mgt');?></label><br>
													<label><strong>{{Hospital Name}}</strong> <?php esc_html_e('Name Of Hospital','hospital_mgt'); ?></label><br>
												</div>
											</div>
										</div>
										<?php 
							              if($user_access_add == 1 OR $user_access_edit == 1 )
							             {
									     ?>
										<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
											<input value="<?php esc_html_e('Save','hospital_mgt'); ?>" name="virtual_class_invite_mail_template_save" class="btn btn-success" type="submit">
										</div>
										<?php } ?>
									</form>
								  </div><!-- PANEL BODY DIV END--> 
								</div>
							</div>	<!-- PANEL DEFAULT DIV END--> 

							<div class="panel panel-default accordion-item"><!-- PANEL DEFAULT DIV START--> 
								<div class="panel-heading padding_0">
								  <h4 class="panel-title">
								  	<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsenintitn1" aria-expanded="false" aria-controls="collapsenintitn1">
											<?php esc_html_e('Virtual Appointment Doctor Reminder Template','hospital_mgt'); ?>
										  </button>
								  </h4>
								</div>
								<div id="collapsenintitn1" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
									  <div class="accordion-body panel-body"><!-- PANEL BODY DIV START--> 
									<form id="registration_email_template_form" class="form-horizontal" method="post" action="" name="parent_form">									
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-labe form-labell"><?php esc_html_e('Email Subject','hospital_mgt');?> </label>
												<div class="col-md-8">
													<input class="form-control validate[required]" name="virtual_appointment_doctor_reminder_mail_subject" id="virtual_appointment_doctor_reminder_mail_subject" placeholder="Enter email subject" value="<?php print get_option('virtual_appointment_doctor_reminder_mail_subject'); ?>">
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Patient Appointment Approve Mail template','hospital_mgt'); ?> </label>
												<div class="col-md-8">
													<textarea  name="virtual_appointment_doctor_reminder_mail_content" class="form-control validate[required] min_height_200"><?php print get_option('virtual_appointment_doctor_reminder_mail_content'); ?></textarea>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">
													<label><?php esc_html_e('You can use following variables in the email template:','hospital_mgt');?></label><br>
													<label><strong>{{doctor_name}}</strong> <?php esc_html_e('Name Of Doctor','hospital_mgt'); ?></label><br>
													<label><strong>{{topic}}</strong> <?php esc_html_e('Topic','hospital_mgt'); ?></label><br>
													<label><strong>{{date&time}}</strong> <?php esc_html_e('Date And Time','hospital_mgt'); ?></label><br>
													<label><strong>{{virtual_class_id}}</strong> <?php esc_html_e('Virtual Appointment ID','hospital_mgt'); ?></label><br>
													<label><strong>{{password}}</strong> <?php esc_html_e('Password','hospital_mgt'); ?></label><br>
													<label><strong>{{start_zoom_virtual_class}} - </strong><?php esc_attr_e('Start Zoom Virtual Appointment','hospital_mgt');?></label><br>
													<label><strong>{{Hospital Name}}</strong> <?php esc_html_e('Name Of Hospital','hospital_mgt'); ?></label><br>
												</div>
											</div>
										</div>
										<?php 
							              if($user_access_add == 1 OR $user_access_edit == 1 )
							             {
									     ?>
										<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
											<input value="<?php esc_html_e('Save','hospital_mgt'); ?>" name="virtual_appointment_doctor_mail_template_save" class="btn btn-success" type="submit">
										</div>
										<?php } ?>
									</form>
								  </div><!-- PANEL BODY DIV END--> 
								</div>
							</div>	<!-- PANEL DEFAULT DIV END--> 		

							<div class="panel panel-default accordion-item"><!-- PANEL DEFAULT DIV START--> 
								<div class="panel-heading padding_0">
								  <h4 class="panel-title">
								  	<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsenintitn2" aria-expanded="false" aria-controls="collapsenintitn2">
											<?php esc_html_e('Virtual Appointment Patient Reminder Template','hospital_mgt'); ?>
										  </button>
								  </h4>
								</div>
								<div id="collapsenintitn2" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
									  <div class="accordion-body panel-body"><!-- PANEL BODY DIV START--> 
									<form id="registration_email_template_form" class="form-horizontal" method="post" action="" name="parent_form">									
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-labe form-labell"><?php esc_html_e('Email Subject','hospital_mgt');?> </label>
												<div class="col-md-8">
													<input class="form-control validate[required]" name="virtual_appointment_patient_reminder_mail_subject" id="virtual_appointment_patient_reminder_mail_subject" placeholder="Enter email subject" value="<?php print get_option('virtual_appointment_patient_reminder_mail_subject'); ?>">
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Patient Appointment Approve Mail template','hospital_mgt'); ?> </label>
												<div class="col-md-8">
													<textarea  name="virtual_appointment_patient_reminder_mail_content" class="form-control validate[required] min_height_200"><?php print get_option('virtual_appointment_patient_reminder_mail_content'); ?></textarea>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">
													<label><?php esc_html_e('You can use following variables in the email template:','hospital_mgt');?></label><br>
													<label><strong>{{patient_name}}</strong> <?php esc_html_e('Name Of Patient','hospital_mgt'); ?></label><br>
													<label><strong>{{topic}}</strong> <?php esc_html_e('Topic','hospital_mgt'); ?></label><br>
													<label><strong>{{date&time}}</strong> <?php esc_html_e('Date And Time','hospital_mgt'); ?></label><br>
													<label><strong>{{virtual_class_id}}</strong> <?php esc_html_e('Virtual Appointment ID','hospital_mgt'); ?></label><br>
													<label><strong>{{password}}</strong> <?php esc_html_e('Password','hospital_mgt'); ?></label><br>
													<label><strong>{{join_zoom_virtual_class}} - </strong><?php esc_attr_e('Join Zoom Virtual Appointment','hospital_mgt');?></label><br>
													<label><strong>{{Hospital Name}}</strong> <?php esc_html_e('Name Of Hospital','hospital_mgt'); ?></label><br>
												</div>
											</div>
										</div>
										<?php 
							              if($user_access_add == 1 OR $user_access_edit == 1 )
							             {
									     ?>
										<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
											<input value="<?php esc_html_e('Save','hospital_mgt'); ?>" name="virtual_appointment_patient_mail_template_save" class="btn btn-success" type="submit">
										</div>
										<?php } ?>
									</form>
								  </div><!-- PANEL BODY DIV END--> 
								</div>
							</div>	<!-- PANEL DEFAULT DIV END--> 


							<div class="panel panel-default accordion-item"><!-- PANEL DEFAULT DIV START--> 
								<div class="panel-heading padding_0">
								  <h4 class="panel-title">
								  	<button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsenintitn21" aria-expanded="false" aria-controls="collapsenintitn21">
											<?php esc_html_e('Medicine Out Of Stock Laboratory Staff Reminder Template','hospital_mgt'); ?>
										  </button>
								  </h4>
								</div>
								<div id="collapsenintitn21" class="accordion-collapse collapse" data-bs-parent="#accordionExample">
									  <div class="accordion-body panel-body"><!-- PANEL BODY DIV START--> 
									<form id="registration_email_template_form" class="form-horizontal" method="post" action="" name="parent_form">									
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-labe form-labell"><?php esc_html_e('Email Subject','hospital_mgt');?> </label>
												<div class="col-md-8">
													<input class="form-control validate[required]" name="MJ_hmgt_medicine_out_of_stock_email_subject" id="MJ_hmgt_medicine_out_of_stock_email_subject" placeholder="Enter email subject" value="<?php print get_option('MJ_hmgt_medicine_out_of_stock_email_subject'); ?>">
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<label for="first_name" class="col-sm-3 control-label form-label"><?php esc_html_e('Medicine Out Of Stock Reminder Mail template','hospital_mgt'); ?> </label>
												<div class="col-md-8">
													<textarea  name="MJ_hmgt_medicine_out_of_stock_email_template" class="form-control validate[required] min_height_200"><?php print get_option('MJ_hmgt_medicine_out_of_stock_email_template'); ?></textarea>
												</div>
											</div>
										</div>
										<div class="form-group">
											<div class="mb-3 row">	
												<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">
													<label><?php esc_html_e('You can use following variables in the email template:','hospital_mgt');?></label><br>
													<label><strong>{{User Name}}</strong> <?php esc_html_e('Name Of Patient','hospital_mgt'); ?></label><br>
													<label><strong>{{Medicine Name}}</strong> <?php esc_html_e('Topic','hospital_mgt'); ?></label><br>
													<label><strong>{{Hospital Name}}</strong> <?php esc_html_e('Name Of Hospital','hospital_mgt'); ?></label><br>
												</div>
											</div>
										</div>
										<?php 
							             if($user_access_add == 1 OR $user_access_edit == 1 )
							             {
									     ?>
										<div class="offset-sm-3 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
											<input value="<?php esc_html_e('Save','hospital_mgt'); ?>" name="medicine_out_of_stock_mail_template_save" class="btn btn-success" type="submit">
										</div>
										<?php 
										 } ?>
									</form>
								  </div><!-- PANEL BODY DIV END--> 
								</div>
							</div>	<!-- PANEL DEFAULT DIV END--> 
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>