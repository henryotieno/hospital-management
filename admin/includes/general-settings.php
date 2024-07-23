<?php
$user_role=MJ_hmgt_get_roles(get_current_user_id());
if($user_role == 'administrator')
{
	$user_access_add=1;
	$user_access_edit=1;
	$user_access_view=1;
}
else
{
	$user_access=MJ_hmgt_get_access_right_for_management_user_page('gnrl_settings');
	$user_access_add=$user_access['add'];
	$user_access_edit=$user_access['edit'];
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
		if (isset ( $_REQUEST ['page'] ) && 'gnrl_settings' == $user_access['page_link'] && ($_REQUEST['action']=='edit'))
		{
			if($user_access['edit']=='0')
			{	
				MJ_hmgt_access_right_page_not_access_message_admin();
				die;
			}			
		}
		if (isset ( $_REQUEST ['page'] ) && 'gnrl_settings' == $user_access['page_link'] && ($_REQUEST['action']=='insert')) 
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
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	"use strict";
	 <?php
	if (is_rtl())
		{
		?>	
			$('#setting_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#setting_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>
	
	var hmgt_payment_method_setting = $('.hmgt_payment_method_setting').val();
	if(hmgt_payment_method_setting == '1')
	{
		$(".stripe_div").show();
		$(".paypal_div").hide();
	}
	else
	{
		$(".stripe_div").hide();
		$(".paypal_div").show();
	}
	$(".hmgt_payment_method_setting").on('change',function()
	{
		var hmgt_payment_method_setting = $('.hmgt_payment_method_setting').val();
		if(hmgt_payment_method_setting == '1')
		{
			$(".stripe_div").show();
			$(".paypal_div").hide();
		}
		else
		{
			$(".stripe_div").hide();
			$(".paypal_div").show();
		}
	});
});
</script>
<div class="page-inner min_height_1631"><!-- PAGE INNER DIV START-->		
   <div class="page-title">
		<h3><img src="<?php echo esc_url(get_option( 'hmgt_hospital_logo', 'hospital_mgt' )) ?>" class="img-circle head_logo" width="40" height="40" /><?php echo esc_html(get_option('hmgt_hospital_name','hospital_mgt'));?></h3>
	</div>
	<?php
	MJ_hmgt_browser_javascript_check();	
	//---------------- SAVE GENAREL SETTING --------------//
	if(isset($_POST['save_setting']))
	{
		$nonce = $_POST['_wpnonce'];
		if (wp_verify_nonce( $nonce, 'save_setting_nonce' ) )
		{
			$txturl=$_POST['hmgt_hospital_logo'];
			$txturl1=$_POST['hmgt_hospital_background_image'];
			$ext=MJ_hmgt_check_valid_extension($txturl);
			$ext1=MJ_hmgt_check_valid_extension($txturl1);
			if(!$ext == 0 && !$ext1==0)
			{	
				$optionval=MJ_hmgt_option();
				foreach($optionval as $key=>$val)
				{
					if(isset($_POST[$key]))
					{
						$result=update_option( $key, $_POST[$key]);
					}
				}
			}			
			else
			{ ?>
				<div id="message" class="updated below-h2 notice is-dismissible">
				<p>
					<?php esc_html_e('Sorry, only JPG, JPEG, PNG & GIF And BMP files are allowed.','hospital_mgt');?>
				</p></div>				 
				<?php 
			}	
		
			if(isset($_REQUEST['hmgt_enable_change_profile_picture']))
					update_option( 'hmgt_enable_change_profile_picture', 'yes' );
				else 
					update_option( 'hmgt_enable_change_profile_picture', 'no' );	
			if(isset($_REQUEST['hmgt_enable_hospitalname_in_priscription']))
					update_option( 'hmgt_enable_hospitalname_in_priscription', 'yes' );
				else 
					update_option( 'hmgt_enable_hospitalname_in_priscription', 'no' );	
			if(isset($_REQUEST['hmgt_enable_staff_can_message']))
					update_option( 'hmgt_enable_staff_can_message', 'yes' );
				else 
					update_option( 'hmgt_enable_staff_can_message', 'no' );	

			if(isset($_REQUEST['hmgt_patient_approve']))
					update_option( 'hmgt_patient_approve', 'yes' );
				else 
					update_option( 'hmgt_patient_approve', 'no' );	

		   if(isset($_REQUEST['hospital_enable_notifications']))
					update_option( 'hospital_enable_notifications', 'yes' );
				else 
					update_option( 'hospital_enable_notifications', 'no' );
				
			if(isset($_REQUEST['hospital_enable_sandbox']))
				update_option( 'hospital_enable_sandbox', 'yes' );
			else 	
				update_option( 'hospital_enable_sandbox', 'no' );
			
			//	UPDATE GENERAL SETTINGS OPTION
			if(isset($_REQUEST['hmgt_paymaster_pack']))
			{
				update_option( 'hmgt_paymaster_pack', 'yes' );
			}
			else
			{
				update_option( 'hmgt_paymaster_pack', 'no' );
			}
			if(isset($_REQUEST['hmgt_enable_virtual_appointment']))
			{
				update_option( 'hmgt_enable_virtual_appointment', 'yes' );
			}
			else 
			{
				update_option( 'hmgt_enable_virtual_appointment', 'no' );
			}
			if(isset($_REQUEST['hmgt_enable_virtual_appointment_reminder']))
			{
				update_option( 'hmgt_enable_virtual_appointment_reminder', 'yes' );
			}
			else 
			{
				update_option( 'hmgt_enable_virtual_appointment_reminder', 'no' );
			}
			
			if(isset($_REQUEST['hmgt_enable_sms_virtual_appointment_reminder']))
			{
				update_option( 'hmgt_enable_sms_virtual_appointment_reminder', 'yes' );
			}
			else 
			{
				update_option( 'hmgt_enable_sms_virtual_appointment_reminder', 'no' );
			}
			if(isset($result))
			{?>
				<div id="message" class="updated below-h2 notice is-dismissible">
					<p><?php esc_html_e('Record updated successfully','hospital_mgt');?></p>
				</div>
				<?php 
			}
		}
	}
	?>
	<div id="main-wrapper"><!-- MAIN WRAPPER DIV START-->		
	    <div class="panel panel-white"><!-- PANEL WHITE DIV START-->		
				<div class="panel-body"><!-- PANEL BODY DIV START-->		
					<h2>	
					<?php  echo esc_html( esc_html__( 'General Settings', 'hospital_mgt')); ?>
					</h2>
					<div class="panel-body"><!-- PANEL BODY DIV START-->	
						<form name="student_form" action="" method="post" class="form-horizontal" id="setting_form">
						<div class="form-group">
							<div class="mb-3 row">	
								<label class="col-sm-2 control-label form-label" for="hmgt_hospital_name"><?php esc_html_e('Hospital Name','hospital_mgt');?><span class="require-field">*</span></label>
								<div class="col-sm-8">
									<input id="hmgt_hospital_name" class="form-control validate[required,custom[popup_category_validation]]" type="text" maxlength="50" value="<?php echo get_option( 'hmgt_hospital_name' );?>"  name="hmgt_hospital_name">
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="mb-3 row">	
								<label class="col-sm-2 control-label form-label" for="hmgt_staring_year"><?php esc_html_e('Starting Year','hospital_mgt');?></label>
								<div class="col-sm-8">
									<input id="hmgt_staring_year" class="form-control" min="0" type="number" onKeyPress="if(this.value.length==4) return false;" value="<?php echo get_option( 'hmgt_staring_year' );?>"  name="hmgt_staring_year">
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="mb-3 row">	
								<label class="col-sm-2 control-label form-label" for="hmgt_hospital_address"><?php esc_html_e('Hospital Address','hospital_mgt');?><span class="require-field">*</span></label>
								<div class="col-sm-8">
									<input id="hmgt_hospital_address" class="form-control validate[required,custom[address_description_validation]]" maxlength="150" type="text" value="<?php echo get_option( 'hmgt_hospital_address' );?>"  name="hmgt_hospital_address">
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="mb-3 row">	
								<label class="col-sm-2 control-label form-label" for="hmgt_contact_number"><?php esc_html_e('Official Phone Number','hospital_mgt');?><span class="require-field">*</span></label>
								<div class="col-sm-8">
									<input id="hmgt_contact_number" class="form-control validate[required,custom[phone_number]] text-input" minlength="6" maxlength="15" type="text" value="<?php echo get_option( 'hmgt_contact_number' );?>" name="hmgt_contact_number">
								</div>
							</div>
						</div>
						<div class="form-group" class="form-control" id="">
							<div class="mb-3 row">	
								<label class="col-sm-2 control-label form-label" for="hmgt_contry"><?php esc_html_e('Country','hospital_mgt');?></label>
								<div class="col-sm-8">														
								<?php 
									$url = plugins_url( 'countrylist.xml', __FILE__ );
									
									if(MJ_hmgt_get_remote_file($url))
									{
										$xml =simplexml_load_string(MJ_hmgt_get_remote_file($url));
									}
									else
									{
										die("Error: Cannot create object");
									}
								?>
									<select name="hmgt_contry" class="form-control validate[required] max_width_100" id="smgt_contry">
										<option value=""><?php esc_html_e('Select Country','hospital_mgt');?></option>
										<?php
											foreach($xml as $country)
											{  
											?>
											 <option value="<?php echo $country->name;?>" <?php selected(get_option( 'hmgt_contry' ), $country->name);  ?>><?php echo $country->name;?></option>
										<?php }?>
									</select> 
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="mb-3 row">	
								<label class="col-sm-2 control-label form-label" for="hmgt_email"><?php esc_html_e('Email','hospital_mgt');?><span class="require-field">*</span></label>
								<div class="col-sm-8">
									<input id="hmgt_email" class="form-control validate[required,custom[email]] text-input" maxlength="100" type="text" value="<?php echo get_option( 'hmgt_email' );?>"  name="hmgt_email">
								</div>
							</div>
						</div>
						
						<div class="form-group">
							<div class="mb-3 row">	
								<label class="col-sm-2 control-label form-label" for="hmgt_currency_code"><?php esc_html_e('Date Format','hospital_mgt');?><span class="require-field">*</span></label>
								<div class="col-sm-8">
							  		<select name="MJ_hmgt_date_formate" class="form-control validate[required] text-input">
									  <option value=""> <?php esc_html_e('Select Date Format','hospital_mgt');?></option>
									  <option value="Y-m-d" <?php echo selected(get_option( 'MJ_hmgt_date_formate' ),'Y-m-d');?>>
									  <?php esc_html_e('2017-12-12','hospital_mgt');?></option>
									  <option value="m/d/Y" <?php echo selected(get_option( 'MJ_hmgt_date_formate' ),'m/d/Y');?>>
									  <?php esc_html_e('12/31/2017','hospital_mgt');?></option>
									   <option value="d/m/Y" <?php echo selected(get_option( 'MJ_hmgt_date_formate' ),'d/m/Y');?>>
									  <?php esc_html_e('31/12/2017','hospital_mgt');?></option>  
									  <option value="F j, Y" <?php echo selected(get_option( 'MJ_hmgt_date_formate' ),'F j, Y');?>>
									  <?php esc_html_e('December 12, 2017','hospital_mgt');?></option>
									</select>
								</div>
							</div>
						</div>						
						<!-- notification template   -->
						<div class="form-group">
							<div class="mb-3 row">	
								<label class="col-sm-2 control-label form-label" for="hospital_enable_notifications"><?php esc_html_e('Enable Notifications','hospital_mgt');?></label>
								<div class="col-sm-8 send_mail_checkbox">
									<div class="checkbox">
										<label>
											<input type="checkbox" name="hospital_enable_notifications"  value="1" <?php echo checked(get_option('hospital_enable_notifications'),'yes');?>/><?php esc_html_e('Enable','hospital_mgt');?>
									  </label>
								  </div>
								</div>
							</div>
						</div>
						<!-- end notification template   -->
						
						<div class="form-group">
							<div class="mb-3 row">	
								<label class="col-sm-2 control-label form-label" for="hmgt_email"><?php esc_html_e('Hospital Logo','hospital_mgt');?><span class="require-field">*</span></label>
								<div class="col-sm-8 ">
									<input type="text" id="hmgt_user_avatar_url"  name="hmgt_hospital_logo" class="validate[required] margin_bottom_5px" value="<?php  echo get_option( 'hmgt_hospital_logo' ); ?>" readonly />
									<input id="upload_user_avatar_button" type="button" class="button" value="<?php esc_html_e( 'Upload image', 'hospital_mgt' ); ?>" />
									<span class="description"><?php esc_html_e('Upload image.', 'hospital_mgt' ); ?></span>
									<div id="upload_user_avatar_preview min_height_100">
										<img class="image_preview_css" src="<?php  echo get_option( 'hmgt_hospital_logo' ); ?>" />
									</div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="mb-3 row">	
								<label class="col-sm-2 control-label form-label" for="hmgt_cover_image"><?php esc_html_e('Profile Cover Image','hospital_mgt');?></label>
								<div class="col-sm-8 ">
									<input type="text" class="margin_bottom_5px" id="hmgt_hospital_background_image" name="hmgt_hospital_background_image" value="<?php 		 echo get_option( 'hmgt_hospital_background_image' ); ?>" readonly />	
									<input id="upload_image_button" type="button" class="button upload_user_cover_button" value="<?php esc_html_e( 'Upload Cover Image', 'hospital_mgt' ); ?>" />
									<span class="descriptions"><?php esc_html_e('Upload Cover Image', 'hospital_mgt' ); ?></span>
											 
									<div id="upload_hospital_cover_preview min_height_100_m_t_5">
										<img class="width_100" src="<?php  echo get_option( 'hmgt_hospital_background_image' ); ?>" />
									</div>
								</div>
							</div>
						</div>	
						<?php wp_nonce_field( 'save_setting_nonce' ); ?>
						
						<?php if(is_plugin_active('paymaster/paymaster.php')) 
							{ ?> 
								<div class="header">	<hr>
									<h3><?php esc_html_e('Paymaster Setting','hospital_mgt');?></h3>
								</div>
								<div class="form-group">
									<div class="mb-3 row">	
										<label for="hmgt_paymaster_pack" class="col-sm-2 control-label"><?php esc_html_e('Use Paymaster Payment Gateways','hospital_mgt');?></label>
										<div class="col-sm-4 send_mail_checkbox">
											<div class="checkbox">
											<label><input type="checkbox" value="yes" <?php echo checked(get_option('hmgt_paymaster_pack'),'yes');?> name="hmgt_paymaster_pack"><?php esc_html_e('Enable','hospital_mgt') ?> </label>
										  </div>
										</div>
									</div>
								</div>
								<?php 
							} ?>
						<!-- PAYPAL SETTING-->
						<div class="header">	<hr>
							<h3><?php esc_html_e('Payment Setting','hospital_mgt');?></h3>
						</div>
						<div class="form-group">
							<div class="mb-3 row">
								<label class="col-sm-2 control-label form-label" for="hmgt_payment_method_setting"><?php esc_html_e('Payment Method','hospital_mgt');?><span class="require-field">*</span></label>
								<div class="col-sm-8">
									<select name="hmgt_payment_method_setting" class="form-control validate[required] text-input hmgt_payment_method_setting">
									  <option value=""> <?php esc_html_e('Select Payment method','hospital_mgt');?></option>
									  <option value="0" <?php echo selected(get_option( 'hmgt_payment_method_setting' ),'0');?>>
									  <?php esc_html_e('Paypal','hospital_mgt');?></option>
									  <option value="1" <?php echo selected(get_option( 'hmgt_payment_method_setting' ),'1');?>>
									  <?php esc_html_e('Stripe','hospital_mgt');?></option>
									</select>
								</div>
							</div>
						</div>
						<div class="paypal_div">
							<div class="form-group">
								<div class="mb-3 row">	
									<label class="col-sm-2 control-label form-label" for="hospital_enable_sandbox"><?php esc_html_e('Enable Sandbox','hospital_mgt');?></label>
									<div class="col-sm-8 send_mail_checkbox">
										<div class="checkbox">
											<label>
												<input type="checkbox" name="hospital_enable_sandbox"  value="1" <?php echo checked(get_option('hospital_enable_sandbox'),'yes');?>/><?php esc_html_e('Enable','hospital_mgt');?>
										</label>
									</div>
									</div>
								</div>
							</div> 
							
							<!--PAYPAL IMAIL-->
							<div class="form-group">
								<div class="mb-3 row">	
									<label class="col-sm-2 control-label form-label" for="hospital_paypal_email"><?php esc_html_e('Paypal Email Id','hospital_mgt');?><span class="require-field">*</span></label>
									<div class="col-sm-8">
										<input id="hospital_paypal_email" class="form-control validate[required,custom[email]] text-input" maxlength="50" type="text" value="<?php echo get_option( 'hospital_paypal_email' );?>"  name="hospital_paypal_email">
									</div>
								</div>
							</div>
						</div>
						<div class="stripe_div">
							<div class="mb-3 row">
								<label class="col-sm-2 control-label form-label" for="hmgt_stripe_secret_key"><?php esc_html_e('Secret Key','hospital_mgt');?><span class="require-field">*</span></label>
								<div class="col-sm-8">
									<input id="hmgt_stripe_secret_key" class="form-control text-input validate[required]" maxlength="100" type="text" value="<?php echo get_option( 'hmgt_stripe_secret_key' );?>"  name="hmgt_stripe_secret_key">
								</div>
							</div>
							<div class="mb-3 row">
								<label class="col-sm-2 control-label form-label" for="hmgt_stripe_publishable_key"><?php esc_html_e('Publishable Key','hospital_mgt');?><span class="require-field">*</span></label>
								<div class="col-sm-8">
									<input id="hmgt_stripe_publishable_key" class="form-control text-input validate[required]" maxlength="100" type="text" value="<?php echo get_option( 'hmgt_stripe_publishable_key' );?>"  name="hmgt_stripe_publishable_key">
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="mb-3 row">	
								<label class="col-sm-2 control-label form-label" for="hmgt_currency_code"><?php esc_html_e('Select Currency','hospital_mgt');?><span class="require-field">*</span></label>
								<div class="col-sm-8">
									<select name="hmgt_currency_code" class="form-control validate[required] max_width_100 text-input">
									  <option value=""> <?php esc_html_e('Select Currency','hospital_mgt');?></option>
									  <option value="AUD" <?php echo selected(get_option( 'hmgt_currency_code' ),'AUD');?>>
									  <?php esc_html_e('Australian Dollar','hospital_mgt');?></option>
									  <option value="BRL" <?php echo selected(get_option( 'hmgt_currency_code' ),'BRL');?>>
									  <?php esc_html_e('Brazilian Real','hospital_mgt');?> </option>
									  <option value="CAD" <?php echo selected(get_option( 'hmgt_currency_code' ),'CAD');?>>
									  <?php esc_html_e('Canadian Dollar','hospital_mgt');?></option>
									  <option value="CZK" <?php echo selected(get_option( 'hmgt_currency_code' ),'CZK');?>>
									  <?php esc_html_e('Czech Koruna','hospital_mgt');?></option>
									  
									  <option value="COP" <?php echo selected(get_option( 'hmgt_currency_code' ),'COP');?>>
									  <?php esc_html_e('Colombia Peso','hospital_mgt');?></option>
									  
									  <option value="DKK" <?php echo selected(get_option( 'hmgt_currency_code' ),'DKK');?>>
									  <?php esc_html_e('Danish Krone','hospital_mgt');?></option>
									  <option value="EUR" <?php echo selected(get_option( 'hmgt_currency_code' ),'EUR');?>>
									  <?php esc_html_e('Euro','hospital_mgt');?></option>
									  <option value="EGP" <?php echo selected(get_option( 'hmgt_currency_code' ),'EGP');?>>
									  <?php esc_html_e('Egypt','hospital_mgt');?></option>
									  <option value="GHC" <?php echo selected(get_option( 'hmgt_currency_code' ),'GHC');?>>
									   <?php esc_html_e('Cedis','hospital_mgt');?></option>
									  <option value="HKD" <?php echo selected(get_option( 'hmgt_currency_code' ),'HKD');?>>
									  <?php esc_html_e('Hong Kong Dollar','hospital_mgt');?></option>
									  <option value="HUF" <?php echo selected(get_option( 'hmgt_currency_code' ),'HUF');?>>
									  <?php esc_html_e('Hungarian Forint','hospital_mgt');?> </option>
									  <option value="INR" <?php echo selected(get_option( 'hmgt_currency_code' ),'INR');?>>
									  <?php esc_html_e('Indian Rupee','hospital_mgt');?></option>
									  <option value="ILS" <?php echo selected(get_option( 'hmgt_currency_code' ),'ILS');?>>
									  <?php esc_html_e('Israeli New Sheqel','hospital_mgt');?></option>
									  <option value="JPY" <?php echo selected(get_option( 'hmgt_currency_code' ),'JPY');?>>
									  <?php esc_html_e('Japanese Yen','hospital_mgt');?></option>
									  <option value="MYR" <?php echo selected(get_option( 'hmgt_currency_code' ),'MYR');?>>
									  <?php esc_html_e('Malaysian Ringgit','hospital_mgt');?></option>
									  
									  <option value="MAD" <?php echo selected(get_option( 'hmgt_currency_code' ),'MAD');?>>
									  <?php esc_html_e('Moroccan Dirham','hospital_mgt');?></option>
									  
									   <option value="MXN" <?php echo selected(get_option( 'hmgt_currency_code' ),'MXN');?>>
									  <?php esc_html_e('Mexican Peso','hospital_mgt');?></option>
									  
									  <option value="NOK" <?php echo selected(get_option( 'hmgt_currency_code' ),'NOK');?>>
									  <?php esc_html_e('Norwegian Krone','hospital_mgt');?></option>
									  <option value="NZD" <?php echo selected(get_option( 'hmgt_currency_code' ),'NZD');?>>
									  <?php esc_html_e('New Zealand Dollar','hospital_mgt');?></option>
									  <option value="NGN" <?php echo selected(get_option( 'hmgt_currency_code' ),'NGN');?>>
									  <?php esc_html_e('Nigeria Naira','hospital_mgt');?></option>
									  
									  <option value="PHP" <?php echo selected(get_option( 'hmgt_currency_code' ),'PHP');?>>
									  <?php esc_html_e('Philippine Peso','hospital_mgt');?></option>
									  <option value="PLN" <?php echo selected(get_option( 'hmgt_currency_code' ),'PLN');?>>
									  <?php esc_html_e('Polish Zloty','hospital_mgt');?></option>
									  <option value="GBP" <?php echo selected(get_option( 'hmgt_currency_code' ),'GBP');?>>
									  <?php esc_html_e('Pound Sterling','hospital_mgt');?></option>
									  <option value="SGD" <?php echo selected(get_option( 'hmgt_currency_code' ),'SGD');?>>
									  <?php esc_html_e('Singapore Dollar','hospital_mgt');?></option>
									  <option value="SEK" <?php echo selected(get_option( 'hmgt_currency_code' ),'SEK');?>>
									  <?php esc_html_e('Swedish Krona','hospital_mgt');?></option>
									  <option value="CHF" <?php echo selected(get_option( 'hmgt_currency_code' ),'CHF');?>>
									  <?php esc_html_e('Swiss Franc','hospital_mgt');?></option>
									  <option value="ZAR" <?php echo selected(get_option( 'hmgt_currency_code' ),'ZAR');?>>
									  <?php esc_html_e('South Africa','hospital_mgt');?></option>
									  <option value="TWD" <?php echo selected(get_option( 'hmgt_currency_code' ),'TWD');?>>
									  <?php esc_html_e('Taiwan New Dollar','hospital_mgt');?></option>
									  <option value="THB" <?php echo selected(get_option( 'hmgt_currency_code' ),'THB');?>>
									  <?php esc_html_e('Thai Baht','hospital_mgt');?></option>
									  <option value="TRY" <?php echo selected(get_option( 'hmgt_currency_code' ),'TRY');?>>
									  <?php esc_html_e('Turkish Lira','hospital_mgt');?></option>
									  <option value="USD" <?php echo selected(get_option( 'hmgt_currency_code' ),'USD');?>>
									  <?php esc_html_e('U.S. Dollar','hospital_mgt');?></option>
									  <option value="VND" <?php echo selected(get_option( 'hmgt_currency_code' ),'VND');?>>
									  <?php esc_html_e('Vietnamese Dong','hospital_mgt');?></option>
									</select>
								</div>
								<div class="col-sm-1">
									<span class="font_20"><?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' ));?></span>
								</div>
							</div>
						</div>
						<div class="header">	<hr>
							<h3><?php esc_html_e('User Can Change Profile Picture Setting','hospital_mgt');?></h3>
						</div>
						<div class="form-group">
							<div class="mb-3 row">	
								<label class="col-sm-2 control-label form-label" for="hmgt_enable_change_profile_picture"><?php esc_html_e("User Can Change Profile Picture","hospital_mgt");?></label>
								<div class="col-sm-8 send_mail_checkbox">
									<div class="checkbox">
										<label>
											<input type="checkbox" name="hmgt_enable_change_profile_picture"  value="yes" <?php echo checked(get_option('hmgt_enable_change_profile_picture'),'yes');?>/><?php esc_html_e('Enable','hospital_mgt');?>
									  </label>
								  </div>
								</div>
							</div>
						</div>
						
						<div class="header">	<hr>
							<h3><?php esc_html_e('Keeps Hospital Name In Prescription print','hospital_mgt');?></h3>
						</div>
						<div class="form-group">
							<div class="mb-3 row">	
								<label class="col-sm-2 control-label form-label" for="hmgt_enable_hospitalname_in_priscription"><?php esc_html_e("Hospital Name print in Prescription","hospital_mgt");?></label>
								<div class="col-sm-8 send_mail_checkbox">
									<div class="checkbox">
										<label>
											<input type="checkbox" name="hmgt_enable_hospitalname_in_priscription"  value="yes" <?php echo checked(get_option('hmgt_enable_hospitalname_in_priscription'),'yes');?>/><?php esc_html_e('Enable','hospital_mgt');?>
									  </label>
								  </div>
								</div>
							</div>
						</div>
						<div class="head">
							<hr>
							<h4 class="section"><?php _e('Virtual Appointment Setting(Zoom)','hospital_mgt');?></h4>
						</div>
						<div class="form-group">
							<div class="mb-3 row">	
								<label class="col-sm-2 control-label form-label" for="hmgt_enable_virtual_appointment"><?php _e('Virtual Appointment','hospital_mgt');?></label>
								<div class="col-sm-8">
									<div class="checkbox">
										<label>
						              		<input type="checkbox" name="hmgt_enable_virtual_appointment"  class="margin_bottom_min_15_res" value="1" <?php echo checked(get_option('hmgt_enable_virtual_appointment'),'yes');?>/><?php _e('Enable','hospital_mgt');?>
						              </label>
					              </div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="mb-3 row">	
								<label class="col-sm-2 control-label form-label" for="hmgt_virtual_appointment_client_id"><?php _e('Client Id','hospital_mgt');?></label>
								<div class="col-sm-8">
									<input id="hmgt_virtual_appointment_client_id" class="form-control text-input" type="text" value="<?php echo get_option( 'hmgt_virtual_appointment_client_id' );?>"  name="hmgt_virtual_appointment_client_id">
									<span class="description"><?php _e('That will be provided by zoom.', 'hospital_mgt' ); ?></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="mb-3 row">	
								<label class="col-sm-2 control-label form-label" for="hmgt_virtual_appointment_client_secret_id"><?php _e('Client Secret Id','hospital_mgt');?></label>
								<div class="col-sm-8">
									<input id="hmgt_virtual_appointment_client_secret_id" class="form-control text-input" type="text" value="<?php echo get_option( 'hmgt_virtual_appointment_client_secret_id' );?>"  name="hmgt_virtual_appointment_client_secret_id">
									<span class="description"><?php _e('That will be provided by zoom.', 'hospital_mgt' ); ?></span>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="mb-3 row">	
								<label class="col-sm-2 control-label form-label" for="hmgt_virtual_appointment_client_id"><?php _e('Redirect URL','hospital_mgt');?></label>
								<div class="col-sm-8">
									<input id="" class="form-control text-input" type="text" value="<?php echo site_url().'/?page=callback';?>"  name="" disabled>
									<span class="description"><?php _e('Please copy this Redirect URL and add in your zoom account Redirect URL.', 'hospital_mgt' ); ?></span>
								</div>
							</div>
						</div>


						<div class="form-group">
							<div class="mb-3 row">	
								<label class="col-sm-2 control-label form-label" for="hmgt_enable_virtual_appointment_reminder"><?php _e('Mail Notification Virtual Appointment Reminder','hospital_mgt');?></label>
								<div class="col-sm-8">
									<div class="checkbox">
										<label>
						              		<input id="virtual_classroom_reminder" type="checkbox" name="hmgt_enable_virtual_appointment_reminder"  class="margin_bottom_min_15_res" value="1" <?php echo checked(get_option('hmgt_enable_virtual_appointment_reminder'),'yes');?>/><?php _e('Enable','hospital_mgt');?>
						              	</label>
					              	</div>
								</div>
							</div>
						</div>

						<div class="form-group">
							<div class="mb-3 row">	
								<label class="col-sm-2 control-label form-label" for="hmgt_enable_sms_virtual_appointment_reminder"><?php _e('SMS Notification Virtual Appointment Reminder','hospital_mgt');?></label>
								<div class="col-sm-8">
									<div class="checkbox">
										<label>
						              		<input id="virtual_classroom_reminder" type="checkbox" name="hmgt_enable_sms_virtual_appointment_reminder"  class="margin_bottom_min_15_res" value="1" <?php echo checked(get_option('hmgt_enable_sms_virtual_appointment_reminder'),'yes');?>/><?php _e('Enable','hospital_mgt');?>
						              	</label>
					              	</div>
								</div>
							</div>
						</div>
					
						<div class="form-group">
							<div class="mb-3 row">	
								<label class="col-sm-2 control-label form-label" for="hmgt_virtual_appointment_reminder_before_time"><?php esc_html_e('Reminder Before Time','hospital_mgt');?></label>
								<div class="col-sm-8">
									<input id="hmgt_virtual_appointment_reminder_before_time" class="form-control" min="0" type="number" onKeyPress="if(this.value.length==2) return false;"  placeholder="<?php esc_html_e('01 Minute','hospital_mgt');?>"value="<?php echo get_option( 'hmgt_virtual_appointment_reminder_before_time' );?>"  name="hmgt_virtual_appointment_reminder_before_time">
								</div>
							</div>
						</div>
						<div class="header">	
							<hr>
							<h3><?php esc_html_e('Message Setting','hospital_mgt');?></h3>
						</div>
						<div class="form-group margin_bottom_5px">
							<div class="mb-3 row">	
								<label class="col-sm-2 control-label form-label" for="hmgt_enable_staff_can_message"><?php esc_html_e("Staff can Message To Admin","hospital_mgt");?></label>
								<div class="col-sm-8 margin_bottom_5px send_mail_checkbox">
									<div class="checkbox">
										<label>
											<input type="checkbox" name="hmgt_enable_staff_can_message"  value="yes" <?php echo checked(get_option('hmgt_enable_staff_can_message'),'yes');?>/><?php esc_html_e('Enable','hospital_mgt');?>
									  </label>
								  </div>
								</div>
							</div>
						</div>	
						<div class="header">	
							<hr>
							<h3><?php esc_html_e('Patient Approval Setting','hospital_mgt');?></h3>
						</div>
						<div class="form-group margin_bottom_5px">
							<div class="mb-3 row">	
								<label class="col-sm-2 control-label form-label" for="hmgt_patient_approve"><?php esc_html_e("Admin Approve","hospital_mgt");?></label>
								<div class="col-sm-8 margin_bottom_5px send_mail_checkbox">
									<div class="checkbox">
										<label>
											<input type="checkbox" name="hmgt_patient_approve"  value="yes" <?php echo checked(get_option('hmgt_patient_approve'),'yes');?>/><?php esc_html_e('Enable','hospital_mgt');?>
									  </label>
								  </div>
								</div>
							</div>
						</div>
                      <?php 
					if($user_access_add == 1 OR $user_access_edit == 1 )
					{?>
                     					   
						<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">							
							<input type="submit" value="<?php esc_html_e('Save', 'hospital_mgt' ); ?>" name="save_setting" class="btn btn-success"/>
						</div>				
					<?php 
					} 
					?>
				 </form>
					</div><!-- PANEL BODY DIV END-->	
                </div><!-- PANEL BODY DIV END-->	
        </div><!-- PANEL WHITE DIV END-->
    </div><!-- MAIN WRAPPER DIV END-->
</div><!-- PAGE INNER DIV END-->