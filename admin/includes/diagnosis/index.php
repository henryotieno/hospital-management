<?php
$obj_dignosis = new MJ_hmgt_dignosis();
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
	$user_access=MJ_hmgt_get_access_right_for_management_user_page('diagnosis');
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
			if (isset ( $_REQUEST ['page'] ) && 'diagnosis' == $user_access['page_link'] && ($_REQUEST['action']=='edit'))
			{
				if($user_access['edit']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}			
			}
			if (isset ( $_REQUEST ['page'] ) && 'diagnosis' == $user_access['page_link'] && ($_REQUEST['action']=='delete'))
			{
				if($user_access['delete']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}	
			}
			if (isset ( $_REQUEST ['page'] ) && 'diagnosis' == $user_access['page_link'] && ($_REQUEST['action']=='insert')) 
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
$active_tab = isset($_GET['tab'])?$_GET['tab']:'diagnosislist';
?>
<!-- POP up code -->
<div class="popup-bg zindex_100000">
    <div class="overlay-content overlay_content_css">
		<div class="modal-content">
			<div class="category_list">
			</div>
		</div>
    </div> 
</div>
<!-- End POP-UP Code -->

<div class="page-inner min_height_1631"><!-- PAGE INNER DIV START-->
	<div class="page-title"><!-- PAGE TITILE DIV START-->
		<h3><img src="<?php echo get_option( 'hmgt_hospital_logo', 'hospital_mgt'); ?>" class="img-circle head_logo" width="40" height="40" /><?php echo get_option('hmgt_hospital_name','hospital_mgt');?></h3>
	</div><!-- PAGE TITILE DIV END-->
	<?php 
	//---------------- SAVE DIAGNOSIS -------------------//
	if(isset($_REQUEST['save_diagnosis']))
	{	
		$nonce = $_POST['_wpnonce'];
		if (wp_verify_nonce( $nonce, 'save_diagnosis_nonce' ) )
		{
			if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'insert' || $_REQUEST['action'] == 'edit'))
			{	 
				if(isset($_FILES['document']) && !empty($_FILES['document']) && $_FILES['document']['size'] !=0)
				{		
					$valid='0';
					
					$count_array=count($_FILES['document']['name']);

					for($a=0;$a<$count_array;$a++)
					{
						
						foreach($_FILES['document'] as $image_key=>$image_val)
						{						
							$value = explode(".", $_FILES['document']['name'][$a]);
						
							$file_ext = strtolower(array_pop($value));
							$extensions = array("jpg","jpeg","png","doc","gif","pdf","zip","");
							if(in_array($file_ext,$extensions ) == false)
							{
								$valid='1';
							}	
						}
					}
					if($valid == '1')
					{
					?>
						<div id="message" class="updated below-h2 notice is-dismissible">
						<p>
						<?php 
							esc_html_e('Sorry, Only JPG,JPEG,PNG,GIF,DOC,PDF and ZIP files are allowed.','hospital_mgt');
						?></p></div>
						<?php 
					}
					else
					{ 
					
						$result = $obj_dignosis->MJ_hmgt_add_dignosis($_POST);
					
						if($result)
						{
							if($_REQUEST['action'] == 'edit')
							{
								wp_redirect ( admin_url() . 'admin.php?page=hmgt_diagnosis&tab=diagnosislist&message=2');
							}
							else
							{
								wp_redirect ( admin_url() . 'admin.php?page=hmgt_diagnosis&tab=diagnosislist&message=1');
							}
						}	
					}		
				}
				else
				{
					
					$result = $obj_dignosis->MJ_hmgt_add_dignosis($_POST);
					
					
					if($result)
					{
						if($_REQUEST['action'] == 'edit')
						{
							wp_redirect ( admin_url() . 'admin.php?page=hmgt_diagnosis&tab=diagnosislist&message=2');
						}
						else
						{
							wp_redirect ( admin_url() . 'admin.php?page=hmgt_diagnosis&tab=diagnosislist&message=1');
						}
					}	
				}					
			}
		}
	}
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete')
	{
		$result = $obj_dignosis->MJ_hmgt_delete_dignosis($_REQUEST['diagnosis_id']);
		if($result)
		{
			wp_redirect ( admin_url() . 'admin.php?page=hmgt_diagnosis&tab=diagnosislist&message=3');
		}
	}
	if(isset($_REQUEST['delete_selected']))
	{		
		if(!empty($_REQUEST['selected_id']))
		{
			
			foreach($_REQUEST['selected_id'] as $id)
			{
				$result=$obj_dignosis->MJ_hmgt_delete_dignosis($id);
			}
			if($result)
			{
				wp_redirect ( admin_url() . 'admin.php?page=hmgt_diagnosis&tab=diagnosislist&message=3');
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
		{	?>
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
							<a href="?page=hmgt_diagnosis&tab=diagnosislist" class="nav-tab <?php echo $active_tab == 'diagnosislist' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-menu"></span>'.esc_html__('Diagnosis Report List', 'hospital_mgt'); ?></a>
							
							<?php  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
							{?>
							<a href="?page=hmgt_diagnosis&tab=adddiagnosis&&action=edit&diagnosis_id=<?php echo $_REQUEST['diagnosis_id'];?>" class="nav-tab <?php echo $active_tab == 'adddiagnosis' ? 'nav-tab-active' : ''; ?>">
							<?php esc_html_e('Edit Diagnosis Report', 'hospital_mgt'); ?></a>  
							<?php 
							}
							else
							{
								if($user_access_add == 1)
							{?>
								<a href="?page=hmgt_diagnosis&tab=adddiagnosis" class="nav-tab <?php echo $active_tab == 'adddiagnosis' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-plus-alt"></span>'.esc_html__('Add Diagnosis Report', 'hospital_mgt'); ?></a>  
							<?php } }?>
						   
						</h2>
						<?php 
						if($active_tab == 'diagnosislist')
						{ 
						
						?>	
						<script type="text/javascript">
						jQuery(document).ready(function() {
							"use strict";
							jQuery('#diagnosis').DataTable({
								"responsive": true,
								 "order": [[ 1, "desc" ]],
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
							<div class="panel-body"><!-- PANEL BODY DIV START-->
								<div class="table-responsive"><!-- TABLE RESPONSIVE DIV START-->
									<table id="diagnosis" class="display" cellspacing="0" width="100%">
										<thead>
										<tr>
											<th><input type="checkbox" class="select_all"></th>
										<th><?php  esc_html_e( 'Date', 'hospital_mgt' ) ;?></th>
										 <th> <?php esc_html_e( 'Patient ID-Name', 'hospital_mgt' ) ;?></th>
										  <th> <?php esc_html_e( 'Report Type & Amount', 'hospital_mgt' ) ;?></th>
											<th width="250px"> <?php esc_html_e( 'Description', 'hospital_mgt' ) ;?></th>
											<th> <?php esc_html_e( 'Report', 'hospital_mgt' ) ;?></th>
											<th><?php esc_html_e( 'Report Cost', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
											<th><?php esc_html_e( 'Tax Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
											<th><?php esc_html_e( 'Total Report Cost', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
											<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
										</tr>
										</thead>
										<tfoot>
											<tr>
												<th></th>
											<th><?php  esc_html_e( 'Date', 'hospital_mgt' ) ;?></th>
											<th> <?php esc_html_e( 'Patient ID-Name', 'hospital_mgt' ) ;?></th>
											  <th> <?php esc_html_e( 'Report Type & Amount', 'hospital_mgt' ) ;?></th>
												<th width="250px">  <?php esc_html_e( 'Description', 'hospital_mgt' ) ;?></th>
												<th> <?php esc_html_e( 'Report', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e( 'Report Cost', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
												<th><?php esc_html_e( 'Tax Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
												<th><?php esc_html_e( 'Total Report Cost', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
												<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
											</tr>
										</tfoot>
										<tbody>
										<?php 
										$dignosis_data=$obj_dignosis->MJ_hmgt_get_all_dignosis_report();
										if(!empty($dignosis_data))
										{
											foreach ($dignosis_data as $retrieved_data)
											{ 
											?>
											<tr>
												<td class="title"><input type="checkbox" name="selected_id[]" class="sub_chk" value="<?php echo esc_attr($retrieved_data->diagnosis_id); ?>"></td>
												<td class="date"><?php  echo date(MJ_hmgt_date_formate(),strtotime($retrieved_data->diagnosis_date));	?></td>
												<td class="patient_id">
												<?php 
													$patient = MJ_hmgt_get_user_detail_byid( $retrieved_data->patient_id);
													echo esc_html($patient['id']." - ".$patient['first_name']." ".$patient['last_name']);
												
												?></td>
											
												<?php 
												  $report_type=new MJ_hmgt_dignosis();
												  $report_type_data=explode(",",$retrieved_data->report_type);
												?>
												<td class="report_type">
												<?php
												  $i=1;
												
												  if(!empty($retrieved_data->report_type))
												  {	  
													  foreach ($report_type_data as $report_id)
													  {
														$report_data=$report_type->MJ_hmgt_get_report_by_id($report_id);
														$report_type_array=json_decode($report_data);
														echo '('.$i .') '.$report_type_array->category_name.'=>'.$report_type_array->report_cost.'';
														?>
														</br>
														<?php
														 $i++;
													  }
												  }	  
												?>
												 </td> 
												
												<td class="description"><?php echo esc_html($retrieved_data->diagno_description);?></td>		
												<td class="report">
												<?php
													if(MJ_hmgt_isJSON($retrieved_data->attach_report))
													{
														$dignosis_array=json_decode($retrieved_data->attach_report);
														
														foreach($dignosis_array as $key=>$value)
														{
															$report_type=new MJ_hmgt_dignosis();
															$report_data=$report_type->MJ_hmgt_get_report_by_id($value->report_id);
															$report_type_array=json_decode($report_data);
														
															echo '<a href="'.content_url().'/uploads/hospital_assets/'.$value->attach_report.'" class="btn btn-default" target="_blank"><i class="fa fa-eye"></i> '.$report_type_array->category_name.' '.esc_html__('Report','hospital_mgt').'</a></br>';
														}
													}	
													elseif(trim($retrieved_data->attach_report) != "")
													{
														echo '<a href="'.content_url().'/uploads/hospital_assets/'.$retrieved_data->attach_report.'" class="btn btn-default" target="_blank"><i class="fa fa-eye"></i>  '. esc_html__( "Download", "hospital_mgt" ) .' </a>';
													}
													else 
													{
														 esc_html_e( 'No any Report', 'hospital_mgt' ) ; 
													}
												?>
												</td>	
												<td class=""><?php echo number_format($retrieved_data->report_cost, 2, '.', ''); ?></td>
												<td class=""><?php echo number_format($retrieved_data->total_tax, 2, '.', ''); ?></td>
												<td class=""><?php echo number_format($retrieved_data->total_cost, 2, '.', ''); ?></td>	
												<td class="action"> 
												<?php
												if($retrieved_data->total_cost!="")
												{
													if($user_access_edit == 1)
													{?>
												
													<a href="?page=hmgt_diagnosis&tab=adddiagnosis&action=edit&diagnosis_id=<?php echo esc_attr($retrieved_data->diagnosis_id);?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
													<?php
													}
												}
												?>
												<?php if($user_access_delete == 1)
												{?>	
													<a href="?page=hmgt_diagnosis&tab=diagnosislist&action=delete&diagnosis_id=	<?php echo esc_attr($retrieved_data->diagnosis_id);?>" class="btn btn-danger" 
													onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this record?','hospital_mgt');?>');">
													<?php esc_html_e( 'Delete', 'hospital_mgt' ) ;?> </a>
													<?php }
												?>
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
									<?php }
												?>
								</div><!-- TABLE RESPONSIVE DIV END-->
							</div><!-- PANEL BODY DIV END-->						   
						</form>
						<?php 
						}						
						if($active_tab == 'adddiagnosis')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/diagnosis/add_diagnosis.php';
						}
						?>
				</div><!-- PANEL BODY DIV END-->			
			</div><!-- PANEL WHITE DIV END-->
		</div>
	</div><!-- ROW DIV END-->