<?php
//Compose mail
MJ_hmgt_browser_javascript_check();
?>
<script type="text/javascript">

$(document).ready(function() {
	"use strict";
	<?php
	if (is_rtl())
		{
		?>	
			$('#message_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#message_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>
	$('.onlyletter_number_space_validation').keypress(function( e ) 
	{     
		var regex = new RegExp("^[0-9a-zA-Z \b]+$");
		var key = String.fromCharCode(!event.charCode ? event.which: event.charCode);
		if (!regex.test(key)) 
		{
			event.preventDefault();
			return false;
		} 
   });  
} );
</script>
<div class="mailbox-content"><!-- START MAIL BOX CONTENT DIV -->
	<h2>
	<?php  
	$edit=0;
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
	{
		echo esc_html( esc_html__( 'Edit Message', 'hospital_mgt') );
		$edit=1;
		$exam_data= get_exam_by_id($_REQUEST['exam_id']);
	}
	?>
	</h2>
	<?php
	if(isset($message))
		echo '<div id="message" class="updated below-h2"><p>'.$message.'</p></div>';
	?>
	<form name="message_form" action="" method="post" class="form-horizontal" id="message_form"><!-- START MESSAGE FORM -->
        <?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
		<input type="hidden" name="action" value="<?php echo $action;?>">
        <div class="form-group">
        	<div class="mb-3 row">	
				<label class="col-sm-2 control-label form-label" for="to"><?php esc_html_e('Message To','hospital_mgt');?><span class="require-field">*</span></label>
				<div class="col-sm-8">
				
				<select name="receiver" class="form-control validate[required] text-input" id="to">
					<?php				
					if(MJ_hmgt_get_current_user_role() != 'patient' )
					{
						?>
						<option value="patient"><?php esc_html_e('All Patients','hospital_mgt');?></option>	
						<option value="doctor"><?php esc_html_e('All Doctors','hospital_mgt');?></option>	
						<option value="nurse"><?php esc_html_e('All Nurses','hospital_mgt');?></option>
						<option value="receptionist"><?php esc_html_e('All Support Staffs','hospital_mgt');?></option>		
						<option value="pharmacist"><?php esc_html_e('All Pharmacists','hospital_mgt');?></option>	
						<option value="laboratorist"><?php esc_html_e('All Laboratory Staffs','hospital_mgt');?></option>	
						<option value="accountant"><?php esc_html_e('All Accountants','hospital_mgt');?></option>
						<?php
					}
					?>
					<?php if(get_option('hmgt_enable_staff_can_message')=='yes'){?>
					<option value="administrator"><?php esc_html_e('Admin','hospital_mgt');?></option>	
					<?php } ?>
					<?php MJ_hmgt_get_all_user_in_message();?>
					</select>
				</div>
			</div>	
		</div>
        <div class="form-group">
        	<div class="mb-3 row">	
				<label class="col-sm-2 control-label form-label" for="subject"><?php esc_html_e('Subject','hospital_mgt');?><span class="require-field">*</span></label>
				<div class="col-sm-8">
				   <input id="subject" class="form-control validate[required,custom[popup_category_validation]] text-input onlyletter_number_space_validation" maxlength="50" type="text" name="subject" >
				</div>
			</div>
		</div>
        <div class="form-group">
        	<div class="mb-3 row">	
				<label class="col-sm-2 control-label form-label" for="subject"><?php esc_html_e('Message Comment','hospital_mgt');?><span class="require-field">*</span></label>
				<div class="col-sm-8">
				  <textarea name="message_body" id="message_body" maxlength="150" class="form-control validate[required,custom[address_description_validation]] text-input"></textarea>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="mb-3 row">	
				<label class="col-sm-2 control-label form-label" for="enable"><?php esc_html_e('Send SMS','hospital_mgt');?></label>
				<div class="col-sm-8">
					 <div class="checkbox">
					 	<label>
	  						<input id="chk_sms_sent" type="checkbox" <?php $smgt_sms_service_enable = 0;if($smgt_sms_service_enable) echo "checked";?> value="1" name="hmgt_sms_service_enable">
	  					</label>
	  				</div>				 
				</div>
			</div>
		</div>
		<?php wp_nonce_field( 'save_message_nonce' ); ?>
		<div id="hmsg_message_sent" class="hmsg_message_none">
		<div class="form-group">
			<div class="mb-3 row">	
				<label class="col-sm-2 control-label form-label" for="sms_template"><?php esc_html_e('SMS Text','hospital_mgt');?><span class="require-field">*</span></label>
				<div class="col-sm-8">
					<textarea name="sms_template" class="form-control validate[required,custom[address_description_validation]]" maxlength="160"></textarea>
					<label><?php esc_html_e('Max. 160 Character','hospital_mgt');?></label>
				</div>
			</div>
		</div>
		</div>
        <div class="form-group">
        	<div class="mb-3 row">	
				<div class="col-sm-10">
					<div class="pull-right">
						<input type="submit" value="<?php if($edit){ esc_html_e('Save Message','hospital_mgt'); }else{ esc_html_e('Send Message','hospital_mgt');}?>" name="save_message" class="btn btn-success"/>
					</div>
				</div>
			</div>
		</div>
    </form> <!-- END MESSAGE FORM -->       
</div><!-- START MAIL BOX CONTENT DIV -->