<?php
//Manage bed
$obj_instrument = new MJ_hmgt_Instrumentmanage();
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
	$user_access=MJ_hmgt_get_access_right_for_management_user_page('instrument');
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
			if (isset ( $_REQUEST ['page'] ) && 'instrument' == $user_access['page_link'] && ($_REQUEST['action']=='edit'))
			{
				if($user_access['edit']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}			
			}
			if (isset ( $_REQUEST ['page'] ) && 'instrument' == $user_access['page_link'] && ($_REQUEST['action']=='delete'))
			{
				if($user_access['delete']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}	
			}
			if (isset ( $_REQUEST ['page'] ) && 'instrument' == $user_access['page_link'] && ($_REQUEST['action']=='insert')) 
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
$active_tab = isset($_GET['tab'])?$_GET['tab']:'instrumentlist';
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
<div class="page-inner min_height_1631"><!-- PAGE INNER DIV START-->	
	<div class="page-title"><!-- PAGE TITLE DIV START-->	
		<h3><img src="<?php echo esc_url(get_option( 'hmgt_hospital_logo', 'hospital_mgt' )) ?>" class="img-circle head_logo" width="40" height="40" /><?php echo esc_html(get_option('hmgt_hospital_name','hospital_mgt'));?></h3>
	</div><!-- PAGE TITLE DIV END-->
	<?php 
	//--------------------- SAVE INSTRUMENT ---------------//
	if(isset($_REQUEST['save_instrument']))
	{	
		$nonce = $_POST['_wpnonce'];
		if (wp_verify_nonce( $nonce, 'save_instrument_nonce' ) )
		{
			if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'insert' || $_REQUEST['action'] == 'edit'))
			{	
				$result = $obj_instrument->MJ_hmgt_hmgt_add_instrument($_POST);
				if($result)
				{
					if($_REQUEST['action'] == 'edit')
					{
						wp_redirect ( admin_url() . 'admin.php?page=hmgt_instrument_mgt&tab=instrumentlist&message=2');
					}
					else
					{
						wp_redirect ( admin_url() . 'admin.php?page=hmgt_instrument_mgt&tab=instrumentlist&message=1');
					}	
				}
			}
		}
	}
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete')
	{
		if(isset($_REQUEST['instumrnt_id']))
		{
			$result = $obj_instrument->MJ_hmgt_delete_instrument($_REQUEST['instumrnt_id']);
			if($result)
			{
				wp_redirect ( admin_url() . 'admin.php?page=hmgt_instrument_mgt&tab=instrumentlist&message=3');
			}
		}
		if(isset($_REQUEST['assign_instument_id']))
		{
			$result = $obj_instrument->MJ_hmgt_delete_assigned_instrument($_REQUEST['assign_instument_id']);
			if($result)
			{
				wp_redirect ( admin_url() . 'admin.php?page=hmgt_instrument_mgt&tab=assigned_instrumentlist&message=3');
			}
		}
	}
	if(isset($_REQUEST['delete_selected']))
	{		
		if(!empty($_REQUEST['selected_id']))
		{
			
			foreach($_REQUEST['selected_id'] as $id)
			{
				$result=$obj_instrument->MJ_hmgt_delete_instrument($id);
			}
			if($result)
			{
				wp_redirect ( admin_url() . 'admin.php?page=hmgt_instrument_mgt&tab=instrumentlist&message=3');
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
				$result=$obj_instrument->MJ_hmgt_delete_assigned_instrument($id);
			}
			if($result)
			{
				wp_redirect ( admin_url() . 'admin.php?page=hmgt_instrument_mgt&tab=assigned_instrumentlist&message=3');
			}
		}
		else
		{
			echo '<script language="javascript">';
			echo 'alert("'.esc_html__('Please select at least one record.','hospital_mgt').'")';
			echo '</script>';
		}
	}
	//----------------- ASSIGN INSTRUMENT --------------------------//
	if(isset($_POST['assign_instrument']))
	{	
		
		$result1=$obj_instrument->MJ_hmgt_get_single_assigned_instrument_by_patient_id($_POST['patient_id']);
		$nonce = $_POST['_wpnonce'];
		if (wp_verify_nonce( $nonce, 'assign_instrument_nonce' ) )
		{
			if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'insert' || $_REQUEST['action'] == 'edit'))
			{	

				if(!empty($result1))
				{
					$start_date=' '.$result1->start_date;
					if($result1->instrument_id == $_POST['instrument_id'] && $result1->patient_id == $_POST['patient_id'] && $start_date == $_POST['start_date'])
					{
						echo '<script type="text/javascript">alert("'.esc_html__('You have already assigned the same instrument for the same date.','hospital_mgt').'");</script>';
					}
					else
					{
						$result = $obj_instrument->MJ_hmgt_assign_instrument($_POST);
						
						if($result)
						{
							if($_REQUEST['action'] == 'edit')
							{
								wp_redirect ( admin_url() . 'admin.php?page=hmgt_instrument_mgt&tab=assigned_instrumentlist&message=2');
							}
							else
							{
								wp_redirect ( admin_url() . 'admin.php?page=hmgt_instrument_mgt&tab=assigned_instrumentlist&message=1');
							}	
						}
					}
				}
				else
				{
					$result = $obj_instrument->MJ_hmgt_assign_instrument($_POST);
					
					if($result)
					{
						if($_REQUEST['action'] == 'edit')
						{
							wp_redirect ( admin_url() . 'admin.php?page=hmgt_instrument_mgt&tab=assigned_instrumentlist&message=2');
						}
						else
						{
							wp_redirect ( admin_url() . 'admin.php?page=hmgt_instrument_mgt&tab=assigned_instrumentlist&message=1');
						}	
					}
				}
			}
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
		{?>
			<div id="message" class="updated below-h2 notice is-dismissible"><p><?php
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
			?></div></p>
			<?php				
		}
	}
	?>
	<div id="main-wrapper"><!-- MAIN WRAPPER DIV START -->
		<div class="row"><!-- ROW DIV START -->
			<div class="col-md-12">
				<div class="panel panel-white"><!-- PANE WHITE DIV START -->
					<div class="panel-body nav_tab_responsive_4_tab rtl_res_div_display"><!-- PANEL BODY DIV START -->
						<h2 class="nav-tab-wrapper">
							<a href="?page=hmgt_instrument_mgt&tab=instrumentlist" class="nav-tab <?php echo $active_tab == 'instrumentlist' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-menu"></span>'.esc_html__('Instrument List', 'hospital_mgt'); ?></a>
							<?php  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && $_REQUEST['tab'] == 'addinstrument')
							{?>
							<a href="?page=hmgt_instrument_mgt&tab=addinstrument&&action=edit&instumrnt_id=<?php echo $_REQUEST['instumrnt_id'];?>" class="nav-tab <?php echo $active_tab == 'addinstrument' ? 'nav-tab-active' : ''; ?>">
							<?php esc_html_e('Edit Instrument', 'hospital_mgt'); ?></a>  
							
							<?php 
							}
							else
							{
								if($user_access_add == 1)
							{
							?>
								<a href="?page=hmgt_instrument_mgt&tab=addinstrument" class="nav-tab <?php echo $active_tab == 'addinstrument' ? 'nav-tab-active' : ''; ?>">
								<?php echo '<span class="dashicons dashicons-plus-alt"></span>'.esc_html__('Add Instrument', 'hospital_mgt'); ?></a>  
							
							<?php  
							}
							}
							?>
							<a href="?page=hmgt_instrument_mgt&tab=assigned_instrumentlist" class="nav-tab <?php echo $active_tab == 'assigned_instrumentlist' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-menu"></span>'.esc_html__('Assigned Instrument List', 'hospital_mgt'); ?></a>
							<?php  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && $_REQUEST['tab']=='assigninstrument')
							{?>
							<a href="?page=hmgt_instrument_mgt&tab=assigninstrument&&action=edit&assign_instument_id=<?php echo $_REQUEST['assign_instument_id'];?>" class="nav-tab <?php echo $active_tab == 'assigninstrument' ? 'nav-tab-active' : ''; ?>">
							<?php esc_html_e('Edit Assigned Instrument', 'hospital_mgt'); ?></a>  
							
							<?php 
							}
							else 
							{if($user_access_add == 1)
							{?>
								<a href="?page=hmgt_instrument_mgt&tab=assigninstrument" class="nav-tab <?php echo $active_tab == 'assigninstrument' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-plus-alt"></span>'.esc_html__('Assign Instrument', 'hospital_mgt'); ?></a>  
							<?php } }?>
						</h2>
						<?php 						
						if($active_tab == 'instrumentlist')
						{ 					
						?>	
							<script type="text/javascript">
							jQuery(document).ready(function()
							{
								"use strict";
								jQuery('#instrument_list').DataTable({
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
												  {"bSortable": false}],
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
									<div class="table-responsive"><!--TABLE RESPONSIVE DIV START-->
										<table id="instrument_list" class="display" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th><input type="checkbox" class="select_all"></th>
													<th><?php esc_html_e( 'Instrument Code', 'hospital_mgt' ) ;?></th>
													<th><?php esc_html_e( 'Name', 'hospital_mgt' ) ;?></th>
													<th><?php esc_html_e( 'Charges', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
													<th><?php esc_html_e( 'Tax', 'hospital_mgt' ) ;?></th>
													<th><?php esc_html_e( 'Charges Type', 'hospital_mgt' ) ;?></th>
													<th><?php esc_html_e( 'Description', 'hospital_mgt' ) ;?></th>
													<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
												</tr>
											</thead>
											<tfoot>
												<tr>
													<th></th>
													<th><?php esc_html_e( 'Instrument Code', 'hospital_mgt' ) ;?></th>
													 <th><?php esc_html_e( 'Name', 'hospital_mgt' ) ;?></th>
													 <th><?php esc_html_e( 'Charges', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
													 <th><?php esc_html_e( 'Tax', 'hospital_mgt' ) ;?></th>
													 <th><?php esc_html_e( 'Charges Type', 'hospital_mgt' ) ;?></th>
													  <th><?php esc_html_e( 'Description', 'hospital_mgt' ) ;?></th>
													  <th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
												</tr>
											</tfoot>
											<tbody>
											<?php 
											$instrumentdata=$obj_instrument->MJ_hmgt_get_all_instrument();
											if(!empty($instrumentdata))
											{
												foreach ($instrumentdata as $retrieved_data){ 
											?>
												<tr>
													<td class="title"><input type="checkbox" name="selected_id[]" class="sub_chk" value="<?php echo esc_attr($retrieved_data->id); ?>"></td>
													<td class="bed_number"><a href="?page=hmgt_instrument_mgt&tab=addinstrument&action=edit&instumrnt_id=<?php echo esc_attr($retrieved_data->id);?>"><?php echo esc_html($retrieved_data->instrument_code);?></a></td>
													<td class="bed_type"><?php echo esc_html($retrieved_data->instrument_name);?></td>
													<td class="charge">	<?php echo esc_html($retrieved_data->instrument_charge);?></td>
													<td class="tax"><?php 
													if(!empty($retrieved_data->tax))
													{          
														echo MJ_hmgt_tax_name_array_by_tax_id_array($retrieved_data->tax);
													}
													else
													{
														echo '-'; 
													}
													?></td>
													<td class="descrition"><?php echo esc_html__("$retrieved_data->charge_type","hospital_mgt");?></td>
													<td class="descrition"><?php echo esc_html($retrieved_data->instrument_description);?></td>
													<td class="action"> 
													<a href="javascript:void(0);" class="view_details_popup btn btn-default" id="<?php echo esc_attr( $retrieved_data->id); ?>" type="<?php echo 'view_instrument';?>"><i class="fa fa-eye"> </i> <?php esc_html_e('View', 'hospital_mgt' ) ;?> </a>
													<?php if($user_access_edit == 1)
												{?>
													<a href="?page=hmgt_instrument_mgt&tab=addinstrument&action=edit&instumrnt_id=<?php echo esc_attr($retrieved_data->id);?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
													<?php 
												} 
												?>
												<?php if($user_access_delete == 1)
												{?>	
													<a href="?page=hmgt_instrument_mgt&tab=instrumentlist&action=delete&instumrnt_id=<?php echo esc_attr($retrieved_data->id);?>" class="btn btn-danger" 
													onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this record?','hospital_mgt');?>');">
													<?php esc_html_e( 'Delete', 'hospital_mgt' ) ;?> </a>      
<?php 
												 } ?>														
													</td>
												   
												</tr>
												<?php } 
											}?>
											</tbody>
										</table>
										<?php if($user_access_delete == 1)
												{?>	
										<div class="print-button pull-left">
											<input  type="submit" value="<?php esc_html_e('Delete Selected','hospital_mgt');?>" name="delete_selected" class="btn btn-danger delete_selected "/>
										</div>
										<?php  
											}?>
									</div><!--TABLE RESPONSIVE DIV END -->
								</div><!-- TABLE BODY DIV END -->								   
							</form>
						 <?php 
						}
						if($active_tab == 'addinstrument')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/instrument-mgt/add-instrument.php';
						}
						if($active_tab == 'assigned_instrumentlist')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/instrument-mgt/assigned-instrument-list.php';
						}
						if($active_tab == 'assigninstrument')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/instrument-mgt/assign-instrument.php';
						}
						 ?>
                    </div><!-- PANEL BODY DIV END -->		
		        </div><!-- PANEL WHITE DIV END -->		
	        </div>
        </div><!-- ROW DIV END -->		
    </div><!-- END MAIN WRAPER DIV -->	