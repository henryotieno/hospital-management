<?php
MJ_hmgt_browser_javascript_check();
$obj_message = new MJ_hmgt_message();
//access right
$user_access=MJ_hmgt_get_userrole_wise_access_right_array();
if (isset ( $_REQUEST ['page'] ))
{	
	if($user_access['view']=='0')
	{	
		MJ_hmgt_access_right_page_not_access_message();
		die;
	}
	if(!empty($_REQUEST['action']))
	{
		if (isset ( $_REQUEST ['page'] ) && $_REQUEST ['page'] == $user_access['page_link'] && ($_REQUEST['action']=='edit'))
		{
			if($user_access['edit']=='0')
			{	
				MJ_hmgt_access_right_page_not_access_message();
				die;
			}			
		}
		if (isset ( $_REQUEST ['page'] ) && $_REQUEST ['page'] == $user_access['page_link'] && ($_REQUEST['action']=='delete'))
		{
			if($user_access['delete']=='0')
			{	
				MJ_hmgt_access_right_page_not_access_message();
				die;
			}	
		}
		if (isset ( $_REQUEST ['page'] ) && $_REQUEST ['page'] == $user_access['page_link'] && ($_REQUEST['action']=='insert'))
		{
			if($user_access['add']=='0')
			{	
				MJ_hmgt_access_right_page_not_access_message();
				die;
			}	
		} 
	}
}
//SAVE Message DATA
if(isset($_POST['save_message']))
{
	$nonce = $_POST['_wpnonce'];
	if (wp_verify_nonce( $nonce, 'save_message_nonce' ) )
	{
		$result = $obj_message->MJ_hmgt_add_message($_POST);	
		if(isset($result))
		{
			wp_redirect ( home_url() . '?dashboard=user&page=message&tab=inbox&message=1');
		}
	}
}
	if(isset($_REQUEST['message']))
	{
		$message =$_REQUEST['message'];
		if($message == 1)
		{?>
			<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
			</button>
				<p>
				<?php 
					esc_html_e('Message send successfully','hospital_mgt');
				?></p>
			</div>
				<?php 
		}
		elseif($message == 2)
		{?><div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
		</button><p><?php
			esc_html_e("Message deleted successfully",'hospital_mgt');
			?></p>
			</div>
			<?php
		}
	}		
	
$active_tab = isset($_GET['tab'])?$_GET['tab']:'inbox';
?>

<div class="row mailbox-header"><!-- START MAIL BOX HEADER DIV-->
								
	<div class="col-md-2 responsive_compose_div">
		<?php
		if($user_access['add']=='1')
		{
		?>	
			<a class="btn btn-success btn-block margin_left_15" href="?dashboard=user&page=message&tab=compose"><?php esc_html_e('Compose','hospital_mgt');?></a>
		<?php
		}
		?>	
	</div>
	<div class="col-md-6 responsive_compose_div_6 ">
		<h2>
		<?php
		if(!isset($_REQUEST['tab']) || ($_REQUEST['tab'] == 'inbox'))
		echo esc_html( esc_html__( 'Inbox', 'hospital_mgt' ) );
		else if(isset($_REQUEST['page']) && $_REQUEST['tab'] == 'sentbox')
		echo esc_html( esc_html__( 'Sent Item', 'hospital_mgt' ) );
		else if(isset($_REQUEST['page']) && $_REQUEST['tab'] == 'compose')
			echo esc_html( esc_html__( 'Compose', 'hospital_mgt' ) );
		?>
		</h2>
	</div>
   
</div><!-- END MAIL BOX HEADER DIV-->
 <div class="col-md-2 unstyled_list">
	<ul class="list-unstyled mailbox-nav">
		<li <?php if(!isset($_REQUEST['tab']) || ($_REQUEST['tab'] == 'inbox')){?>class="active"<?php }?>>
		<a href="?dashboard=user&page=message&tab=inbox"><i class="fa fa-inbox"></i> <?php esc_html_e('Inbox','hospital_mgt');?><span class="badge badge-success pull-right">
		<?php 
		
		//echo count($obj_message->MJ_hmgt_count_inbox_item(get_current_user_id()));
		echo MJ_hmgt_count_unread_message_admin(get_current_user_id());
		?></span></a></li>
		<li <?php if(isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'sentbox'){?>class="active"<?php }?>><a href="?dashboard=user&page=message&tab=sentbox"><i class="fas fa-sign-out"></i><?php esc_html_e('Sent','hospital_mgt');?></a></li>                                
	</ul>
</div>
 <div class="col-md-10">
 <?php  
 	if(isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'sentbox')
 		require_once HMS_PLUGIN_DIR. '/template/message/sendbox.php';
 	if(!isset($_REQUEST['tab']) || ($_REQUEST['tab'] == 'inbox'))
 		require_once HMS_PLUGIN_DIR. '/template/message/inbox.php';
 	if(isset($_REQUEST['tab']) && ($_REQUEST['tab'] == 'compose'))
 		require_once HMS_PLUGIN_DIR. '/template//message/composemail.php';
 	if(isset($_REQUEST['tab']) && ($_REQUEST['tab'] == 'view_message'))
 		require_once HMS_PLUGIN_DIR. '/template/message/view_message.php';
 	?>
 </div>