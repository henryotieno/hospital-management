<?php
MJ_hmgt_browser_javascript_check();
$user_object=new MJ_hmgt_user();
$active_tab=isset($_REQUEST['tab'])?$_REQUEST['tab']:'receptionistlist'; 
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
//SAVE RECEPTIONIST DATA
if(isset($_POST['save_receptionist']))
{
	$nonce = $_POST['_wpnonce'];
	if (wp_verify_nonce( $nonce, 'save_receptionist_nonce' ) )
	{
		if(isset($_FILES['upload_user_avatar_image']) && !empty($_FILES['upload_user_avatar_image']) && $_FILES['upload_user_avatar_image']['size'] !=0)
		{
			if($_FILES['upload_user_avatar_image']['size'] > 0)
			{
			 $support_staff_image=MJ_hmgt_load_documets($_FILES['upload_user_avatar_image'],'upload_user_avatar_image','pimg');
			$support_staff_url=content_url().'/uploads/hospital_assets/'.$support_staff_image;
			}
			else 
			{
				$support_staff_image=$_REQUEST['hidden_upload_user_avatar_image'];
			$support_staff_url=$support_staff_image;
			}
				
		}
		else
		{		
			if(isset($_REQUEST['hidden_upload_user_avatar_image']))
				$support_staff_image=$_REQUEST['hidden_upload_user_avatar_image'];
			$support_staff_url=$support_staff_image;
		}
		
		if(isset($_REQUEST['action'])&& $_REQUEST['action']=='edit')
		{
			$ext=MJ_hmgt_check_valid_extension($support_staff_url);
			if(!$ext == 0)
			{
				$result=$user_object->MJ_hmgt_add_user($_POST);
				$returnans=update_user_meta( $result,'hmgt_user_avatar',$support_staff_url);	
				if($result)
				{
					wp_redirect ( home_url() . '?dashboard=user&page=supportstaff&tab=receptionistlist&message=2');
						
				}
			}
			else
			{ ?>
				<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
				</button>
					<p><?php esc_html_e('Sorry, Only JPG, JPEG, PNG & GIF Files Are Allowed.','hospital_mgt');?></p>
				</div>
			<?php 	
			}
		}
		else
		{
			if( !email_exists( $_POST['email'] ) && !username_exists( $_POST['username'] ))
			{
			$ext=MJ_hmgt_check_valid_extension($support_staff_url);
			if(!$ext == 0)
			{
		
				$result=$user_object->MJ_hmgt_add_user($_POST);
				$returnans=update_user_meta( $result,'hmgt_user_avatar',$support_staff_url);
				if($result)
				{
					wp_redirect ( home_url() . '?dashboard=user&page=supportstaff&tab=receptionistlist&message=1');
				}
					
			}
						
			else{ ?>
				<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
				</button>
					<p><p><?php esc_html_e('Sorry, Only JPG, JPEG, PNG & GIF Files Are Allowed.','hospital_mgt');?></p></p>
				</div>
				<?php 
				}
			}
				else
			{?>
					<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
					</button>
					<p><p><?php esc_html_e('Username Or Emailid All Ready Exist.','hospital_mgt');?></p></p>
					</div>
							
		  <?php }
		}	
	}
}
if(isset($_REQUEST['action'])&& $_REQUEST['action']=='delete')
{
	$result=$user_object->MJ_hmgt_delete_usedata(MJ_hmgt_id_decrypt($_REQUEST['receptionist_id']));
	if($result)
	{
		wp_redirect ( home_url() . '?dashboard=user&page=supportstaff&tab=receptionistlist&message=3');
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
				esc_html_e("Record updated successfully.",'hospital_mgt');
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
}	 
?>
<!-- POP up code -->
<div class="popup-bg">
    <div class="overlay-content">
		<div class="modal-content">
		  <div class="category_list"></div>		 
        </div>
    </div> 
</div>
<!-- End POP-UP Code -->
<script type="text/javascript">
jQuery(document).ready(function() {
	"use strict";
	jQuery('#staff_list').DataTable({
		"responsive": true,		
		"order": [[ 1, "asc" ]],
		"aoColumns":[
	                  {"bSortable": false},
	                  {"bSortable": true},
	                  {"bSortable": true},
	                  {"bSortable": true},
	                  {"bSortable": true},
					  {"bSortable": false}
							
						],
		language:<?php echo MJ_hmgt_datatable_multi_language();?>			  
	});
} );
</script>
<div class="panel-body panel-white"><!-- START PANEL BODY DIV-->
    <ul class="nav nav-tabs panel_tabs" role="tablist">
        <li class="<?php if($active_tab=='receptionistlist'){?>active<?php }?>">
          <a href="?dashboard=user&page=supportstaff&tab=receptionistlist" class="tab <?php echo $active_tab == 'receptionistlist' ? 'active' : ''; ?>" >
             <i class="fa fa-align-justify"></i> <?php esc_html_e('Support Staff List', 'hospital_mgt'); ?></a>
          </a>
        </li>
	    <li class="<?php if($active_tab=='addreceptionist'){?>active<?php }?>">
	   <?php  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
		{?>
			<a href="?dashboard=user&page=supportstaff&tab=addreceptionist&&action=edit&receptionist_id=<?php echo $_REQUEST['receptionist_id'];?>" class="tab <?php echo $active_tab == 'addreceptionist' ? 'active' : ''; ?>">
			<i class="fa fa"></i> <?php esc_html_e('Edit Support Staff', 'hospital_mgt'); ?></a>
		 <?php 
		}
		else
		{
			if($user_access['add']=='1')
			{			
			?>				
				<a href="?dashboard=user&page=supportstaff&tab=addreceptionist&&action=insert" class="tab <?php echo $active_tab == 'addreceptionist' ? 'active' : ''; ?>">
				<i class="fa fa-plus-circle"></i> <?php esc_html_e('Add New Support Staff', 'hospital_mgt'); ?></a>
			<?php
			}
		}
		?>	  
	    </li>
    </ul>
<?php
if($active_tab=='receptionistlist')
{
?>
	<div class="tab-content"><!-- START TAB CONTENT DIV-->
		<div class="panel-body"><!-- START PANEL BODY DIV-->
            <div class="table-responsive"><!-- START TABLE RESPONSIVE DIV-->
				    <table id="staff_list" class="display dataTable " cellspacing="0" width="100%"><!-- START STAFF LIST TABLE-->
							<thead>
								<tr>
									<th class="height_width_50"><?php  esc_html_e( 'Photo', 'hospital_mgt' ) ;?></th>
									<th><?php esc_html_e( 'Name', 'hospital_mgt' ) ;?></th>
									<th><?php esc_html_e( 'Department', 'hospital_mgt' ) ;?></th>
									<th> <?php esc_html_e( 'Mobile Number', 'hospital_mgt' ) ;?></th>
								    <th> <?php esc_html_e( 'Email', 'hospital_mgt' ) ;?></th>
									<th> <?php esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
								</tr>
							</thead>
							<tfoot>
								<tr>
								  <th><?php  esc_html_e( 'Photo', 'hospital_mgt' ) ;?></th>
								  <th><?php esc_html_e( 'Name', 'hospital_mgt' ) ;?></th>
								  <th><?php esc_html_e( 'Department', 'hospital_mgt' ) ;?></th>
								  <th> <?php esc_html_e( 'Mobile Number', 'hospital_mgt' ) ;?></th>
								  <th> <?php esc_html_e( 'Email', 'hospital_mgt' ) ;?></th>
								  <th> <?php esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
								</tr>
							</tfoot>
			 
						   <tbody>
							<?php 
							$own_data=$user_access['own_data'];
							if($obj_hospital->role == 'receptionist') 
							{
								if($own_data == '1')
								{
									$user_id=get_current_user_id();	
									$receptionistdata=array();
									$receptionistdata[]=get_userdata($user_id);		
								}
								else
								{
									$get_receptionist = array('role' => 'receptionist');
									$receptionistdata=get_users($get_receptionist);
								}
							}
							else
							{
								if($own_data == '1')
								{
									$user_id=get_current_user_id();		
									
									$receptionistdata= get_users(
									array(
											'role' => 'receptionist',
											'meta_query' => array(
												array(
													'key' => 'created_by',
													'value' => get_current_user_id(),
													'compare' => '='
												)
											)
									));

								}
								else
								{
									$get_receptionist = array('role' => 'receptionist');
									$receptionistdata=get_users($get_receptionist);
								}
							}
							
							if(!empty($receptionistdata))
							{
								foreach ($receptionistdata as $retrieved_data)
								{
								?>
								<tr>
									<td class="user_image"><?php $uid=$retrieved_data->ID;
												$userimage=get_user_meta($uid, 'hmgt_user_avatar', true);
											if(empty($userimage))
											{
												echo '<img src='.esc_url(get_option( 'hmgt_support_thumb' )).' height="50px" width="50px" class="img-circle" />';
											}
											else
												echo '<img src='.esc_url($userimage).' height="50px" width="50px" class="img-circle"/>';
									?></td>
									<td class="name"><?php echo esc_html($retrieved_data->display_name);?></td>
									<td class="department"><?php 
									$postdata=get_post($retrieved_data->department);
									echo esc_html($postdata->post_title);?></td>
									<td class="phone">
									<?php 
										echo get_user_meta($uid, 'mobile', true);
									?></td>
									
									<td class="email"><?php echo esc_html($retrieved_data->user_email);?></td>   
															
										<td class="action">
										<a href="javascript:void(0);" class="view_details_popup btn btn-default" id="<?php echo esc_attr($retrieved_data->ID) ?>" type="<?php echo 'view_support';?>"><i class="fa fa-eye"> </i> <?php esc_html_e('View', 'hospital_mgt' ) ;?> </a>
										<?php
										if($user_access['edit']=='1')
										{
										?>
											<a href="?dashboard=user&page=supportstaff&tab=addreceptionist&&action=edit&receptionist_id=<?php echo MJ_hmgt_id_encrypt(esc_attr($retrieved_data->ID));?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
										<?php
										}
										if($user_access['delete']=='1')
										{
										?>	
											<a href="?dashboard=user&page=supportstaff&tab=receptionistlist&action=delete&receptionist_id=<?php echo MJ_hmgt_id_encrypt(esc_attr($retrieved_data->ID));?>" class="btn btn-danger" 
											onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this record?','hospital_mgt');?>');">
											<?php esc_html_e( 'Delete', 'hospital_mgt' ) ;?> </a>
										<?php
										}
										?>	
										</td>
									
								</tr>
								<?php } 
								
							}?>
						 
							</tbody>
					</table><!-- END STAFF LIST TABLE-->
 		    </div><!-- END TABLE RESPONSIVE DIV-->
		</div><!-- END PANEL BODY DIV-->
	</div><!-- END TABLE CONTENT DIV-->
<?php
}
if($active_tab=='addreceptionist')
{
	$role='receptionist';
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		"use strict";
		<?php
		if (is_rtl())
			{
			?>	
				$('#nurse_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
			<?php
			}
			else{
				?>
				$('#nurse_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
				<?php
			}
		?>
		$.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
		  $('#birth_date').datepicker({
		 endDate: '+0d',
			autoclose: true,
	   }); 
		//username not  allow space validation
		$('#username').keypress(function( e ) {
		   if(e.which === 32) 
			 return false;
		});
	} );
	</script>
    <?php 	
	if($active_tab == 'addreceptionist')
	{
        	$receptionist_id=0;
			$edit=0;
			if(isset($_REQUEST['receptionist_id']))
				$receptionist_id=MJ_hmgt_id_decrypt($_REQUEST['receptionist_id']);
			if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit'){
					$edit=1;
					$user_info = get_userdata($receptionist_id);
				}?>
        <div class="panel-body"><!-- START PANEL BODY DIV-->
			<form name="nurse_form" action="" method="post" class="form-horizontal" id="nurse_form" enctype="multipart/form-data">	<!-- START STAFF FORM-->
				<?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
				<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
				<input type="hidden" name="role" value="<?php echo esc_attr($role);?>"  />
				<input type="hidden" name="user_id" value="<?php echo esc_attr($receptionist_id);?>"  />
				<div class="header">	
				<h3 class="first_hed"><?php esc_html_e('Personal Information','hospital_mgt');?></h3>
				<hr>
			</div>		
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="first_name"><?php esc_html_e('First Name','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="first_name" class="form-control validate[required,custom[onlyLetter_specialcharacter]] text-input" maxlength="50" type="text" value="<?php if($edit){ echo esc_attr($user_info->first_name);}elseif(isset($_POST['first_name'])) echo esc_attr($_POST['first_name']);?>" name="first_name">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label" for="middle_name"><?php esc_html_e('Middle Name','hospital_mgt');?></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="middle_name" class="form-control validate[custom[onlyLetter_specialcharacter]]" type="text" maxlength="50" value="<?php if($edit){ echo esc_attr($user_info->middle_name);}elseif(isset($_POST['middle_name'])) echo esc_attr($_POST['middle_name']);?>" name="middle_name">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="last_name"><?php esc_html_e('Last Name','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="last_name" class="form-control validate[required,custom[onlyLetter_specialcharacter]] text-input" maxlength="50" type="text"  value="<?php if($edit){ echo esc_attr($user_info->last_name);}elseif(isset($_POST['last_name'])) echo esc_attr($_POST['last_name']);?>" name="last_name">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label" for="birth_date"><?php esc_html_e('Date of birth','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="birth_date" class="form-control validate[required]" type="text"  name="birth_date"
						value="<?php if($edit){ echo date(MJ_hmgt_date_formate(),strtotime($user_info->birth_date));}elseif(isset($_POST['birth_date'])) echo esc_attr($_POST['birth_date']);?>" readonly>
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
						<input id="address" class="form-control validate[required,custom[address_description_validation]]" maxlength="150" type="text"  name="address" 
						value="<?php if($edit){ echo esc_attr($user_info->address);}elseif(isset($_POST['address'])) echo esc_attr($_POST['address']);?>">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="city_name"><?php esc_html_e('City','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="city_name" class="form-control validate[required,custom[city_state_country_validation]]" maxlength="50" type="text"  name="city_name" 
						value="<?php if($edit){ echo esc_attr($user_info->city_name);}elseif(isset($_POST['city_name'])) echo esc_attr($_POST['city_name']);?>">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="state_name"><?php esc_html_e('State','hospital_mgt');?></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="state_name" class="form-control validate[custom[city_state_country_validation]]" maxlength="50" type="text"  name="state_name" 
						value="<?php if($edit){ echo esc_attr($user_info->state_name);}elseif(isset($_POST['state_name'])) echo esc_attr($_POST['state_name']);?>">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="country_name"><?php esc_html_e('Country','hospital_mgt');?></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="country_name" class="form-control validate[custom[city_state_country_validation]]" maxlength="50" type="text"  name="country_name" 
						value="<?php if($edit){ echo esc_attr($user_info->country_name);}elseif(isset($_POST['country_name'])) echo esc_attr($_POST['country_name']);?>">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="zip_code"><?php esc_html_e('Zip Code','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="zip_code" class="form-control  validate[required,custom[onlyLetterNumber]]" type="text" maxlength="15" name="zip_code" 
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
					<input type="text" value="<?php if($edit) { echo esc_attr($user_info->phonecode);  }elseif(isset($_POST['phonecode'])){ echo esc_attr($_POST['phonecode']); }else{ ?>+<?php echo MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }?>"  class="form-control  validate[required] onlynumber_and_plussign" name="phonecode" maxlength="5">
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
						<input id="email" class="form-control validate[required,custom[email]] text-input" maxlength="100" type="text"  name="email" 
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
						<input id="password" class="form-control <?php if(!$edit){ echo 'validate[required,minSize[8]]'; }else{ echo 'validate[minSize[8]]'; }?>" type="password" maxlength="12" name="password" value="">
					</div>
				</div>
			</div>
			<div class="header">
				<h3><?php esc_html_e('Other Information','hospital_mgt');?></h3>
				<hr>
			</div>
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="department"><?php esc_html_e('Department','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-3 margin_bottom_5px">
					<?php if($edit){ $departmentid=$user_info->department; }elseif(isset($_POST['department'])){$departmentid=$_POST['department'];}else{$departmentid='';}?>
						<select name="department" class="form-control validate[required]" id="department">
						<option value="" ><?php esc_html_e('Select Department','hospital_mgt');?></option>
						<?php 
						
							$department_array = $user_object->MJ_hmgt_get_staff_department();
							if(!empty($department_array))
							{
								foreach ($department_array as $retrieved_data){?>
									<option value="<?php echo esc_attr($retrieved_data->ID); ?>" <?php selected($departmentid,$retrieved_data->ID);?>><?php echo esc_html($retrieved_data->post_title);?></option>
								<?php }
							}
						?>
						</select>
					</div>
					<div class="col-sm-2"><button id="addremove" model="department"><?php esc_html_e('Add Or Remove','hospital_mgt');?></button></div>
				</div>
			</div>	
			<?php wp_nonce_field( 'save_receptionist_nonce' ); ?>
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
									<img class="image_preview_css" alt="" src="<?php echo get_option( 'hmgt_support_thumb' ); ?>">
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
								<img class="image_preview_css" alt="" src="<?php echo get_option( 'hmgt_support_thumb' ); ?>">
								<?php 
							}?>
						</div>
					</div>
				</div>
			</div>		
				<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
					<input type="submit" value="<?php if($edit){ esc_html_e('Save','hospital_mgt'); }else{ esc_html_e('Save','hospital_mgt');}?>" name="save_receptionist" class="btn btn-success"/>
				</div>
			</form><!-- END STAFF FORM-->
        </div><!-- END PANEL BODY DIV-->
     <?php 
	}
}
?>	
</div>
<!-- END PANEL WHITE DIV -->