<?php
MJ_hmgt_browser_javascript_check();
$obj_var=new MJ_hmgt_prescription();
$obj_treatment=new MJ_hmgt_treatment();
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
	$user_access=MJ_hmgt_get_access_right_for_management_user_page('prescription');
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
			if (isset ( $_REQUEST ['page'] ) && 'prescription' == $user_access['page_link'] && ($_REQUEST['action']=='edit'))
			{
				if($user_access['edit']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}			
			}
			if (isset ( $_REQUEST ['page'] ) && 'prescription' == $user_access['page_link'] && ($_REQUEST['action']=='delete'))
			{
				if($user_access['delete']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}	
			}
			if (isset ( $_REQUEST ['page'] ) && 'prescription' == $user_access['page_link'] && ($_REQUEST['action']=='insert')) 
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
$active_tab = isset($_GET['tab'])?$_GET['tab']:'prescriptionlist';
?>
<div class="popup-bg zindex_100000">
	<div class="overlay-content">
		<div class="prescription_content"></div>    
	</div> 
</div>  
<div class="page-inner min_height_1631"><!--paGE INNER DIV START-->	      
   <div class="page-title"><!--paGE TITLE DIV START-->
		<h3><img src="<?php echo esc_url(get_option( 'hmgt_hospital_logo', 'hospital_mgt' )) ?>" class="img-circle head_logo" width="40" height="40" /><?php echo esc_html(get_option('hmgt_hospital_name','hospital_mgt'));?></h3>
	</div><!--paGE TITLE DIV END-->
	<?php 
	if(isset($_POST['save_prescription']))
	{
		$nonce = $_POST['_wpnonce'];
		if (wp_verify_nonce( $nonce, 'save_prescription_nonce' ) )
		{	
			if($_REQUEST['action']=='edit')
			{
				$result=$obj_var->MJ_hmgt_add_prescription($_POST);
				if($result)
				{
					wp_redirect ( admin_url() . 'admin.php?page=hmgt_prescription&tab=prescriptionlist&message=2');
				}
			}
			else
			{
				$result=$obj_var->MJ_hmgt_add_prescription($_POST);
				if($result)
				{
					wp_redirect ( admin_url() . 'admin.php?page=hmgt_prescription&tab=prescriptionlist&message=1');
				}
			}
		}
	}
	if(isset($_REQUEST['action'])&& $_REQUEST['action']=='delete')
	{
			
		$result=$obj_var->MJ_hmgt_delete_prescription($_REQUEST['prescription_id']);
		if($result)
		{
			wp_redirect ( admin_url() . 'admin.php?page=hmgt_prescription&tab=prescriptionlist&message=3');
		}
	}
	if(isset($_REQUEST['delete_selected']))
	{		
		if(!empty($_REQUEST['selected_id']))
		{
			
			foreach($_REQUEST['selected_id'] as $id)
			{
				$result=$obj_var->MJ_hmgt_delete_prescription($id);
			}
			if($result)
			{
				wp_redirect ( admin_url() . 'admin.php?page=hmgt_prescription&tab=prescriptionlist&message=3');
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
	}
	?>
	<!--WRAPPER DIV START-->
	<div id="main-wrapper">
		<div class="row"><!--ROW DIV START-->
			<div class="col-md-12">
			<!--PANEL WHITE DIV START-->
				<div class="panel panel-white">
				<!--PANEL BODY DIV START-->
					<div class="panel-body">
						<h2 class="nav-tab-wrapper">
							<a href="?page=hmgt_prescription&tab=prescriptionlist" class="nav-tab <?php echo $active_tab == 'prescriptionlist' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-menu"></span> '.esc_html__('Prescription List', 'hospital_mgt'); ?></a>
							
							<?php  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
							{?>
							<a href="?page=hmgt_prescription&tab=addprescription&&action=edit&prescription_id=<?php echo $_REQUEST['prescription_id'];?>" class="nav-tab <?php echo $active_tab == 'addprescription' ? 'nav-tab-active' : ''; ?>">
							<?php esc_html_e('Edit Prescription', 'hospital_mgt'); ?></a>  
							<?php 
							}
							else
							{if($user_access_add == 1)
							{?>
								<a href="?page=hmgt_prescription&tab=addprescription" class="nav-tab <?php echo $active_tab == 'addprescription' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-plus-alt"></span> '.esc_html__('Add Prescription', 'hospital_mgt'); ?></a>  
							<?php  }}?>
						</h2>
						<?php 
						if($active_tab == 'prescriptionlist')
						{ ?>	
							<script type="text/javascript">
							jQuery(document).ready(function() {
								"use strict";
								jQuery('#prescription_list').DataTable({
									"responsive": true,							
									"order": [[ 1, "desc" ]],
									"aoColumns":[
												  {"bSortable": false},
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
								<div class="panel-body"><!--PANEL BODY DIV START-->
									<div class="table-responsive"><!--TABLE RESPONSIVE DIV START-->
										<table id="prescription_list" class="display" cellspacing="0" width="100%">
											<thead>
												<tr>
													<th><input type="checkbox" class="select_all"></th>
													<th><?php  esc_html_e( 'Date', 'hospital_mgt' ) ;?></th>
													<th> <?php esc_html_e( 'Patient ID', 'hospital_mgt' ) ;?></th>
													<th> <?php esc_html_e( 'Patient Name', 'hospital_mgt' ) ;?></th>
													<th> <?php esc_html_e( 'Type', 'hospital_mgt' ) ;?></th>
													<th> <?php esc_html_e( 'Treatment', 'hospital_mgt' ) ;?></th>
													<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
												</tr>
											</thead>
											<tfoot>
												<tr>
													<th></th>
													<th><?php  esc_html_e( 'Date', 'hospital_mgt' ) ;?></th>
													<th> <?php esc_html_e( 'Patient ID', 'hospital_mgt' ) ;?></th>
													<th> <?php esc_html_e( 'Patient Name', 'hospital_mgt' ) ;?></th>
													<th> <?php esc_html_e( 'Type', 'hospital_mgt' ) ;?></th>
													<th> <?php esc_html_e( 'Treatment', 'hospital_mgt' ) ;?></th>
													<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
												</tr>
											</tfoot>
											<tbody>
											 <?php 
											$prescriptiondata=$obj_var->MJ_hmgt_get_all_prescription();
											
											if(!empty($prescriptiondata))
											{
												foreach ($prescriptiondata as $retrieved_data){ 
											 ?>
												<tr>
													<td class="title"><input type="checkbox" name="selected_id[]" class="sub_chk" value="<?php echo esc_attr($retrieved_data->priscription_id); ?>"></td>
													<td class="name"><a href="?page=hmgt_prescription&tab=addprescription&action=edit&prescription_id=<?php echo $retrieved_data->priscription_id;?>"><?php  echo date(MJ_hmgt_date_formate(),strtotime($retrieved_data->pris_create_date));?></a></td>
													<td class="patient">
														 <?php 
														   echo $patient_id=get_user_meta($retrieved_data->patient_id, 'patient_id', true);
														 ?>
													</td>
													<td class="patient">
														 <?php 
															$patient = MJ_hmgt_get_user_detail_byid( $retrieved_data->patient_id);
															echo  esc_html($patient['first_name']." ".$patient['last_name']);?>
													</td>
												   <td class=""><?php 
												   
												    if(!empty($retrieved_data->prescription_type))
												    {
														if($retrieved_data->prescription_type == "treatment")
														{
															$prescription_type=esc_html__("Treatment","hospital_mgt");
														}
													   elseif($retrieved_data->prescription_type == "report")
														{
															$prescription_type=esc_html__("Report","hospital_mgt");
														}
														else
														{ 
															$prescription_type="-";
														}
													
														echo $prescription_type; 
													}
													else
													{ 
														echo '-'; 
													}
												   ?> </td>
													<td class="treatment"><?php 
													if(!empty($retrieved_data->teratment_id)){ echo $treatment=$obj_treatment->MJ_hmgt_get_treatment_name($retrieved_data->teratment_id); }else{ echo '-'; } ?></td>
													<td class="action"> 
														  <a href="javascript:void(0);" class="btn btn-default view-prescription" id="<?php echo esc_attr($retrieved_data->priscription_id);?>" prescription_type="<?php echo esc_attr($retrieved_data->prescription_type); ?>"><i class="fa fa-eye"></i> <?php esc_html_e('View','hospital_mgt');?></a>
														  <?php if($user_access_edit == 1)
															{?>
														<a href="?page=hmgt_prescription&tab=addprescription&action=edit&prescription_id=<?php echo esc_attr($retrieved_data->priscription_id);?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
														<?php 
															} 
															?>
															<?php if($user_access_delete == 1)
															{?>	
														<a href="?page=hmgt_prescription&tab=prescriptionlist&action=delete&prescription_id=<?php echo esc_attr($retrieved_data->priscription_id);?>" class="btn btn-danger" 
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
										<?php 
										if($user_access_delete == 1)
										{?>
										<div class="print-button pull-left">
											<input  type="submit" value="<?php esc_html_e('Delete Selected','hospital_mgt');?>" name="delete_selected" class="btn btn-danger delete_selected "/>
										</div>
										<?php 
											} ?>
									</div><!-- TABLE RESPONSIVE DIV END-->
								</div> <!--PANEL BODY DIV END-->
							</form>
						<?php 
						}
						if($active_tab == 'addprescription')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/prescription/add_prescription.php';
						}
						?>
                    </div><!--PANEL BODY DIV END-->
	            </div><!--PANEL WHITE DIV END-->
	        </div>
        </div><!-- END ROW DIV-->
    </div>
	<!-- END WRAPPER DIV-->
<?php  ?>