<script>
jQuery(document).ready(function()
{
  jQuery("span.timeago").timeago();
});
</script>
<script type="text/javascript">
jQuery(document).ready(function($) 
{
	"use strict";
	<?php
	if (is_rtl())
		{
		?>	
			$('#message-replay').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#message-replay').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>
});
</script>
<?php 
MJ_hmgt_browser_javascript_check();
//access right
$user_access=MJ_hmgt_get_userrole_wise_access_right_array();
//send box
if($_REQUEST['from']=='sendbox')
{
	$message = get_post($_REQUEST['id']);
	MJ_hmgt_change_read_status_reply($_REQUEST['id']);
	$author = $message->post_author;	
	$box='sendbox';
	if(isset($_REQUEST['delete']))
	{
		echo $_REQUEST['delete'];
		wp_delete_post($_REQUEST['id']);
		wp_safe_redirect(home_url()."?dashboard=user&page=message&tab=sentbox" );
		exit();
	}
}
//inbox
if($_REQUEST['from']=='inbox')
{
	if(isset($_REQUEST['id']))
	{
		$message = $obj_message->MJ_hmgt_get_message_by_id($_REQUEST['id']);
		
		MJ_hmgt_change_read_status($_REQUEST['id']);
		$message1 = get_post($message->post_id);
		$author = $message1->post_author;	
		MJ_hmgt_change_read_status_reply($message1->ID);
		$box='inbox';

		if(isset($_REQUEST['delete']))
		{			
			$obj_message->MJ_hmgt_delete_message($_REQUEST['id']);
			wp_safe_redirect(home_url()."?dashboard=user&page=message&tab=inbox" );
			exit();
		}
	}
}
//reply message
if(isset($_POST['replay_message']))
{
	
		$message_id=$_REQUEST['id'];
		$message_from=$_REQUEST['from'];
		$result=$obj_message->MJ_hmgt_send_replay_message($_POST);
		if($result)
			wp_safe_redirect(home_url()."?dashboard=user&page=message&tab=view_message&from=".$message_from."&id=$message_id&message=1" );
	
}
//DELETE REPLY
if(isset($_REQUEST['action'])&& $_REQUEST['action']=='delete-reply')
{
	$message_id=$_REQUEST['id'];
	$message_from=$_REQUEST['from'];
	if(isset($_REQUEST['reply_id']))
	{
		$result=$obj_message->MJ_hmgt_delete_reply($_REQUEST['reply_id']);
			if($result)
			{
				wp_redirect ( home_url().'?dashboard=user&page=message&tab=view_message&action=delete-reply&from='.$message_from.'&id='.$message_id.'&message=2');
			}
	}
}?>
<div class="mailbox-content"><!-- START MAIL BOX CONTENT DIV -->
 	<div class="message-header">
		<h3><span><?php esc_html_e('Subject','hospital_mgt')?> :</span>  <?php if($box=='sendbox'){ echo $message->post_title; } else{ echo $message->msg_subject; } ?></h3>
        <p class="message-date"><?php  if($box=='sendbox') { echo  date(MJ_hmgt_date_formate(),strtotime($message->post_date) );} else { echo  date(MJ_hmgt_date_formate(), strtotime($message->msg_date )) ; }?></p>
	</div>
	<div class="message-sender">  <!-- START MESSAGE SENDER DIV -->                              
    	<p><?php if($box=='sendbox'){
				$message_for=get_post_meta($_REQUEST['id'],'message_for',true);
				echo "".esc_html__("From","hospital_mgt")." : ".MJ_hmgt_get_display_name($message->post_author)."<span>&lt;".MJ_hmgt_get_emailid_byuser_id($message->post_author)."&gt;</span><br>";
				if($message_for == 'user'){
				echo "".esc_html__('To','hospital_mgt')." : ".MJ_hmgt_get_display_name(get_post_meta($_REQUEST['id'],'message_for_userid',true))."<span>&lt;".MJ_hmgt_get_emailid_byuser_id(get_post_meta($_REQUEST['id'],'message_for_userid',true))."&gt;</span><br>";}
				else{
				echo "".esc_html__('To','hospital_mgt')." : ".esc_html__('Group','hospital_mgt');}?>
			<?php } 
			else
			{ 
				echo "".esc_html__("From","hospital_mgt")." : ".MJ_hmgt_get_display_name($message->sender)."<span>&lt;".MJ_hmgt_get_emailid_byuser_id($message->sender)."&gt;</span><br> ".esc_html__('To','hospital_mgt')." : ".MJ_hmgt_get_display_name($message->receiver);  ?> <span>&lt;<?php echo MJ_hmgt_get_emailid_byuser_id($message->receiver);?>&gt;</span>
			<?php }?>
		</p>
    </div>
	
    <div class="message-content"><!-- START MESSAGE CONTENT DIV -->
		<p><?php $receiver_id=0;
		if($box=='sendbox'){ 
		echo wordwrap($message->post_content,120,"<br>\n",TRUE);
		$receiver_id=(get_post_meta($_REQUEST['id'],'message_for_userid',true));} else{ echo wordwrap($message->message_body,120,"<br>\n",TRUE);
		$receiver_id=$message->sender;}?></p>
		<?php
		if($user_access['delete']=='1')
		{
		?>
			<div class="message-options pull-right">
			<a class="btn btn-default" href="?dashboard=user&page=message&tab=view_message&id=<?php echo $_REQUEST['id'];?>	&from=<?php echo $box;?>&delete=1" onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this record?','hospital_mgt');?>');"><i class="fa fa-trash m-r-xs"></i><?php esc_html_e('Delete','hospital_mgt')?></a> 
		</div>
		<?php
		}
		?>
   </div>
   
    <?php if(isset($_REQUEST['from']) && $_REQUEST['from']=='inbox')
			{
				$allreply_data=$obj_message->MJ_hmgt_get_all_replies($message->post_id);
			}
			else
			{
				$allreply_data=$obj_message->MJ_hmgt_get_all_replies($_REQUEST['id']);
			}
		if(!empty($allreply_data))		
		{
			foreach($allreply_data as $reply)
			{
				$receiver_name=MJ_hmgt_get_receiver_name_array($reply->message_id,$reply->sender_id,$reply->created_date,$reply->message_comment);
				
				
				if($reply->sender_id == get_current_user_id() || $reply->receiver_id == get_current_user_id())
				{
				?>
				<div class="message-content">
					<p><?php echo esc_html($reply->message_comment);?><br>
					<h5>
					<?php
						esc_html_e('Reply By : ','hospital_mgt'); 
						echo MJ_hmgt_get_display_name($reply->sender_id); 
						esc_html_e(' || ','hospital_mgt'); 	
						esc_html_e('Reply To : ','hospital_mgt'); 
						echo esc_html($receiver_name); 
						esc_html_e(' || ','hospital_mgt'); 	
					if($reply->sender_id == get_current_user_id())
					{
						if($user_access['delete']=='1')
						{	
						?>	
							<span class="timeago" title="<?php echo MJ_hmgt_hmgtConvertTime($reply->created_date);?>"></span>
							<span class="comment-delete">
							<a href="?dashboard=user&page=message&tab=view_message&action=delete-reply&from=<?php echo $_REQUEST['from'];?>&id=<?php echo $_REQUEST['id'];?>&reply_id=<?php echo $reply->id;?>"><?php esc_html_e('Delete','hospital_mgt');?></a></span> 
						<?php
						}
					} 
					?>
					
					</h5> 
					</p>
				</div>
			<?php } 
			}
		}
		?>
		<script type="text/javascript">
	$(document).ready(function() 
	{			
		 $('#selected_users').multiselect(
			{
				nonSelectedText :'<?php esc_html_e('Select user to reply','hospital_mgt');?>',
				includeSelectAllOption: true,
			    selectAllText : '<?php esc_html_e('Select all','hospital_mgt'); ?>',
				templates: {
			            button: '<button class="multiselect btn btn-default dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="multiselect-selected-text"></span><b class="caret"></b></button>',
			        },
					buttonContainer: '<div class="dropdown" />'
			});		
		 $("body").on("click","#check_reply_user",function()
		 {
			var checked = $(".dropdown-menu input:checked").length;

			if(!checked)
			{
				alert("<?php esc_html_e('Please select atleast one users to reply','hospital_mgt');?>");
				return false;
			}		
		});
		$("body").on("click","#replay_message_btn",function()
		 {
			$(".replay_message_div").show();	
			$(".replay_message_btn").hide();	
		});     
	});
	</script>
	
	<form name="message-replay" method="post" id="message-replay"><!--MESSAGE REPLAY FORM--->
   <input type="hidden" name="message_id" value="<?php if($_REQUEST['from']=='sendbox') echo esc_attr($_REQUEST['id']); else echo esc_attr($message->post_id);?>">
   <input type="hidden" name="user_id" value="<?php echo get_current_user_id();?>">		
	<!-- <input type="hidden" name="receiver_id" value="<?php echo esc_attr($receiver_id);?>">    -->
   <input type="hidden" name="from" value="<?php echo esc_attr($_REQUEST['from']);?>">
  	<?php
	global $wpdb;
	$tbl_name = $wpdb->prefix .'hmgt_message';
	$current_user_id=get_current_user_id();
	if((string)$current_user_id == $author)
	{		
		if($_REQUEST['from']=='sendbox')
		{
			$msg_id=$_REQUEST['id']; 
			$msg_id_integer=(int)$msg_id;
			$reply_to_users =$wpdb->get_results("SELECT *  FROM $tbl_name where post_id = $msg_id_integer");			
		}
		else
		{
			$msg_id=$message->post_id;			
			$msg_id_integer=(int)$msg_id;
			$reply_to_users =$wpdb->get_results("SELECT *  FROM $tbl_name where post_id = $msg_id_integer");			
		}		
	}
	else
	{
		$reply_to_users=array();
		$reply_to_users[]=(object)array('receiver'=>$author);
	}
	?>
	<div class="message-options pull-right">
		<button type="button" name="replay_message_btn" class="btn btn-default replay_message_btn" id="replay_message_btn"><i class="fa fa-reply m-r-xs"></i><?php esc_html_e('Reply','hospital_mgt')?></button>
 	</div>

    <div class="message-content float_left_width_100 replay_message_div display_none">
    	<div class="col-sm-12">
			<div class="form-group" >
				<label class="col-sm-3 control-label" ><?php esc_html_e('Select user to reply','hospital_mgt');?><span class="require-field">*</span></label>
				<div class="col-sm-9 margin_bottom_20">			
					<select name="receiver_id[]" class="form-control" id="selected_users" multiple="true">
						<?php						
						foreach($reply_to_users as $reply_to_user)
						{  	
							$user_data=get_userdata($reply_to_user->receiver);
							if(!empty($user_data))
							{								
								if($reply_to_user->receiver != get_current_user_id())
								{
									?>
									<option  value="<?php echo $reply_to_user->receiver;?>" ><?php echo MJ_hmgt_get_display_name($reply_to_user->receiver); ?></option>
									<?php
								}
							}							
						} 
						?>
					</select>
				</div>
			</div>
		</div>
		<div class="col-sm-12">
			<div class="form-group">
				<label class="col-sm-3 control-label" for="photo"><?php esc_html_e('Message Comment','hospital_mgt');?><span class="require-field">*</span></label>
				<div class="col-sm-8 margin_bottom_10">
					<textarea name="replay_message_body" maxlength="150" id="replay_message_body" class="validate[required] form-control text-input"></textarea>
				</div>
			</div>
		</div>	  
	   <div class="message-options pull-right reply-message-btn">
			<button type="submit" name="replay_message" class="btn btn-success" id="check_reply_user"><?php esc_html_e('Send','hospital_mgt')?></button>
		
	 	</div>
    </div>
	</form><!--END MESSAGE REPLAY FORM--->
 </div><!-- END MAIL BOX CONTENT DIV -->