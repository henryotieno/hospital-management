<?php
$active_tab = isset($_GET['tab'])?$_GET['tab']:'setup';
?>
<div id="hmgt_imgSpinner1">
</div>
<div class="cmgt_ajax-ani"></div>
<div class="hmgt_ajax-img">
	<img src="<?php echo HMS_PLUGIN_URL.'/assets/images/loading.gif';?>" height="50px" width="50px">
</div>
<div class="page-inner min_height_1088"><!-- PAGE INNER DIV START-->
	<div class="page-title">
		<h3><img src="<?php echo esc_url(get_option( 'hmgt_hospital_logo', 'hospital_mgt' )) ?>" class="img-circle head_logo" width="40" height="40" /><?php echo esc_html(get_option('hmgt_hospital_name','hospital_mgt'));?></h3>
	</div>
	<?php 
	//-----------------  VARIFY KEY --------------//
	if(isset($_REQUEST['varify_key']))
	{
		$nonce = $_POST['_wpnonce'];
		if (wp_verify_nonce( $nonce, 'varify_key_save_nonce' ) )
		{
			$verify_result = MJ_hmgt_submit_setupform($_POST);
			
			if($verify_result['hmgt_verify']== '0' || $verify_result['hmgt_verify']== '2')
			{ 
				echo '<div id="message" class="updated notice notice-success is-dismissible"><p>'.$verify_result['message'].'</p>
				<button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';			
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
			$('#verification_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else
		{
		?>
			$('#verification_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>
	});
	</script>
	<?php 
	if(isset($_SESSION['hmgt_verify']) && $_SESSION['hmgt_verify'] == '3')
	{
		?>
		<div id="message" class="updated notice notice-success">
			<p>
			<?php esc_html_e('There seems to be some problem please try after sometime or contact us on sales@mojoomla.com','hospital_mgt');?>
				
			</p>
		</div>
	<?php 
	}
	elseif(isset($_SESSION['hmgt_verify']) && $_SESSION['hmgt_verify'] == '1')
	{
		?>
		<div id="message" class="updated notice notice-success">
			<p>
			<?php esc_html_e('Please provide correct Envato purchase key.','hospital_mgt');?>
			</p>
		</div>
	<?php 
	}
	else
	{
	?>
		<div id="message" class="updated notice notice-success display_none"></div>
	<?php 
	}
	?> 

	<div id="main-wrapper"><!-- MAIN WRAPPER DIV START -->
		<div class="row"><!-- ROW DIV START -->
			<div class="col-md-12">
				<div class="panel panel-white"><!-- PANEL WHITE DIV START -->
					<div class="panel-body">	<!-- PANEL BODY DIV START -->
					  <form name="verification_form" action="" method="post" class="form-horizontal" id="verification_form">
							<div class="form-group">
								<div class="mb-3 row">
									<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="domain_name">
									<?php esc_html_e('Domain','hospital_mgt');?> <span class="require-field">*</span></label>
									<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
										<input id="server_name" class="form-control validate[required]" type="text" 
										value="<?php echo esc_attr($_SERVER['SERVER_NAME']);?>" name="domain_name" readonly>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="mb-3 row">
									<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="licence_key"><?php esc_html_e('Envato License key','hospital_mgt');?> <span class="require-field">*</span></label>
									<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
										<input id="licence_key" class="form-control validate[required]" type="text"  value="" name="licence_key">
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="mb-3 row">
									<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="enter_email"><?php esc_html_e('Email','hospital_mgt');?> <span class="require-field">*</span></label>
									<div class="col-lg-8 col-md-8 col-sm-8 col-xs-12">
										<input id="enter_email" class="form-control validate[required,custom[email]]" type="text"  value="" name="enter_email">
									</div>
								</div>
							</div>
							<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
								<input type="submit" value="<?php esc_html_e('Save','hospital_mgt');?>" name="varify_key" id="varify_key" class="btn btn-success"/>
							</div>
							<?php wp_nonce_field( 'varify_key_save_nonce' ); ?>
						</form>						
					</div>	<!-- PANEL BODY DIV END -->		
				</div><!-- PANEL WHITE DIV END -->
			</div>
		</div><!-- ROW DIV END -->
	</div><!-- MAIN WRAPPERDIV END -->
</div><!-- PAGE INNER DIV END -->