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
			$('#blooddonor_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#blooddonor_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>
	$.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
      	$('#last_donate_date').datepicker({
     	endDate: '+0d',
        autoclose: true,
   }); 
} );
</script>
     <?php 	
	if($active_tab == 'addblooddonor')	
	{
		MJ_hmgt_browser_javascript_check();
		$edit=0;
		if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit'){
		$edit=1;
		$result = $obj_bloodbank->MJ_hmgt_get_single_blooddonor($_REQUEST['blooddonor_id']);
	}?>
    <div class="panel-body"><!-- PANEL BODY DIV START-->
        <form name="blooddonor_form" action="" method="post" class="form-horizontal" id="blooddonor_form">
			 <?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
			<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
			<input type="hidden" name="old_blood_group" value="<?php if($edit){ echo esc_attr($result->blood_group); } ?>">
			<input type="hidden" name="old_blood_status" value="<?php if($edit){ echo esc_attr($result->blood_status); } ?>">
			<input type="hidden" name="blooddonor_id" value="<?php if(isset($_REQUEST['blooddonor_id'])) echo esc_attr($_REQUEST['blooddonor_id']);?>"  />
			
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="first_name"><?php esc_html_e('Full Name','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="bool_dodnor_name" class="form-control validate[required,custom[onlyLetter_specialcharacter]] text-input"  maxlength="50" type="text" value="<?php if($edit){ echo esc_attr($result->donor_name);}elseif(isset($_POST['bool_dodnor_name'])) echo esc_attr($_POST['bool_dodnor_name']);?>" name="bool_dodnor_name">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="gender"><?php esc_html_e('Gender','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
					<?php $genderval = "male"; if($edit){ $genderval=$result->donor_gender; }elseif(isset($_POST['gender'])) {$genderval=$_POST['gender'];}?>
						<label class="radio-inline">
						 <input type="radio" value="male" class="tog" name="gender"  <?php  checked( 'male', $genderval);  ?>/><?php esc_html_e('Male','hospital_mgt');?>
						</label>
						<label class="radio-inline">
						  <input type="radio" value="female" class="tog" name="gender"  <?php  checked( 'female', $genderval);  ?>/><?php esc_html_e('Female','hospital_mgt');?> 
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="med_category_name"><?php esc_html_e('Age','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="dodnor_age" class="form-control validate[required,custom[integer],min[18]] text-input" min="0" max="99" type="number" onKeyPress="if(this.value.length==2) return false;"  value="<?php if($edit){ echo esc_attr($result->donor_age);}elseif(isset($_POST['dodnor_age'])) echo esc_attr($_POST['dodnor_age']);?>" name="dodnor_age">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">		
					<label class="col-sm-2 control-label form-label" for="phone"><?php esc_html_e('Phone','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="phone" class="form-control validate[required,custom[phone_number]] text-input" minlength="6" maxlength="15" type="text" value="<?php if($edit){ echo esc_attr($result->donor_phone);}elseif(isset($_POST['phone'])) echo esc_attr($_POST['phone']);?>" name="phone">					
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="email"><?php esc_html_e('Email','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="email" class="form-control validate[required,custom[email]] text-input" type="text" maxlength="100"  name="email" 
						value="<?php if($edit){ echo esc_attr($result->donor_email);}elseif(isset($_POST['email'])) echo esc_attr($_POST['email']);?>">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="bloodgruop"><?php esc_html_e('Blood Group','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<?php if($edit){ $userblood=$result->blood_group; }elseif(isset($_POST['blood_group'])){$userblood=$_POST['blood_group'];}else{$userblood='';}?>
						<select id="blood_group" class="form-control validate[required] max_width_100" name="blood_group">
							<option value=""><?php esc_html_e('Select Blood Group','hospital_mgt');?></option>
							<?php foreach(MJ_hmgt_blood_group() as $blood){ ?>
								<option value="<?php echo esc_attr($blood);?>" <?php selected($userblood,$blood);  ?>><?php echo esc_html($blood); ?> </option>
							<?php } ?>
						</select>
					</div>
				</div>
			</div>
			<?php wp_nonce_field( 'save_blooddonor_nonce' ); ?>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="blood_status"><?php esc_html_e('Number Of Bags','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="blood_status" class="form-control validate[required] text-input" type="number" min="1" onKeyPress="if(this.value.length==1) return false;" value="<?php if($edit){ echo esc_attr($result->blood_status);}elseif(isset($_POST['blood_status'])) echo esc_attr($_POST['blood_status']);?>" name="blood_status">
					</div>
				</div>
			</div>
		
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="last_donet_date"><?php esc_html_e('Last Donation Date','hospital_mgt');?></label>
					<div class="col-sm-8 margin_bottom_5px">
						<input id="last_donate_date" class="form-control " type="text"  value="<?php if($edit){ if(!empty($result->last_donet_date!='0000-00-00')) { echo date(MJ_hmgt_date_formate(),strtotime($result->last_donet_date)); }else{ echo ''; }}elseif(isset($_POST['last_donate_date'])) echo esc_attr($_POST['last_donate_date']);?>" name="last_donate_date">
					</div>
				</div>
			</div>
			
			<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
				<input type="submit" value="<?php if($edit){ esc_html_e('Save','hospital_mgt'); }else{ esc_html_e('Save','hospital_mgt');}?>" name="save_blooddonor" class="btn btn-success"/>
			</div>
	    </form>
    </div><!-- PANEL BODY DIV END-->
<?php 
 }
?>