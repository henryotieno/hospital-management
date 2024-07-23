<?php
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
class MJ_hmgt_message
{	
	//Add message data
	public function MJ_hmgt_add_message($data)
	{
		global $wpdb;
		$table_message=$wpdb->prefix."hmgt_message";
		//-------usersmeta table data--------------
		$created_date = date("Y-m-d H:i:s");
		$subject = MJ_hmgt_strip_tags_and_stripslashes($data['subject']);
		$message_body = MJ_hmgt_strip_tags_and_stripslashes($data['message_body']);
		$role=$data['receiver'];
		$userdata=get_users(array('role'=>$role));
		if($role == 'doctor' || $role == 'patient' || $role == 'nurse' || $role == 'receptionist' || $role == 'pharmacist' || $role == 'laboratorist' || $role == 'accountant' || $role == 'administrator')
		{ 
		if(!empty($userdata))
		{
			$mail_id = array();
			
			foreach($userdata as $user)
			{
				$mail_id[]=$user->ID;
			}
			$post_id = wp_insert_post( array(
					'post_status' => 'publish',
					'post_type' => 'hmgt_message',
					'post_title' => $subject,
					'post_content' =>$message_body
		
			) );
			foreach($mail_id as $user_id)
			{
				$reciever_id = $user_id;
				$message_data=array('sender'=>get_current_user_id(),
						'receiver'=>$user_id,
						'msg_subject'=>$subject,
						'message_body'=>$message_body,
						'msg_date'=>$created_date,
						'msg_status' =>0,
						'post_id' =>$post_id
				);
				$result=$wpdb->insert( $table_message, $message_data );
				//start mail subject
				$user = get_userdata($user_id);
				$role=$user->roles;
				$reciverrole=$role[0];
				if($reciverrole == 'administrator' )
				{
					$page_link=admin_url().'admin.php?page=hmgt_message&tab=inbox';
				}
				else
				{
					$page_link=home_url().'/?dashboard=user&page=message&tab=inbox';
				} 
				$reciever_data=get_userdata($user_id);
				$reciever_name=$reciever_data->display_name;;
				$reciever_email=$reciever_data->user_email;
				$sender_id=get_current_user_id();
				$senderdata=get_userdata($sender_id);
				$sendername_name=$senderdata->display_name;;
		        $hospital_name = get_option('hmgt_hospital_name');
			    $msg_subject =get_option('MJ_hmgt_message_received_subject');
			    $sub_arr['{{Sender Name}}']=$sendername_name;
			    $sub_arr['{{Hospital Name}}']=$hospital_name;
			    $msg_subject_replace = MJ_hmgt_subject_string_replacemnet($sub_arr,$msg_subject);
			
				$arr['{{Receiver Name}}']=$reciever_name;			
				$arr['{{Sender Name}}']=$sendername_name;
				$arr['{{Message Content}}']=$message_body;
				$arr['{{Hospital Name}}']=$hospital_name;
				$arr['{{Message_Link}}']=$page_link;
		
				$message = get_option('MJ_hmgt_message_received_template');
				$message_replacement = MJ_hmgt_string_replacemnet($arr,$message);	
				$to[]=$reciever_email;
				MJ_hmgt_send_mail($to,$msg_subject_replace,$message_replacement);
				//end send mail			
				$hmgt_sms_service_enable=0;
				if(isset($_POST['hmgt_sms_service_enable']))
					$hmgt_sms_service_enable = $_POST['hmgt_sms_service_enable'];
				if($hmgt_sms_service_enable)
				{						
					$userdata=get_users(array('role'=>$role));
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
						if(is_plugin_active('sms-pack/sms-pack.php'))
						{
							$message_content = MJ_hmgt_strip_tags_and_stripslashes($_POST['sms_template']);
							$current_sms_service 	= get_option( 'smgt_sms_service');
							$args = array();
							$args['mobile']=$user_number;
							$args['message_from']="Message";
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
			}
			MJ_hmgt_append_audit_log(''.esc_html__('Message sent ','hospital_mgt').'',get_current_user_id());
			$result=add_post_meta($post_id, 'message_for',$role);
			$result = 1;
		}
		}
		else 
		{
			$user_id = $data['receiver'];
			
			$post_id = wp_insert_post( array(
					'post_status' => 'publish',
					'post_type' => 'hmgt_message',
					'post_title' => $subject,
					'post_content' =>$message_body			
			) );
			$message_data=array('sender'=>get_current_user_id(),
					'receiver'=>$user_id,
					'msg_subject'=>$subject,
					'message_body'=>$message_body,
					'msg_date'=>$created_date,
					'msg_status' =>0,
					'post_id' =>$post_id
			);
			//start mail subject
					$user = get_userdata($user_id);
					$role=$user->roles;
					$reciverrole=$role[0];
					if($reciverrole == 'administrator' )
					{
						$page_link=admin_url().'admin.php?page=hmgt_message&tab=inbox';
					}
					else
					{
					  $page_link=home_url().'/?dashboard=user&page=message&tab=inbox';
					} 
				     
					$reciever_data=get_userdata($user_id);
					$reciever_name=$reciever_data->display_name;
					$reciever_email=$reciever_data->user_email;
					$sender_id=get_current_user_id();
					$senderdata=get_userdata($sender_id);
					$sendername_name=$senderdata->display_name;
		            $hospital_name = get_option('hmgt_hospital_name');
			 
				   $subject =get_option('MJ_hmgt_message_received_subject');
				   $sub_arr['{{Sender Name}}']=$sendername_name;
				   $sub_arr['{{Hospital Name}}']=$hospital_name;
				   $subject = MJ_hmgt_subject_string_replacemnet($sub_arr,$subject);
				
				$arr['{{Receiver Name}}']=$reciever_name;			
				$arr['{{Sender Name}}']=$sendername_name;
				$arr['{{Message Content}}']=$message_body;
				$arr['{{Hospital Name}}']=$hospital_name;
				$arr['{{Message_Link}}']=$page_link;
		
				$message = get_option('MJ_hmgt_message_received_template');	
				$message_replacement = MJ_hmgt_string_replacemnet($arr,$message);	
				$to[]=$reciever_email;
				MJ_hmgt_send_mail($to,$subject,$message_replacement);
				
            //end send mail	
			$result=$wpdb->insert( $table_message, $message_data);
			$hmgt_sms_service_enable=0;
			if(isset($_POST['hmgt_sms_service_enable']))
				$hmgt_sms_service_enable = $_POST['hmgt_sms_service_enable'];
			if($hmgt_sms_service_enable)
			{
				if(!empty(get_user_meta($user_id, 'phonecode',true))){ $phone_code=get_user_meta($user_id, 'phonecode',true); }else{ $phone_code='+'.MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }
				
				$reciever_number = $phone_code.get_user_meta($user_id, 'mobile',true);
				
				$message_content = MJ_hmgt_strip_tags_and_stripslashes($_POST['sms_template']);
				if(is_plugin_active('sms-pack/sms-pack.php'))
				{
					$current_sms_service 	= get_option( 'smgt_sms_service');
					$args = array();
					$args['mobile']=array($reciever_number);
					$args['message_from']="Message";
					$args['message']=$message_content;					
					if($current_sms_service=='telerivet' || $current_sms_service ="MSG91" || $current_sms_service=='bulksmsgateway.in' || $current_sms_service=='textlocal.in' || $current_sms_service=='bulksmsnigeria' || $current_sms_service=='africastalking' || $current_sms_service == 'clickatell')
					{				
						$send = send_sms($args);							
					}
					
				}
				$current_sms_service = get_option( 'hmgt_sms_service');
				if($current_sms_service == 'clickatell')
				{
					 $clickatell=get_option('hmgt_clickatell_sms_service');
					
					$username = urlencode($clickatell['username']);
					$password = urlencode($clickatell['password']);
					$api_id = urlencode($clickatell['api_key']);
					$to = $reciever_number;
					$message = urlencode($message_content);
					$send=file_get_contents("https://api.clickatell.com/http/sendmsg". "?user=$username&password=$password&api_id=$api_id&to=$to&text=$message"); 
				   
				}
				if($current_sms_service == 'twillo')
				{
					//Twilio lib
					require_once HMS_PLUGIN_DIR. '/lib/twilio/Services/Twilio.php';
					$twilio=get_option( 'hmgt_twillo_sms_service');
					$account_sid = $twilio['account_sid']; //Twilio SID
					$auth_token = $twilio['auth_token']; // Twilio token
					$from_number = $twilio['from_number'];//My number
					$message = urlencode($message_content);
					$receiver ='9512610761'; //Receiver Number
					$message = 'hello'; // Message Text
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
			
			MJ_hmgt_append_audit_log(''.esc_html__('Message sent ','hospital_mgt').'',get_current_user_id());
			$result=add_post_meta($post_id, 'message_for','user');
			$result=add_post_meta($post_id, 'message_for_userid',$user_id);
		}
		return $result;
		
	}
	//delete message data
	public function MJ_hmgt_delete_message($mid)
	{
		global $wpdb;
		$table_hmgt_message = $wpdb->prefix. 'hmgt_message';
		$result = $wpdb->query("DELETE FROM $table_hmgt_message where message_id= ".$mid);
		MJ_hmgt_append_audit_log(''.esc_html__('Delete message ','hospital_mgt').'',get_current_user_id());
		return $result;
	}
	//count send message 
	public function MJ_hmgt_count_send_item($user_id)
	{
		global $wpdb;
		$posts = $wpdb->prefix."posts";
		$total =$wpdb->get_var("SELECT Count(*) FROM ".$posts." Where post_type = 'hmgt_message' AND post_author = $user_id");
		return $total;
	}
	//count inbox message item
	public function MJ_hmgt_count_inbox_item($user_id)
	{
		global $wpdb;
		$tbl_name_message = $wpdb->prefix .'hmgt_message';
		
		$inbox =$wpdb->get_results("SELECT *  FROM $tbl_name_message where receiver = $user_id AND msg_status=0" );
		
		return $inbox;
	}
	//get5 inbox message data
	public function MJ_hmgt__get_inbox_message($user_id,$p=0,$lpm1=10)
	{
		global $wpdb;
		$tbl_name_message = $wpdb->prefix.'hmgt_message';
		$tbl_name_message_replies = $wpdb->prefix .'hmgt_message_replies';

		$inbox = $wpdb->get_results("SELECT DISTINCT b.message_id, a.* FROM $tbl_name_message a LEFT JOIN $tbl_name_message_replies b ON a.post_id = b.message_id WHERE ( a.receiver = $user_id OR b.receiver_id =$user_id)  group BY a.post_id ORDER BY msg_date DESC limit $p , $lpm1");
		                       
		return $inbox;
	}
	
	//pagination
	public function MJ_hmgt_pagination($totalposts,$p,$prev,$next,$page)
	{
		
		$pagination = "";
		
		if($totalposts > 1)
		{
			$pagination .= '<div class="btn-group">';
		
			if ($p > 1)
				$pagination.= "<a href=\"?$page&pg=$prev\" class=\"btn btn-default\"><i class=\"fa fa-angle-left\"></i></a> ";
			else
				$pagination.= "<a class=\"btn btn-default disabled\"><i class=\"fa fa-angle-left\"></i></a> ";
		
			if ($p < $totalposts)
				$pagination.= " <a href=\"?$page&pg=$next\" class=\"btn btn-default next-page\"><i class=\"fa fa-angle-right\"></i></a>";
			else
				$pagination.= " <a class=\"btn btn-default disabled\"><i class=\"fa fa-angle-right\"></i></a>";
			$pagination.= "</div>\n";
		}
		return $pagination;
	}
	//get send message data
	public function MJ_hmgt_get_send_message($user_id,$max=10,$offset=0)
	{	
		$args['post_type'] = 'hmgt_message';
		$args['posts_per_page'] =$max;
		$args['offset'] = $offset;
		$args['post_status'] = 'public';
		$args['author'] = $user_id;			
		$q = new WP_Query();
		$sent_message = $q->query( $args );
		return $sent_message;
	}
	//get message by id
	public function MJ_hmgt_get_message_by_id($id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "hmgt_message";		
		$qry = $wpdb->prepare( "SELECT * FROM $table_name WHERE message_id= %d ",$id);
		return $retrieve_subject = $wpdb->get_row($qry);
	
	}
	//get reply message data
	public function MJ_hmgt_send_replay_message($data)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "hmgt_message_replies";
		if(!empty($data['receiver_id']))
		{
			foreach($data['receiver_id'] as $receiver_id)
			{
				$messagedata['message_id'] = $data['message_id'];
				$messagedata['sender_id'] = $data['user_id'];
				$messagedata['receiver_id'] = $receiver_id;
				$messagedata['message_comment'] = $data['replay_message_body'];
				$messagedata['msg_status'] =0;
				$messagedata['created_date'] = date("Y-m-d h:i:s");
				$result=$wpdb->insert( $table_name, $messagedata );
			}
		}
		if($result)	
		return $result;			
	}
	//get all replies data
	public function MJ_hmgt_get_all_replies($id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "hmgt_message_replies";
		return $result =$wpdb->get_results("SELECT *  FROM $table_name where message_id = $id GROUP BY message_id,sender_id,message_comment ORDER BY id ASC");
		
	}
	//count reply item data
	public function MJ_hmgt_count_reply_item($id)
	{
		global $wpdb;
		$tbl_name = $wpdb->prefix .'hmgt_message_replies';
		
		$result=$wpdb->get_var("SELECT count(*)  FROM $tbl_name where message_id = $id");
		return $result;
	}
	//delete reply
	public function MJ_hmgt_delete_reply($id)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . "hmgt_message_replies";
		$reply_id['id']=$id;
		return $result=$wpdb->delete( $table_name, $reply_id);
	}
}
?>