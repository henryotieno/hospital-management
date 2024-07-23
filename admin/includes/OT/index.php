<?php
//Operation Theator
$obj_ot = new MJ_hmgt_operation();
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
	$user_access=MJ_hmgt_get_access_right_for_management_user_page('operation');
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
			if (isset ( $_REQUEST ['page'] ) && 'operation' == $user_access['page_link'] && ($_REQUEST['action']=='edit'))
			{
				if($user_access['edit']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}			
			}
			if (isset ( $_REQUEST ['page'] ) && 'operation' == $user_access['page_link'] && ($_REQUEST['action']=='delete'))
			{
				if($user_access['delete']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}	
			}
			if (isset ( $_REQUEST ['page'] ) && 'operation' == $user_access['page_link'] && ($_REQUEST['action']=='insert')) 
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
$active_tab = isset($_GET['tab'])?$_GET['tab']:'operationlist';
?>
<div class="datas"> </div>
<!-- POP up code -->
<div class="popup-bg zindex_100000">
    <div class="overlay-content overlay_content_css">
		<div class="modal-content">
		   <div class="category_list"></div>
		</div>
    </div> 
</div>
<!-- End POP-UP Code -->
<div class="page-inner min_height_1631"><!-- PAGE INNER DIV START-->
	<div class="page-title"><!-- PAGE TITILE DIV START-->
		<h3><img src="<?php echo esc_url(get_option( 'hmgt_hospital_logo', 'hospital_mgt' )) ?>" class="img-circle head_logo" width="40" height="40" /><?php echo esc_html(get_option('hmgt_hospital_name','hospital_mgt'));?></h3>
	</div><!-- PAGE TITLE DIV END-->
<?php 
//-------------- SAVE OPERATION -----------//
if(isset($_REQUEST['save_operation']))
{
	$nonce = $_POST['_wpnonce'];
	if (wp_verify_nonce( $nonce, 'save_operation_nonce' ) )
	{	
		if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'insert' || $_REQUEST['action'] == 'edit'))
		{
			$result = $obj_ot->MJ_hmgt_add_operation_theater($_POST);
			if($result)
			{
				if($_REQUEST['action'] == 'edit')
				{
					wp_redirect ( admin_url().'admin.php?page=hmgt_operation&tab=operationlist&message=2');
				}
				else 
				{
					wp_redirect ( admin_url().'admin.php?page=hmgt_operation&tab=operationlist&message=1'); 
				}
			}
		}
	}
}
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete')
{			
	$result = $obj_ot->MJ_hmgt_delete_oprationtheater($_REQUEST['ot_id']);
	if($result)
	{
		wp_redirect ( admin_url().'admin.php?page=hmgt_operation&tab=operationlist&message=3');
	}
}
if(isset($_REQUEST['delete_selected']))
{		
	if(!empty($_REQUEST['selected_id']))
	{
		
		foreach($_REQUEST['selected_id'] as $id)
		{
			$result=$obj_ot->MJ_hmgt_delete_oprationtheater($id);
		}
		if($result)
		{
			wp_redirect ( admin_url().'admin.php?page=hmgt_operation&tab=operationlist&message=3');
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
}?>
 <!-- MAIN WRAPPER DIV START-->
	<div id="main-wrapper">
		<div class="row"> <!-- ROW DIV START-->
			<div class="col-md-12">
				<div class="panel panel-white"> <!-- PANEL WHITE DIV START-->
				<!-- PANEL BODY DIV START -->
					<div class="panel-body">
						<h2 class="nav-tab-wrapper">
							<a href="?page=hmgt_operation&tab=operationlist" class="nav-tab <?php echo $active_tab == 'operationlist' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-menu"></span>'.esc_html__('Operation List', 'hospital_mgt'); ?></a>
							<?php  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
							{?>
							<a href="?page=hmgt_operation&tab=addoperation&&action=edit&ot_id=<?php echo $_REQUEST['ot_id'];?>" class="nav-tab <?php echo $active_tab == 'addoperation' ? 'nav-tab-active' : ''; ?>">
							<?php esc_html_e('Edit Operation List', 'hospital_mgt'); ?></a>  
							<?php 
							}
							else
							{
								if($user_access_add == 1)
							{?>
								<a href="?page=hmgt_operation&tab=addoperation" class="nav-tab <?php echo $active_tab == 'addoperation' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-plus-alt"></span>'.esc_html__('Add Operation', 'hospital_mgt'); ?></a>  
							<?php  } }?>
						   
						</h2>
						<?php				 
						if($active_tab == 'operationlist')
						{ ?>	
							<script type="text/javascript">
						   jQuery(document).ready(function() {
							   "use strict";
							jQuery('#hmgt_operation').DataTable({
								"responsive": true,
								 "order": [[ 4, "desc" ]],
						
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
						    <div class="panel-body">
								<div class="table-responsive">
									<table id="hmgt_operation" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th><input type="checkbox" class="select_all"></th>
												 <th><?php esc_html_e( 'Operation Name', 'hospital_mgt' ) ;?></th>
												 <th><?php esc_html_e( 'Patient', 'hospital_mgt' ) ;?></th>
												 <th><?php esc_html_e( 'Surgeon', 'hospital_mgt' ) ;?></th>
												 <th><?php esc_html_e( 'Date', 'hospital_mgt' ) ;?></th>
												 <th><?php esc_html_e( 'Operation Charge', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
												  <th><?php esc_html_e( 'Tax Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
												   <th><?php esc_html_e( 'Total Operation Charge', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
												 <th><?php esc_html_e( 'Operation Status', 'hospital_mgt' ) ;?></th>
												 <th><?php esc_html_e( 'Out Come Status', 'hospital_mgt' ) ;?></th>
												 <th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
											</tr>
									    </thead>
										<tfoot>
											<tr>
												<th></th>
												<th><?php esc_html_e( 'Operation Name', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e( 'Patient', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e( 'Surgeon', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e( 'Date', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e( 'Operation Charge', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
												<th><?php esc_html_e( 'Tax Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
												<th><?php esc_html_e( 'Total Operation Charge', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
												<th><?php esc_html_e( 'Operation Status', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e( 'Out Come Status', 'hospital_mgt' ) ;?></th>
												<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
											</tr>
										</tfoot>
										<tbody>
										 <?php 
										$ot_data=$obj_ot->MJ_hmgt_get_all_operation();
										if(!empty($ot_data))
										{
											foreach ($ot_data as $retrieved_data)
											{ 
												$patient_data =	MJ_hmgt_get_user_detail_byid($retrieved_data->patient_id);
											?>
											<tr>
												<td class="title"><input type="checkbox" name="selected_id[]" class="sub_chk" value="<?php echo esc_attr($retrieved_data->operation_id); ?>"></td>
												<td class="operation_name"><?php echo $obj_ot->MJ_hmgt_get_operation_name($retrieved_data->operation_title);?></td>
												<td class="patient"><?php echo $patient_data['first_name']." ".$patient_data['last_name']."(".$patient_data['patient_id'].")";?></td>
												<td class="surgen">
												<?php 
													$surgenlist =  $obj_ot->MJ_hmgt_get_doctor_by_oprationid($retrieved_data->operation_id) ;
													$surgenlist_names = '';
													foreach($surgenlist as $assign_id)
													{
														$doctory_data =	MJ_hmgt_get_user_detail_byid($assign_id->child_id);
													  $surgenlist_names.= $doctory_data['first_name']." ".$doctory_data['last_name'].",";
													}
													echo rtrim($surgenlist_names, ',');
												?></td>
												
												<td class=""><?php  echo date(MJ_hmgt_date_formate(),strtotime($retrieved_data->operation_date));	?></td>
												<td class=""><?php echo number_format($retrieved_data->ot_charge, 2, '.', '');?></td>
												<td class=""><?php echo number_format($retrieved_data->ot_tax, 2, '.', ''); ?></td>
												<td class=""><?php echo number_format($retrieved_data->operation_charge, 2, '.', ''); ?></td>
												<td class=""><?php if(!empty($retrieved_data->operation_status)) { esc_html_e(''.$retrieved_data->operation_status.'','hospital_mgt'); }else{ echo '-'; } ?></td>
												<td class=""><?php if(!empty($retrieved_data->out_come_status)) { esc_html_e(''.$retrieved_data->out_come_status.'','hospital_mgt'); }else{ echo '-'; } ?></td>
												<td class="action"> 
												
												<a href="javascript:void(0);" class="view_details_popup btn btn-default" id="<?php echo esc_attr( $retrieved_data->operation_id); ?>" type="<?php echo 'view_ot';?>"><i class="fa fa-eye"> </i> <?php esc_html_e('View', 'hospital_mgt' ) ;?> </a>
												<?php if($user_access_edit == 1)
												{?>
												<a href="?page=hmgt_operation&tab=addoperation&action=edit&ot_id=<?php echo esc_attr($retrieved_data->operation_id);?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
												<?php 
												} 
												?>
												<?php if($user_access_delete == 1)
												{?>	
												<a href="?page=hmgt_operation&tab=operationlist&action=delete&ot_id=<?php echo esc_attr( $retrieved_data->operation_id);?>" class="btn btn-danger" 
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
							    </div>
						    </div>						   
					    </form>
						<?php 
						}						
						if($active_tab == 'addoperation')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/OT/add-opration.php';
						}
						?>
                    </div><!-- PANEL BODY DIV END--> 	
		        </div><!-- PANEL WHITE DIV END-->
	        </div>
        </div><!-- END ROW DIV -->
    </div><!-- END MAIN WRAPPER DIV -->