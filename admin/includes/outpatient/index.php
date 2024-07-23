<?php
MJ_hmgt_browser_javascript_check();
$user_role=MJ_hmgt_get_roles(get_current_user_id());
if($user_role == 'administrator')
{
	$user_access_add=1;
	$user_access_edit=1;
	$user_access_delete=1;
	$user_access_view=1;
	$user_access_patient_edit=1;
	
}
else
{
	$user_access=MJ_hmgt_get_access_right_for_management_user_page('outpatient');
	$user_access_add=$user_access['add'];
	$user_access_edit=$user_access['edit'];
	$user_access_delete=$user_access['delete'];
	$user_access_view=$user_access['view'];
	
	
	$user_access_patient=MJ_hmgt_get_access_right_for_management_user_page('patient');
	$user_access_patient_edit=$user_access_patient['edit'];
	
	
if (isset ( $_REQUEST ['page'] ))
{	
	if($user_access_view == '0')
	{	
		MJ_hmgt_access_right_page_not_access_message_admin();
		die;
	}
	if(!empty($_REQUEST['action']))
	{
		if (isset ( $_REQUEST ['page'] ) && 'outpatient' == $user_access['page_link'] && ($_REQUEST['action']=='edit'))
		{
			if($user_access['edit']=='0')
			{	
				MJ_hmgt_access_right_page_not_access_message_admin();
				die;
			}			
		}
		if (isset ( $_REQUEST ['page'] ) && 'outpatient' == $user_access['page_link'] && ($_REQUEST['action']=='delete'))
		{
			if($user_access['delete']=='0')
			{	
				MJ_hmgt_access_right_page_not_access_message_admin();
				die;
			}	
		}
		if (isset ( $_REQUEST ['page'] ) && 'outpatient' == $user_access['page_link'] && ($_REQUEST['action']=='insert')) 
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
$role='outpatient';
$id=0;
$user_object=new MJ_hmgt_user();
$blood_obj=new MJ_hmgt_bloodbank();
$valtemp=0;
?>
<?php
$active_tab = isset($_GET['tab'])?$_GET['tab']:'outpatientlist';
?>
<!-- POP up code -->
<div class="popup-bg" >
    <div class="overlay-content">
		<div class="modal-content">
		   <div class="patient_data"></div>
		</div>
    </div> 
</div>
<!-- End POP-UP Code -->
<!-- Page inner Div START-->
<div class="page-inner min_height_1631">
    <!-- Page title Div START-->
    <div class="page-title">
		<h3><img src="<?php echo esc_url(get_option( 'hmgt_hospital_logo', 'hospital_mgt' )) ?>" class="img-circle head_logo" width="40" height="40" /><?php echo esc_html(get_option('hmgt_hospital_name','hospital_mgt'));?></h3>
	</div><!-- Page title Div END-->
	<?php 
	//export Outpatient in csv
	if(isset($_POST['export_csv']))
	{		
		$get_patient = array('role' => 'patient','meta_key'=>'patient_type','meta_value'=>'outpatient');
		$patient_list=get_users($get_patient);										
		
		if(!empty($patient_list))
		{
			$header = array();			
			$header[] = 'Username';
			$header[] = 'Email';
			$header[] = 'Password';
			$header[] = 'patient_id';
			$header[] = 'first_name';
			$header[] = 'middle_name';
			$header[] = 'last_name';			
			$header[] = 'gender';
			$header[] = 'birth_date';
			$header[] = 'blood_group';				
			$header[] = 'address';
			$header[] = 'city_name';
			$header[] = 'state_name';
			$header[] = 'country_name';
			$header[] = 'zip_code';
			$header[] = 'phonecode';
			$header[] = 'mobile';
			$header[] = 'phone';	
			$header[] = 'patient_type';	
			
			$document_dir = WP_CONTENT_DIR;
			$document_dir .= '/uploads/export/';
			$document_path = $document_dir;
			if (!file_exists($document_path))
			{
				mkdir($document_path, 0777, true);		
			}
			
			$filename=$document_path.'export_outpatients.csv';
			$fh = fopen($filename, 'w') or die("can't open file");
			fputcsv($fh, $header);
			foreach($patient_list as $retrive_data)
			{
				$row = array();
				$user_info = get_userdata($retrive_data->ID);
				
				$row[] = $user_info->user_login;
				$row[] = $user_info->user_email;			
				$row[] = $user_info->user_pass;			
			
				$row[] =  get_user_meta($retrive_data->ID, 'patient_id',true);
				$row[] =  get_user_meta($retrive_data->ID, 'first_name',true);
				$row[] =  get_user_meta($retrive_data->ID, 'middle_name',true);
				$row[] =  get_user_meta($retrive_data->ID, 'last_name',true);
				$row[] =  get_user_meta($retrive_data->ID, 'gender',true);
				$row[] =  get_user_meta($retrive_data->ID, 'birth_date',true);
				$row[] =  get_user_meta($retrive_data->ID, 'blood_group',true);					
				$row[] =  get_user_meta($retrive_data->ID, 'address',true);					
				$row[] =  get_user_meta($retrive_data->ID, 'city_name',true);				
				$row[] =  get_user_meta($retrive_data->ID, 'state_name',true);				
				$row[] =  get_user_meta($retrive_data->ID, 'country_name',true);				
				$row[] =  get_user_meta($retrive_data->ID, 'zip_code',true);			
				$row[] =  get_user_meta($retrive_data->ID, 'phonecode',true);				
				$row[] =  get_user_meta($retrive_data->ID, 'mobile',true);				
				$row[] =  get_user_meta($retrive_data->ID, 'phone',true);				
				$row[] =  get_user_meta($retrive_data->ID, 'patient_type',true);				
								
				fputcsv($fh, $row);
				
			}
			fclose($fh);
	
			//download csv file.
			ob_clean();
			$file=$document_path.'export_outpatients.csv';//file location
			
			$mime = 'text/plain';
			header('Content-Type:application/force-download');
			header('Pragma: public');       // required
			header('Expires: 0');           // no cache
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Last-Modified: '.gmdate ('D, d M Y H:i:s', filemtime ($file)).' GMT');
			header('Cache-Control: private',false);
			header('Content-Type: '.$mime);
			header('Content-Disposition: attachment; filename="'.basename($file).'"');
			header('Content-Transfer-Encoding: binary');			
			header('Connection: close');
			readfile($file);		
			exit;				
		}
		else
		{
			?>
			<div class="alert_msg alert alert-danger alert-dismissible fade in" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
				</button>
				<?php esc_html_e('Records not found.','hospital_mgt');?>
			</div>
			<?php	
		}		
	}
	//upload Outpatient csv	
	if(isset($_REQUEST['upload_csv_file']))
	{	
		if(isset($_FILES['csv_file']))
		{			
			$errors= array();
			$file_name = $_FILES['csv_file']['name'];
			$file_size =$_FILES['csv_file']['size'];
			$file_tmp =$_FILES['csv_file']['tmp_name'];
			$file_type=$_FILES['csv_file']['type'];

			$value = explode(".", $_FILES['csv_file']['name']);
			$file_ext = strtolower(array_pop($value));
			$extensions = array("csv");
			$upload_dir = wp_upload_dir();
			if(in_array($file_ext,$extensions )=== false){
				$errors[]="this file not allowed, please choose a CSV file.";
				wp_redirect ( admin_url().'admin.php?page=hmgt_outpatient&tab=outpatientlist&message=5');
			}
			if($file_size > 2097152)
			{
				$errors[]='File size limit 2 MB';
				wp_redirect ( admin_url().'admin.php?page=hmgt_outpatient&tab=outpatientlist&message=6');
			}
			
			if(empty($errors)==true)
			{	
				
				$rows = array_map('str_getcsv', file($file_tmp));		
			
				$header = array_map('strtolower',array_shift($rows));
					
				$csv = array();
				foreach ($rows as $row) 
				{	
					$header_size=sizeof($header);
					$row_size=sizeof($row);
					if($header_size == $row_size)
					{
						$csv = array_combine($header, $row);
						
						$username = $csv['username'];
						$email = $csv['email'];
						$user_id = 0;
						
						$password = $csv['password'];
						
						$problematic_row = false;
						
						if( username_exists($username) )
						{ // if user exists, we take his ID by login
							$user_object = get_user_by( "login", $username );
							$user_id = $user_object->ID;
						
							if( !empty($password) )
								wp_set_password( $password, $user_id );
						}
						elseif( email_exists( $email ) )
						{ // if the email is registered, we take the user from this
							$user_object = get_user_by( "email", $email );
							$user_id = $user_object->ID;					
							$problematic_row = true;
						
							if( !empty($password) )
								wp_set_password( $password, $user_id );
						}
						else{
							if( empty($password) ) // if user not exist and password is empty but the column is set, it will be generated
								$password = wp_generate_password();
						
							$user_id = wp_create_user($username, $password, $email);
						}
						
						if( is_wp_error($user_id) )
						{ // in case the user is generating errors after this checks
							echo '<script>alert("'.esc_html__('Problems with user','hospital_mgt').'" : "'.esc_html__($username,'hospital_mgt').'","'.esc_html__('we are going to skip','hospital_mgt').'");</script>';
							continue;
						}

						//if(!( in_array("administrator", MJ_hmgt_get_roles($user_id), FALSE) || is_multisite() && is_super_admin( $user_id ) ))
							
						
						wp_update_user(array ('ID' => $user_id, 'role' => 'patient')) ;
						wp_update_user(array ('ID' => $user_id, 'display_name' => $csv['first_name'] .' '.$csv['last_name'])) ;
						
					  $get_user_data=array();
					   $get_user_data=get_users(array(
						'meta_key' => 'patient_id',
						'meta_value' => $csv['patient_id']
					  ));
					  $patient_id_new='';
					  
					   if(empty($get_user_data))
					    {
							$patient_id_new=$csv['patient_id'];
						}
						else
						{
							$role='patient';
							
							$newpatient=MJ_hmgt_get_lastpatient_id($role);
							
						}
						if(isset($csv['patient_id']))
							update_user_meta( $user_id, "patient_id", $patient_id_new );
						if(isset($csv['first_name']))
							update_user_meta( $user_id, "first_name", $csv['first_name'] );
						if(isset($csv['middle_name']))
							update_user_meta( $user_id, "middle_name", $csv['middle_name'] );
						if(isset($csv['last_name']))
							update_user_meta( $user_id, "last_name", $csv['last_name'] );
						if(isset($csv['gender']))
							update_user_meta( $user_id, "gender", $csv['gender'] );
						if(isset($csv['birth_date']))
							update_user_meta( $user_id, "birth_date",$csv['birth_date']);
						
						if(isset($csv['blood_group']))
							update_user_meta( $user_id, "blood_group",$csv['blood_group']);

						if(isset($csv['address']))
							update_user_meta( $user_id, "address", $csv['address'] );
						
						if(isset($csv['city_name']))
							update_user_meta( $user_id, "city_name", $csv['city_name'] );
						if(isset($csv['state_name']))
							update_user_meta( $user_id, "state_name", $csv['state_name'] );
						if(isset($csv['country_name']))
							update_user_meta( $user_id, "country_name", $csv['country_name'] );
						if(isset($csv['zip_code']))
							update_user_meta( $user_id, "zip_code", $csv['zip_code'] );
						if(isset($csv['phonecode']))
							update_user_meta( $user_id, "phonecode", $csv['phonecode'] );
						if(isset($csv['mobile']))
							update_user_meta( $user_id, "mobile", $csv['mobile'] );
						if(isset($csv['phone']))
							update_user_meta( $user_id, "phone", $csv['phone'] );	
						if(isset($csv['patient_type']))
							update_user_meta( $user_id, "patient_type", $csv['patient_type'] );
						$success = 1;	
					}
					else
					{
						wp_redirect ( admin_url().'admin.php?page=hmgt_outpatient&tab=outpatientlist&message=7');						
					}
				}
			}
			else
			{
				foreach($errors as &$error) echo $error;
			}
			if(isset($success))
			{
			?>
			<div id="message" class="updated below-h2 notice is-dismissible">
				<p><?php esc_html_e('Outpatients CSV Successfully Uploaded.','hospital_mgt');?></p>
			</div>
			<?php
			} 
		}
	}
	//----------------- SAVE OUT PATIENT -------------//
	if(isset($_POST['save_outpatient']))
	{	
		$nonce = $_POST['_wpnonce'];
		if (wp_verify_nonce( $nonce, 'save_outpatient_nonce' ) )
		{ 
			if(isset($_REQUEST['action'])&& $_REQUEST['action']=='insert')
			{
				//multiple dignosis insert
				$upload_dignosis_array=array();
			
				if(!empty($_FILES['diagnosis']['name']))
				{
					$count_array=count($_FILES['diagnosis']['name']);

					for($a=0;$a<$count_array;$a++)
					{			
						foreach($_FILES['diagnosis'] as $image_key=>$image_val)
						{	
							if($_FILES['diagnosis']['name'][$a]!='')
							{	
								$diagnosis_array[$a]=array(
								'name'=>$_FILES['diagnosis']['name'][$a],
								'type'=>$_FILES['diagnosis']['type'][$a],
								'tmp_name'=>$_FILES['diagnosis']['tmp_name'][$a],
								'error'=>$_FILES['diagnosis']['error'][$a],
								'size'=>$_FILES['diagnosis']['size'][$a]
								);							
							}	
						}
					}
					if(!empty($diagnosis_array))
					{
						foreach($diagnosis_array as $key=>$value)		
						{	
							$get_file_name=$diagnosis_array[$key]['name'];	
							
							$upload_dignosis_array[]=MJ_hmgt_load_multiple_documets($value,$value,$get_file_name);				
						} 	
					}					
				}			
				if( !email_exists( $_POST['email'] ) && !username_exists( $_POST['username'] )) 
				{
					$txturl=$_POST['hmgt_user_avatar'];
					  
					$ext=MJ_hmgt_check_valid_extension($txturl);
					if(!$ext == 0)
					{
						$diagnosis_report_url=$upload_dignosis_array;
						$ext1=MJ_hmgt_check_valid_file_extension_for_diagnosis($diagnosis_report_url);
						if($ext1 == 0 )
						{
							$result=$user_object->MJ_hmgt_add_user($_POST);
							if($result)
							{
								$guardian_data=array('patient_id'=>$result,
										'doctor_id'=>$_POST['doctor'],
										'symptoms'=>implode(",",$_POST['symptoms']),
										'inpatient_create_date'=>date("Y-m-d H:i:s"),'inpatient_create_by'=>get_current_user_id()
								);
								$inserted=MJ_hmgt_add_guardian($guardian_data,$id);
								$user_object->MJ_hmgt_upload_multiple_diagnosis_report($result,$upload_dignosis_array);
								if($inserted)
								{
									wp_redirect ( admin_url() . 'admin.php?page=hmgt_outpatient&tab=outpatientlist&message=1');
								}
							}
						}						
						else
						{ 
							?>
							<div id="message" class="updated below-h2 notice is-dismissible">
								<p><p><?php esc_html_e('Sorry, only JPG,JPEG,PNG,GIF,DOC,PDF and ZIP files are allowed.','hospital_mgt'); ?></p></p>
							</div><?php 
						} 
					}					 
					else
					{
					?>
						<div id="message" class="updated below-h2 notice is-dismissible">
							<p><p><?php esc_html_e('Sorry, only JPG, JPEG, PNG & GIF files are allowed.','hospital_mgt');?></p></p>
						</div>
					<?php				
					}
				}
				else
				{
				?>
					<div id="message" class="updated below-h2 notice is-dismissible">
					<p><p><?php esc_html_e('Username Or Emailid Already Exist.','hospital_mgt');?></p></p>
					</div>
				<?php 
				}		
			}
			if(isset($_REQUEST['action'])&& $_REQUEST['action']=='edit')
			{
				//multiple dignosis upload
				$upload_dignosis_array=array();
				$not_upload_dignosis_array=array();
			
				if(!empty($_FILES['diagnosis']['name']))
				{
					$count_array=count($_FILES['diagnosis']['name']);

					for($a=0;$a<$count_array;$a++)
					{			
						foreach($_FILES['diagnosis'] as $image_key=>$image_val)
						{	
							if($_FILES['diagnosis']['name'][$a]!='')
							{	
								$diagnosis_array[$a]=array(
								'name'=>$_FILES['diagnosis']['name'][$a],
								'type'=>$_FILES['diagnosis']['type'][$a],
								'tmp_name'=>$_FILES['diagnosis']['tmp_name'][$a],
								'error'=>$_FILES['diagnosis']['error'][$a],
								'size'=>$_FILES['diagnosis']['size'][$a]
								);							
							}
							else
							{
								if(!empty($_POST['hidden_attach_report'][$a]))
								{
									$not_upload_dignosis_array[$a]=$_POST['hidden_attach_report'][$a];
								}	
							}
						}
					}
					if(!empty($diagnosis_array))
					{
						foreach($diagnosis_array as $key=>$value)		
						{	
							$get_file_name=$diagnosis_array[$key]['name'];	
							
							$upload_dignosis_array[]=MJ_hmgt_load_multiple_documets($value,$value,$get_file_name);				
						} 	
					}					
				}
				$upload_array_merge=array_merge($upload_dignosis_array,$not_upload_dignosis_array);

				$txturl=$_POST['hmgt_user_avatar'];
				$ext=MJ_hmgt_check_valid_extension($txturl);
				if(!$ext == 0)
				{			   
				   $ext1=MJ_hmgt_check_valid_file_extension_for_diagnosis($upload_array_merge);
				   if($ext1 == 0 )
				   {					   						   
						$result=$user_object->MJ_hmgt_add_user($_POST);
						$data=MJ_hmgt_get_guardianby_patient($_REQUEST['outpatient_id']);
						if(!empty($data))
						{
							$guardian_data=array('patient_id'=>$_REQUEST['outpatient_id'],
									'symptoms'=>implode(",",$_POST['symptoms']),
									'doctor_id'=>$_POST['doctor'],
									'inpatient_create_date'=>date("Y-m-d H:i:s"),'inpatient_create_by'=>get_current_user_id()
									);
													
							$result1=MJ_hmgt_update_guardian($guardian_data,$_REQUEST['outpatient_id']);
						}
						else
						{
							$guardian_data=array('patient_id'=>$_REQUEST['outpatient_id'],
							'doctor_id'=>$_POST['doctor'],
							'symptoms'=>implode(",",$_POST['symptoms']),
							'inpatient_create_date'=>date("Y-m-d H:i:s"),'inpatient_create_by'=>get_current_user_id()
							);
							$result1=MJ_hmgt_add_guardian($guardian_data,$_REQUEST['outpatient_id']);
						}
						// var_dump($result1);
						// die;
						$outpatient_id=0;
						if(isset($_POST['patient_convert']))
						{
							$outpatient_id=$_REQUEST['outpatient_id'];
						}
						global $wpdb;
						$table_diagnosis = $wpdb->prefix. 'hmgt_diagnosis';
						$result_delete_dignosis = $wpdb->query("DELETE FROM $table_diagnosis where report_cost IS NULL AND patient_id = ".$_REQUEST['outpatient_id']);
						
						$returnans=$user_object->MJ_hmgt_upload_multiple_diagnosis_report($_REQUEST['outpatient_id'],$upload_array_merge);					
					}				  
					else
					{						  
					  ?>
						<div id="message" class="updated below-h2 notice is-dismissible">
						<p><p><?php esc_html_e('Sorry, only JPG,JPEG,PNG,GIF,DOC,PDF and ZIP files are allowed.','hospital_mgt'); ?></p></p>
						</div>
						<?php 
					}					
				}					
				else
				{	
				?>
					<div id="" class="updated below-h2 notice is-dismissible">
						<p><p><?php esc_html_e('Sorry, only JPG, JPEG, PNG & GIF files are allowed.','hospital_mgt');?></p></p>
					</div>
				<?php 
				}	
				if(isset($result) ||isset($result1) || isset($returnans))
				{
					wp_redirect ( admin_url() . 'admin.php?page=hmgt_outpatient&tab=outpatientlist&message=2&outpatient_id='.$outpatient_id);
				}
			}
		}
	}
		
	if(isset($_REQUEST['action'])&& $_REQUEST['action']=='delete')
	{
		
		$result=$user_object->MJ_hmgt_delete_usedata($_REQUEST['outpatient_id']);
		$result1=MJ_hmgt_delete_guardian($_REQUEST['outpatient_id']);
		if($result)
		{
			wp_redirect ( admin_url() . 'admin.php?page=hmgt_outpatient&tab=outpatientlist&message=3');
			
		}
	}
	if(isset($_REQUEST['delete_selected']))
	{		
		if(!empty($_REQUEST['selected_id']))
		{
			
			foreach($_REQUEST['selected_id'] as $id)
			{
				$result=$user_object->MJ_hmgt_delete_usedata($id);
				$result1=MJ_hmgt_delete_guardian($id);
			}
			if($result)
			{
				wp_redirect ( admin_url() . 'admin.php?page=hmgt_outpatient&tab=outpatientlist&message=3');
			}
		}
		else
		{
			echo '<script language="javascript">';
			echo 'alert("'.esc_html__('Please select at least one record.','hospital_mgt').'")';
			echo '</script>';
		}
	}
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'active_patient')
	{
		//-------------------- SEND SMS ---------------------//
		
		if(!empty(get_user_meta($_REQUEST['outpatient_id'], 'phonecode',true))){ $phone_code=get_user_meta($_REQUEST['outpatient_id'], 'phonecode',true); }else{ $phone_code='+'.MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }
							
		$user_number[] = $phone_code.get_user_meta($_REQUEST['outpatient_id'], 'mobile',true);
		$user_number1 = $phone_code.get_user_meta($_REQUEST['outpatient_id'], 'mobile',true);
		if(is_plugin_active('sms-pack/sms-pack.php'))
		{
			$hospital_name = get_option('hmgt_hospital_name');
			$message_content ="Your registration successfully approved form $hospital_name";
			$current_sms_service 	= get_option( 'smgt_sms_service');
			$args = array();
			$args['mobile']=$user_number;
			$args['message_from']="Approved";
			$args['message']=$message_content;					
			if($current_sms_service=='telerivet' || $current_sms_service ="MSG91" || $current_sms_service=='bulksmsgateway.in' || $current_sms_service=='textlocal.in' || $current_sms_service=='bulksmsnigeria' || $current_sms_service=='africastalking' || $current_sms_service == 'clickatell')
			{				
				$send = send_sms($args);							
			}
		}
		$current_sms_service = get_option('hmgt_sms_service');
		$message_content ="Your registration successfully approved";
		if($current_sms_service == 'clickatell')
		{
			$clickatell=get_option('hmgt_clickatell_sms_service');
			$username = urlencode($clickatell['username']);
			$password = urlencode($clickatell['password']);
			$api_id = urlencode($clickatell['api_key']);
			$to = $user_number1;
			$message = urlencode($message_content);
			$send=file_get_contents("https://api.clickatell.com/http/sendmsg". "?user=$username&password=$password&api_id=$api_id&to=$to&text=$message"); 
		}
		if($current_sms_service == 'msg91')
		{
			//MSG91
			$mobile_number=$user_number1;
			$country_code="+".MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' ));
			$message = $message_content; // Message Text
			MJ_hmgt_msg91_send_mail_function($mobile_number,$message,$country_code);
		}						
		$out_patient_id = $_REQUEST['outpatient_id'];
		delete_user_meta($out_patient_id, 'hmgt_hash');
		$user_info = get_userdata($_REQUEST['outpatient_id']);
		$to = $user_info->user_email; 
		$patientname=$user_info->display_name;
		$login_link=home_url();
		$subject =get_option('MJ_hmgt_patient_approved_subject'); 
		$hospital_name = get_option('hmgt_hospital_name');
		$sub_arr['{{Hospital Name}}']=$hospital_name;
	    $subject = MJ_hmgt_subject_string_replacemnet($sub_arr,$subject);
		$search=array('{{Patient Name}}','{{Hospital Name}}','{{Login Link}} ');
		$replace = array($patientname,$hospital_name,$login_link);
		$message_replacement = str_replace($search, $replace,get_option('Patient_Approved_Email_Template'));	
		 MJ_hmgt_send_mail($to,$subject,$message_replacement);	 
		
		wp_redirect ( admin_url() . 'admin.php?page=hmgt_outpatient&tab=outpatientlist&message=4');
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
		{
			if(isset($_REQUEST['outpatient_id']))
				$valtemp=$_REQUEST['outpatient_id'];
			
			?><div id="message" class="updated below-h2 notice is-dismissible"><p><?php
					esc_html_e("Record updated successfully.",'hospital_mgt');
					?></p>
					</div>
				<?php 
			
		}
		elseif($message == 3) 
		{?>
		<div id="message" class="updated below-h2 notice is-dismissible"><p>
		<?php 
			esc_html_e('Record deleted successfully','hospital_mgt');
		?></div></p><?php
				
		}
		elseif($message == 4) 
		{?>
		<div id="message" class="updated below-h2 notice is-dismissible"><p>
		<?php 
			esc_html_e('Patient Actived Successfully','hospital_mgt');
		?></div></p><?php
				
		}
		elseif($message == 5) 
		{?>
		<div id="message" class="updated below-h2 notice is-dismissible"><p>
		<?php 
			esc_html_e('Only CSV file are allow.','hospital_mgt');
		?></div></p><?php
				
		}
		
		elseif($message == 6) 
		{?>
		<div id="message" class="updated below-h2 notice is-dismissible"><p>
		<?php 
			esc_html_e('File size limit 2 MB allow.','hospital_mgt');
		?></div></p><?php
				
		}
		elseif($message == 7) 
		{?>
			<div id="message" class="updated below-h2 notice is-dismissible"><p>
			<?php 
				esc_html_e('This file formate not proper.Please select CSV file with proper formate.','hospital_mgt');
			?></p></div>
			<?php				
		}
	}
	?>
	<!-- main-wrapper DIV START-->
	<div id="main-wrapper">
	<!--    row DIV START-->
		<div class="row">
			<div class="col-md-12">
			    <!--    PANAL white DIV START-->
				<div class="panel panel-white">
				     <!--    PANAL BODY DIV START -->	
					<div class="panel-body">
						<h2 class="nav-tab-wrapper">
							<a href="?page=hmgt_outpatient&tab=outpatientlist" class="nav-tab <?php echo $active_tab == 'outpatientlist' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-menu"></span> '.esc_html__('Outpatient List', 'hospital_mgt'); ?></a>
							
							<?php  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
							{?>
							<a href="?page=hmgt_outpatient&tab=addoutpatient&&action=edit&outpatient_id=<?php echo $_REQUEST['outpatient_id'];?>" class="nav-tab <?php echo $active_tab == 'addoutpatient' ? 'nav-tab-active' : ''; ?>">
							<?php esc_html_e('Edit Outpatient', 'hospital_mgt'); ?></a>  
							<?php 
							}
							else
							{?>
							<?php 
							 if($user_access_add == 1)
							 {?>
								<a href="?page=hmgt_outpatient&tab=addoutpatient" class="nav-tab <?php echo $active_tab == 'addoutpatient' ? 'nav-tab-active' : ''; ?>">
								<?php echo '<span class="dashicons dashicons-plus-alt"></span> '.esc_html__('Add New Outpatient', 'hospital_mgt'); ?></a>  
								<?php  
							 }
							}
							?>
						</h2>
						<?php 
						if($active_tab == 'outpatientlist')
						{
						 ?>
							<script>
							jQuery(document).ready(function() {
							"use strict";
							jQuery('#outpatient_list').DataTable({ 
							    "responsive": true,
								"order": [[ 0, "asc" ]],
								"dom": 'Bfrtip',
								"buttons": [
									'colvis'
								], 
								"aoColumns":[
											  {"bSortable": false},
											  {"bSortable": false},
											  {"bSortable": true},
											  {"bSortable": true},
											  {"bSortable": true},
											  {"bVisible": true},
											  {"bVisible": true},
											  {"bVisible": true},
											  {"bSortable": false}
										   ],
								language:<?php echo MJ_hmgt_datatable_multi_language();?>
							});
							var tempval=<?php echo $valtemp;?>;
								if(tempval!=0){
								swal({
												title: "Outpatient successfully converted to inpatient!",
												text: "Do you Want to Admit this patient?",
												type: "warning",
												showCancelButton: true,
												confirmButtonColor: '#22baa0',
												confirmButtonText: 'Yes',
												cancelButtonText: "No",
												closeOnConfirm: false,
												closeOnCancel: true
											},
												function(isConfirm){
												if (isConfirm){
													window.location.href = "<?php echo admin_url().'admin.php?page=hmgt_patient&tab=addpatient_step2&action=edit&patient_id='.$valtemp; ?>";
												} else {
													tempval=0;
												 window.location.href = "<?php echo admin_url() . 'admin.php?page=hmgt_outpatient&tab=outpatientlist&message=2';?>";
												}
											});
								}
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
							<!--    PANAL BODY DIV START -->	
							<div class="panel-body">
							<?php 
							if($user_access_add == 1)
							{?>
							<input type="submit" value="<?php esc_html_e('Export CSV','hospital_mgt');?>" name="export_csv" class="btn btn-success margin_bottom_5px"/> 
							<input type="button" value="<?php esc_html_e('Import CSV','hospital_mgt');?>" name="import_csv" class="btn btn-success importdata margin_bottom_5px"/> 
							<?php 
							} ?>
								<div class="table-responsive">
									<table id="outpatient_list" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th><input type="checkbox" class="select_all"></th>
												<th><?php  esc_html_e( 'Photo', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e( 'Patient Name', 'hospital_mgt' ) ;?></th>
												 <th><?php esc_html_e( 'Patient Id', 'hospital_mgt' ) ;?></th>           
												 <th> <?php esc_html_e( 'Mobile Number', 'hospital_mgt' ) ;?></th>
												 <th> <?php esc_html_e( 'Blood Group', 'hospital_mgt' ) ;?></th>
												 <th> <?php esc_html_e( 'Email', 'hospital_mgt' ) ;?></th>
												 <th> <?php esc_html_e( 'Assigned Doctor Name', 'hospital_mgt' ) ;?></th>
												 <th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
											</tr>
										</thead>
										<tfoot>
											<tr>
												<th></th>
												<th><?php  esc_html_e( 'Photo', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e( 'Patient Name', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e('Patient Id','hospital_mgt'); ?></th>				
												<th> <?php esc_html_e( 'Mobile Number', 'hospital_mgt' ) ;?></th>
												<th> <?php esc_html_e( 'Blood Group', 'hospital_mgt' ) ;?></th>
												<th> <?php esc_html_e( 'Email', 'hospital_mgt' ) ;?></th>
												 <th> <?php esc_html_e( 'Assigned Doctor Name', 'hospital_mgt' ) ;?></th>
												<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
											</tr>
										</tfoot>
							 
										<tbody>
										<?php 
										$get_patient = array('role' => 'patient','meta_key'=>'patient_type','meta_value'=>'outpatient');
										$patientdata=get_users($get_patient);
										if(!empty($patientdata))
										{
										  foreach ($patientdata as $retrieved_data){

												$doctordetail=MJ_hmgt_get_guardianby_patient($retrieved_data->ID);
												
												if(!empty($doctordetail['doctor_id']))
												{
													$doctor = get_userdata($doctordetail['doctor_id']);
												}												
											?>
											<tr>
												<td class="title"><input type="checkbox" name="selected_id[]" class="sub_chk" value="<?php echo esc_attr($retrieved_data->ID); ?>"></td>
												<td class="user_image"><?php $uid=$retrieved_data->ID;
													$userimage=get_user_meta($uid, 'hmgt_user_avatar', true);
													if(empty($userimage))
													{
														echo '<img src='.get_option( 'hmgt_patient_thumb' ).' height="50px" width="50px" class="img-circle" />';
													}
													else
													{
														echo '<img src='.$userimage.' height="50px" width="50px" class="img-circle"/>';
													}
												?></td>
												<td class="name"><a href="?page=hmgt_outpatient&tab=addoutpatient&action=edit&outpatient_id=<?php echo esc_attr($retrieved_data->ID);?>"><?php echo $retrieved_data->display_name;?></a></td>
												<td class="patient_id">
												<?php 
														echo get_user_meta($uid, 'patient_id', true);
												?></td>
												<td class="phone"><?php echo get_user_meta($uid, 'mobile', true);?></td>
												<td class="email"><?php echo esc_html__(get_user_meta($uid, 'blood_group', true),"hospital_mgt");?></td>
												<td class="email"><?php echo esc_html($retrieved_data->user_email);?></td>
												<td class=""><?php if(!empty($doctor)) { echo esc_html($doctor->display_name); }?></td>

												<td class="action"> 
												<?php 
												if( !get_user_meta($retrieved_data->ID, 'hmgt_hash', true))
												 {
												?>
													<a href="javascript:void(0);" class="show-view-popup btn btn-default" idtest="<?php echo esc_attr($retrieved_data->ID); ?>" type="<?php echo 'view_outpatient';?>">
													<i class="fa fa-eye"></i> <?php esc_html_e('Patient Detail', 'hospital_mgt');?></a>
													
													<a href="?page=hmgt_invoice&tab=addinvoice&patient=<?php echo esc_attr($retrieved_data->ID); ?>" class="btn btn-default"> <?php esc_html_e('Billing', 'hospital_mgt' );?></a>
													<?php
													if($user_access_patient_edit == 1)
													{
													?>
													  <a href="?page=hmgt_outpatient&tab=addoutpatient&action=edit&outpatient_id=<?php echo esc_attr($retrieved_data->ID); ?>" class="btn btn-default"> <?php esc_html_e('Admit', 'hospital_mgt' );?></a>
													<?php 
													} 
													?>
													<a  href="?page=hmgt_outpatient&action=view_status&outpatient_id=<?php echo esc_attr($retrieved_data->ID);?>" class="show-popup btn btn-default" idtest="<?php echo esc_attr($retrieved_data->ID); ?>"><i class="fa fa-eye"></i> <?php esc_html_e('View Detail', 'hospital_mgt');?></a>
													<a  href="?page=hmgt_outpatient&action=view_status&patient_id=<?php echo esc_attr($retrieved_data->ID);?>" class="show-charges-popup btn btn-default" idtest="<?php echo esc_attr($retrieved_data->ID); ?>">
													<i class="fa fa-money"></i> <?php esc_html_e('Charges', 'hospital_mgt');?></a>
													<?php 
													if($user_access_edit == 1)
													{?>
													<a href="?page=hmgt_outpatient&tab=addoutpatient&action=edit&outpatient_id=<?php echo esc_attr($retrieved_data->ID);?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
													<?php } ?>
													<?php 
													if($user_access_delete == 1)
													{?>
													<a href="?page=hmgt_outpatient&tab=outpatientlist&action=delete&outpatient_id=<?php echo esc_attr($retrieved_data->ID);?>" class="btn btn-danger" 
													onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this record?','hospital_mgt');?>');">
													<?php esc_html_e( 'Delete', 'hospital_mgt' ) ;?> </a>
											        <?php 
													}
											    }
												else
												{
													if($user_access_add == 1)
													{
													?>
														<a  href="?page=hmgt_outpatient&action=active_patient&outpatient_id=<?php echo esc_attr($retrieved_data->ID);?>" class="btn btn-default" > <?php esc_html_e('Active', 'hospital_mgt');?></a>
													<?php
													}
												}
												?>
												</td>
											   
											</tr>
											<?php } 
											
										}?>
									 
										</tbody>
									</table>
									<?php 
									if($user_access_delete == 1)
									{?> 
									<div class="print-button pull-left">
										<input  type="submit" value="<?php esc_html_e('Delete Selected','hospital_mgt');?>" name="delete_selected" class="btn btn-danger delete_selected "/>
									</div>
									<?php 
									} ?>
								</div>
							</div>							
							<!--  END PANAL BODY DIV -->					   
						</form>
						 <?php 
						}
						if($active_tab == 'addoutpatient')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/outpatient/add_out_patient.php';
						}						 
						?>
                    </div>
					<!-- END PANAL BODY DIV -->
			
		        </div>
				<!-- PANAL white DIV -->
	        </div>
        </div>
		<!--END ROW DIV -->
	</div>
<!-- END main-wrapper DIV -->