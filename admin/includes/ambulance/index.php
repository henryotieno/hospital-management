<?php
// error_reporting(0);
$obj_ambulance = new MJ_hmgt_ambulance();
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
	$user_access=MJ_hmgt_get_access_right_for_management_user_page('ambulance');
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
			if (isset ( $_REQUEST ['page'] ) && 'ambulance' == $user_access['page_link'] && ($_REQUEST['action']=='edit'))
			{
				if($user_access['edit']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}			
			}
			if (isset ( $_REQUEST ['page'] ) && 'ambulance' == $user_access['page_link'] && ($_REQUEST['action']=='delete'))
			{
				if($user_access['delete']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}	
			}
			if (isset ( $_REQUEST ['page'] ) && 'ambulance' == $user_access['page_link'] && ($_REQUEST['action']=='insert')) 
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
$active_tab = isset($_GET['tab'])?$_GET['tab']:'ambulance_req_list';
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
<!--PAGE INNER DIV START-->
<div class="page-inner min_height_1631">
	<div class="page-title"><!--PAGE TITLE DIV START-->
		<h3><img src="<?php echo esc_url(get_option( 'hmgt_hospital_logo', 'hospital_mgt' )) ?>" class="img-circle head_logo" width="40" height="40" /><?php echo esc_html(get_option('hmgt_hospital_name','hospital_mgt'));?></h3>
	</div><!--PAGE INNER DIV END-->
	<!--SAVE Ambulance-->
	<?php 
	if(isset($_REQUEST['save_ambulance']))
	{	
		$nonce = $_POST['_wpnonce'];
		if (wp_verify_nonce( $nonce, 'save_ambulance_nonce' ) )
		{
			if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'insert' || $_REQUEST['action'] == 'edit'))
			{
				$txturl=$_POST['driver_image'];
				$registerd_no=$_POST['registerd_no'];
				
				$ext=MJ_hmgt_check_valid_extension($txturl);
					
				if(!$ext == 0)
				{
			       if($_REQUEST['action'] == 'edit')
				   {
						$result = $obj_ambulance->MJ_hmgt_add_ambulance($_POST);
						if($result)
						{
							wp_redirect ( admin_url() . 'admin.php?page=hmgt_ambulance&tab=ambulance_list&message=2');
						}
				   }
				   else
				   {
					   $ambulance_data = $obj_ambulance->MJ_hmgt_get_all_ambulance_by_ragister_number($registerd_no);
					   if(empty($ambulance_data))
					   {
						   $result = $obj_ambulance->MJ_hmgt_add_ambulance($_POST);
						   if($result)
						   {
							   wp_redirect ( admin_url() . 'admin.php?page=hmgt_ambulance&tab=ambulance_list&message=1');
						   }
					   }
					   else
						{ ?>
							<div id="message" class="updated below-h2 notice is-dismissible">
								<p><p><?php esc_html_e('Ambulance already registered with this number','hospital_mgt');?></p></p>
							</div>
						<?php 
						}
					   
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
	}
	//--------------- SAVE Ambulance Request ------------------//
	if(isset($_REQUEST['save_ambulance_request']))
	{
		$nonce = $_POST['_wpnonce'];
		if (wp_verify_nonce( $nonce, 'save_ambulance_request_nonce' ) )
		{
			if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'insert' || $_REQUEST['action'] == 'edit'))
			{	
				
				$result = $obj_ambulance->MJ_hmgt_add_ambulance_request($_POST);
			
				if($result)
				{
					if($_REQUEST['action'] == 'edit')
					{
						wp_redirect ( admin_url() . 'admin.php?page=hmgt_ambulance&tab=ambulance_req_list&message=2');
					}
					else
					{
						wp_redirect ( admin_url() . 'admin.php?page=hmgt_ambulance&tab=ambulance_req_list&message=1');
					}
				}
			}
		}
	}
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete')
	{
		if($_GET['tab'] == 'ambulance_req_list')
		{
			$result = $obj_ambulance->MJ_hmgt_delete_ambulance_req($_REQUEST['amb_req_id']);
			if($result)
			{
				wp_redirect ( admin_url() . 'admin.php?page=hmgt_ambulance&tab=ambulance_req_list&message=3');
			}
		}
		else
		{
			$result = $obj_ambulance->MJ_hmgt_delete_ambulance($_REQUEST['amb_id']);
			if($result)
			{
				wp_redirect ( admin_url() . 'admin.php?page=hmgt_ambulance&tab=ambulance_list&message=3');
			}
		}
	
	}
	if(isset($_REQUEST['delete_selected']))
	{		
		if(!empty($_REQUEST['selected_id']))
		{
			
			foreach($_REQUEST['selected_id'] as $id)
			{
				$result=$obj_ambulance->MJ_hmgt_delete_ambulance($id);
			}
			if($result)
			{
				wp_redirect ( admin_url() . 'admin.php?page=hmgt_ambulance&tab=ambulance_req_list&message=3');
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
				$result=$obj_ambulance->MJ_hmgt_delete_ambulance($id);
			}
			if($result)
			{
				wp_redirect ( admin_url() . 'admin.php?page=hmgt_ambulance&tab=ambulance_list&message=3');
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
	}
	?>
	<div id="main-wrapper"><!-- MAIN WRAPPER DIV START-->
		<div class="row"><!-- ROW DIV START-->
			<div class="col-md-12">
				<div class="panel panel-white"><!-- PANEL WHITE DIV START-->
					<div class="panel-body nav_tab_responsive_4_tab_ambulance"><!--PANEL BODY START-->
						<h2 class="nav-tab-wrapper">
							<a href="?page=hmgt_ambulance&tab=ambulance_req_list" class="nav-tab <?php echo $active_tab == 'ambulance_req_list' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-menu"></span> '.esc_html__('Ambulance Requested List', 'hospital_mgt'); ?>
							</a>
							 <?php  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && $active_tab == 'add_ambulance_req')
							{?>
							<a href="?page=hmgt_ambulance&tab=add_ambulance_req&&action=edit&amb_req_id=<?php echo $_REQUEST['amb_req_id'];?>" class="nav-tab <?php echo $active_tab == 'add_ambulance_req' ? 'nav-tab-active' : ''; ?>">
							<?php esc_html_e('Edit Ambulance Request', 'hospital_mgt'); ?></a>  
							<?php 
							}
							else
							{if($user_access_add == 1)
							{?>
								<a href="?page=hmgt_ambulance&tab=add_ambulance_req" class="nav-tab <?php echo $active_tab == 'add_ambulance_req' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-plus-alt"></span> '.esc_html__(' Add Ambulance Request', 'hospital_mgt'); ?></a>  
							<?php } }?>
							 <a href="?page=hmgt_ambulance&tab=ambulance_list" class="nav-tab <?php echo $active_tab == 'ambulance_list' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-menu"></span> '.esc_html__(' Ambulance List', 'hospital_mgt'); ?>
							</a>
							 <?php  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && $active_tab == 'add_ambulance')
							{?>
							<a href="?page=hmgt_ambulance&tab=add_ambulance&&action=edit&amb_id=<?php echo $_REQUEST['amb_id'];?>" class="nav-tab <?php echo $active_tab == 'add_ambulance' ? 'nav-tab-active' : ''; ?>">
							<?php esc_html_e('Edit Ambulance', 'hospital_mgt'); ?></a>  
							<?php 
							}
							else
							{
								if($user_access_add == 1)
								{?>
								<a href="?page=hmgt_ambulance&tab=add_ambulance" class="nav-tab <?php echo $active_tab == 'add_ambulance' ? 'nav-tab-active' : ''; ?>">
								<?php echo '<span class="dashicons dashicons-plus-alt"></span> '.esc_html__(' Add New Ambulance', 'hospital_mgt'); ?></a> 
								<?php
								} 
							}?>
						  
						</h2>
						 <?php 				
						if($active_tab == 'ambulance_req_list')
						{ 
						
						?>	
							<script type="text/javascript">
							jQuery(document).ready(function() {
								"use strict";
								jQuery('#ambulance_request').DataTable({
									"responsive": true,
									 "order": [[ 3, "desc" ]],
									  "dom": 'Bfrtip',
										"buttons": [
											'colvis'
										], 
									 "aoColumns":[
												  {"bSortable": false},
												  {"bSortable": true},
												  {"bSortable": true},
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
							});
							</script>
							<form name="wcwm_report" action="" method="post">						
								<div class="panel-body"><!-- PANEL BODY DIV START-->
									<div class="table-responsive"><!-- TABLE RESPONSIVE DIV START-->
										<table id="ambulance_request" class="display" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th><input type="checkbox" class="select_all"></th>
													<th><?php esc_html_e( 'Ambulance', 'hospital_mgt' ) ;?></th>
												   <th><?php esc_html_e( 'Patient', 'hospital_mgt' ) ;?></th>
													<th><?php esc_html_e( 'Date', 'hospital_mgt' ) ;?></th>	
													<th><?php esc_html_e( 'Time', 'hospital_mgt' ) ;?></th>
													<th><?php esc_html_e( 'Dispatch Time', 'hospital_mgt' ) ;?></th>
													<th><?php esc_html_e( 'Charges', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
													<th><?php esc_html_e( 'Tax Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
													<th><?php esc_html_e( 'Total Charges', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
													<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
												</tr>
											</thead>
											<tfoot>
												<tr>
													<th></th>
													<th><?php esc_html_e( 'Ambulance', 'hospital_mgt' ) ;?></th>
													<th><?php esc_html_e( 'Patient', 'hospital_mgt' ) ;?></th>
													<th><?php esc_html_e( 'Date', 'hospital_mgt' ) ;?></th>	
													<th><?php esc_html_e( 'Time', 'hospital_mgt' ) ;?></th>
													<th><?php esc_html_e( 'Dispatch Time', 'hospital_mgt' ) ;?></th>
													<th><?php esc_html_e( 'Charges', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
													<th><?php esc_html_e( 'Tax Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
													<th><?php esc_html_e( 'Total Charges', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
													<th><?php esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
												</tr>
											</tfoot>									 
											<tbody>
												 <?php 
												$ambulancereq_data=$obj_ambulance->MJ_hmgt_get_all_ambulance_request();
												 if(!empty($ambulancereq_data))
												 {
													foreach ($ambulancereq_data as $retrieved_data){ 
														$patient_data =	MJ_hmgt_get_user_detail_byid($retrieved_data->patient_id);
													
												 ?>
												<tr>
													<td class="title"><input type="checkbox" name="selected_id[]"  class="sub_chk" value="<?php echo esc_attr($retrieved_data->amb_req_id); ?>"></td>
													<td class="ambulanceid"><?php echo esc_html($obj_ambulance->MJ_hmgt_get_ambulance_id($retrieved_data->ambulance_id));?></td>
													
													<td class="patient"><?php echo esc_html($patient_data['first_name']." ".$patient_data['last_name']."(".$patient_data['patient_id'].")");?></td>
										
													<td class="date"><?php if($retrieved_data->request_date!="0000-00-00" && "00/00/0000" && "00/00/0000"){ echo date(MJ_hmgt_date_formate(),strtotime($retrieved_data->request_date)); }?></td>
													
													<td class=""><?php echo esc_html($retrieved_data->request_time);?></td>
													
													<td class="dispatchtime"><?php echo esc_html($retrieved_data->dispatch_time);?></td>
													
													<td class=""><?php echo number_format($retrieved_data->charge, 2, '.', ''); ?></td>
													
													<td class=""><?php echo number_format($retrieved_data->total_tax, 2, '.', ''); ?></td>
													
													<td class=""><?php echo number_format($retrieved_data->total_charge, 2, '.', ''); ?></td>
													
													<td class="action"> 
														<a href="javascript:void(0);" class="view_details_popup btn btn-default" id="<?php echo esc_attr( $retrieved_data->amb_req_id); ?>" type="<?php echo 'view_ambulance_req';?>"><i class="fa fa-eye"> </i> <?php _e('View', 'hospital_mgt' ) ;?> </a>
														<?php if($user_access_edit == 1)
														{?>
														<a href="?page=hmgt_ambulance&tab=add_ambulance_req&action=edit&amb_req_id=<?php echo esc_attr($retrieved_data->amb_req_id);?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
														<?php 
														} 
														?>
														<?php if($user_access_delete == 1)
														{?>	
														<a href="?page=hmgt_ambulance&tab=ambulance_req_list&action=delete&amb_req_id=<?php echo esc_attr($retrieved_data->amb_req_id);?>" class="btn btn-danger" 
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
														{?>	
										<div class="print-button pull-left">
											<input  type="submit" value="<?php esc_html_e('Delete Selected','hospital_mgt');?>" name="delete_selected" class="btn btn-danger delete_selected "/>
										</div>
										<?php 
														} ?>
									</div><!-- TABLE RESPONSIVE DIV END-->
								</div>	<!-- PANEL BODY DIV END-->							   
							</form>
						 <?php 
						}
						if($active_tab == 'ambulance_list')
						{
						 ?>
							<script type="text/javascript">
							jQuery(document).ready(function() {
								"use strict";
								jQuery('#ambulance_list').DataTable({
									"responsive": true,
									 "order": [[ 2, "asc" ]],
									 "aoColumns":[
												  {"bSortable": false},
												  {"bSortable": false},
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
							<div class="panel-body">
								<div class="table-responsive">
									<table id="ambulance_list" class="display" cellspacing="0" width="100%">
										<thead>
										<tr>
											<th><input type="checkbox" class="select_all"></th>
											<th class="height_width_50"><?php esc_html_e( 'Image', 'hospital_mgt' ) ;?></th>
											<th><?php esc_html_e( 'Ambulance ID', 'hospital_mgt' ) ;?></th>
											<th><?php esc_html_e( 'Reg NO', 'hospital_mgt' ) ;?></th>
											<th><?php esc_html_e( 'Driver Name', 'hospital_mgt' ) ;?></th>	
											<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
										</tr>
										</thead>
										<tfoot>
											<tr>
												<th></th>
												<th><?php esc_html_e( 'Image', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e( 'Ambulance ID', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e( 'Reg NO', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e( 'Driver Name', 'hospital_mgt' ) ;?></th>
												<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
											</tr>
										</tfoot>							 
										<tbody>
										<?php 
										$ambulance_data=$obj_ambulance->MJ_hmgt_get_all_ambulance();
										 if(!empty($ambulance_data))
										 {
											foreach ($ambulance_data as $retrieved_data)
											{ 
											?>
											<tr>
												<td class="title"><input type="checkbox" name="selected_id[]" class="sub_chk" value="<?php echo esc_attr($retrieved_data->amb_id); ?>"></td>
												<td class="driver_image">
													<?php 
													if(trim($retrieved_data->driver_image) == "")
													{
														echo '<img src='.esc_url(get_option( 'hmgt_driver_thumb' )).' height="50px" width="50px" class="img-circle"/>';
													}
													else
													{
														echo '<img src='.esc_url($retrieved_data->driver_image).' height="50px" width="50px" class="img-circle"/>';
													}
													?>
												</td>
												<td class="amb_id"><?php echo esc_html($retrieved_data->ambulance_id);?></td>
												<td class="regno"><?php echo esc_html($retrieved_data->registerd_no);?></td>
												<td class="driver_name"><?php echo esc_html($retrieved_data->driver_name);?></td>
											   
												<td class="action"> 
													<a href="javascript:void(0);" class="view_details_popup btn btn-default" id="<?php echo esc_attr( $retrieved_data->amb_id); ?>" type="<?php echo 'view_ambulance';?>"><i class="fa fa-eye"> </i> <?php _e('View', 'hospital_mgt' ) ;?> </a>
													<?php if($user_access_edit == 1)
												{?>
													<a href="?page=hmgt_ambulance&tab=add_ambulance&action=edit&amb_id=<?php echo esc_attr($retrieved_data->amb_id);?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
													<?php 
												} 
												?>
												<?php if($user_access_delete == 1)
												{?>	
													<a href="?page=hmgt_ambulance&tab=ambulance_list&action=delete&amb_id=<?php echo esc_attr($retrieved_data->amb_id);?>" class="btn btn-danger" 
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
									{?>	
									<div class="print-button pull-left">
										<input  type="submit" value="<?php esc_html_e('Delete Selected','hospital_mgt');?>" name="delete_selected2" class="btn btn-danger delete_selected "/>
									</div>
									<?php 
									} ?>
								</div>
							</div>
							</form>
						<?php 
						}					 
						if($active_tab == 'add_ambulance_req')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/ambulance/add-ambulance-req.php';
						}
						if($active_tab == 'add_ambulance')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/ambulance/add-new-ambulance.php';
						}
						?>
					</div>	<!--PANEL BODY END`-->		
				</div><!-- PANEL WHITE DIV END-->
			</div>
		</div><!-- ROW DIV END-->
	</div><!-- ROW DIV END-->
</div><!-- ROW DIV END-->