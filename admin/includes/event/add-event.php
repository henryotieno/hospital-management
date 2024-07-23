<?php
MJ_hmgt_browser_javascript_check();
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	"use strict";
	 <?php
	if (is_rtl())
		{
		?>	
			$('#notice_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#notice_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>
		$('.event_for_multiselect').multiselect(
		{
			nonSelectedText :'<?php esc_html_e('Select Role','hospital_mgt');?>',
			includeSelectAllOption: true,
		    selectAllText : '<?php esc_html_e('Select all','hospital_mgt'); ?>',
			templates: {
		            button: '<button class="multiselect btn btn-default dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="multiselect-selected-text"></span><b class="caret"></b></button>',
		        },
				buttonContainer: '<div class="dropdown" />'
		});
		var start = new Date();
		var end = new Date(new Date().setYear(start.getFullYear()+1));
		 $.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
		$('.datepicker1').datepicker({
			startDate : start,
			endDate   : end,
			autoclose: true
		}).on('changeDate', function (selected) {
        var minDate = new Date(selected.date.valueOf());
        $('.datepicker2').datepicker('setStartDate', minDate);
		}); 
		$.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
		$('.datepicker2').datepicker({
			startDate : start,
			endDate   : end,
			autoclose: true
		}).on('changeDate', function (selected) {
            var maxDate = new Date(selected.date.valueOf());
            $('.datepicker1').datepicker('setEndDate', maxDate);
        });
		$("body").on("click",".event_for_alert",function()
		{
		var checked = $(".multiselect_validation_role .dropdown-menu input:checked").length;

		if(!checked)
		{
		alert("<?php esc_html_e('Please select atleast one Role','hospital_mgt');?>");
		return false;
		}
		}); 
		
} );
</script>
<?php  
$edit=0;
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
{
	$edit=1;
	$postdata = get_post($_REQUEST['notice_id']);		
}
?>
<div class="panel-body"> <!-- PANEL BODY DIV START-->	
	<form name="class_form" action="" method="post" class="form-horizontal" id="notice_form">
		<?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
		<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
		<input type="hidden" name="notice_id"   value="<?php if($edit){ echo esc_attr($postdata->ID);}?>"/> 
		<div class="form-group">
			<div class="mb-3 row">	
				<label class="col-sm-2 control-label form-label" for="notice_type"><?php esc_html_e('Event/Notice','hospital_mgt');?><span class="require-field">*</span></label>
				<div class="col-sm-8">
					<select id="notice_type" class="form-control validate[required] max_width_100" name="notice_type">
						<option value=""><?php esc_html_e('Select Type','hospital_mgt');?></option>
						<option value="hmgt_notice" <?php if($edit) selected('hmgt_notice',$postdata->post_type);?>><?php echo esc_html_e('Notice','hospital_mgt'); ?></option>
						<option value="hmgt_event" <?php if($edit) selected('hmgt_event',$postdata->post_type);?>><?php echo esc_html_e('Event','hospital_mgt'); ?></option>
					</select>				 
				</div>
			</div>
		</div>
	   <div class="form-group">
	   		<div class="mb-3 row">	
				<label class="col-sm-2 control-label form-label" for="notice_title"><?php esc_html_e('Event Title','hospital_mgt');?><span class="require-field">*</span></label>
				<div class="col-sm-8">
					<input id="notice_title" class="form-control validate[required,custom[popup_category_validation]] text-input" maxlength="50" type="text" value="<?php if($edit){ echo esc_attr($postdata->post_title);}?>" name="notice_title">
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="mb-3 row">	
				<label class="col-sm-2 control-label form-label" for="notice_content"><?php esc_html_e('Event Comment','hospital_mgt');?></label>
				<div class="col-sm-8">
				<textarea name="notice_content" class="form-control validate[custom[address_description_validation]]" maxlength="150" id="notice_content"><?php if($edit){ echo esc_attr($postdata->post_content);}?></textarea>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="mb-3 row">	
				<label class="col-sm-2 control-label form-label" for="notice_content"><?php esc_html_e('Event Start Date','hospital_mgt');?><span class="require-field">*</span></label>
				<div class="col-sm-8">
				<input id="notice_Start_date" class="datepicker1 form-control validate[required] text-input" type="text"  value="<?php if($edit){ echo date(MJ_hmgt_date_formate(),strtotime(get_post_meta($postdata->ID,'start_date',true)));}?>" name="start_date" autocomplete="off">
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="mb-3 row">	
				<label class="col-sm-2 control-label form-label" for="notice_content"><?php esc_html_e('Event End Date','hospital_mgt');?><span class="require-field">*</span></label>
				<div class="col-sm-8">
				<input id="notice_end_date" class="datepicker2 form-control validate[required] text-input" type="text"  value="<?php if($edit){ echo  date(MJ_hmgt_date_formate(),strtotime(get_post_meta($postdata->ID,'end_date',true)));}?>" name="end_date" autocomplete="off">
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="mb-3 row">	
				<label class="col-sm-2 control-label form-label" for="notice_for"><?php esc_html_e('Event For','hospital_mgt');?><span class="require-field">*</span></label>
				<div class="col-sm-8 multiselect_validation_role">
				 <select name="notice_for[]" id="notice_for" multiple="multiple"  class="form-control validate[required] text-input event_for_multiselect max_width_100">					  
						   <?php 
						   if($edit)
						   {					
								$notice_for_array=explode(",",get_post_meta( $postdata->ID, 'notice_for',true));
							?>							
								<option value="patient" <?php if(in_array('patient',$notice_for_array)){ echo 'selected'; } ?>><?php esc_html_e('Patient','hospital_mgt');?></option>
								<option value="doctor" <?php if(in_array('doctor',$notice_for_array)){ echo 'selected'; } ?>><?php esc_html_e('Doctor','hospital_mgt');?></option>	
								<option value="nurse" <?php if(in_array('nurse',$notice_for_array)){ echo 'selected'; } ?>><?php esc_html_e('Nurse','hospital_mgt');?></option>	
								<option value="receptionist" <?php if(in_array('receptionist',$notice_for_array)){ echo 'selected'; } ?>><?php esc_html_e('Support Staff','hospital_mgt');?></option>	
								<option value="pharmacist" <?php if(in_array('pharmacist',$notice_for_array)){ echo 'selected'; } ?>><?php esc_html_e('Pharmacist','hospital_mgt');?></option>	
								<option value="laboratorist" <?php if(in_array('laboratorist',$notice_for_array)){ echo 'selected'; } ?>><?php esc_html_e('Laboratory Staff','hospital_mgt');?></option>	
								<option value="accountant" <?php if(in_array('accountant',$notice_for_array)){ echo 'selected'; } ?>><?php esc_html_e('Accountant','hospital_mgt');?></option>	
						   <?php 
						   }
						   else
						   {
							?>
								<!--<option value="all"><?php esc_html_e('All','hospital_mgt');?></option>-->
								<option value="patient"><?php esc_html_e('Patient','hospital_mgt');?></option>	
								<option value="doctor"><?php esc_html_e('Doctor','hospital_mgt');?></option>	
								<option value="nurse"><?php esc_html_e('Nurse','hospital_mgt');?></option>	
								<option value="receptionist"><?php esc_html_e('Support Staff','hospital_mgt');?></option>	
								<option value="pharmacist"><?php esc_html_e('Pharmacist','hospital_mgt');?></option>	
								<option value="laboratorist"><?php esc_html_e('Laboratory Staff','hospital_mgt');?></option>	
								<option value="accountant"><?php esc_html_e('Accountant','hospital_mgt');?></option>	
							<?php 
						   }
							  ?>
						</select>
				</div>
			</div>
		</div>
		<div class="form-group margin_bottom_5px">
			<div class="mb-3 row">	
				<label class="col-sm-2 control-label form-label" for="enable"><?php esc_html_e('Send SMS','hospital_mgt');?></label>
				<div class="col-sm-8 margin_bottom_5px send_mail_checkbox">
					<div class="checkbox">
						<label>
							<input id="chk_sms_sent" type="checkbox" <?php $smgt_sms_service_enable = 0;if($smgt_sms_service_enable) echo "checked";?> value="1" name="hmgt_sms_service_enable">
						</label>
					</div>
					 
				</div>
			</div>
		</div>
		<?php wp_nonce_field( 'save_notice_nonce' ); ?>
		<div id="hmsg_message_sent" class="hmsg_message_none margin_bottom_5px">
		<div class="form-group ">
			<div class="mb-3 row">	
				<label class="col-sm-2 control-label form-label" for="sms_template"><?php esc_html_e('SMS Text','hospital_mgt');?><span class="require-field">*</span></label>
				<div class="col-sm-8">
					<textarea name="sms_template" class="form-control validate[required,custom[address_description_validation]]" maxlength="160"></textarea>
					<label><?php esc_html_e('Max. 160 Character','hospital_mgt');?></label>
				</div>
			</div>
		</div>
		</div>
		<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
			<input type="submit" value="<?php if($edit){ esc_html_e('Save','hospital_mgt'); }else{ esc_html_e('Save','hospital_mgt');}?>" name="save_notice" class="btn btn-success event_for_alert" />
		</div>
	</form>
</div><!-- PANEL BODY DIV END-->	
<?php
?>