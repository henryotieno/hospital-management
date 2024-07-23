<?php
MJ_hmgt_browser_javascript_check();
$user_object=new MJ_hmgt_user();
$role='patient';
$patient_type='inpatient';
$obj_bloodbank=new MJ_hmgt_bloodbank();
$current_user_id=get_current_user_id();
//access right function
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
<script type="text/javascript">
$(document).ready(function() {
	"use strict";
	<?php
	if (is_rtl())
		{
		?>	
			$('#guardian_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
			$('#patient_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
			$('#admit_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#guardian_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			$('#patient_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			$('#admit_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>
	$('#symptoms').multiselect(
	{
		nonSelectedText :'<?php esc_html_e('Select Symptoms','hospital_mgt');?>',
		includeSelectAllOption: true,
	    selectAllText : '<?php esc_html_e('Select all','hospital_mgt'); ?>',
		templates: {
	            button: '<button class="multiselect btn btn-default dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="multiselect-selected-text"></span><b class="caret"></b></button>',
	        },
			buttonContainer: '<div class="dropdown" />'
	});
	jQuery('#patient_list').DataTable({ 
		"responsive": true,
		"order": [[ 1, "asc" ]],
		"aoColumns":[
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
	
	$.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
      $('#birth_date').datepicker({
     endDate: '+0d',
        autoclose: true
   }); 
	$('.timepicker').timepicki({
		show_meridian:false,
		min_hour_value:0,
		max_hour_value:23,
		step_size_minutes:15,
		overflow_minutes:true,
		increase_direction:'up',
		disable_keyboard_mobile: true});
	//username not  allow space validation
	$('#username').keypress(function( e ) {
       if(e.which === 32) 
         return false;
    });
	
	        var date = new Date();
            date.setDate(date.getDate()-0);
            $('#admit_date').datepicker({ 
           //startDate: date,
		    autoclose: true
            });
	$("body").on("click",".symptoms_alert",function()
	 {
		var checked = $(".dropdown-menu input:checked").length;

		if(!checked)
		{
			alert("<?php esc_html_e('Please select atleast one Symtoms','hospital_mgt');?>");
			return false;
		}		
	});
} );
</script>
<?php 
$active_tab = isset($_REQUEST['tab'])?$_REQUEST['tab']:'patientlist';
    //save patient data
	if(isset($_POST['save_patient']))
	{
		$nonce = $_POST['_wpnonce'];
		if (wp_verify_nonce( $nonce, 'save_patient_nonce' ) )
		{		
			if(isset($_FILES['upload_user_avatar_image']) && !empty($_FILES['upload_user_avatar_image']) && $_FILES['upload_user_avatar_image']['size'] !=0)
			{
				if($_FILES['upload_user_avatar_image']['size'] > 0)
				 $patient_image=MJ_hmgt_load_documets($_FILES['upload_user_avatar_image'],'upload_user_avatar_image','pimg');
				$patient_image_url=content_url().'/uploads/hospital_assets/'.$patient_image;
			}
			else
			{
				if(isset($_REQUEST['hidden_upload_user_avatar_image']))
					$patient_image=$_REQUEST['hidden_upload_user_avatar_image'];
				$patient_image_url=$patient_image;
			}
			if(isset($_REQUEST['action'])&& $_REQUEST['action']=='insert')
			{
				if( !email_exists( $_POST['email'] ) && !username_exists( $_POST['username'] ))
				{	
					$ext=MJ_hmgt_check_valid_extension($patient_image_url);
					if(!$ext == 0)
					{
					$result=$user_object->MJ_hmgt_add_user($_POST);
					$returnans=update_user_meta( $result,'hmgt_user_avatar',$patient_image_url);
					if($result)
						{
						wp_redirect ( site_url() . '/?dashboard=user&page=patient&tab=addpatient_step2&patient_id='.MJ_hmgt_id_encrypt($result));?>	
					 <?php }
					}
					else
					{  ?>
					<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
					</button>
					<p><p><?php esc_html_e('Sorry, only JPG, JPEG, PNG & GIF files are allowed!.','hospital_mgt');?></p></p>
					</div><?php 
					}
				}
			}
			else			
			{   
				$result=$user_object->MJ_hmgt_add_user($_POST);
				$returnans=update_user_meta( $result,'hmgt_user_avatar',$patient_image_url);
				if($result)
					{
						wp_redirect (site_url(). '/?dashboard=user&page=patient&tab=addpatient_step2&action=edit&patient_id='.MJ_hmgt_id_encrypt($result));?>	
			  <?php }
			}
		}
	}
	//save patient step 3
	if(isset($_POST['save_patient_step3']))
	{
		$nonce = $_POST['_wpnonce'];
		if (wp_verify_nonce( $nonce, 'save_patient_step3_nonce' ) )
		{
			$guardian_data=array('admit_date'=>date(MJ_hmgt_get_format_for_db($_POST['admit_date'])),
			'admit_time'=>$_POST['admit_time'],
			'patient_status'=>$_POST['patient_status'],
			'doctor_id'=>$_POST['doctor'],
			'symptoms'=>implode(",",$_POST['symptoms'])
			);
			if(isset($_REQUEST['action'])&& $_REQUEST['action']=='edit')
			{
				$result=MJ_hmgt_update_guardian($guardian_data,MJ_hmgt_id_decrypt($_REQUEST['patient_id']));
				if($result)
				{
					//patint asign to doctor patient mail template code start
					$doctorid=$_POST['doctor'];
					$doctorinfo=get_userdata($doctorid);
					$doctorname=$doctorinfo->display_name;
					$doctoremail=$doctorinfo->user_email;
					$departmentsname=get_post($doctorinfo->department);
					$dep=$departmentsname->post_title; 
					$userinfo=get_userdata(MJ_hmgt_id_decrypt($_REQUEST['patient_id']));
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
								wp_redirect ( home_url() . '?dashboard=user&page=patient&tab=patientlist&message=2');
				}
			}
			else
			{
				$result=MJ_hmgt_update_guardian($guardian_data,MJ_hmgt_id_decrypt($_REQUEST['patient_id']));
				
				if($result)
				{
				   //patint asign to doctor patient mail template code start
					$doctorid=$_POST['doctor'];
					$doctorinfo=get_userdata($doctorid);
					$doctorname=$doctorinfo->display_name;
					$doctoremail=$doctorinfo->user_email;
					$departmentsname=get_post($doctorinfo->department);
					$dep=$departmentsname->post_title; 
					$userinfo=get_userdata(MJ_hmgt_id_decrypt($_REQUEST['patient_id']));
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
						//patient assign to doctor patient mail template code end
						// patient assign to doctor decor mail template code  start
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
						wp_redirect ( home_url() . '?dashboard=user&page=patient&tab=patientlist&message=1');
				}
			}
		}
    }
	if(isset($_REQUEST['action'])&& $_REQUEST['action']=='delete')
		{
			
			$result=$user_object->MJ_hmgt_delete_usedata(MJ_hmgt_id_decrypt($_REQUEST['patient_id']));
			$result=MJ_hmgt_delete_guardian($_REQUEST['patient_id']);
			if($result)
			{
				wp_redirect ( home_url() . '?dashboard=user&page=patient&tab=patientlist&message=3');
			}
		}
	if(isset($_POST['save_discharge']))
	{
		
		if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'MJ_hmgt_discharge_popup' ))
		{
			$result = $user_object->add_discharge_data($_POST);
			if($result)
			{
				wp_redirect ( home_url() . '?dashboard=user&page=patient&tab=patientlist&message=4');
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
					esc_html_e('Record inserted successfully','hospital_mgt');
				?></p></div>
				<?php 
			
		}
		elseif($message == 2)
		{?><div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
		</button><p><?php
					esc_html_e("Record updated successfully",'hospital_mgt');
					?></p>
					</div>
				<?php 
			
		}
		elseif($message == 3) 
		{?>
		<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
		</button><p>
		<?php 
			esc_html_e('Record deleted successfully','hospital_mgt');
		?></div></p><?php
				
		}
		elseif($message == 4) 
		{?>
		<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
		</button><p>
		<?php 
			esc_html_e('Patient discharge successfully','hospital_mgt');
		?></div></p><?php
				
		}
	}	?>
	
   <div class="panel-body panel-white"><!-- START panel body DIV-->
	    <ul class="nav nav-tabs panel_tabs" role="tablist">
		    <li class="<?php if($active_tab=='patientlist'){?>active<?php }?>">
		
				<a href="?dashboard=user&page=patient&tab=patientlist" class="tab <?php echo $active_tab == 'patientlist' ? 'active' : ''; ?>">
				 <i class="fa fa-align-justify"></i> <?php esc_html_e('Patient List', 'hospital_mgt'); ?></a>

		    </li>
		 
		    <li class="<?php if($active_tab=='addpatient'){?>active<?php }?>">			
				<?php  
				if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
				{?>
					<a href="?dashboard=user&page=patient&tab=addpatient&action=edit&patient_id=<?php echo $_REQUEST['patient_id'];?>" class="tab <?php echo $active_tab == 'addpatient' ? 'active' : ''; ?>">
					<?php esc_html_e('Edit Patient', 'hospital_mgt'); ?></a> 
				<?php 
				}
				else
				{				
					if($user_access['add']=='1')
					{	
					?>
						<a href="?dashboard=user&page=patient&tab=addpatient&&action=insert" class="tab <?php echo $active_tab == 'addpatient' ? 'active' : ''; ?>">
							<i class="fa fa-plus-circle"></i> <?php esc_html_e('Add Patient', 'hospital_mgt'); ?></a> 
						</a>  
			 <?php 
					}
				}
			 ?>
		    </li>
		    <li class="<?php if($active_tab=='addpatient_step2'){?>active<?php }?>">
				<?php  
				if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
				{?>
					<a href="?dashboard=user&page=patient&tab=addpatient_step2&action=edit&patient_id=<?php echo $_REQUEST['patient_id'];?>" class="tab <?php echo $active_tab == 'addpatient_step2' ? 'active' : ''; ?>">
					<?php esc_html_e('Edit Patient Step-2', 'hospital_mgt'); ?></a> 
				<?php
				}
				else
				{
					if($user_access['add']=='1')
					{	
					?>
						<a href="?dashboard=user&page=patient&tab=addpatient_step2&&action=insert" class="tab <?php echo $active_tab == 'addpatient_step2' ? 'active' : ''; ?>">
							<i class="fa fa-plus-circle"></i> <?php esc_html_e('Add New Patient Step-2', 'hospital_mgt'); ?></a> 
						</a>  
			 <?php
					}
				}	
				?>
		    </li>
		    <li class="<?php if($active_tab=='addpatient_step3'){?>active<?php }?>">
		    <?php  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
				{?>
					<a href="?dashboard=user&page=patient&tab=addpatient_step3&action=edit&patient_id=<?php echo $_REQUEST['patient_id'];?>" class="tab <?php echo $active_tab == 'addpatient_step3' ? 'active' : ''; ?>">
					<?php esc_html_e('Edit Patient Step-3', 'hospital_mgt'); ?></a> 
				<?php 
				}
				else
				{
					if($user_access['add']=='1')
					{	
					?>
						<a href="?dashboard=user&page=patient&tab=addpatient_step3&&action=insert" class="tab <?php echo $active_tab == 'addpatient_step3' ? 'active' : ''; ?>">
							<i class="fa fa-plus-circle"></i> <?php esc_html_e('Add New Patient Step-3', 'hospital_mgt'); ?></a> 
						</a>  
					<?php
					} 
				}
			 ?>
		    </li>  
	    </ul>
	<div class="tab-content"><!-- START TAB CONTENT DIV-->
    <?php if($active_tab=='patientlist'){ ?>
		<div class="panel-body"><!-- START PANEL BODY DIV-->
            <div class="table-responsive"><!-- START TABLE RESPONSIVE DIV-->
			    <table id="patient_list" class="display dataTable " cellspacing="0"	><!-- START PATIENT LIST TABLR-->
					<thead>
						<tr>
							<th class="height_width_50"><?php  esc_html_e( 'Photo', 'hospital_mgt' ) ;?></th>
							<th><?php esc_html_e( 'Patient Name', 'hospital_mgt' ) ;?></th>
							<th><?php esc_html_e( 'Patient Id', 'hospital_mgt' ) ;?></th>
							<th> <?php esc_html_e( 'Phone', 'hospital_mgt' ) ;?></th>
							<th> <?php esc_html_e( 'Status', 'hospital_mgt' ) ;?></th>
							<th> <?php esc_html_e( 'Blood Group', 'hospital_mgt' ) ;?></th>
							<th> <?php esc_html_e( 'Assigned Doctor Name', 'hospital_mgt' ) ;?></th>
							<th> <?php esc_html_e( 'Admitted Date', 'hospital_mgt' ) ;?></th>
							<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
						</tr>
				    </thead>
					<tfoot>
						<tr>
							<th><?php  esc_html_e( 'Photo', 'hospital_mgt' ) ;?></th>
							<th><?php esc_html_e( 'Patient Name', 'hospital_mgt' ) ;?></th>
							<th><?php esc_html_e( 'Patient Id', 'hospital_mgt' ) ;?></th>
							<th> <?php esc_html_e( 'Phone', 'hospital_mgt' ) ;?></th>
							<th> <?php esc_html_e( 'Status', 'hospital_mgt' ) ;?></th>
							<th> <?php esc_html_e( 'Blood Group', 'hospital_mgt' ) ;?></th>
							<th> <?php esc_html_e( 'Assigned Doctor Name', 'hospital_mgt' ) ;?></th>
							<th> <?php esc_html_e( 'Admitted Date', 'hospital_mgt' ) ;?></th>
							<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
					   </tr>
					</tfoot>
					<tbody>
						<?php 
						$id=get_current_user_id();
					    $role=MJ_hmgt_get_current_user_role();
						if($role == 'doctor')
					    {
							$own_data=$user_access['own_data'];
							if($own_data == '1')
							{ 
							   $patientlist=$obj_hospital->patient;							   
							}
							else
							{
								$get_patient = array('role' => 'patient','meta_key'=>'patient_type','meta_value'=>'inpatient');
				                $patientlist=get_users($get_patient);
							}
					    }
						elseif($role == 'nurse') 
						{
							$own_data=$user_access['own_data'];
							
							if($own_data == '1')
							{				
								$patient_id_array=MJ_hmgt_nurse_patientid_array();
								$get_patient = array(
									'role' => 'patient',
									'meta_query' => array(
									'relation'    => 'AND',
									array(
											'key' => 'patient_type',
											'value' =>'inpatient',
											'compare' => '='
										),
									array(
											'relation'    => 'OR',
											array(
												'key' => 'created_by',
												'value' =>$current_user_id,
												'compare' => '='
											), 
											array(
												'key' => 'patient_id',
												'value' =>$patient_id_array,
												'compare' => 'IN'
											),
										)
									)
								);
								$patientlist=get_users($get_patient);
							}
							else
							{
								$get_patient = array('role' => 'patient','meta_key'=>'patient_type','meta_value'=>'inpatient');
				                $patientlist=get_users($get_patient);
							}
						}
						elseif($role == 'patient') 
					    {
							$own_data=$user_access['own_data'];
							if($own_data == '1')
							{ 
								$current_user_id=get_current_user_id();
								$usertype=get_user_meta($current_user_id,'patient_type',true);
								
								if($usertype=='inpatient')
								{
									$patientlist=array();
									$patientlist[]=get_userdata($current_user_id);	
								}
							}
							else
							{
								$get_patient = array('role' => 'patient','meta_key'=>'patient_type','meta_value'=>'inpatient');
				                $patientlist=get_users($get_patient);
							}
					    }
						else
						{
							$get_patient = array('role' => 'patient','meta_key'=>'patient_type','meta_value'=>'inpatient');
				            $patientlist=get_users($get_patient);
						}							
						
						if(!empty($patientlist))
						{
							foreach ($patientlist as $retrieved_data)
							{ 
								$doctordetail=MJ_hmgt_get_guardianby_patient($retrieved_data->ID);
											
								$doctor = get_userdata($doctordetail['doctor_id']);
							?>
							<tr>
								<td class="user_image"><?php $uid=$retrieved_data->ID;
									$userimage=get_user_meta($uid, 'hmgt_user_avatar', true);
									if(empty($userimage))
									{
										echo '<img src='.esc_url(get_option( 'hmgt_patient_thumb' )).' height="50px" width="50px" class="img-circle" />';
									}
									else
									{
									  echo '<img src='.esc_url($userimage).' height="50px" width="50px" class="img-circle"/>';
									}
								?></td>
								<td class="name">
								<?php
								if($user_access['edit']=='1')
								{
								?>	
								<a href="?dashboard=user&page=patient&tab=addpatient&action=edit&patient_id=<?php echo MJ_hmgt_id_encrypt(esc_attr($retrieved_data->ID));?>">
								<?php 
								echo esc_html($retrieved_data->display_name);
								?></a>
								<?php
								}
								else
								{
								 ?>
									<a href="javascript:void(0);"><?php
									if(!empty($retrieved_data->display_name))
									{
										echo esc_html($retrieved_data->display_name);
									}
									else
									{
										echo "-";
									}
									?></a>
								<?php
								}
								?>
								</td>
								<td class="patient_id">
								<?php 
									echo get_user_meta($uid, 'patient_id', true);
									
								?>
								
								</td>
								<?php 
								$blood_group=get_user_meta($uid, 'blood_group', true);
								?>
								<td class="phone"><?php echo get_user_meta($uid, 'mobile', true);?></td>
								<td class="email">
									<?php
									$patient_status=MJ_hmgt_get_patient_status($retrieved_data->ID);
									echo esc_html__("$patient_status","hospital_mgt"); 
									?>
								</td>
								<td class="bldgroup"><?php echo esc_html__("$blood_group","hospital_mgt");?></td>

								<td class=""><?php
									if(!empty($doctor->display_name)){
										echo esc_html($doctor->display_name);
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
								<a  href="?dashboard=user&page=patient&action=view_status&patient_id=<?php echo esc_attr($retrieved_data->ID);?>" class="show-popup btn btn-default" idtest="<?php echo esc_attr($retrieved_data->ID); ?>"><i class="fa fa-eye"></i> <?php esc_html_e('View Detail', 'hospital_mgt');?></a>
								
								<?php  $role=MJ_hmgt_get_current_user_role();
								if($role == 'doctor'){ ?>
								<?php if($doctordetail['patient_status'] !== 'Discharged')
								{?>
								<a href="javascript:void(0);" class="show-discharge-popup btn btn-default" idtest="<?php echo $retrieved_data->ID; ?>" >
								<i class="fa fa-ambulance"></i> <?php esc_html_e('Discharge', 'hospital_mgt');?></a>
								<?php }?>
								
								<?php if($doctordetail['patient_status'] == 'Discharged')
								{?>
								<a href="javascript:void(0);" class="show-discharge_data-popup btn btn-default" idtest="<?php echo $retrieved_data->ID; ?>" >
								<i class="fa fa-eye"></i> <?php esc_html_e('Discharge Detail', 'hospital_mgt');?></a>
								<?php }?>
								<?php }?>
								
								<a  href="?dashboard=user&page=patient&action=view_status&patient_id=<?php echo esc_attr($retrieved_data->ID);?>" class="show-charges-popup btn btn-default" idtest="<?php echo esc_attr($retrieved_data->ID); ?>">
								<i class="fa fa-money"></i> <?php esc_html_e('Charges', 'hospital_mgt');?></a>
								<?php
								if($user_access['edit']=='1')
								{
								?>	
									<a href="?dashboard=user&page=patient&tab=addpatient&action=edit&patient_id=<?php echo MJ_hmgt_id_encrypt(esc_attr($retrieved_data->ID));?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' );?></a>
								<?php 
								}
								if($user_access['delete']=='1')
								{
								?>	
									<a href="?dashboard=user&page=patient&tab=patientlist&action=delete&patient_id=<?php echo MJ_hmgt_id_encrypt(esc_attr($retrieved_data->ID));?>" class="btn btn-danger" onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this record?','hospital_mgt');?>');"><?php esc_html_e('Delete', 'hospital_mgt' );?></a>
								<?php 
								}
								?>                
								</td>
							</tr>
							<?php } 
							} 
						?>
					</tbody>
				</table><!-- END PATIENT LIST TABLE-->
 		    </div><!-- END TABLE RESPONSIVE DIV-->
		</div><!-- START PANEL BODY DIV-->
<?php }
		if($active_tab == 'addpatient')
		{ ?>
        <?php 
				$edit=0;
				if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit'){
					$edit=1;
					$user_info = get_userdata(MJ_hmgt_id_decrypt($_REQUEST['patient_id']));
				}
				else
				{
				 	$newpatient=MJ_hmgt_get_lastpatient_id($role);
				}
				?>
        <div class="panel-body"><!-- START PANEL BODY DIV-->
			<form name="patient_form" action="" method="post" class="form-horizontal" id="patient_form" enctype="multipart/form-data"><!-- START OUTPATIENT FORM-->
				<?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
				<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
				<input type="hidden" name="role" value="<?php echo esc_attr($role);?>"  />
				<input type="hidden" name="hmgt_user_avatar" value="<?php echo get_option( 'hmgt_guardian_thumb' );?>"  />
				<input type="hidden" name="patient_type" value="<?php echo esc_attr($patient_type);?>"  />
				<input type="hidden" name="user_id" value="<?php if(isset($_REQUEST['patient_id'])) echo MJ_hmgt_id_decrypt(esc_attr($_REQUEST['patient_id']));?>"  />
				<div class="header">	
				<h3 class="first_hed"><?php esc_html_e('Personal Information','hospital_mgt');?></h3>
				<hr>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="roll_id"><?php esc_html_e('Patient Id','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="patient_id" class="form-control validate[required]" type="text" 
						value="<?php if($edit){ echo esc_attr($user_info->patient_id);}else echo esc_attr($newpatient);?>"  readonly name="patient_id">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="first_name"><?php esc_html_e('First Name','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="first_name" class="form-control validate[required,custom[onlyLetter_specialcharacter]] text-input" maxlength="50" type="text" value="<?php if($edit){ echo esc_attr($user_info->first_name);}elseif(isset($_POST['first_name'])) echo esc_attr($_POST['first_name']);?>" name="first_name">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="middle_name"><?php esc_html_e('Middle Name','hospital_mgt');?></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="middle_name" class="form-control validate[custom[onlyLetter_specialcharacter]]" type="text" maxlength="50" value="<?php if($edit){ echo esc_attr($user_info->middle_name);}elseif(isset($_POST['middle_name'])) echo esc_attr($_POST['middle_name']);?>" name="middle_name">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="last_name"><?php esc_html_e('Last Name','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="last_name" class="form-control validate[required,custom[onlyLetter_specialcharacter]] text-input" maxlength="50" type="text"  value="<?php if($edit){ echo esc_attr($user_info->last_name);}elseif(isset($_POST['last_name'])) echo esc_attr($_POST['last_name']);?>" name="last_name">
					</div>
				</div>
			</div>
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="birth_date"><?php esc_html_e('Date of birth','hospital_mgt');?><span class="require-field">*</span></label>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
							<input id="birth_date" class="form-control validate[required]" type="text"  name="birth_date"
							value="<?php if($edit){ echo date(MJ_hmgt_date_formate(),strtotime($user_info->birth_date));}elseif(isset($_POST['birth_date'])) echo esc_attr($_POST['birth_date']);?>" readonly>
						</div>
						<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="blood_group"><?php esc_html_e('Blood Group','hospital_mgt');?><span class="require-field">*</span></label>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
							<?php if($edit){ $userblood=$user_info->blood_group; }elseif(isset($_POST['blood_group'])){$userblood=$_POST['blood_group'];}else{$userblood='';}?>
							<select id="blood_group" class="form-control validate[required] max_width_100" name="blood_group">
							<option value=""><?php esc_html_e('Select Blood Group','hospital_mgt');?></option>
							<!--<option><?php esc_html_e('Select Blood Group','hospital_mgt');?></option>-->
							<?php foreach(MJ_hmgt_blood_group() as $blood)
							{ ?>
									<option value="<?php echo esc_attr($blood);?>" <?php selected($userblood,$blood);  ?>><?php echo esc_html($blood); ?> </option>
							<?php } ?>
							</select>
						</div>
					</div>
			</div>	
			
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="gender"><?php esc_html_e('Gender','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
					<?php $genderval = "male"; if($edit){ $genderval=$user_info->gender; }elseif(isset($_POST['gender'])) {$genderval=$_POST['gender'];}?>
						<label class="radio-inline">
						 <input type="radio" value="male" class="tog" name="gender"  <?php  checked( 'male', $genderval);  ?>/><?php esc_html_e('Male','hospital_mgt');?>
						</label>
						<label class="radio-inline">
						  <input type="radio" value="female" class="tog" name="gender"  <?php  checked( 'female', $genderval);  ?>/><?php esc_html_e('Female','hospital_mgt');?> 
						</label>
					</div>
				</div>
			</div>
			<div class="header">
				<h3><?php esc_html_e('HomeTown Address Information','hospital_mgt');?></h3>
				<hr>
			</div> 
			
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="address"><?php esc_html_e('Home Town Address','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="address" class="form-control validate[required,custom[address_description_validation]]" type="text" maxlength="150" name="address" 
						value="<?php if($edit){ echo esc_attr($user_info->address);}elseif(isset($_POST['address'])) echo esc_attr($_POST['address']);?>">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="city_name"><?php esc_html_e('City','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="city_name" class="form-control validate[required,custom[city_state_country_validation]]" type="text" maxlength="50" name="city_name" 
						value="<?php if($edit){ echo esc_attr($user_info->city_name);}elseif(isset($_POST['city_name'])) echo esc_attr($_POST['city_name']);?>">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="state_name"><?php esc_html_e('State','hospital_mgt');?></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="state_name" class="form-control validate[custom[city_state_country_validation]]" type="text" maxlength="50"  name="state_name" 
						value="<?php if($edit){ echo esc_attr($user_info->state_name);}elseif(isset($_POST['state_name'])) echo esc_attr($_POST['state_name']);?>">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="country_name"><?php esc_html_e('Country','hospital_mgt');?></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="country_name" class="form-control validate[custom[city_state_country_validation]]" type="text" maxlength="50" name="country_name" 
						value="<?php if($edit){ echo esc_attr($user_info->country_name);}elseif(isset($_POST['country_name'])) echo esc_attr($_POST['country_name']);?>">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="zip_code"><?php esc_html_e('Zip Code','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="zip_code" class="form-control  validate[required,custom[onlyLetterNumber]]" type="text" maxlength="15"  name="zip_code" 
						value="<?php if($edit){ echo esc_attr($user_info->zip_code);}elseif(isset($_POST['zip_code'])) echo esc_attr($_POST['zip_code']);?>">
					</div>
				</div>
			</div>
			<div class="header">
				<h3><?php esc_html_e('Contact Information','hospital_mgt');?></h3>
				<hr>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="mobile"><?php esc_html_e('Mobile Number','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 margin_bottom_5px">
					<input type="text" value="<?php if($edit) { if(!empty($user_info->phonecode)){ echo esc_attr($user_info->phonecode); }else{ ?>+<?php echo MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }  }elseif(isset($_POST['phonecode'])){ echo esc_attr($_POST['phonecode']); }else{ ?>+<?php echo MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }?>"  class="form-control  validate[required] onlynumber_and_plussign" name="phonecode" maxlength="5">
					</div>
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 has-feedback">
						<input id="mobile" class="form-control validate[required,custom[phone_number]] text-input" minlength="6" maxlength="15" type="text" value="<?php if($edit){ echo esc_attr($user_info->mobile);}elseif(isset($_POST['mobile'])) echo esc_attr($_POST['mobile']);?>" name="mobile">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="phone"><?php esc_html_e('Phone','hospital_mgt');?></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="phone" class="form-control validate[custom[phone_number]] text-input" minlength="6" maxlength="15" type="text" value="<?php if($edit){ echo esc_attr($user_info->phone);}elseif(isset($_POST['phone'])) echo esc_attr($_POST['phone']);?>" name="phone">			
					</div>
				</div>
			</div>
			<div class="header">
				<h3><?php esc_html_e('Login Information','hospital_mgt');?></h3>
				<hr>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="email"><?php esc_html_e('Email','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="email" class="form-control validate[required,custom[email]] text-input" type="text" maxlength="100" name="email" 
						value="<?php if($edit){ echo esc_attr($user_info->user_email);}elseif(isset($_POST['email'])) echo esc_attr($_POST['email']);?>">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="username"><?php esc_html_e('User Name','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="username" class="form-control validate[required,custom[username_validation]]" type="text" maxlength="30" name="username" 
						value="<?php if($edit){ echo esc_attr($user_info->user_login);}elseif(isset($_POST['username'])) echo esc_attr($_POST['username']);?>" <?php if($edit) echo "readonly";?>>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="password"><?php esc_html_e('Password','hospital_mgt');?><?php if(!$edit) {?><span class="require-field">*</span><?php }?></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="password" class="form-control <?php if(!$edit) echo 'validate[required,minSize[8]]';?>" type="password"  maxlength="12"  name="password" value="">
					</div>
				</div>
			</div>
			<div class="header">
				<h3><?php esc_html_e('Other Information','hospital_mgt');?></h3>
				<hr>
			</div>
			<?php wp_nonce_field( 'save_patient_nonce' ); ?>
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="photo"><?php esc_html_e('Image','hospital_mgt');?></label>
						<div class="col-sm-3">
							<input type="hidden" id="hmgt_user_avatar_url" class="form-control" name="hmgt_user_avatar_url"  
							value="<?php if($edit)echo esc_url( $user_info->hmgt_user_avatar );elseif(isset($_POST['upload_user_avatar_image'])) echo $_POST['upload_user_avatar_image']; ?>" readonly />
							<input type="hidden" name="hidden_upload_user_avatar_image" 
							value="<?php if($edit){ echo $user_info->hmgt_user_avatar;}elseif(isset($_POST['upload_user_avatar_image'])) echo $_POST['upload_user_avatar_image'];
							else echo get_option('hmgt_patient_thumb');?>">
		       				 <input id="upload_user_avatar_image" name="upload_user_avatar_image" type="file" class="form-control file" value="<?php esc_html_e( 'Upload image', 'hospital_mgt' ); ?>" />
					</div>
					<div class="clearfix"></div>
					
					<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
		                <div id="upload_user_avatar_preview" >
						<?php 
							if($edit) 
							{
								if($user_info->hmgt_user_avatar == "")
								{	?>
									<img class="image_preview_css" alt="" src="<?php echo get_option( 'hmgt_patient_thumb' ); ?>">
								<?php 
								}
								else 
								{
								?>
									<img class="image_preview_css" src="<?php if($edit) echo esc_url( $user_info->hmgt_user_avatar ); ?>" />
								<?php 
								}
							}
							else 
							{
								?>
								<img class="image_preview_css" alt="" src="<?php echo get_option( 'hmgt_patient_thumb' ); ?>">
								<?php 
							}?>
						</div>
					</div>
				</div>
			</div>
			<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">        	
				<input type="submit" value="<?php if($edit){ esc_html_e('Save And Next Step','hospital_mgt'); }else{ esc_html_e('Save And Next Step','hospital_mgt');}?>" name="save_patient" class="btn btn-success patient_btn1"/>
			</div>
		    </form><!-- END OUTPATIENT FORM-->
        </div><!-- END PANEL BODY DIV-->
     <?php } 
		if($active_tab == 'addpatient_step2')
		{ ?>
		<?php 
		$patient_id=0;
		if(isset($_REQUEST['patient_id']))
			$patient_id=MJ_hmgt_id_decrypt($_REQUEST['patient_id']);
		$patient_no=get_user_meta($patient_id, 'patient_id', true);
		if(isset($_POST['save_patient_step2']))
		{
			$nonce = $_POST['_wpnonce'];
			if (wp_verify_nonce( $nonce, 'save_patient_step2_nonce' ) )
			{
				if(isset($_FILES['upload_user_avatar_image']) && !empty($_FILES['upload_user_avatar_image']) && $_FILES['upload_user_avatar_image']['size'] !=0)
					{
						if($_FILES['upload_user_avatar_image']['size'] > 0)
						 $patient_image=MJ_hmgt_load_documets($_FILES['upload_user_avatar_image'],'upload_user_avatar_image','pimg');
						$patient_image_url=content_url().'/uploads/hospital_assets/'.$patient_image;
					}
					else
					{
						if(isset($_REQUEST['hidden_upload_user_avatar_image']))
							$patient_image=$_REQUEST['hidden_upload_user_avatar_image'];
						$patient_image_url=$patient_image;
					}
		       $guardian_data=array('guardian_id'=>$_POST['guardian_id'],
						'patient_id'=>MJ_hmgt_id_decrypt($_REQUEST['patient_id']),
						'first_name'=>MJ_hmgt_strip_tags_and_stripslashes($_POST['first_name']),
						'middle_name'=>MJ_hmgt_strip_tags_and_stripslashes($_POST['middle_name']),
						'last_name'=>MJ_hmgt_strip_tags_and_stripslashes($_POST['last_name']),
						'gr_gender'=>$_POST['gender'],
						'gr_address'=>MJ_hmgt_strip_tags_and_stripslashes($_POST['address']),
						'gr_city'=>MJ_hmgt_strip_tags_and_stripslashes($_POST['city_name']),
						'gr_state'=>MJ_hmgt_strip_tags_and_stripslashes($_POST['state_name']),
						'gr_zipcode'=>$_POST['zip_code'],
						'gr_phone'=>$_POST['phone'],
						'gr_mobile'=>$_POST['mobile'],						
						'gr_relation'=>MJ_hmgt_strip_tags_and_stripslashes($_POST['guardian_realtion']),
						'image'=>$patient_image_url,
						'inpatient_create_date'=>date("Y-m-d H:i:s"),'inpatient_create_by'=>get_current_user_id());
		
				if(isset($_REQUEST['action'])&& $_REQUEST['action']=='edit')
				{
					$guardian_info = MJ_hmgt_get_guardianby_patient(MJ_hmgt_id_decrypt($_REQUEST['patient_id']));
					
					if($guardian_info!="")
					{
						$image_url=$guardian_data['image'];
						$ext=MJ_hmgt_check_valid_extension($image_url);
						if(!$ext == 0)
						{		
							$patient_id=MJ_hmgt_id_decrypt($_REQUEST['patient_id']);
							$result=MJ_hmgt_update_guardian($guardian_data,$patient_id);
							if($result)
							{
								wp_redirect ( site_url() . '/?dashboard=user&page=patient&tab=addpatient_step3&patient_id='.MJ_hmgt_id_encrypt($patient_id).'&action=edit');						 
							}
						}					 
						else
						{   ?>
								<div id="message" class="updated below-h2">
									<p><p><?php esc_html_e('Sorry, only JPG, JPEG, PNG & GIF files are allowed.','hospital_mgt');?></p></p>
								</div>
							<?php 					
						}
					}	
					else
					{				
						 $image_url=$guardian_data['image'];
						$ext=MJ_hmgt_check_valid_extension($image_url);
						if(!$ext == 0)
						{
							$result=MJ_hmgt_add_guardian($guardian_data,'');
							 if($result)
							 {
								 wp_redirect ( site_url() . '/?dashboard=user&page=patient&tab=addpatient_step3&patient_id='.MJ_hmgt_id_encrypt($_REQUEST['patient_id']).'&action=edit');									
							 }
						}
							 
						else 
						{   ?>
							<div id="message" class="updated below-h2">
								<p><p><?php esc_html_e('Sorry, only JPG, JPEG, PNG & GIF files are allowed.','hospital_mgt');?></p></p>
							</div>
						<?php 					
						}				
					}	
				}
				else
				{
						$ext=MJ_hmgt_check_valid_extension($guardian_data['image']);
						if(!$ext == 0)
						{
							$result=MJ_hmgt_add_guardian($guardian_data,'');
							 if($result)
							{
								wp_redirect ( site_url() . '/?dashboard=user&page=patient&tab=addpatient_step3&patient_id='.$_REQUEST['patient_id']);		
							 }
						}
						else{ 
						?>
						<div id="message" class="updated below-h2">
							<p><p><?php esc_html_e('Sorry, only JPG, JPEG, PNG & GIF files are allowed!.','hospital_mgt');?></p></p>
						</div>
						<?php 
						   }
				}
			}			
	    }
		$edit=0;
		if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit'){
			$edit=1;
			$user_info = MJ_hmgt_get_guardianby_patient($patient_id);
		}
		?>
        <div class="panel-body"><!-- START PANEL BODY DIV-->
			<form name="guardian_form" action="" method="post" class="form-horizontal" id="guardian_form" enctype="multipart/form-data"><!-- START GUARDIAN FORM-->
				<?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
				<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
				<input type="hidden" name="role" value="<?php echo esc_attr($role);?>"  />
				<div class="header">	
				<h3 class="first_hed"><?php esc_html_e('Guardian Information','hospital_mgt');?></h3>
				<hr>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="guardian_number"><?php esc_html_e('Guardian Id','hospital_mgt');?></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="guardian_id" class="form-control " min="0" type="number" onKeyPress="if(this.value.length==6) return false;"
						value="<?php if($edit){ if(isset($user_info['guardian_id'])) { print esc_attr($user_info['guardian_id']); } } elseif(isset($_POST['guardian_id'])) echo esc_attr($_POST['guardian_id']);?>"   name="guardian_id">
					</div>

					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="patient_id"><?php esc_html_e('Patient Id','hospital_mgt');?></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						
						<input id="patient_no" class="form-control" type="text" 
						value="<?php if($edit){ echo get_user_meta($patient_id, 'patient_id', true);}elseif(isset($patient_no)) echo esc_attr($patient_no);?>"
						name="patient_no" readonly>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="first_name"><?php esc_html_e('First Name','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="first_name" class="form-control validate[required,custom[onlyLetter_specialcharacter]] text-input" maxlength="50" type="text" value="<?php if($edit){ if(isset($user_info['first_name'])) { echo esc_attr($user_info['first_name']);} }elseif(isset($_POST['first_name'])) echo esc_attr($_POST['first_name']);?>" name="first_name">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="middle_name"><?php esc_html_e('Middle Name','hospital_mgt');?></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="middle_name" class="form-control validate[custom[onlyLetter_specialcharacter]]" type="text" maxlength="50" value="<?php if($edit){ if(isset($user_info['middle_name'])) {  echo esc_attr($user_info['middle_name']);}}elseif(isset($_POST['middle_name'])) echo esc_attr($_POST['middle_name']);?>" name="middle_name">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="last_name"><?php esc_html_e('Last Name','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="last_name" class="form-control validate[required,custom[onlyLetter_specialcharacter]] text-input" maxlength="50" type="text"  value="<?php if($edit){ if(isset($user_info['last_name'])) {  echo esc_attr($user_info['last_name']);} }elseif(isset($_POST['last_name'])) echo esc_attr($_POST['last_name']);?>" name="last_name">
					</div>
					 <label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="gender"><?php esc_html_e('Gender','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
					<?php $genderval = "male"; if($edit){  if(empty($user_info['gr_gender'])){ $genderval = "male"; }elseif(isset($user_info['gr_gender'])){ $genderval=$user_info['gr_gender']; } } elseif(isset($_POST['gender'])) {$genderval=$_POST['gender'];}?>
						<label class="radio-inline">
						 <input type="radio" value="male" class="tog" name="gender"  <?php  checked( 'male', $genderval); ?>/><?php esc_html_e('Male','hospital_mgt');?>	
						</label>
						<label class="radio-inline">
						  <input type="radio" value="female" class="tog" name="gender"  <?php  checked( 'female', $genderval);?>/><?php esc_html_e('Female','hospital_mgt');?> 
						</label>	
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="guardian_realtion"><?php esc_html_e('Relation With Patient','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="guardian_realtion" class="form-control validate[required,custom[onlyLetter_specialcharacter]] text-input" type="text" maxlength="30" name="guardian_realtion" 
						value="<?php if($edit){ if(isset($user_info['gr_relation'])) {  echo esc_attr($user_info['gr_relation']);} }elseif(isset($_POST['guardian_realtion'])) echo esc_attr($_POST['guardian_realtion']);?>">
					</div>
				</div>
			</div>
			<div class="header">
				<h3><?php esc_html_e('HomeTown Address Information','hospital_mgt');?></h3>
				<hr>
			</div> 
			
		    <div class="form-group">
		    	<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="address"><?php esc_html_e('Home Town Address','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="address" class="form-control validate[required,custom[address_description_validation]]" type="text" maxlength="150" name="address" 
						value="<?php if($edit) { if(isset($user_info['gr_address'])) { echo esc_attr($user_info['gr_address']); } } elseif(isset($_POST['address'])) echo esc_attr($_POST['address']);?>">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="city_name"><?php esc_html_e('City','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="city_name" class="form-control validate[required,custom[city_state_country_validation]]" maxlength="50" type="text"  name="city_name" 
						value="<?php if($edit){ if(isset($user_info['gr_city'])) { echo esc_attr($user_info['gr_city']);} }elseif(isset($_POST['city_name'])) echo esc_attr($_POST['city_name']);?>">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="state_name"><?php esc_html_e('State','hospital_mgt');?></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="state_name" class="form-control validate[custom[city_state_country_validation]]" maxlength="50" type="text"  name="state_name" 
						value="<?php if($edit){ if(isset($user_info['gr_state'])) {  echo esc_attr($user_info['gr_state']);} }elseif(isset($_POST['state_name'])) echo esc_attr($_POST['state_name']);?>">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="country_name"><?php esc_html_e('Country','hospital_mgt');?></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="country_name" class="form-control validate[custom[city_state_country_validation]]" maxlength="50" type="text"  name="country_name" 
						value="<?php if($edit){ if(isset($user_info['gr_country'])) {  echo esc_attr($user_info['gr_country']);} }elseif(isset($_POST['country_name'])) echo esc_attr($_POST['country_name']);?>">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="zip_code"><?php esc_html_e('Zip Code','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="zip_code" class="form-control  validate[required,custom[onlyLetterNumber]]" type="text" maxlength="15" name="zip_code" 
						value="<?php if($edit){  if(isset($user_info['gr_zipcode'])) { echo esc_attr($user_info['gr_zipcode']);} }elseif(isset($_POST['zip_code'])) echo esc_attr($_POST['zip_code']);?>">
					</div>
				</div>
			</div>
			<div class="header">
				<h3><?php esc_html_e('Contact Information','hospital_mgt');?></h3>
				<hr>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="mobile"><?php esc_html_e('Mobile Number','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="mobile" class="form-control validate[required,custom[phone_number]] text-input" minlength="6" maxlength="15" type="text" value="<?php if($edit){  if(isset($user_info['gr_mobile'])) {  echo esc_attr($user_info['gr_mobile']);} }elseif(isset($_POST['mobile'])) echo esc_attr($_POST['mobile']);?>" name="mobile">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="phone"><?php esc_html_e('Phone','hospital_mgt');?></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="phone" class="form-control validate[custom[phone_number]] text-input" minlength="6" maxlength="15" type="text" value="<?php if($edit){  if(isset($user_info['gr_phone'])) {  echo esc_attr($user_info['gr_phone']);} }elseif(isset($_POST['phone'])) echo esc_attr($_POST['phone']);?>" name="phone">
					</div>
				</div>
			</div>
			<div class="header">
				<h3><?php esc_html_e('Other Information','hospital_mgt');?></h3>
				<hr>
			</div>
				<?php wp_nonce_field( 'save_patient_step2_nonce' ); ?>
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="photo"><?php esc_html_e('Image','hospital_mgt');?></label>
						<div class="col-sm-3">
							<input type="hidden" id="hmgt_user_avatar_url" class="form-control" name="hmgt_user_avatar"  
							value="<?php if($edit)echo esc_url($user_info['image']);elseif(isset($_POST['hmgt_user_avatar'])) echo $_POST['hmgt_user_avatar']; ?>" readonly />
							<input type="hidden" name="hidden_upload_user_avatar_image" 
							value="<?php if($edit){ echo $user_info->hmgt_user_avatar;}elseif(isset($_POST['upload_user_avatar_image'])) echo $_POST['upload_user_avatar_image'];
							else echo get_option('hmgt_patient_thumb');?>">
		       				 <input id="upload_user_avatar_image" name="upload_user_avatar_image" type="file" class="form-control file" value="<?php esc_html_e( 'Upload image', 'hospital_mgt' ); ?>" />
					</div>
					<div class="clearfix"></div>
					
					<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
		                <div id="upload_user_avatar_preview" >
						<?php 
							if($edit) 
							{
								if($user_info['image'] == "")
								{	?>
									<img class="image_preview_css" alt="" src="<?php echo get_option( 'hmgt_guardian_thumb' ); ?>">
								<?php 
								}
								else 
								{
								?>
									<img class="image_preview_css" src="<?php if($edit) echo esc_url( $user_info['image']); ?>" />
								<?php 
								}
							}
							else 
							{
								?>
								<img class="image_preview_css" alt="" src="<?php echo get_option( 'hmgt_guardian_thumb' ); ?>">
								<?php 
							}?>
						</div>
					</div>
				</div>
			</div>		
				<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
					<a href="?dashboard=user&page=patient&tab=addpatient&action=edit&patient_id=<?php echo MJ_hmgt_id_encrypt($patient_id); ?>">
					<input type="button" value="<?php if($edit){ esc_html_e('Back To Last Step','hospital_mgt'); }else{ esc_html_e('Back To Last Step','hospital_mgt');}?>" name="back_step" class="btn btn-success margin_bottom_5px"/>
					</a>
					<input type="submit" value="<?php if($edit){ esc_html_e('Save And Next Step','hospital_mgt'); }else{ esc_html_e('Save And Next Step','hospital_mgt');}?>" name="save_patient_step2" class="btn btn-success margin_bottom_5px"/>
					
				</div>
				
			</form><!-- END GUARDIAN FORM-->
        </div><!-- END PANEL BODY DIV-->
	<?php }
		if($active_tab == 'addpatient_step3')
		{
			$patient_id=0;
			if(isset($_REQUEST['patient_id']))
			$patient_id=MJ_hmgt_id_decrypt($_REQUEST['patient_id']);
			$patient_no=get_user_meta($patient_id,'patient_id', true);
	
        	$edit=0;
				if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
				{
					$edit=1;
					$user_info = MJ_hmgt_get_guardianby_patient(MJ_hmgt_id_decrypt($_REQUEST['patient_id']));
					$doctordetail=MJ_hmgt_get_guardianby_patient(MJ_hmgt_id_decrypt($_REQUEST['patient_id']));
					 
					if(!empty($doctordetail))
					{
						$doctor = get_userdata($doctordetail['doctor_id']);						
					}
					else
					{
						$doctor="";
					}
					
				}?>
		   <!-- POP up code -->
			<div class="popup-bg zindex_100000">
				<div class="overlay-content">
					<div class="modal-content">
						<div class="category_list">
						</div>
					 
					</div>
				</div> 
			</div>
			<!-- End POP-UP Code -->
			<div class="panel-body"><!-- START PANEL BODY DIV-->
				<form name="admit_form" action="" method="post" class="form-horizontal" id="admit_form"><!-- START PATIENT ADMIT FORM-->
					 <?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
					<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
					<input type="hidden" name="role" value="<?php echo esc_attr($role);?>"  />
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="admit_date"><?php esc_html_e('Admit Date','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-sm-8">
								<input id="admit_date" class="form-control validate[required]" type="text" 
								value="<?php if($edit){  if(isset($user_info['admit_date']))  { if(!empty($user_info['admit_date']!='0000-00-00')) { echo date(MJ_hmgt_date_formate(),strtotime($user_info['admit_date'])); } } }elseif(isset($_POST['admit_date'])) echo esc_attr($_POST['admit_date']);?>" name="admit_date" autocomplete = "off">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="admit_time"><?php esc_html_e('Admit Time','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-sm-8">
								<input id="admit_time" class="form-control validate[required] timepicker"  type="text" value="<?php if($edit){ if(isset($user_info['admit_time']))  { echo $user_info['admit_time'];} }elseif(isset($_POST['admit_time'])) echo esc_attr($_POST['admit_time']);?>" name="admit_time" placeholder="H:M">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="patient_status"><?php esc_html_e('Patient Status','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-sm-8" >
								<?php if($edit){ if(isset($user_info['patient_status']))  {  $patient_status=$user_info['patient_status']; } else{ $patient_status=''; } }elseif(isset($_POST['patient_status'])){$patient_status=$_POST['patient_status'];}else{$patient_status='';}?>
								<select name="patient_status" class="form-control validate[required]" >
								<option value=""><?php esc_html_e('Select Patient Status','hospital_mgt');?></option>
								<?php foreach(MJ_hmgt_admit_reason() as $reason)
								{?>
									<option value="<?php echo esc_attr($reason);?>" <?php selected($patient_status,$reason);?>><?php echo esc_html($reason);?></option>
								<?php }?>				
								</select>				
							</div>
						</div>	
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="doctor"><?php esc_html_e('Assign Doctor','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-sm-8">
							
							<?php if($edit) { if(!empty($doctor)) $doctorid=$doctor->ID;  else $doctorid=""; }elseif(isset($_POST['doctor'])){$doctorid=$_POST['doctor'];}else{$doctorid='';}?>
								<select name="doctor" class="form-control validate[required]">
								<option value=""><?php esc_html_e('Select Doctor','hospital_mgt');?></option>
								<?php 
									if($obj_hospital->role == 'doctor') 
									{
										$get_doctor = get_current_user_id();
										$doctordata=array();
										$doctordata[]=get_userdata($get_doctor);
									}
									else
									{
										$get_doctor = array('role' => 'doctor');
										$doctordata=get_users($get_doctor);
									}	
									 if(!empty($doctordata))
									 {
										foreach ($doctordata as $retrieved_data){?>
											<option value="<?php echo esc_attr($retrieved_data->ID); ?>" <?php selected($doctorid,$retrieved_data->ID);?>><?php echo esc_html($retrieved_data->display_name);?> - <?php echo MJ_hmgt_doctor_specialization_title($retrieved_data->ID); ?></option>
										<?php }
									 }
						?>
								</select>
							</div>
						</div>
					</div>
					<?php wp_nonce_field( 'save_patient_step3_nonce' ); ?>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 col-md-2 control-label form-label" for="symptoms"><?php esc_html_e('Symptoms','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-sm-2 col-md-3 multiselect_validation_symtoms margin_bottom_5px">
								<select class="form-control symptoms_list" multiple="multiple" name="symptoms[]" id="symptoms">
								<!--<option value=""><?php esc_html_e('Select Symptoms','hospital_mgt');?></option>-->
								<?php 
								
								$symptoms_category = $user_object->MJ_hmgt_getPatientSymptoms();
								
								if(!empty($symptoms_category))
								{
									foreach ($symptoms_category as $retrive_data)
									{
										$symptoms_array=explode(",",$doctordetail['symptoms']);
										?>
										<option value="<?php echo esc_attr($retrive_data->ID); ?>" <?php if(in_array($retrive_data->ID,$symptoms_array)){ echo 'selected'; } ?>><?php echo esc_html($retrive_data->post_title); ?></option>
										<?php
									}
								}
								?>					
								</select>
								<br>					
							</div>
								<div class="col-sm-3 col-md-3"><button id="addremove" model="symptoms"><?php esc_html_e('Add Or Remove','hospital_mgt');?></button></div>
							</div>
					</div>
					
					<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12 patient_btn1">
						<a href="?dashboard=user&&page=patient&tab=addpatient_step2&action=edit&patient_id=<?php echo MJ_hmgt_id_encrypt($patient_id);?>"><input type="button" value="<?php  esc_html_e('Back To Last Step','hospital_mgt');?>" name="back_step" class="btn btn-success margin_bottom_5px" /></a>
					<input type="submit" value="<?php  esc_html_e('Save Patient','hospital_mgt'); ?>" name="save_patient_step3" class="btn btn-success symptoms_alert margin_bottom_5px"/>
					</div>
						
				</form><!-- END Patient ADMIT FORM !-->
			</div><!-- END PANEL BODY DIV-->
<?php } ?>
</div><!-- END TAB CONTENT DIV-->