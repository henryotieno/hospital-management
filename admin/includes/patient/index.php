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
	$user_access=MJ_hmgt_get_access_right_for_management_user_page('patient');
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
			if (isset ( $_REQUEST ['page'] ) && 'patient' == $user_access['page_link'] && ($_REQUEST['action']=='edit'))
			{
				if($user_access['edit']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}			
			}
			if (isset ( $_REQUEST ['page'] ) && 'patient' == $user_access['page_link'] && ($_REQUEST['action']=='delete'))
			{
				if($user_access['delete']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}	
			}
			if (isset ( $_REQUEST ['page'] ) && 'patient' == $user_access['page_link'] && ($_REQUEST['action']=='insert')) 
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

$user_object=new MJ_hmgt_user();
$obj_bloodbank=new MJ_hmgt_bloodbank();
?>
<?php
$active_tab = isset($_GET['tab'])?$_GET['tab']:'patientlist';
?>
<!-- POP up code -->
<div class="popup-bg">
    <div class="overlay-content">
		<div class="modal-content">
			<div class="patient_data">
			</div>
		</div>
    </div> 
</div>
<!-- End POP-UP Code -->
		
<div class="page-inner min_height_1631"><!-- PAGE INNER DIV START-->
    <!-- PAGE TITLE DIV START-->
	<div class="page-title">
		<h3><img src="<?php echo esc_url(get_option( 'hmgt_hospital_logo', 'hospital_mgt' )) ?>" class="img-circle head_logo" width="40" height="40" /><?php echo esc_html(get_option('hmgt_hospital_name','hospital_mgt'));?></h3>
    </div><!-- PAGE TITLE DIV END-->
	<?php 
	if(isset($_POST['save_patient']))
	{
		$nonce = $_POST['_wpnonce'];
		if (wp_verify_nonce( $nonce, 'save_inpatient_nonce' ) )
		{ 	
			if(isset($_REQUEST['action'])&& $_REQUEST['action']=='insert')
			{
				if( !email_exists( $_POST['email'] ) && !username_exists( $_POST['username'] )) {
				   $txturl=$_POST['hmgt_user_avatar'];
				   $ext=MJ_hmgt_check_valid_extension($txturl);
				   if(!$ext == 0)
				   {
						$result=$user_object->MJ_hmgt_add_user($_POST);
						
						if($result)
						{
							
							wp_redirect ( admin_url () . 'admin.php?page=hmgt_patient&tab=addpatient_step2&patient_id='.$result );	
						}
				   }
					else
					{ ?>
						<div id="message" class="updated below-h2 notice is-dismissible">
								<p><p><?php esc_html_e('Sorry, only JPG, JPEG, PNG & GIF files are allowed.','hospital_mgt');?></p></p>
						</div>
					<?php 
					}			
				}
			}
			else			
			{
				$result=$user_object->MJ_hmgt_add_user($_POST);
				if($result)
				{
					wp_redirect ( admin_url () . 'admin.php?page=hmgt_patient&tab=addpatient_step2&action=edit&patient_id='.$result); 
				}
			}
		}
	}
	//---------------- SAVE PATIENT STEP 3 -----------------------//
	if(isset($_POST['save_patient_step3']))
	{
		$nonce = $_POST['_wpnonce'];
		if (wp_verify_nonce( $nonce, 'save_inpatient2_nonce' ) )
		{ 
			$guardian_data=array('admit_date'=>date(MJ_hmgt_get_format_for_db($_POST['admit_date'])) ,			              	'admit_time'=>$_POST['admit_time'].":00",
										'patient_status'=>$_POST['patient_status'],
										'doctor_id'=>$_POST['doctor'],
										'symptoms'=>implode(",",$_POST['symptoms'])
								);
				if(isset($_REQUEST['action'])&& $_REQUEST['action']=='edit')
				{
				 $result=MJ_hmgt_update_guardian($guardian_data,$_REQUEST['patient_id']);
				  if($result)
					{
						//patint asign to doctor patient mail template code start
						$doctorid=$_POST['doctor'];
						$doctorinfo=get_userdata($doctorid);
						$doctorname=$doctorinfo->display_name;
						$doctoremail=$doctorinfo->user_email;
						$departmentsname=get_post($doctorinfo->department);
						$dep=$departmentsname->post_title; 
						$userinfo=get_userdata($_REQUEST['patient_id']);
						$username=$userinfo->display_name;
						$user_email=$userinfo->user_email; 
						$hospital_name = get_option('hmgt_hospital_name');
							$subject =get_option('MJ_hmgt_patient_assigned_to_doctor_patient_email_subject');
							$sub_arr['{{Doctor Name}}']=$doctorname;
							$subject = MJ_hmgt_subject_string_replacemnet($sub_arr,$subject);
							$arr['{{Patient Name}}']=$username;			
							$arr['{{Doctor Name}}']=$doctorname;			
							$arr['{{Department Name}}']=$dep;
							$arr['{{Hospital Name}}']=$hospital_name;
							$message = get_option('MJ_hmgt_patient_assigned_to_doctor_patient_email_template');
							$message_replacement = MJ_hmgt_string_replacemnet($arr,$message);	
								$to[]=$user_email;
							MJ_hmgt_send_mail($to,$subject,$message_replacement);
					
						   //patint asign to doctor patient mail template code end
					
						   // patint asign to doctor docor mail template code  start
							$subject =get_option('MJ_hmgt_patient_assigned_to_doctor_mail_subject');
							$sub_arr['{{Patient Name}}']=$username;
							$subject = MJ_hmgt_subject_string_replacemnet($sub_arr,$subject);
							$arr['{{Patient Name}}']=$username;			
							$arr['{{Doctor Name}}']=$doctorname;			
							$arr['{{Hospital Name}}']=$hospital_name;
							$message = get_option('MJ_hmgt_patient_assigned_to_doctor_mail_template');
							$message_replacement = MJ_hmgt_string_replacemnet($arr,$message);	
							$doctoremail_to[]=$doctoremail;
							MJ_hmgt_send_mail($doctoremail_to,$subject,$message_replacement);
										wp_redirect ( admin_url() . 'admin.php?page=hmgt_patient&tab=patientlist&message=2');
					}
									
				}
				else
				{
					if(!empty($_REQUEST['patient_id']))
					{
						$result=MJ_hmgt_update_guardian($guardian_data,$_REQUEST['patient_id']);
						if($result)
						{
							//patint asign to doctor patient mail template code start
							$doctorid=$_POST['doctor'];
							$doctorinfo=get_userdata($doctorid);
							$doctorname=$doctorinfo->display_name;
							$doctoremail=$doctorinfo->user_email;
							$departmentsname=get_post($doctorinfo->department);
							$dep=$departmentsname->post_title; 
							$userinfo=get_userdata($_REQUEST['patient_id']);
							$username=$userinfo->display_name;
							$user_email=$userinfo->user_email; 
							$hospital_name = get_option('hmgt_hospital_name');
								$subject =get_option('MJ_hmgt_patient_assigned_to_doctor_patient_email_subject');
								$sub_arr['{{Doctor Name}}']=$doctorname;
								$subject = MJ_hmgt_subject_string_replacemnet($sub_arr,$subject);
								$arr['{{Patient Name}}']=$username;			
								$arr['{{Doctor Name}}']=$doctorname;			
								$arr['{{Department Name}}']=$dep;
								$arr['{{Hospital Name}}']=$hospital_name;
								$message = get_option('MJ_hmgt_patient_assigned_to_doctor_patient_email_template');
								$message_replacement = MJ_hmgt_string_replacemnet($arr,$message);	
									$to[]=$user_email;
								MJ_hmgt_send_mail($to,$subject,$message_replacement);
						
								//patint asign to doctor patient mail template code end
								
								// patint asign to doctor docor mail template code  start
								$subject =get_option('MJ_hmgt_patient_assigned_to_doctor_mail_subject');
								$sub_arr['{{Patient Name}}']=$username;
								$subject = MJ_hmgt_subject_string_replacemnet($sub_arr,$subject);
								$arr['{{Patient Name}}']=$username;			
								$arr['{{Doctor Name}}']=$doctorname;			
								$arr['{{Hospital Name}}']=$hospital_name;
								$message = get_option('MJ_hmgt_patient_assigned_to_doctor_mail_template');
								$message_replacement = MJ_hmgt_string_replacemnet($arr,$message);	
								$doctoremail_to[]=$doctoremail;
								MJ_hmgt_send_mail($doctoremail_to,$subject,$message_replacement);
										wp_redirect ( admin_url() . 'admin.php?page=hmgt_patient&tab=patientlist&message=1');
						}
					}
			}	
		}
	}
	
		if(isset($_REQUEST['action'])&& $_REQUEST['action']=='delete')
		{
			$result=$user_object->MJ_hmgt_delete_usedata($_REQUEST['patient_id']);
			$result=MJ_hmgt_delete_guardian($_REQUEST['patient_id']);
			if($result)
			{
				wp_redirect ( admin_url() . 'admin.php?page=hmgt_patient&tab=patientlist&message=3');
			}
		}

		if(isset($_REQUEST['delete_selected']))
		{		
			if(!empty($_REQUEST['selected_id']))
			{
				
				foreach($_REQUEST['selected_id'] as $id)
				{
					$result=$user_object->MJ_hmgt_delete_usedata($id);
					$result=MJ_hmgt_delete_guardian($id);
				}
				if($result)
				{
					wp_redirect ( admin_url() . 'admin.php?page=hmgt_patient&tab=patientlist&message=3');
				}
			}
			else
			{
				echo '<script language="javascript">';
				echo 'alert("'.esc_html__('Please select at least one record.','hospital_mgt').'")';
				echo '</script>';
			}
		}
	
		if(isset($_POST['save_discharge']))
		{
			
			if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'MJ_hmgt_discharge_popup' ))
			{
				$result = $user_object->add_discharge_data($_POST);
				if($result)
				{
					
					wp_redirect ( admin_url().'admin.php?page=hmgt_patient&tab=patientlist&message=4');
						
				}
			}
		
		}
		?>
		<?php 
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
		?></div></p><?php
				
		}
		elseif($message == 4) 
		{?>
		<div id="message" class="updated below-h2 notice is-dismissible"><p>
		<?php 
			esc_html_e('Patient discharge successfully','hospital_mgt');
		?></div></p><?php
				
		}
	}
		
	?>
	<!-- MAIN WRAPPER DIV START-->	
	<div id="main-wrapper">
		<div class="row"> <!-- ROW DIV START-->
			<div class="col-md-12">
			    <!-- PANEL WHITE DIV START-->
				<div class="panel panel-white">
				<!-- PANEL BODY DIV START-->
					<div class="panel-body nav_tab_responsive_4_tab">
						<h2 class="nav-tab-wrapper">
							<a href="?page=hmgt_patient&tab=patientlist" class="nav-tab <?php echo $active_tab == 'patientlist' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-menu"></span> '.esc_html__('Patient List', 'hospital_mgt'); ?></a>
							
							<?php  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
							{?>
							<a href="?page=hmgt_patient&tab=addpatient&action=edit&patient_id=<?php echo $_REQUEST['patient_id'];?>" class="nav-tab <?php echo $active_tab == 'addpatient' ? 'nav-tab-active' : ''; ?>">
							<?php esc_html_e('Edit Patient', 'hospital_mgt'); ?></a> 
								
							<a href="?page=hmgt_patient&tab=addpatient_step2&action=edit&patient_id=<?php echo $_REQUEST['patient_id'];?>" class="nav-tab <?php echo $active_tab == 'addpatient_step2' ? 'nav-tab-active' : ''; ?>">
							<?php echo esc_html__('Edit Patient Step-2', 'hospital_mgt'); ?></a>  
							<a href="?page=hmgt_patient&tab=addpatient_step3&action=edit&patient_id=<?php echo $_REQUEST['patient_id'];?>" class="nav-tab <?php echo $active_tab == 'addpatient_step3' ? 'nav-tab-active' : ''; ?>">
							<?php echo esc_html__('Edit Patient Step-3', 'hospital_mgt'); ?></a>
							<?php 
							}
							else
							{
								if($user_access_add == 1)
								{
									?>
									<a href="?page=hmgt_patient&tab=addpatient" class="nav-tab <?php echo $active_tab == 'addpatient' ? 'nav-tab-active' : ''; ?>">
									<?php echo '<span class="dashicons dashicons-plus-alt"></span> '.esc_html__('Add New Patient', 'hospital_mgt'); ?></a>  
									<a href="?page=hmgt_patient&tab=addpatient_step2" class="nav-tab <?php echo $active_tab == 'addpatient_step2' ? 'nav-tab-active' : ''; ?>">
									<?php echo '<span class="dashicons dashicons-plus-alt"></span> '.esc_html__('Add New Patient Step-2', 'hospital_mgt'); ?></a>  
									<a href="?page=hmgt_patient&tab=addpatient_step3" class="addpatient_step3_tab nav-tab <?php echo $active_tab == 'addpatient_step3' ? 'nav-tab-active' : ''; ?>">
									<?php echo '<span class="dashicons dashicons-plus-alt"></span> '.esc_html__('Add New Patient Step-3', 'hospital_mgt'); ?></a>  
									<?php
								}
							}?>
						   
						</h2>
						 <?php 
						
						if($active_tab == 'patientlist')
						{
						?>	
							<script>
								jQuery(document).ready(function() {
								"use strict";
								jQuery('#patient_list').DataTable({ 
								"responsive": true,
									"order": [[ 2, "asc" ]],
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
												  {"bVisible": true},
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
							<div class="panel-body"> <!-- PANEL BODY DIV START-->
								<div class="table-responsive"> <!-- TABLE RESPONSIVE DIV START-->
									<table id="patient_list" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th><input type="checkbox" class="select_all"></th>
												<th><?php  esc_html_e( 'Photo', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e( 'Patient Name', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e( 'Patient Id', 'hospital_mgt' ) ;?></th>             
												<th> <?php esc_html_e( 'Mobile Number', 'hospital_mgt' ) ;?></th>
												<th> <?php esc_html_e( 'Status', 'hospital_mgt' ) ;?></th>
												<th> <?php esc_html_e( 'Blood Group', 'hospital_mgt' ) ;?></th>
												<th> <?php esc_html_e( 'Assigned Doctor Name', 'hospital_mgt' ) ;?></th>
												<th> <?php esc_html_e( 'Admitted Date', 'hospital_mgt' ) ;?></th>
												<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
											</tr>
										</thead>
										<tfoot>
											<tr>
												<th></th>
											   <th><?php  esc_html_e( 'Photo', 'hospital_mgt' ) ;?></th>
											   <th><?php esc_html_e( 'Patient Name', 'hospital_mgt' ) ;?></th>
											   <th><?php esc_html_e( 'Patient Id', 'hospital_mgt' ) ;?></th>            
											   <th> <?php esc_html_e( 'Mobile Number', 'hospital_mgt' ) ;?></th>
											   <th> <?php esc_html_e( 'Status', 'hospital_mgt' ) ;?></th>
											   <th> <?php esc_html_e( 'Blood Group', 'hospital_mgt' ) ;?></th>
											   <th> <?php esc_html_e( 'Assigned Doctor Name', 'hospital_mgt' ) ;?></th>
											   <th> <?php esc_html_e( 'Admitted Date', 'hospital_mgt' ) ;?></th>
											   <th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
											</tr>
										</tfoot>
										<tbody>
										<?php 
										$get_patient = array('role' => 'patient','meta_key'=>'patient_type','meta_value'=>'inpatient');
										$patientdata=get_users($get_patient);
										 if(!empty($patientdata))
										 {
											foreach ($patientdata as $retrieved_data)
											{
												$doctordetail=MJ_hmgt_get_guardianby_patient($retrieved_data->ID);
												if(!empty($doctordetail['doctor_id'])){
													$doctor = get_userdata($doctordetail['doctor_id']);
												}
												// $doctor = get_userdata($doctordetail['doctor_id']);
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
													echo '<img src='.$userimage.' height="50px" width="50px" class="img-circle"/>';
												?>
												</td>
												<td class="name"><a href="?page=hmgt_patient&tab=addpatient&action=edit&patient_id=<?php echo esc_attr($retrieved_data->ID);?>"><?php echo esc_html($retrieved_data->display_name);?></a></td>
												<td class="patient_id">
												<?php 
														echo get_user_meta($uid, 'patient_id', true);
												?></td>
												<?php 
								                   $blood_group=get_user_meta($uid, 'blood_group', true);
								                ?>
												<td class="phone"><?php echo get_user_meta($uid, 'mobile', true);?></td>
												<td class="email"><?php
												$patient_status=MJ_hmgt_get_patient_status($retrieved_data->ID);
												echo esc_html__("$patient_status","hospital_mgt"); ?></td>
												<td class="bldgroup"><?php echo esc_html__("$blood_group","hospital_mgt");?></td>
												<td class=""><?php
												if(!empty($doctor->display_name))
												{	
													echo esc_html($doctor->display_name);
												}
												else
												{
													echo "-";
												}
												?></td>
											
												<td class=""><?php	
												if(!empty($doctordetail['admit_date']) && $doctordetail['admit_date'] != '0000-00-00')	
												{
													
													echo date(MJ_hmgt_date_formate(),strtotime($doctordetail['admit_date']));
												}
												else
												{
													echo '-';
												}
												?></td>
												<td class="action">
													<a href="javascript:void(0);" class="show-view-popup btn btn-default" idtest="<?php echo esc_attr($retrieved_data->ID); ?>" type="<?php echo 'view_inpatient';?>">
													<i class="fa fa-eye"></i> <?php esc_html_e('Patient Detail', 'hospital_mgt');?></a>
													
													<?php if($doctordetail['patient_status'] !== 'Discharged')
													{?>
													<a href="javascript:void(0);" class="show-discharge-popup btn btn-default" idtest="<?php echo esc_attr($retrieved_data->ID); ?>" >
													<i class="fa fa-ambulance"></i> <?php esc_html_e('Discharge', 'hospital_mgt');?></a>
													<?php }?>
													
													<?php if($doctordetail['patient_status'] == 'Discharged')
													{?>
													<a href="javascript:void(0);" class="show-discharge_data-popup btn btn-default" idtest="<?php echo esc_attr($retrieved_data->ID); ?>" >
													<i class="fa fa-eye"></i> <?php esc_html_e('Discharge Detail', 'hospital_mgt');?></a>
													<?php }?>
													
													<a href="?page=hmgt_invoice&tab=addinvoice&patient=<?php echo esc_attr($retrieved_data->ID); ?>" class="btn btn-default"> <?php esc_html_e('Billing', 'hospital_mgt' );?></a>
													<a href="?page=hmgt_bedallotment&tab=bedassign&patient_id=<?php echo esc_attr($retrieved_data->ID); ?>" class="btn btn-default"> <?php esc_html_e('Stay', 'hospital_mgt' );?></a>
													<a  href="javascript:void(0);" class="show-popup btn btn-default" idtest="<?php echo esc_attr($retrieved_data->ID); ?>"><i class="fa fa-eye"></i> <?php esc_html_e('View Detail', 'hospital_mgt');?></a>
													<a  href="javascript:void(0);" class="show-charges-popup btn btn-default" idtest="<?php echo esc_attr( $retrieved_data->ID); ?>">
													<i class="fa fa-money"></i> <?php esc_html_e('Charges', 'hospital_mgt');?></a>
													<?php if($user_access_edit == 1)
													{?>
														<a href="?page=hmgt_patient&tab=addpatient&action=edit&patient_id=<?php echo esc_attr($retrieved_data->ID);?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' );?></a>
													<?php 
													} 
													?>
													<?php if($user_access_delete == 1)
													{
														?>	
														<a href="?page=hmgt_patient&tab=patientlist&action=delete&patient_id=<?php echo esc_attr($retrieved_data->ID);?>" class="btn btn-danger" 
														onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this record?','hospital_mgt');?>');">
														<?php esc_html_e( 'Delete', 'hospital_mgt' ) ;?> </a>
														<?php 
													} ?>
												</td>
											</tr>
											<?php
											} 
										}
										?>
										</tbody>
									</table>
									<?php if($user_access_delete == 1)
										{
											?>	
											<div class="print-button pull-left">
												<input  type="submit" value="<?php esc_html_e('Delete Selected','hospital_mgt');?>" name="delete_selected" class="btn btn-danger delete_selected "/>
											</div>
										<?php 
										} ?>
								</div><!--TABLE RESPONSIVE DIV END-->
							</div><!-- PANEL BODY DIV END-->
						</form>
						<?php 
						}
						if($active_tab == 'addpatient')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/patient/add_patient.php';
						}
						if($active_tab == 'addpatient_step2')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/patient/add_patient_step2.php';
						}
						if($active_tab == 'addpatient_step3')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/patient/add_patient_step3.php';
						}
						?>
					</div><!-- PANEL BODY DIV END-->		
		        </div>	<!-- PANEL WHITE DIV END-->
	        </div>
        </div> <!-- ROW DIV END-->
	</div> <!-- MAIN WRAPPER DIV END-->