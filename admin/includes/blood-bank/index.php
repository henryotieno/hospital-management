<?php
$obj_bloodbank=new MJ_hmgt_bloodbank();
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
	$user_access=MJ_hmgt_get_access_right_for_management_user_page('bloodbank');
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
			if (isset ( $_REQUEST ['page'] ) && 'bloodbank' == $user_access['page_link'] && ($_REQUEST['action']=='edit'))
			{
				if($user_access['edit']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}			
			}
			if (isset ( $_REQUEST ['page'] ) && 'bloodbank' == $user_access['page_link'] && ($_REQUEST['action']=='delete'))
			{
				if($user_access['delete']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}	
			}
			if (isset ( $_REQUEST ['page'] ) && 'bloodbank' == $user_access['page_link'] && ($_REQUEST['action']=='insert')) 
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
$active_tab = isset($_GET['tab'])?$_GET['tab']:'bloodbanklist';
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
<div class="page-inner min_height_1631"><!-- page INNER DIV START-->
    <div class="page-title"><!-- page title DIV START-->
		<h3><img src="<?php echo esc_url(get_option( 'hmgt_hospital_logo', 'hospital_mgt' )) ?>" class="img-circle head_logo" width="40" height="40" /><?php echo esc_html(get_option('hmgt_hospital_name','hospital_mgt'));?></h3>
	</div><!-- page title DIV END-->
	<?php 
	//--------------------- DISPACTH BLOOD --------------------------//
	if(isset($_POST['save_dispatch_blood']))
	{
		$nonce = $_POST['_wpnonce'];
		if (wp_verify_nonce( $nonce, 'save_dispatch_blood_nonce' ) )
		{
			global $wpdb;		
			$table_bloodbank=$wpdb->prefix. 'hmgt_blood_bank';
			
			if($_REQUEST['action']=='edit')
			{	
				//check blood stock
				$blood_group=$_POST['blood_group'];
				$blood_status=$_POST['blood_status'];
				$old_blood_group=$_POST['old_blood_group'];
				$old_blood_status=$_POST['old_blood_status'];
				$dispatchblood_id=$_POST['dispatchblood_id'];
				
				$result_blood_group = $wpdb->get_row("SELECT * FROM $table_bloodbank where blood_group='$blood_group'");
				$oldblood_status=$result_blood_group->blood_status;
			
				if($blood_group == $old_blood_group)
				{
					$oldblood_status=$oldblood_status+$old_blood_status;
					if(empty($result_blood_group))
					{
						wp_redirect ( admin_url() . 'admin.php?page=hmgt_bloodbank&tab=adddispatchblood&action=edit&dispatchblood_id='.$dispatchblood_id.'&message=5');
					}
					elseif($blood_status>$oldblood_status)
					{
						wp_redirect ( admin_url() . 'admin.php?page=hmgt_bloodbank&tab=adddispatchblood&action=edit&dispatchblood_id='.$dispatchblood_id.'&message=5');
					}
					else
					{
						$result=$obj_bloodbank->MJ_hmgt_add_dispatch_blood($_POST);
						if($result)
						{	
							wp_redirect ( admin_url() . 'admin.php?page=hmgt_bloodbank&tab=dispatchbloodlist&message=2');
						}
					}
				}
				else
				{
					if(empty($result_blood_group))
					{
						wp_redirect ( admin_url() . 'admin.php?page=hmgt_bloodbank&tab=adddispatchblood&action=edit&dispatchblood_id='.$dispatchblood_id.'&message=5');
					}
					elseif($blood_status>$oldblood_status)
					{
						wp_redirect ( admin_url() . 'admin.php?page=hmgt_bloodbank&tab=adddispatchblood&action=edit&dispatchblood_id='.$dispatchblood_id.'&message=5');
					}
					else
					{
						$result=$obj_bloodbank->MJ_hmgt_add_dispatch_blood($_POST);
						if($result)
						{	
							wp_redirect ( admin_url() . 'admin.php?page=hmgt_bloodbank&tab=dispatchbloodlist&message=2');
						}
					}	
				}
			}
			else
			{
				//check blood stock
				$blood_group=$_POST['blood_group'];
				$blood_status=$_POST['blood_status'];
				$result_blood_group = $wpdb->get_row("SELECT * FROM $table_bloodbank where blood_group='$blood_group'");
				$oldblood_status=$result_blood_group->blood_status;
				
				if(empty($result_blood_group))
				{
					wp_redirect ( admin_url() . 'admin.php?page=hmgt_bloodbank&tab=adddispatchblood&message=5');
				}
				elseif($blood_status>$oldblood_status)
				{
					wp_redirect ( admin_url() . 'admin.php?page=hmgt_bloodbank&tab=adddispatchblood&message=5');
				}
				else
				{	
					$result=$obj_bloodbank->MJ_hmgt_add_dispatch_blood($_POST);
					if($result)
					{
						wp_redirect ( admin_url() . 'admin.php?page=hmgt_bloodbank&tab=dispatchbloodlist&message=1');
					}
				}
			}
		}		
	}
	//------------------ SAVE BLOODDONER -------------------------//
	if(isset($_POST['save_blooddonor']))
	{
		
		$nonce = $_POST['_wpnonce'];
		if (wp_verify_nonce( $nonce, 'save_blooddonor_nonce' ) )
		{	
			if($_REQUEST['action']=='edit')
			{				
				$result=$obj_bloodbank->MJ_hmgt_add_blood_donor($_POST);
				if($result)
				{	
					wp_redirect ( admin_url() . 'admin.php?page=hmgt_bloodbank&tab=blooddonorlist&message=2');
				}
			}
			else
			{
				$result=$obj_bloodbank->MJ_hmgt_add_blood_donor($_POST);
				if($result)
				{
					wp_redirect ( admin_url() . 'admin.php?page=hmgt_bloodbank&tab=blooddonorlist&message=1');
				}
			}	
		}
	}
	//------------------ SAVE BLOOD GROUP -------------//
	if(isset($_POST['save_bloodgroup']))
	{	
		$nonce = $_POST['_wpnonce'];
		if (wp_verify_nonce( $nonce, 'save_bloodgroup_nonce' ) )
		{
			if($_REQUEST['action']=='edit')
			{
				global $wpdb;
				$table_bloodbank=$wpdb->prefix. 'hmgt_blood_bank';
				$blood_group=$_POST['blood_group'];
				$bloodgroup_id=$_POST['bloodgroup_id'];
				$allready_added_result = $wpdb->get_row("SELECT * FROM $table_bloodbank where blood_group = '$blood_group' AND blood_id !=".$bloodgroup_id);
				
				if(!empty($allready_added_result))
				{				
					wp_redirect ( admin_url() . 'admin.php?page=hmgt_bloodbank&tab=addbloodgroop&action=edit&bloodgroup_id='.$bloodgroup_id.'&message=4');
				}
				else
				{			
					$result=$obj_bloodbank->MJ_hmgt_add_blood_group($_POST);
					if($result)
					{
						wp_redirect ( admin_url() . 'admin.php?page=hmgt_bloodbank&tab=bloodbanklist&message=2');
					}
				}	
			}
			else
			{
				global $wpdb;
				$table_bloodbank=$wpdb->prefix. 'hmgt_blood_bank';
				$blood_group=$_POST['blood_group'];
				$allready_added_result = $wpdb->get_row("SELECT * FROM $table_bloodbank where blood_group = '$blood_group'");
				
				if(!empty($allready_added_result))
				{				
					wp_redirect ( admin_url() . 'admin.php?page=hmgt_bloodbank&tab=addbloodgroop&message=4');
				}
				else
				{		
					$result=$obj_bloodbank->MJ_hmgt_add_blood_group($_POST);
					if($result)
					{
						wp_redirect ( admin_url() . 'admin.php?page=hmgt_bloodbank&tab=bloodbanklist&message=1');
					}
				}
			}
		}
	}	

	if(isset($_REQUEST['action'])&& $_REQUEST['action']=='delete')
	{
		if(isset($_REQUEST['blooddonor_id']))
		{
			$result=$obj_bloodbank->MJ_hmgt_delete_blooddonor($_REQUEST['blooddonor_id']);
			wp_redirect( admin_url () . 'admin.php?page=hmgt_bloodbank&tab=blooddonorlist&message=3');
		}
		if(isset($_REQUEST['bloodgroup_id']))
		{
			$result=$obj_bloodbank->MJ_hmgt_delete_bloodgroup($_REQUEST['bloodgroup_id']);
			wp_redirect( admin_url () . 'admin.php?page=hmgt_bloodbank&tab=bloodbanklist&message=3');
		}
		if(isset($_REQUEST['dispatchblood_id']))
		{
			$result=$obj_bloodbank->MJ_hmgt_delete_dispatchblood_data($_REQUEST['dispatchblood_id']);
			wp_redirect( admin_url () . 'admin.php?page=hmgt_bloodbank&tab=dispatchbloodlist&message=3');
		}
			
	}
	if(isset($_REQUEST['delete_selected']))
	{		
		if(!empty($_REQUEST['selected_id']))
		{
			
			foreach($_REQUEST['selected_id'] as $id)
			{
				$result=$obj_bloodbank->MJ_hmgt_delete_bloodgroup($id);
			}
			if($result)
			{
				wp_redirect( admin_url () . 'admin.php?page=hmgt_bloodbank&tab=bloodbanklist&message=3');
			}
		}
		else
		{
			echo '<script language="javascript">';
			echo 'alert("'.esc_html__('Please select at least one record.','hospital_mgt').'")';
			echo '</script>';
		}
	}
	if(isset($_REQUEST['delete_selected2']))
	{		
		if(!empty($_REQUEST['selected_id']))
		{
			
			foreach($_REQUEST['selected_id'] as $id)
			{
				$result=$obj_bloodbank->MJ_hmgt_delete_blooddonor($id);
			}
			if($result)
			{
				wp_redirect( admin_url () . 'admin.php?page=hmgt_bloodbank&tab=blooddonorlist&message=3');
			}
		}
		else
		{
			echo '<script language="javascript">';
			echo 'alert("'.esc_html__('Please select at least one record.','hospital_mgt').'")';
			echo '</script>';
		}
	}
	if(isset($_REQUEST['delete_selected3']))
	{		
		if(!empty($_REQUEST['selected_id']))
		{
			
			foreach($_REQUEST['selected_id'] as $id)
			{
				$result=$obj_bloodbank->MJ_hmgt_delete_dispatchblood_data($id);
			}
			if($result)
			{
				wp_redirect( admin_url () . 'admin.php?page=hmgt_bloodbank&tab=dispatchbloodlist&message=3');
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
			esc_html_e('This blood group allready added you want to update it','hospital_mgt');
		?></div></p><?php
				
		}
		elseif($message == 5) 
		{?>
		<div id="message" class="updated below-h2 notice is-dismissible"><p>
		<?php 
			esc_html_e('This Blood group is not available in the stock.','hospital_mgt');
		?></div></p><?php
				
		}
	}
	?>
	<div id="main-wrapper"><!-- MAIN WRAPPER DIV START -->
		<div class="row"><!-- ROW DIV START-->
			<div class="col-md-12">
				<div class="panel panel-white"><!-- PANEL WHITE DIV START-->
				<!-- PANEL BODY DIV START-->
					<div class="panel-body nav_tab_responsive_blood">
						<h2 class="nav-tab-wrapper">
							<a href="?page=hmgt_bloodbank&tab=bloodbanklist" class="nav-tab <?php echo $active_tab == 'bloodbanklist' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-menu"></span>'.esc_html__('Blood Manage', 'hospital_mgt'); ?></a>
							<?php  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && isset($_REQUEST['bloodgroup_id']))
							{?>
								<a href="?page=hmgt_bloodbank&tab=addbloodgroop&action=edit&bloodgroup_id=<?php if(isset($_REQUEST['bloodgroup_id'])) echo $_REQUEST['bloodgroup_id'];?>" class="nav-tab <?php echo $active_tab == 'addbloodgroop' ? 'nav-tab-active' : ''; ?>">
							<?php echo esc_html__('Edit Blood Group', 'hospital_mgt'); ?></a>
							<?php }
							else
							{if($user_access_add == 1)
							{?>
							<a href="?page=hmgt_bloodbank&tab=addbloodgroop" class="nav-tab <?php echo $active_tab == 'addbloodgroop' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-plus-alt"></span>'.esc_html__('Add Blood Group', 'hospital_mgt'); ?></a>
							<?php }}?>
							<a href="?page=hmgt_bloodbank&tab=blooddonorlist" class="nav-tab <?php echo $active_tab == 'blooddonorlist' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-menu"></span>'.esc_html__('Blood Donor List', 'hospital_mgt'); ?></a> 
							<?php 
							if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && isset($_REQUEST['blooddonor_id']))
							{	?>
								<a href="?page=hmgt_bloodbank&tab=addblooddonor&action=edit&blooddonor_id=<?php if(isset($_REQUEST['blooddonor_id'])) echo $_REQUEST['blooddonor_id'];?>" class="nav-tab <?php echo $active_tab == 'addblooddonor' ? 'nav-tab-active' : ''; ?>">
							<?php esc_html_e('Edit Blood Donor', 'hospital_mgt'); ?></a>  
							<?php
							}
							else
							{if($user_access_add == 1)
							{?>
								<a href="?page=hmgt_bloodbank&tab=addblooddonor" class="nav-tab <?php echo $active_tab == 'addblooddonor' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-plus-alt"></span>'.esc_html__('Add Blood Donor', 'hospital_mgt'); ?></a>  
							<?php 
							}
							}
							?>
							<a href="?page=hmgt_bloodbank&tab=dispatchbloodlist" class="nav-tab <?php echo $active_tab == 'dispatchbloodlist' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-menu"></span>'.esc_html__('Dispatch Blood List', 'hospital_mgt'); ?></a>
							<?php							
							if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && isset($_REQUEST['dispatchblood_id']))
							{	?>
							<a href="?page=hmgt_bloodbank&tab=adddispatchblood&action=edit&dispatchblood_id=<?php if(isset($_REQUEST['dispatchblood_id'])) echo $_REQUEST['dispatchblood_id'];?>" class="nav-tab <?php echo $active_tab == 'adddispatchblood' ? 'nav-tab-active' : ''; ?>">
							<?php esc_html_e('Edit Dispatch Blood', 'hospital_mgt'); ?></a>  
							<?php							
							}
							else
							{if($user_access_add == 1)
							{?>
								<a href="?page=hmgt_bloodbank&tab=adddispatchblood" class="nav-tab <?php echo $active_tab == 'adddispatchblood' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-plus-alt"></span>'.esc_html__('Dispatch Blood', 'hospital_mgt'); ?></a>  
							<?php } }?>
						</h2>
						 <?php 
						if($active_tab == 'bloodbanklist')
						{ ?>	
							<script type="text/javascript">
							jQuery(document).ready(function() {
								"use strict";
								jQuery('#bloodbag').DataTable({
									"responsive": true,
									"order": [[ 1, "asc" ]],		
									 "aoColumns":[
												  {"bSortable": false},
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
								<div class="panel-body"><!-- PANEL BODY DIV START-->
										<div class="table-responsive"><!-- TABLE RESPONSIVE DIV START-->
											<table id="bloodbag" class="display" cellspacing="0" width="100%">
												<thead>
													<tr>
														<th><input type="checkbox" class="select_all"></th>
														<th><?php esc_html_e( 'Blood Group', 'hospital_mgt' ) ;?></th>
														<th><?php esc_html_e( 'Number Of Bags', 'hospital_mgt' ) ;?></th> 
														<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
													</tr>
												</thead>
												<tfoot>
													<tr>
														<th></th>
													<th><?php esc_html_e( 'Blood Group', 'hospital_mgt' ) ;?></th>
													   <th><?php esc_html_e( 'Number Of Bags', 'hospital_mgt' ) ;?></th> 
														<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
													</tr>
												</tfoot>
												<tbody>
												 <?php foreach($obj_bloodbank->MJ_hmgt_get_all_bloodgroups() as $retrieved_data){  ?>
													<tr>
														<td class="title"><input type="checkbox" name="selected_id[]" class="sub_chk" value="<?php echo esc_attr($retrieved_data->blood_id); ?>"></td>
														<td class="blood_group">
														<?php 
																echo esc_html__("$retrieved_data->blood_group","hospital_mgt");
														?></td>
														<td class="subject_name"><?php  echo esc_html($retrieved_data->blood_status);?></td>
													  
														<td class="action"> 
														<?php if($user_access_edit == 1)
														{?>
														<a href="?page=hmgt_bloodbank&tab=addbloodgroop&&action=edit&bloodgroup_id=<?php echo esc_attr($retrieved_data->blood_id);?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
														<?php 
														} 
														?>
														<?php if($user_access_delete == 1)
														{?>	
														<a href="?page=hmgt_bloodbank&tab=bloodbanklist&action=delete&bloodgroup_id=<?php  echo esc_attr($retrieved_data->blood_id);?>" class="btn btn-danger" 
														onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this record?','hospital_mgt');?>');">
														<?php esc_html_e( 'Delete', 'hospital_mgt' ) ;?> </a>
														<?php 
														} ?>	
														</td>
													</tr>
													<?php } ?>
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
										</div><!-- TABLE RESPONSIVE DIV START-->
										
								</div><!-- PANEL BODY DIV END-->
							</form>
						<?php 
						}
						if($active_tab == 'addbloodgroop')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/blood-bank/add-blood-group.php';
						}						
						if($active_tab == 'addblooddonor')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/blood-bank/add-blood-donor.php';
						}
						if($active_tab == 'blooddonorlist')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/blood-bank/blood-donor-list.php';
						}
						if($active_tab == 'adddispatchblood')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/blood-bank/add-dispatch-blood.php';
						}
						if($active_tab == 'dispatchbloodlist')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/blood-bank/dispatch-blood-list.php';
						}
						?>
                    </div><!-- END BODY DIV -->
		        </div><!-- END PANEL WHITE DIV -->
	        </div>
        </div><!-- ROW DIV START-->
    </div><!-- END MAIN WRAPER DIV -->		