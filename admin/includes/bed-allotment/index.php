<?php
//bed allotment
$obj_bed = new MJ_hmgt_bedmanage();
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
	$user_access=MJ_hmgt_get_access_right_for_management_user_page('bedallotment');
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
			if (isset ( $_REQUEST ['page'] ) && 'bedallotment' == $user_access['page_link'] && ($_REQUEST['action']=='edit'))
			{
				if($user_access['edit']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}			
			}
			if (isset ( $_REQUEST ['page'] ) && 'bedallotment' == $user_access['page_link'] && ($_REQUEST['action']=='delete'))
			{
				if($user_access['delete']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}	
			}
			if (isset ( $_REQUEST ['page'] ) && 'bedallotment' == $user_access['page_link'] && ($_REQUEST['action']=='insert')) 
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
$active_tab = isset($_GET['tab'])?$_GET['tab']:'allotedbedlist';
?>
<div class="datas"></div>
<!-- POP up code -->
<div class="popup-bg zindex_100000">
    <div class="overlay-content">
		<div class="modal-content">
		   <div class="category_list"></div>
		</div>
    </div> 
</div>
<!-- End POP-UP Code -->
<div class="page-inner min_height_1631"><!-- page INNER DIV START-->
	<div class="page-title"><!-- PAGE TITLE DIV START-->
		<h3><img src="<?php echo esc_url(get_option( 'hmgt_hospital_logo', 'hospital_mgt' )) ?>" class="img-circle head_logo" width="40" height="40" /><?php echo esc_html(get_option('hmgt_hospital_name','hospital_mgt'));?></h3>
	</div><!-- PAGE TITLE DIV END-->
<?php 
//--------------- SAVE BED ALLOMENT -------------//
if(isset($_REQUEST['bedallotment']))
{	
	$nonce = $_POST['_wpnonce'];
	if (wp_verify_nonce( $nonce, 'bedallotment_nonce' ) )
	{
		if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'insert' || $_REQUEST['action'] == 'edit'))
		{
			$result = $obj_bed->MJ_hmgt_add_bed_allotment($_POST);
			if($result)
			{
				if($_REQUEST['action'] == 'edit')
				{
					wp_redirect(admin_url().'admin.php?page=hmgt_bedallotment&tab=allotedbedlist&message=2');
					exit();
				}
				else
				{
					wp_redirect(admin_url().'admin.php?page=hmgt_bedallotment&tab=allotedbedlist&message=1');
				}				
			}
		}
	}
}
if(isset($_POST['bed_transfar']))
{
	$nonce = $_POST['_wpnonce'];
	if (wp_verify_nonce( $nonce, 'bed_transfar_nonce' ) )
	{
		if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'transfar')){	
			$result = $obj_bed->MJ_hmgt_patient_bed_transfar($_POST);
			if($result)
			{		
				wp_redirect(admin_url().'admin.php?page=hmgt_bedallotment&tab=allotedbedlist&message=4');
			}
		}
	}
}
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete')
{
	$result = $obj_bed->MJ_hmgt_delete_bedallocate_record($_REQUEST['allotment_id']);
	if($result)
	{
		wp_redirect ( admin_url() . 'admin.php?page=hmgt_bedallotment&tab=allotedbedlist&message=3');
	}
}
if(isset($_REQUEST['delete_selected']))
	{		
		if(!empty($_REQUEST['selected_id']))
		{
			
			foreach($_REQUEST['selected_id'] as $id)
			{
				$result=$obj_bed->MJ_hmgt_delete_bedallocate_record($id);
			}
			if($result)
			{
				wp_redirect ( admin_url() . 'admin.php?page=hmgt_bedallotment&tab=allotedbedlist&message=3');
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
	if($message == 1){ ?>
		<div id="message" class="updated below-h2 notice is-dismissible"><p>		
			<?php esc_html_e('Record inserted successfully','hospital_mgt'); ?>
		</p></div>
		<?php 			
		}
		elseif($message == 2)
		{ ?>
			<div id="message" class="updated below-h2 notice is-dismissible"><p>
				<?php	esc_html_e("Record updated successfully.",'hospital_mgt');?>
			</p></div>
		<?php 			
		}
		elseif($message == 3) 
		{?>
		<div id="message" class="updated below-h2 notice is-dismissible"><p>
		<?php 
			esc_html_e('Record deleted successfully','hospital_mgt');
		?></div></p><?php
				
		}
		
		elseif($message ==4) 
		{?>
		<div id="message" class="updated below-h2 notice is-dismissible"><p>
		<?php 
			esc_html_e('Bed successfully Transfered','hospital_mgt');
		?></div></p><?php	
		}
	}
	?>
	<div id="main-wrapper"><!-- MAIN WRAPPER DIV START-->
		<div class="row"><!-- ROW DIV START-->
			<div class="col-md-12">
				<div class="panel panel-white"><!-- PANEL WHITE DIV START-->				
					<div class="panel-body"><!-- PANEL BODY DIV START-->
						<h2 class="nav-tab-wrapper">
							<a href="?page=hmgt_bedallotment&tab=allotedbedlist" class="nav-tab <?php echo $active_tab == 'allotedbedlist' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-menu"></span> '.esc_html__('Assigned Bed List', 'hospital_mgt'); ?></a>
							<?php  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
							{?>
							<a href="?page=hmgt_bedallotment&tab=bedassign&&action=edit&allotment_id=<?php echo $_REQUEST['allotment_id'];?>" class="nav-tab <?php echo $active_tab == 'bedassign' ? 'nav-tab-active' : ''; ?>">
							<?php esc_html_e('Edit Assigned Bed', 'hospital_mgt'); ?></a>  
							<?php 
							}
							else
							{
								if($user_access_add == 1)
							{?>
							<a href="?page=hmgt_bedallotment&tab=bedassign" class="nav-tab <?php echo $active_tab == 'bedassign' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-plus-alt"></span> '.esc_html__('Assign Bed', 'hospital_mgt'); ?></a>  
							<?php } }?>
							<?php if($active_tab=="transfar"){ ?>
						   <a href="?page=hmgt_bedallotment&tab=transfar&action=transfar" class="nav-tab <?php echo $active_tab == 'transfar' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-menu"></span> '.esc_html__('Transfer Bed', 'hospital_mgt'); ?></a>
							 <?php } ?>
						</h2>
						<?php 						
						if($active_tab == 'allotedbedlist')
						{ 
						?>	
						<script type="text/javascript">
						jQuery(document).ready(function() {
							"use strict";
							jQuery('#bedallotmentlist').DataTable({
								"responsive": true,
								 "order": [[ 2, "asc" ]],
								 "aoColumns":[
											  {"bSortable": false},
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
						} );
						</script>
						<form name="wcwm_report" action="" method="post">
							<div class="panel-body"><!-- PANEL BODY DIV START-->
								<div class="table-responsive"><!-- TABLE RESPONSIVE DIV START-->
									<table id="bedallotmentlist" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th><input type="checkbox" class="select_all"></th>
												<th><?php esc_html_e( 'Bed Category', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e( 'Bed Number', 'hospital_mgt' ) ;?></th>			 
												<th><?php esc_html_e( 'Patient', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e( 'Nurse', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e( 'Allotment Date', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e( 'Expected Discharge Date', 'hospital_mgt' ) ;?></th>
												<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
											</tr>
									    </thead>
										<tfoot>
											<tr>
												<th></th>
												<th><?php esc_html_e( 'Bed Category', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e( 'Bed Number', 'hospital_mgt' ) ;?></th>			 
												<th><?php esc_html_e( 'Patient', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e( 'Nurse', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e( 'Allotment Date', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e( 'Expected Discharge Date', 'hospital_mgt' ) ;?></th>
												<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
											</tr>
										</tfoot>
										<tbody>
											 <?php 
											$bedallotment_data=$obj_bed->MJ_hmgt_get_all_bedallotment();
											if(!empty($bedallotment_data))
											{
												foreach ($bedallotment_data as $retrieved_data)
												{ 
													$patient_data =	MJ_hmgt_get_user_detail_byid($retrieved_data->patient_id);
											?>
													<tr>
														<td class="title"><input type="checkbox" name="selected_id[]" class="sub_chk" value="<?php echo esc_attr($retrieved_data->bed_allotment_id); ?>"></td>
														<td class="bed_type"><?php echo esc_html($obj_bed->MJ_hmgt_get_bedtype_name($retrieved_data->bed_type_id));	?></td>
														<td class="bed_number"><?php 
														if(!empty($retrieved_data->bed_number))
														{  
															echo $obj_bed->MJ_hmgt_get_bed_number($retrieved_data->bed_number);
														}
														else
														{ 
															echo '-'; 
														}	
														?></td>
														<td class="patient"><?php echo esc_html($patient_data['first_name']." ".$patient_data['last_name']."(".$patient_data['patient_id'].")");?></td>
														<td class="nurse">
														<?php 
														if(!empty($retrieved_data->bed_allotment_id))
														{ 
															$nurselist =  $obj_bed->MJ_hmgt_get_nurse_by_assignid($retrieved_data->bed_allotment_id) ;
															$abc=array();
															foreach($nurselist as $assign_id)
															{
																$nurse_data =	MJ_hmgt_get_user_detail_byid($assign_id->child_id);
																$abc[]=$nurse_data['first_name']." ".$nurse_data['last_name'];
															}
															echo implode(",",$abc);
														}
														else
														{ 
															echo '-'; 
														}	
														?>
														</td>
														<td class="allotment_time"><?php  echo date(MJ_hmgt_date_formate(),strtotime(esc_html($retrieved_data->allotment_date)));?></td>
														<td class="discharge_time"><?php  echo date(MJ_hmgt_date_formate(),strtotime(esc_html($retrieved_data->discharge_time)));?></td>
														<td class="action"> 
														<a href="javascript:void(0);" class="view_details_popup btn btn-default" id="<?php echo esc_attr( $retrieved_data->bed_allotment_id); ?>" type="<?php echo 'view_allotedbed';?>"><i class="fa fa-eye"> </i> <?php _e('View', 'hospital_mgt' ) ;?> </a>
														<a href="?page=hmgt_bedallotment&tab=transfar&action=transfar&allotment_id=<?php echo esc_attr($retrieved_data->bed_allotment_id);?>" class="btn btn-success"> <?php esc_html_e('Transfer Bed', 'hospital_mgt' ) ;?></a>
														<?php if($user_access_edit == 1)
														{?>
														<a href="?page=hmgt_bedallotment&tab=bedassign&action=edit&allotment_id=<?php echo esc_attr($retrieved_data->bed_allotment_id);?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
														<?php 
														} 
														?>
														<?php if($user_access_delete == 1)
														{?>	
														<a href="?page=hmgt_bedallotment&tab=allotedbedlist&action=delete&allotment_id=<?php echo esc_attr($retrieved_data->bed_allotment_id);?>" class="btn btn-danger" 
														onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this record?','hospital_mgt');?>');">
														<?php esc_html_e( 'Delete', 'hospital_mgt' ) ;?> </a>
														<?php 
														} 
														?>
														</td>
														
													</tr>
												<?php 
												} 
											}?>
										</tbody>
									</table>
									<?php if($user_access_delete == 1)
														{?>	
									<div class="print-button pull-left">
										<input  type="submit" value="<?php esc_html_e('Delete Selected','hospital_mgt');?>" name="delete_selected" class="btn btn-danger delete_selected "/>
									</div>
									<?php 
										} 
										?>
							    </div><!-- TABLE RESPONSIVE DIV END-->
							</div><!-- PANEL BODY DIV END-->
					    </form>
						<?php 
						}
						if($active_tab == 'bedassign')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/bed-allotment/bed-allotment.php';
						}
						if($active_tab == 'transfar')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/bed-allotment/transfar.php';
						}
						?>
                    </div><!-- PANEL BODY DIV END-->	
		        </div><!-- PANEL WHITE DIV END-->	
	        </div>
        </div><!-- ROW DIV END-->	
	</div><!-- main-wrapper DIV END -->	