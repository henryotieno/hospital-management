<?php
$obj_class=new MJ_Gmgtclassschedule;
$apikey = get_option('gmgt_mailchimp_api');
$api = new MJ_GYM_MCAPI();
$result=$api->MCAPI($apikey);
$active_tab = isset($_GET['tab'])?$_GET['tab']:'mailchimp_setting';
?>

<div class="page-inner min_height_1631"><!--PAGE INNER DIV START-->
	<div class="page-title"><!--PAGE TITLE DIV START-->
		<h3><img src="<?php echo esc_url(get_option( 'hmgt_hospital_logo', 'hospital_mgt' )) ?>" class="img-circle head_logo" width="40" height="40" /><?php echo esc_html(get_option('hmgt_hospital_name','hospital_mgt'));?></h3>
	</div><!--PAGE TITLE DIV END-->
	<?php 
	if(isset($_REQUEST['save_setting']))
	{
		update_option( 'gmgt_mailchimp_api', $_REQUEST['gmgt_mailchimp_api']);
		$message = esc_html__("Save Setting Successfully","hospital_mgt");
	}
	if(isset($_REQUEST['sychroniz_email']))
	{
		$retval = $api->lists();
		$subcsriber_emil = array();
		if(isset($_REQUEST['syncmail']))
		{
			$syncmail = $_REQUEST['syncmail'];
			foreach ($syncmail as $id)
			{
				$args = array('meta_key' => 'class_id','meta_value' => $id);	
				$usersdata = get_users($args );
				if(!empty($usersdata))
				{
					foreach ($usersdata as $retrieved_data){
						$firstname=get_user_meta($retrieved_data->ID,'first_name',true);
						$lastname=get_user_meta($retrieved_data->ID,'last_name',true);
						if(trim($retrieved_data->user_email) !='')
							$subcsriber_emil[] = array('fname'=>$firstname,'lname'=>$lastname,'email'=>$retrieved_data->user_email);
						}
				}
			}
		}		
		if(!empty($subcsriber_emil))
		{
			foreach ($subcsriber_emil as $value)
			{			
				$merge_vars = array('FNAME'=>$value['fname'], 'LNAME'=>$value['lname']);
				$subscribe = $api->listSubscribe($_REQUEST['list_id'], $value['email'], $merge_vars );
			}
		}
		$message = esc_html__("Synchronize Mail Successfully","hospital_mgt");
	}
	if(isset($_REQUEST['send_campign']))
	{
		$retval = $api->campaigns();
		$retval1 = $api->lists();
		$emails = array();
		$listId = $_REQUEST['list_id'];
		$campaignId =$_REQUEST['camp_id'];
		$listmember = $api->listMembers($listId, 'subscribed', null, 0, 5000 );
		foreach($listmember['data'] as $member)
		{			
			$emails[] = $member['email'];
		}
		
		$retval2 = $api->campaignSendTest($campaignId, $emails);
		
		if ($api->errorCode)
		{			
			$message = esc_html__("Campaign Tests Not Sent!","hospital_mgt");
		}
		else
		{
			$message = esc_html__("Campaign Tests Sent!","hospital_mgt");
		}
	}
	if(isset($message))
	{
	?>
		<div id="message" class="updated below-h2 notice is-dismissible"><p>
		<?php 
			echo $message;
		?></p></div>
		<?php 
	}
	?>
	<div id="main-wrapper"><!--MAIN WRAPPER DIV START-->
		<div class="row"><!-- ROW DIV START-->
			<div class="col-md-12">
				<div class="panel panel-white"><!--PANEL WHITE DIV START-->
					<div class="panel-body"><!--PANEL BODY DIV START-->
						<h2 class="nav-tab-wrapper">
							<a href="?page=gmgt_newsletter&tab=mailchimp_setting" class="nav-tab <?php echo $active_tab == 'mailchimp_setting' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-menu"></span> '.esc_html__('Setting', 'hospital_mgt'); ?></a>
							
						  
							<a href="?page=gmgt_newsletter&tab=sync" class="nav-tab <?php echo $active_tab == 'sync' ? 'nav-tab-active' : ''; ?>">
							<?php esc_html_e('Sync Mail', 'hospital_mgt'); ?></a>  
							
							<a href="?page=gmgt_newsletter&tab=campaign" class="nav-tab <?php echo $active_tab == 'campaign' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-plus-alt"></span> '.esc_html__('Campaign', 'hospital_mgt'); ?></a>
						</h2>
						<?php 						
						if($active_tab == 'mailchimp_setting')
						{ ?>
							<div class="panel-body"><!--PANEL BODY DIV START-->
								<form name="newsletterform" method="post" id="newsletterform" class="form-horizontal">
									<div class="form-group">
											<label class="col-sm-2 control-label" for="wpcrm_mailchimp_api"><?php esc_html_e('MailChimp API key','hospital_mgt');?></label>
											<div class="col-sm-8">
												<input id="gmgt_mailchimp_api" class="form-control" type="text" value="<?php echo get_option( 'gmgt_mailchimp_api' );?>"  name="gmgt_mailchimp_api">
											</div>
										</div>
										
										<div class="col-sm-offset-2 col-sm-8">
											<input type="submit" value="<?php esc_html_e('Save', 'hospital_mgt' ); ?>" name="save_setting" class="btn btn-success"/>
									</div>									
								</form>
							 </div><!--PANEL BODY DIV END-->
						 <?php 
						}
						if($active_tab == 'sync')
						{
							require_once GMS_PLUGIN_DIR. '/admin/news-letter/sync.php';
						}
						if($active_tab == 'campaign')
						{
							require_once GMS_PLUGIN_DIR. '/admin/news-letter/campaign.php';
						}
						?>
					</div><!--PANEL WHITE DIV END-->
				</div><!--PANEL BODY DIV END-->
			</div>
        </div><!--ROW DIV END-->
    </div><!--MAIN WRAPPER DIV END-->
</div><!--PAGE INNER DIV END-->
<?php //} ?>