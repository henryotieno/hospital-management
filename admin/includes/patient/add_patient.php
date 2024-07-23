<?php
$role='patient';
$patient_type='inpatient';
$obj_bloodbank=new MJ_hmgt_bloodbank();
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
	$.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
      $('#birth_date').datepicker({
		endDate: '+0d',
        autoclose: true
   }); 
	//username not  allow space validation
	$('#username').keypress(function( e ) {
       if(e.which === 32) 
        return false;
    });
} );
</script>
<?php 	
if($active_tab == 'addpatient')
{
	MJ_hmgt_browser_javascript_check();
	$edit=0;
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
	{
		$edit=1;
		$user_info = get_userdata($_REQUEST['patient_id']);
	}
	else
	{
		$newpatient=MJ_hmgt_get_lastpatient_id($role);
	}
	?>
	<!-- PANAL BODY DIV START -->	
    <div class="panel-body">
	    <form name="patient_form" action="" method="post" class="form-horizontal" id="patient_form">
			 <?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
			<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
			<input type="hidden" name="role" value="<?php echo esc_attr($role);?>"  />
			<input type="hidden" name="patient_type" value="<?php echo esc_attr($patient_type);?>"  />
			<input type="hidden" name="user_id" value="<?php if(isset($_REQUEST['patient_id'])) echo esc_attr($_REQUEST['patient_id']);?>"  />
			<div class="header">	
				<h3 class="first_hed"><?php esc_html_e('Personal Information','hospital_mgt');?></h3>
				<hr>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="roll_id"><?php esc_html_e('Patient Id','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="patient_id" class="form-control validate[required]" type="text" 
						value="<?php if($edit){ echo esc_attr($user_info->patient_id);}else echo esc_attr($newpatient);?>"  readonly name="patient_id">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="first_name"><?php esc_html_e('First Name','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="first_name" class="form-control validate[required,custom[onlyLetter_specialcharacter]] text-input" maxlength="50" type="text" value="<?php if($edit){ echo esc_attr($user_info->first_name);}elseif(isset($_POST['first_name'])) echo esc_attr($_POST['first_name']);?>" name="first_name">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="middle_name"><?php esc_html_e('Middle Name','hospital_mgt');?></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="middle_name" class="form-control validate[custom[onlyLetter_specialcharacter]]" type="text" maxlength="50" value="<?php if($edit){ echo esc_attr($user_info->middle_name);}elseif(isset($_POST['middle_name'])) echo esc_attr($_POST['middle_name']);?>" name="middle_name">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="last_name"><?php esc_html_e('Last Name','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="last_name" class="form-control validate[required,custom[onlyLetter_specialcharacter]] text-input" maxlength="50" type="text"  value="<?php if($edit){ echo esc_attr($user_info->last_name);}elseif(isset($_POST['last_name'])) echo esc_attr($_POST['last_name']);?>" name="last_name">
					</div>
				</div>
			</div>
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="birth_date"><?php esc_html_e('Date of birth','hospital_mgt');?><span class="require-field">*</span></label>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
							<input id="birth_date" class="form-control validate[required]" type="text"  name="birth_date"
							value="<?php if($edit){ echo date(MJ_hmgt_date_formate(),strtotime($user_info->birth_date));}elseif(isset($_POST['birth_date'])) echo esc_attr($_POST['birth_date']);?>" readonly>
						</div>
						<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="blood_group"><?php esc_html_e('Blood Group','hospital_mgt');?><span class="require-field">*</span></label>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
							<?php if($edit){ $userblood=$user_info->blood_group; }elseif(isset($_POST['blood_group'])){$userblood=$_POST['blood_group'];}else{$userblood='';}?>
							<select id="blood_group" class="form-control validate[required] max_width_100" name="blood_group">
							<option value=""><?php esc_html_e('Select Blood Group','hospital_mgt');?></option>
							<!--<option><?php esc_html_e('Select Blood Group','hospital_mgt');?></option>-->
							<?php foreach(MJ_hmgt_blood_group() as $blood)
							{ ?>
									<option value="<?php echo esc_attr($blood);?>" <?php selected($userblood,$blood);  ?>><?php echo esc_html($blood); ?> </option>
							<?php } ?>
							</select>
						</div>
					</div>
			</div>	
			
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="gender"><?php esc_html_e('Gender','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
					<?php $genderval = "male"; if($edit){ $genderval=$user_info->gender; }elseif(isset($_POST['gender'])) {$genderval=$_POST['gender'];}?>
						<label class="radio-inline">
						 <input type="radio" value="male" class="tog" name="gender"  <?php  checked( 'male', $genderval);  ?>/><?php esc_html_e('Male','hospital_mgt');?>
						</label>
						<label class="radio-inline">
						  <input type="radio" value="female" class="tog" name="gender"  <?php  checked( 'female', $genderval);  ?>/><?php esc_html_e('Female','hospital_mgt');?> 
						</label>
					</div>
				</div>
			</div>
			<div class="header">
				<h3><?php esc_html_e('HomeTown Address Information','hospital_mgt');?></h3>
				<hr>
			</div> 
			
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="address"><?php esc_html_e('Home Town Address','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="address" class="form-control validate[required,custom[address_description_validation]]" type="text" maxlength="150" name="address" 
						value="<?php if($edit){ echo esc_attr($user_info->address);}elseif(isset($_POST['address'])) echo esc_attr($_POST['address']);?>">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="city_name"><?php esc_html_e('City','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="city_name" class="form-control validate[required,custom[city_state_country_validation]]" type="text" maxlength="50" name="city_name" 
						value="<?php if($edit){ echo esc_attr($user_info->city_name);}elseif(isset($_POST['city_name'])) echo esc_attr($_POST['city_name']);?>">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="state_name"><?php esc_html_e('State','hospital_mgt');?></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="state_name" class="form-control validate[custom[city_state_country_validation]]" type="text" maxlength="50"  name="state_name" 
						value="<?php if($edit){ echo esc_attr($user_info->state_name);}elseif(isset($_POST['state_name'])) echo esc_attr($_POST['state_name']);?>">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="country_name"><?php esc_html_e('Country','hospital_mgt');?></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="country_name" class="form-control validate[custom[city_state_country_validation]]" type="text" maxlength="50" name="country_name" 
						value="<?php if($edit){ echo esc_attr($user_info->country_name);}elseif(isset($_POST['country_name'])) echo esc_attr($_POST['country_name']);?>">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="zip_code"><?php esc_html_e('Zip Code','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="zip_code" class="form-control  validate[required,custom[onlyLetterNumber]]" type="text" maxlength="15"  name="zip_code" 
						value="<?php if($edit){ echo esc_attr($user_info->zip_code);}elseif(isset($_POST['zip_code'])) echo esc_attr($_POST['zip_code']);?>">
					</div>
				</div>
			</div>
			<div class="header">
				<h3><?php esc_html_e('Contact Information','hospital_mgt');?></h3>
				<hr>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="mobile"><?php esc_html_e('Mobile Number','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 margin_bottom_5px">
					<input type="text" value="<?php if($edit) { if(!empty($user_info->phonecode)){ echo esc_attr($user_info->phonecode); }else{ ?>+<?php echo MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }  }elseif(isset($_POST['phonecode'])){ echo esc_attr($_POST['phonecode']); }else{ ?>+<?php echo MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }?>"  class="form-control  validate[required] onlynumber_and_plussign" name="phonecode" maxlength="5">
					</div>
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 has-feedback">
						<input id="mobile" class="form-control validate[required,custom[phone_number]] text-input" minlength="6" maxlength="15" type="text" value="<?php if($edit){ echo esc_attr($user_info->mobile);}elseif(isset($_POST['mobile'])) echo esc_attr($_POST['mobile']);?>" name="mobile">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="phone"><?php esc_html_e('Phone','hospital_mgt');?></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="phone" class="form-control validate[custom[phone_number]] text-input" minlength="6" maxlength="15" type="text" value="<?php if($edit){ echo esc_attr($user_info->phone);}elseif(isset($_POST['phone'])) echo esc_attr($_POST['phone']);?>" name="phone">			
					</div>
				</div>
			</div>
			<div class="header">
				<h3><?php esc_html_e('Login Information','hospital_mgt');?></h3>
				<hr>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="email"><?php esc_html_e('Email','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="email" class="form-control validate[required,custom[email]] text-input" type="text" maxlength="100" name="email" 
						value="<?php if($edit){ echo esc_attr($user_info->user_email);}elseif(isset($_POST['email'])) echo esc_attr($_POST['email']);?>">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="username"><?php esc_html_e('User Name','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="username" class="form-control validate[required,custom[username_validation]]" type="text" maxlength="30" name="username" 
						value="<?php if($edit){ echo esc_attr($user_info->user_login);}elseif(isset($_POST['username'])) echo esc_attr($_POST['username']);?>" <?php if($edit) echo "readonly";?>>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="password"><?php esc_html_e('Password','hospital_mgt');?><?php if(!$edit) {?><span class="require-field">*</span><?php }?></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="password" class="form-control <?php if(!$edit) echo 'validate[required,minSize[8]]';?>" type="password"  maxlength="12"  name="password" value="">
					</div>
				</div>
			</div>
			<div class="header">
				<h3><?php esc_html_e('Other Information','hospital_mgt');?></h3>
				<hr>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="photo"><?php esc_html_e('Image','hospital_mgt');?></label>
					<div class="col-sm-3 margin_bottom_5px">
						<input type="text" id="hmgt_user_avatar_url" class="form-control" name="hmgt_user_avatar"  
						value="<?php if($edit)echo esc_url( $user_info->hmgt_user_avatar );elseif(isset($_POST['hmgt_user_avatar'])) echo $_POST['hmgt_user_avatar']; ?>" readonly />
					</div>	
					<div class="col-sm-3">
							 <input id="upload_user_avatar_button" type="button" class="button" value="<?php esc_html_e('Upload image', 'hospital_mgt' ); ?>" />
							 <br>
							 <span class="description"><?php esc_html_e('Upload only JPG, JPEG, PNG & GIF image', 'hospital_mgt' ); ?></span>
					
					</div>
					<?php wp_nonce_field( 'save_inpatient_nonce' ); ?>
					<div class="clearfix"></div>
					
					<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
						<div id="upload_user_avatar_preview" >
						 <?php if($edit) 
							{
							if($user_info->hmgt_user_avatar == "")
							{?>
							<img class="image_preview_css" alt="" src="<?php echo esc_url(get_option( 'hmgt_patient_thumb' )); ?>">
							<?php }
							else {
								?>
							<img class="image_preview_css" src="<?php if($edit)echo esc_url( $user_info->hmgt_user_avatar ); ?>" />
							<?php 
							}
							}
							else {
								?>
								<img class="image_preview_css" alt="" src="<?php echo esc_url(get_option( 'hmgt_patient_thumb' )); ?>">
								<?php 
							}?>
						</div>
					</div>
				</div>
			</div>
			<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
				<input type="submit" value="<?php if($edit){ esc_html_e('Save And Next Step','hospital_mgt'); }else{ esc_html_e('Save And Next Step','hospital_mgt');}?>" name="save_patient" class="btn btn-success patient_btn1"/>
			</div>       
        </form>
    </div>  
<!-- END PANAL BODY DIV -->	      
<?php 
	}
?>