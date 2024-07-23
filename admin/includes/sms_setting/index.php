<?php
MJ_hmgt_browser_javascript_check();
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
	$user_access=MJ_hmgt_get_access_right_for_management_user_page('sms_setting');
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
			if (isset ( $_REQUEST ['page'] ) && 'sms_setting' == $user_access['page_link'] && ($_REQUEST['action']=='edit'))
			{
				if($user_access['edit']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}			
			}
			if (isset ( $_REQUEST ['page'] ) && 'sms_setting' == $user_access['page_link'] && ($_REQUEST['action']=='delete'))
			{
				if($user_access['delete']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}	
			}
			if (isset ( $_REQUEST ['page'] ) && 'sms_setting' == $user_access['page_link'] && ($_REQUEST['action']=='insert')) 
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
$current_sms_service_active =get_option('hmgt_sms_service');
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	"use strict";
	 <?php
	if (is_rtl())
		{
		?>	
			$('#sms_setting_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#sms_setting_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>
});
</script>
<div class="page-inner min_height_1631"><!-- PAGE INNER DIV START-->
    <div class="page-title">
		<h3><img src="<?php echo esc_url(get_option( 'hmgt_hospital_logo', 'hospital_mgt' )) ?>" class="img-circle head_logo" width="40" height="40" /><?php echo esc_html(get_option('hmgt_hospital_name','hospital_mgt'));?></h3>
	</div>
	<?php 
	//---------------- SAVE SMS SETTING -----------------//
	if(isset($_REQUEST['save_sms_setting']))
	{
		if(isset($_REQUEST['select_serveice']) && $_REQUEST['select_serveice'] == 'clickatell')
		{
			$custm_sms_service = array();
			$result=get_option( 'hmgt_clickatell_sms_service');
			$custm_sms_service['username'] = trim($_REQUEST['username']);
			$custm_sms_service['password'] = $_REQUEST['password'];
			$custm_sms_service['api_key'] = $_REQUEST['api_key'];
			$result=update_option( 'hmgt_clickatell_sms_service',$custm_sms_service );
		}
		if(isset($_REQUEST['select_serveice']) && $_REQUEST['select_serveice'] == 'twillo')
		{
			$custm_sms_service = array();
			$result=get_option( 'hmgt_twillo_sms_service');
			$custm_sms_service['account_sid'] = trim($_REQUEST['account_sid']);
			$custm_sms_service['auth_token'] = trim($_REQUEST['auth_token']);
			$custm_sms_service['from_number'] = $_REQUEST['from_number'];
			$result=update_option( 'hmgt_twillo_sms_service',$custm_sms_service );
		}
		update_option( 'hmgt_sms_service',$_REQUEST['select_serveice'] );
		wp_redirect ( admin_url() . 'admin.php?page=hmgt_sms_setting&message=1');
		if(isset($_REQUEST['select_serveice']) && $_REQUEST['select_serveice'] == 'msg91')
	{
		$custm_sms_service = array();
		$result=get_option('hmgt_msg91_sms_service');
		$custm_sms_service['msg91_senderID'] = trim($_REQUEST['msg91_senderID']);
		$custm_sms_service['sms_auth_key'] = trim($_REQUEST['sms_auth_key']);
		$custm_sms_service['wpnc_sms_route'] = $_REQUEST['wpnc_sms_route'];
		$result=update_option('hmgt_msg91_sms_service',$custm_sms_service );
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
				esc_html_e('Record Updated Successfully','hospital_mgt');
			?></p></div>
			<?php 
		}
	}
	?>
	<div  id="main-wrapper" class="marks_list"><!-- MAIN WRAPPER DIV START-->
	    <div class="panel panel-white"><!-- PANEL WHITE DIV START-->
	        <div class="panel-body">  <!-- PANEL BODY DIV START-->
				<h2 class="nav-tab-wrapper">
					<a href="?page=hmgt_sms_setting" class="nav-tab  nav-tab-active">
					<?php echo '<span class="dashicons dashicons-awards"></span>'.esc_html__('SMS Setting', 'hospital_mgt'); ?></a>
				</h2>
				<div class="panel-body"> <!-- PANEL BODY DIV START-->
					<form action="" method="post" class="form-horizontal" id="sms_setting_form">  
						<div class="form-group margin_bottom_5px">
							<div class="mb-3 row">	
								<label class="col-sm-3 control-label form-label" for="enable"><?php esc_html_e('Select Message Service','hospital_mgt');?></label>
								<div class="col-sm-4 send_mail_checkbox padding_top_8px">
									 <div class="radio">
										<label>
											<input id="checkbox" type="radio" <?php echo checked($current_sms_service_active,'clickatell');?>  name="select_serveice" value="clickatell" > <?php esc_html_e('Clickatell','hospital_mgt');?> 
										</label> 
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
										<label>
											<input id="checkbox" type="radio"  <?php echo checked($current_sms_service_active,'msg91');?> name="select_serveice" value="msg91">  <?php _e('MSG91','hospital_mgt');?>
										</label>
										&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
									</div>
								</div>
							</div>
						</div>
						<div id="sms_setting_block">
						<?php if($current_sms_service_active == 'clickatell')
						{
							$clickatell=get_option( 'hmgt_clickatell_sms_service');
							
							?>
							
							<div class="form-group">
								<div class="mb-3 row">	
									<label class="col-sm-2 control-label form-label" for="username"><?php esc_html_e('Username','hospital_mgt');?><span class="require-field">*</span></label>
									<div class="col-sm-8">
										<input id="username" class="form-control validate[required]" type="text" value="<?php echo esc_attr($clickatell['username']);?>" name="username">
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="mb-3 row">	
									<label class="col-sm-2 control-label form-label" for="password"><?php esc_html_e('Password','hospital_mgt');?><span class="require-field">*</span></label>
									<div class="col-sm-8">
										<input id="password" class="form-control validate[required]" type="text" value="<?php echo esc_attr($clickatell['password']);?>" name="password">
									</div>
								</div>
							</div>
							<div class="form-group margin_bottom_5px">
								<div class="mb-3 row">	
									<label class="col-sm-2 control-label form-label" for="api_key"><?php esc_html_e('API Key','hospital_mgt');?><span class="require-field">*</span></label>
									<div class="col-sm-8 margin_bottom_5px">
										<input id="api_key" class="form-control validate[required]" type="text" value="<?php echo esc_attr($clickatell['api_key']);?>" name="api_key">
									</div>
								</div>
							</div>
						<?php 
						}
						if($current_sms_service_active == 'msg91')
						{
							$msg91=get_option( 'hmgt_msg91_sms_service');
							?>
							<div class="form-group">
								<div class="mb-3 row">	
									<label class="col-sm-2 control-label form-label" for="sms_auth_key"><?php esc_html_e('Authentication Key','hospital_mgt');?><span class="require-field">*</span></label>
									<div class="col-sm-8">
										<input id="sms_auth_key" class="form-control validate[required]" type="text" value="<?php echo $msg91['sms_auth_key'];?>" name="sms_auth_key">
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="mb-3 row">	
									<label class="col-sm-2 control-label form-label" for="msg91_senderID"><?php esc_html_e('SenderID','hospital_mgt');?><span class="require-field">*</span></label>
									<div class="col-sm-8">
										<input id="msg91_senderID" class="form-control validate[required] text-input" type="text" name="msg91_senderID" value="<?php echo $msg91['msg91_senderID'];?>">
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="mb-3 row">	
									<label class="col-sm-2 control-label form-label" for="wpnc_sms_route"><?php esc_html_e('Route','hospital_mgt');?><span class="require-field">*</span></label>
									<div class="col-sm-8">
										<input id="wpnc_sms_route" class="form-control validate[required] text-input" type="text" name="wpnc_sms_route" value="<?php echo $msg91['wpnc_sms_route'];?>">
									</div>
								</div>
							</div>
						<?php 
						}
							?>
						</div>
						<?php
						if($user_access_add == 1 OR $user_access_edit == 1 )
						{
							?>
						<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
							<input type="submit" value="<?php  esc_html_e('Save','hospital_mgt');?>" name="save_sms_setting" class="btn btn-success patient_btn1" />
						</div>
						<?php } ?>
					  </form>
				</div><!-- PANEL BODY DIV END-->
				<div class="clearfix"> </div>
			</div><!-- PANEL BODY DIV END-->
	    </div><!-- PANEL WHITE DIV END-->
	</div>  <!-- MAIN WRAPPER DIV END-->  
</div><!-- PAGE INNER DIV END-->