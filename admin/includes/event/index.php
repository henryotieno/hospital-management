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
	$user_access=MJ_hmgt_get_access_right_for_management_user_page('event');
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
			if (isset ( $_REQUEST ['page'] ) && 'event' == $user_access['page_link'] && ($_REQUEST['action']=='edit'))
			{
				if($user_access['edit']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}			
			}
			if (isset ( $_REQUEST ['page'] ) && 'event' == $user_access['page_link'] && ($_REQUEST['action']=='delete'))
			{
				if($user_access['delete']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}	
			}
			if (isset ( $_REQUEST ['page'] ) && 'event' == $user_access['page_link'] && ($_REQUEST['action']=='insert')) 
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
$active_tab = isset($_GET['tab'])?$_GET['tab']:'noticelist';
?>
<!-- View Popup Code START-->	
<div class="popup-bg">
    <div class="overlay-content">
    	<div class="notice_content"></div>    
    </div> 
</div>	
<!-- View Popup Code END-->	
<div class="page-inner min_height_1631"><!-- PAGE INNER DIV START-->	
    <div class="page-title"><!-- PAGE TITLE DIV START-->	
		<h3><img src="<?php echo esc_url(get_option( 'hmgt_hospital_logo', 'hospital_mgt' )) ?>" class="img-circle head_logo" width="40" height="40" /><?php echo esc_html(get_option('hmgt_hospital_name','hospital_mgt'));?></h3>
	</div><!-- PAGE TITLE DIV START-->	
	<?php 
	//-------------------- SAVE NOTICE ------------------------//
	if(isset($_POST['save_notice']))
	{
		$nonce = $_POST['_wpnonce'];
		if (wp_verify_nonce( $nonce, 'save_notice_nonce' ) )
		{
			if($_REQUEST['action']=='edit')
			{
				$args = array(
				  'ID'           => $_REQUEST['notice_id'],
				  'post_type' => $_REQUEST['notice_type'],
				  'post_title'   => MJ_hmgt_strip_tags_and_stripslashes($_REQUEST['notice_title']),
				  'post_content' =>  MJ_hmgt_strip_tags_and_stripslashes($_REQUEST['notice_content']),
				);
				$start_date = MJ_hmgt_get_format_for_db($_POST['start_date']);
				$end_date =  MJ_hmgt_get_format_for_db($_POST['end_date']);
				$result1=wp_update_post( $args );
				$result2=update_post_meta($_REQUEST['notice_id'], 'notice_for', implode(",",$_POST['notice_for']));
				$result3=update_post_meta($_REQUEST['notice_id'], 'start_date',$start_date);
				$result4=update_post_meta($_REQUEST['notice_id'], 'end_date',$end_date);
				$role=$_POST['notice_for'];
				$hmgt_sms_service_enable=0;
				if(isset($_POST['hmgt_sms_service_enable']))
				$hmgt_sms_service_enable = $_POST['hmgt_sms_service_enable'];
				if($hmgt_sms_service_enable)
				{
						$userdata = array();
						foreach($role as $role_data)
						{
							$current_role_users=get_users(array('role'=>$role_data));
							$userdata = array_merge($current_role_users, $userdata);
						}
						if(!empty($userdata))
						{
							$mail_id = array();
							$i	 = 	0;
							foreach($userdata as $user)
							{
								$mail_id[]=$user->ID;
								$i++;
							}
						}
						$user_number=array();
						foreach($mail_id as $user)
						{
							if(!empty(get_user_meta($user, 'phonecode',true))){ $phone_code=get_user_meta($user, 'phonecode',true); }else{ $phone_code='+'.MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }
							
							$user_number[] = $phone_code.get_user_meta($user, 'mobile',true);
						}
						//----------------- SEND MESSAGE ----------------------//
						if(is_plugin_active('sms-pack/sms-pack.php'))
						{
							$message_content = MJ_hmgt_strip_tags_and_stripslashes($_POST['sms_template']);
							$current_sms_service 	= get_option( 'smgt_sms_service');
							$args = array();
							$args['mobile']=$user_number;
							$args['message_from']="Event";
							$args['message']=$message_content;					
							if($current_sms_service=='telerivet' || $current_sms_service ="MSG91" || $current_sms_service=='bulksmsgateway.in' || $current_sms_service=='textlocal.in' || $current_sms_service=='bulksmsnigeria' || $current_sms_service=='africastalking' || $current_sms_service == 'clickatell')
							{				
								$send = send_sms($args);							
							}
						}
							
						foreach($mail_id as $user_id)
						{
							if(!empty(get_user_meta($user_id, 'phonecode',true))){ $phone_code=get_user_meta($user_id, 'phonecode',true); }else{ $phone_code='+'.MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }
							
							$reciever_number = $phone_code.get_user_meta($user_id, 'mobile',true);
							
							$message_content = MJ_hmgt_strip_tags_and_stripslashes($_POST['sms_template']);
							
							$current_sms_service = get_option( 'hmgt_sms_service');
							if($current_sms_service == 'clickatell')
							{
								$clickatell=get_option('hmgt_clickatell_sms_service');
								$username = urlencode($clickatell['username']);
								$password = urlencode($clickatell['password']);
								$api_id = urlencode($clickatell['api_key']);
								$to_mob_number = $reciever_number;
								$message = urlencode($message_content);
								$send=file_get_contents("https://api.clickatell.com/http/sendmsg". "?user=$username&password=$password&api_id=$api_id&to=$to_mob_number&text=$message");
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
								$message = $message_content; // Message Text
								//twilio object								
								$client = new Services_Twilio($account_sid, $auth_token);
								$message_sent = $client->account->messages->sendMessage(
								$from_number, // From a valid Twilio number
								$receiver, // Text this number
								$message
								);
							}
							if($current_sms_service == 'msg91')
							{
								//MSG91
								$mobile_number=$reciever_number;
								$country_code="+".MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' ));
								$message = $message_content; // Message Text
								MJ_hmgt_msg91_send_mail_function($mobile_number,$message,$country_code);
							}			
							
						}
				}			
				if($result1 || $result2 || $result3 || $result4)
				{
					wp_redirect ( admin_url() . 'admin.php?page=hmgt_event&tab=noticelist&message=2');
				}
			}
			else
			{			
				$post_id = wp_insert_post( array(
						'post_status' => 'publish',
						'post_type' => $_REQUEST['notice_type'],
						'post_title' => MJ_hmgt_strip_tags_and_stripslashes($_REQUEST['notice_title']),
						'post_content' => MJ_hmgt_strip_tags_and_stripslashes($_REQUEST['notice_content'])
				) );
					
					$start_date = MJ_hmgt_get_format_for_db($_POST['start_date']);
					$end_date =  MJ_hmgt_get_format_for_db($_POST['end_date']);
					$result=add_post_meta($post_id, 'notice_for',implode(",",$_POST['notice_for']));
					$result=add_post_meta($post_id, 'start_date',$start_date);
					$result=add_post_meta($post_id, 'end_date',$end_date);
					$result=add_post_meta($post_id, 'created_by',get_current_user_id());
					$role=$_POST['notice_for'];
					$hmgt_sms_service_enable=0;
					if(isset($_POST['hmgt_sms_service_enable']))
						$hmgt_sms_service_enable = $_POST['hmgt_sms_service_enable'];
					if($hmgt_sms_service_enable)
					{				
						$userdata = array();
						foreach($role as $role_data)
						{
							$current_role_users=get_users(array('role'=>$role_data));
							$userdata = array_merge($current_role_users, $userdata);
						}
						if(!empty($userdata))
						{
							$mail_id = array();
							$i	 = 	0;
							foreach($userdata as $user)
							{
								$mail_id[]=$user->ID;
								$i++;
							}
						}
						$user_number=array();
						foreach($mail_id as $user)
						{
							if(!empty(get_user_meta($user, 'phonecode',true))){ $phone_code=get_user_meta($user, 'phonecode',true); }else{ $phone_code='+'.MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }
							
							$user_number[] = $phone_code.get_user_meta($user, 'mobile',true);
						}
						//---------------------- SEND MESSAGE ------------------//
						if(is_plugin_active('sms-pack/sms-pack.php'))
						{
							$message_content = MJ_hmgt_strip_tags_and_stripslashes($_POST['sms_template']);
							$current_sms_service 	= get_option( 'smgt_sms_service');
							$args = array();
							$args['mobile']=$user_number;
							$args['message_from']="Event";
							$args['message']=$message_content;					
							if($current_sms_service=='telerivet' || $current_sms_service ="MSG91" || $current_sms_service=='bulksmsgateway.in' || $current_sms_service=='textlocal.in' || $current_sms_service=='bulksmsnigeria' || $current_sms_service=='africastalking' || $current_sms_service == 'clickatell')
							{				
								$send = send_sms($args);							
							}
						}
						foreach($mail_id as $user_id)
						{
							if(!empty(get_user_meta($user_id, 'phonecode',true))){ $phone_code=get_user_meta($user_id, 'phonecode',true); }else{ $phone_code='+'.MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }
							
							$reciever_number = $phone_code.get_user_meta($user_id, 'mobile',true);
							
							$message_content = MJ_hmgt_strip_tags_and_stripslashes($_POST['sms_template']);
							$current_sms_service = get_option( 'hmgt_sms_service');
							if($current_sms_service == 'clickatell')
							{
								$clickatell=get_option('hmgt_clickatell_sms_service');
								$username = urlencode($clickatell['username']);
								$password = urlencode($clickatell['password']);
								$api_id = urlencode($clickatell['api_key']);
								$to_mob_number = $reciever_number;
								$message = urlencode($message_content);
								$send=file_get_contents("https://api.clickatell.com/http/sendmsg". "?user=$username&password=$password&api_id=$api_id&to=$to_mob_number&text=$message");
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
								$message = $message_content; // Message Text							
								//twilio object
								$client = new Services_Twilio($account_sid, $auth_token);
								$message_sent = $client->account->messages->sendMessage(
										$from_number, // From a valid Twilio number
										$receiver, // Text this number
										$message
								);
							}
							if($current_sms_service == 'msg91')
							{
								//MSG91
								$mobile_number=$reciever_number;
								$country_code="+".MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' ));
								$message = $message_content; // Message Text
								MJ_hmgt_msg91_send_mail_function($mobile_number,$message,$country_code);
							}		
						}
					}
					if(isset($result))
					{
						wp_redirect ( admin_url() . 'admin.php?page=hmgt_event&tab=noticelist&message=1');
					}
			}
		}
	}
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete')
	{
		$result=wp_delete_post($_REQUEST['notice_id']);
		if($result)
		{
			wp_redirect ( admin_url() . 'admin.php?page=hmgt_event&tab=noticelist&message=3');
		}
	}
	if(isset($_REQUEST['delete_selected']))
	{		
		if(!empty($_REQUEST['selected_id']))
		{
			
			foreach($_REQUEST['selected_id'] as $id)
			{
				$result=wp_delete_post($id);
			}
			if($result)
			{
				wp_redirect ( admin_url() . 'admin.php?page=hmgt_event&tab=noticelist&message=3');
			}
		}
		else
		{
			echo '<script language="javascript">';
			echo 'alert("'.esc_html__('Please select at least one record.','hospital_mgt').'")';
			echo '</script>';
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
		{?><div id="message" class="updated below-h2 notice is-dismissible"><p><?php
				esc_html_e("Record updated successfully",'hospital_mgt');
				?></p>
				</div>
			<?php
		}
		elseif($message == 3) 
		{?>
		<div id="message" class="updated below-h2 notice is-dismissible"><p>
		<?php 
			esc_html_e('Record deleted successfully','hospital_mgt');
		?></P></div><?php	
		}
	}
	?>
	<div  id="main-wrapper" class="notice_page"><!-- MAIN WRAPPER DIV START-->	
		<div class="panel panel-white"><!-- PANEL WHITE DIV START-->	
			<div class="panel-body"><!-- PANEL BODY DIV START-->	    
				<h2 class="nav-tab-wrapper">
					<a href="?page=hmgt_event&tab=noticelist" class="nav-tab <?php echo $active_tab == 'noticelist' ? 'nav-tab-active' : ''; ?>">
					<?php echo '<span class="dashicons dashicons-menu"></span>'.esc_html__('Event List', 'hospital_mgt'); ?></a>
					 <?php if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
					{?>
				   <a href="?page=hmgt_event&tab=addnotice&action=edit&notice_id=<?php echo $_REQUEST['notice_id'];?>" class="nav-tab <?php echo $active_tab == 'addnotice' ? 'nav-tab-active' : ''; ?>">
					<?php esc_html_e('Edit Event', 'hospital_mgt'); ?></a>  
					<?php 
					}
					else
					{
						if($user_access_add == 1)
							{?>
					<a href="?page=hmgt_event&tab=addnotice" class="nav-tab <?php echo $active_tab == 'addnotice' ? 'nav-tab-active' : ''; ?>">
					<?php echo '<span class="dashicons dashicons-plus-alt"></span>'.esc_html__('Add Event', 'hospital_mgt'); ?></a>  
					<?php } } ?>
				</h2>
				<?php
				if($active_tab == 'noticelist')
				{	
					?>	
					<script type="text/javascript">
					jQuery(document).ready(function() {
						"use strict";
						jQuery('#event_list').DataTable({
							"responsive": true,	
							"order": [[ 1, "asc" ]],					
							 "aoColumns":[
										  {"bSortable": false},
										  {"bSortable": true},
										  {"bSortable": true},
										  {"bSortable": true},
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
					<div class="panel-body"><!--PANEL BODY  DIV START-->	
						<div class="table-responsive"><!-- TABLE RESPONSIVE DIV START-->	
							<table id="event_list" class="display " cellspacing="0" width="100%">
								<thead>
									<tr>  
										<th><input type="checkbox" class="select_all"></th>              
										<th width="190px"><?php esc_html_e('Title','hospital_mgt');?></th>
										<th><?php esc_html_e('Comment','hospital_mgt');?></th>
										<th><?php esc_html_e('Start Date','hospital_mgt');?></th>
										<th><?php esc_html_e('End Date','hospital_mgt');?></th>
										<th><?php esc_html_e('Type','hospital_mgt');?></th>
										<th><?php esc_html_e('For','hospital_mgt');?></th>
										<th width="185px"><?php esc_html_e('Action','hospital_mgt');?></th>               
									</tr>
								</thead>	
								<tfoot>
									<tr>
										<th></th>
								   		<th width="190px"><?php esc_html_e('Title','hospital_mgt');?></th>
										<th><?php esc_html_e('Comment','hospital_mgt');?></th>
										<th><?php esc_html_e('Start Date','hospital_mgt');?></th>
										<th><?php esc_html_e('End Date','hospital_mgt');?></th>
										<th><?php esc_html_e('Type','hospital_mgt');?></th>
										<th><?php esc_html_e('For','hospital_mgt');?></th>
										<th width="185px"><?php esc_html_e('Action','hospital_mgt');?></th>           
									</tr>
								</tfoot>
								<tbody>
								<?php 
								  $args['post_type'] = array('hmgt_event','hmgt_notice');
								  $args['posts_per_page'] = -1;
								  $args['post_status'] = 'public';
								  $q = new WP_Query();
								  $retrieve_class = $q->query( $args );
								  $format =get_option('date_format') ;
								  
								  foreach ($retrieve_class as $retrieved_data)
								  { 
								 ?>
									<tr>
										<td class="title"><input type="checkbox" name="selected_id[]" class="sub_chk" value="<?php echo esc_attr($retrieved_data->ID); ?>"></td>
										<td><?php echo $retrieved_data->post_title;?></td>
										<td><?php 
											$strlength= strlen($retrieved_data->post_content);
											if($strlength > 60)
												echo substr($retrieved_data->post_content, 0,60).'...';
											else
												echo esc_html($retrieved_data->post_content);
										
										?></td>
										<td><?php echo date(MJ_hmgt_date_formate(),strtotime(get_post_meta($retrieved_data->ID,'start_date',true)));?></td> 
										<td><?php echo date(MJ_hmgt_date_formate(),strtotime(get_post_meta($retrieved_data->ID,'end_date',true)));?></td> 
										<td><?php if($retrieved_data->post_type=='hmgt_notice'){ esc_html_e('Notice','hospital_mgt');} if($retrieved_data->post_type=='hmgt_event'){ esc_html_e('Event','hospital_mgt'); }?></td>				
										<td>
										<?php 
											$notice_for_array=explode(",",get_post_meta( $retrieved_data->ID, 'notice_for',true));
											
											echo MJ_hmgt_get_role_name_in_event($notice_for_array);
										?>
										</td>              
									   <td display="inline">
										<a href="javascript:void(0);" class="btn btn-default view-notice" id="<?php echo esc_attr($retrieved_data->ID);?>"> <i class="fa fa-eye"></i> <?php esc_html_e('View','hospital_mgt');?></a>
										<?php if($user_access_edit == 1)
												{?>
									   <a href="?page=hmgt_event&tab=addnotice&action=edit&notice_id=<?php echo esc_attr($retrieved_data->ID);?>"class="btn btn-info"><?php esc_html_e('Edit','hospital_mgt');?></a>
									   <?php 
												} 
												?>
												<?php if($user_access_delete == 1)
												{?>	
									   <a href="?page=hmgt_event&tab=noticelist&action=delete&notice_id=<?php echo esc_attr($retrieved_data->ID);?>" class="btn btn-danger" 
									   onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this record?','hospital_mgt');?>');"> <?php esc_html_e('Delete','hospital_mgt');?></a>
									   <?php 
												 } ?>
									   
										</td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
							<?php if($user_access_delete == 1)
												{?>	
							<div class="print-button pull-left">
								<input  type="submit" value="<?php esc_html_e('Delete Selected','hospital_mgt');?>" name="delete_selected" class="btn btn-danger delete_selected "/>
							</div>
							 <?php 
												 } ?>
						</div><!-- TABLE RESPONSIVE DIV END-->	
					</div><!-- PANEL BODY DIV END-->	
					 </form>
				<?php 
				}
				if($active_tab == 'addnotice')
				{
					require_once HMS_PLUGIN_DIR. '/admin/includes/event/add-event.php';
				}
				?>
	 	    </div><!-- PANEL BODY DIV END-->
	    </div><!-- PANEL WHITE DIV END-->
    </div><!-- MAIN WRAPPER DIV END-->
</div><!-- PAGE INNER DIV END-->