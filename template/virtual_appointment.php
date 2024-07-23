<?php 
require_once HMS_PLUGIN_DIR. '/lib/vendor/autoload.php';
MJ_hmgt_browser_javascript_check();
$obj_virtual_appointment = new MJ_hmgt_virtual_appointment;
$obj_appointment = new MJ_hmgt_appointment;
$active_tab = isset($_GET['tab'])?$_GET['tab']:'meeting_list';
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
/*var_dump($active_tab);
die;*/
// EDIT MEETING IN ZOOM
if(isset($_POST['edit_meeting']))
{

	$nonce = $_POST['_wpnonce'];
	if ( wp_verify_nonce( $nonce, 'edit_meeting_admin_nonce' ) )
	{
		$result = MJ_edit_virtual_meeting_fun($_POST);
		if($result)
		{
			wp_redirect ( home_url() . '?dashboard=user&page=virtual_appointment&tab=meeting_list&message=2');
		}		
	}
}
// DELETE STUDENT IN ZOOM
if(isset($_REQUEST['action'])&& $_REQUEST['action']=='delete')
{
	$result= $obj_virtual_appointment->MJ_hmgt_delete_meeting_in_zoom($_REQUEST['meeting_id']);
	if($result)
	{
		wp_redirect ( home_url() . '?dashboard=user&page=virtual_appointment&tab=meeting_list&message=3');
	}
}
/*Delete selected Subject*/
if(isset($_REQUEST['delete_selected']))
{		
	if(!empty($_REQUEST['id']))
	{
		foreach($_REQUEST['id'] as $meeting_id)
		{
			$result= $obj_virtual_appointment->MJ_hmgt_delete_meeting_in_zoom($meeting_id);
		}
	}
	if($result)
	{
		wp_redirect ( home_url() . '?dashboard=user&page=virtual_appointment&tab=meeting_list&message=3');
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
				esc_html_e('Virtual Appointment Added Successfully.','hospital_mgt');
			?></p>
		</div>
			<?php 
	}
	elseif($message == 2)
	{?>
		<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
		</button><p><?php
				esc_html_e("Virtual Appointment Updated Successfully.",'hospital_mgt');
				?></p>
		</div>
			<?php
	}
	elseif($message == 3) 
	{?>
		<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
		</button><p>
	<?php 
		esc_html_e('Virtual Appointment Deleted Successfully.','hospital_mgt');
	?></div></p><?php	
	}
	elseif($message == 4) 
	{?>
		<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
		</button><p>
	<?php 
		esc_html_e('Your Access Token Is Updated.','hospital_mgt');
	?></div></p><?php	
	}
	elseif($message == 5) 
	{?>
		<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
		</button><p>
	<?php 
		esc_html_e('Something Wrong.','hospital_mgt');
	?></div></p><?php	
	}
	elseif($message == 6) 
	{?>
		<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
		</button><p>
	<?php 
		esc_html_e('First Start Your Virtual Appointment.','hospital_mgt');
	?></div></p><?php	
	}
}
?>
<script type="text/javascript">
$(document).ready(function() {
	var table =  jQuery('#meeting_list').DataTable({
	responsive: true,
	 'order': [1, 'asc'],
	 "aoColumns":[
	 				 // {"bSortable": false},
	                  {"bSortable": true},
	                  {"bSortable": true},
	                  {"bSortable": true},
	                  {"bSortable": true},
	                  {"bSortable": false}],
	language:<?php echo MJ_hmgt_datatable_multi_language();?>	

       });	

    $('#checkbox-select-all').on('click', function(){
     
      var rows = table.rows({ 'search': 'applied' }).nodes();
      $('input[type="checkbox"]', rows).prop('checked', this.checked);
   });
	
	 $("#delete_selected").on('click', function()
		{	
			if ($('.select-checkbox:checked').length == 0 )
			{
				alert("<?php esc_html_e('Please select atleast one record','hospital_mgt');?>");
				return false;
			}
		else{
				var alert_msg=confirm("<?php esc_html_e('Are you sure you want to delete this record?','hospital_mgt');?>");
				if(alert_msg == false)
				{
					return false;
				}
				else
				{
					return true;
				}
			}
	});

   });  
</script>
<style type="text/css">
.popup_dashboard .content_width {
    width: 60% !important;
    padding: 10px 10px 10px !important;
}
</style>
<!-- End POP-UP Code -->
		<div class="panel panel-white">
			<div class="panel-body">
			<ul class="nav nav-tabs panel_tabs" role="tablist">
				<li class="<?php if($active_tab=='meeting_list'){?>active<?php }?>">
					  <a href="?dashboard=user&page=virtual_appointment&tab=meeting_list" class="tab <?php echo $active_tab == 'doctorlist' ? 'active' : ''; ?>" >
						 <i class="fa fa-align-justify"></i> <?php esc_html_e('Virtual Appointment List', 'hospital_mgt'); ?></a>
					  </a>
				</li>
				<li class="<?php if($active_tab=='edit_meeting'){?>active<?php }?>">
				  <?php  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
					{
						if($user_access['edit']=='1')
						{
					?>
						<a href="?dashboard=user&page=virtual_appointment&tab=edit_meeting&action=edit&meeting_id=<?php echo $_REQUEST['meeting_id'];?>" class="tab <?php echo $active_tab == 'edit_meeting' ? 'active' : ''; ?>">
						<i class="fa fa"></i> <?php esc_html_e('Edit Virtual Appointment', 'hospital_mgt'); ?></a>
					 <?php 
					 	}
					}
					if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'view')
					{
						?>				
							<a href="?dashboard=user&page=virtual_appointment&tab=view_past_participle_list&&action=view" class="tab <?php echo $active_tab == 'view_past_participle_list' ? 'active' : ''; ?>">
							<i class="fa fa-eye"></i> <?php esc_html_e('View Past Participle List', 'hospital_mgt'); ?></a>
						<?php
						
					}
					?>	  
				</li>
			</ul>		
			<?php
				if($active_tab == 'meeting_list')
				{	
					$own_data=$user_access['own_data'];
					$user_id=get_current_user_id();
					if($obj_hospital->role == 'nurse' || $obj_hospital->role == 'laboratorist' || $obj_hospital->role == 'pharmacist' || $obj_hospital->role == 'accountant' || $obj_hospital->role == 'receptionist') 
					{
						if($own_data == '1')
						{ 
						  $meeting_list_data = $obj_virtual_appointment->MJ_hmgt_get_all_meeting_created_by($user_id);
						}
						else
						{
							$meeting_list_data = $obj_virtual_appointment->MJ_hmgt_get_all_meeting_data_in_zoom();
						}
					}
					elseif($obj_hospital->role == 'doctor') 
					{
						if($own_data == '1')
						{
							$meeting_list_data = $obj_virtual_appointment->MJ_hmgt_get_all_meeting_created_by($user_id);
						}
						else
						{
							$meeting_list_data = $obj_virtual_appointment->MJ_hmgt_get_all_meeting_data_in_zoom();
						}
					}
					elseif($obj_hospital->role == 'patient')
					{
						$own_data=$user_access['own_data'];
						if($own_data == '1')
						{ 
						  $meeting_list_data = $obj_virtual_appointment->MJ_hmgt_get_all_patient_meeting_data_in_zoom($user_id);
						}
						else
						{
							$meeting_list_data = $obj_virtual_appointment->MJ_hmgt_get_all_meeting_data_in_zoom();
						}			
					}		
					else
					{
							$meeting_list_data = $obj_virtual_appointment->MJ_hmgt_get_all_meeting_data_in_zoom();
					}
					?>	
				<div class="panel-body">
					<form id="frm-example" name="frm-example" method="post">
						<div class="table-responsive">
							<table id="meeting_list" class="display datatable" cellspacing="0" width="100%">
								<thead>
									<tr>
									<!-- 	<th style="width: 20px;"><input name="select_all" value="all" id="checkbox-select-all" type="checkbox" /></th> -->
										<th><?php _e('Date','hospital_mgt');?></th>
										<th><?php _e('Patient','hospital_mgt');?></th>
										<th><?php _e('Doctor','hospital_mgt');?></th>
										<th ><?php _e('Topic','hospital_mgt');?></th>
										<th><?php _e('Action','hospital_mgt');?></th>
									</tr>
								</thead>
					 
								<tfoot>
									<tr>
									<!-- 	<th></th> -->
										<th><?php _e('Date','hospital_mgt');?></th>
										<th><?php _e('Patient','hospital_mgt');?></th>
										<th><?php _e('Doctor','hospital_mgt');?></th>
										<th ><?php _e('Topic','hospital_mgt');?></th>
										<th><?php _e('Action','hospital_mgt');?></th>
									</tr>
								</tfoot>
								<tbody>
								<?php 
								if(!empty($meeting_list_data))
								{
									foreach ($meeting_list_data as $retrieved_data)
									{
										$appointment_data = $obj_appointment->MJ_hmgt_get_single_appointment($retrieved_data->appointment_id);
									?>
										<tr>
											<!-- <td><input type="checkbox" class="select-checkbox" name="id[]" value="<?php echo $retrieved_data->meeting_id;?>"></td> -->
											<td><?php echo date(MJ_hmgt_date_formate(),strtotime($appointment_data->appointment_date));?>(<?php echo MJ_hmgt_appoinment_time_language_translation($appointment_data->appointment_time_with_a); ?>)</td>
											<td><?php 
												$patient_data =	MJ_hmgt_get_user_detail_byid($appointment_data->patient_id);
												echo esc_html($patient_data['first_name']." ".$patient_data['last_name']);
											?></td>
											<td><?php $doctor_data =	MJ_hmgt_get_user_detail_byid($appointment_data->doctor_id);
													echo esc_html($doctor_data['first_name']." ".$doctor_data['last_name']);?></td>
											<td>
												<?php
												if(!empty($retrieved_data->agenda))
												{
													echo $retrieved_data->agenda;
												}
												else
												{
													echo "-";
												}
												?>
											</td>
											<td>
											<a href="" class="btn btn-default show-popup123" meeting_id="<?php echo $retrieved_data->meeting_id; ?>"><i class="fa fa-eye"></i> <?php _e('View','hospital_mgt');?></a> 
											<?php
										//	if($user_id == (int)$retrieved_data->created_by)
											if($obj_hospital->role == 'doctor')
											{
												?>
												<a href="<?php echo $retrieved_data->meeting_start_link;?>" class="btn btn-primary" target="_blank"><i class="fa fa-video-camera" aria-hidden="true"></i> <?php _e('Start Virtual Appointment','hospital_mgt');?> </a>
												<?php
											}
											else
											{
												?>
													<a href="<?php echo $retrieved_data->meeting_join_link;?>" class="btn btn-primary" target="_blank"><i class="fa fa-video-camera" aria-hidden="true"></i> <?php _e('Join Virtual Appointment','hospital_mgt');?> </a>
												<?php
											}
											?>
											
											<?php
											if($user_access['edit']=='1')
											{
												?>
												<a href="?dashboard=user&page=virtual_appointment&tab=edit_meeting&action=edit&meeting_id=<?php echo $retrieved_data->meeting_id;?>" class="btn btn-info"><?php _e('Edit','hospital_mgt');?> </a>
											<?php
											}
											if($user_access['delete']=='1')
											{
											?>
												<a href="?dashboard=user&page=virtual_appointment&tab=meeting_list&action=delete&meeting_id=<?php echo $retrieved_data->meeting_id;?>" class="btn btn-danger" onclick="return confirm('<?php _e('Are you sure you want to delete this record?','hospital_mgt');?>');"> <?php _e('Delete','hospital_mgt');?></a>
											<?php
											}
											if($obj_hospital->role == 'nurse' || $obj_hospital->role == 'receptionist' || $obj_hospital->role == 'doctor') 
											{
											?>
												<a href="?dashboard=user&page=virtual_appointment&tab=view_past_participle_list&action=view&meeting_uuid=<?php echo $retrieved_data->uuid;?>" class="btn btn-success"><?php _e('View Past Participle List','hospital_mgt');?> </a>
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
						</div>
					<!-- 	<div class="print-button pull-left">
							<input id="delete_selected" type="submit" value="<?php _e('Delete Selected','hospital_mgt');?>" name="delete_selected" class="btn btn-danger delete_selected"/>
						</div> -->
					</form>
		        </div>
			    <?php 
				}
				if($active_tab == 'edit_meeting')
				{
					$meeting_data = $obj_virtual_appointment->MJ_hmgt_get_singal_meeting_data_in_zoom($_REQUEST['meeting_id']);
					$appointment_data = $obj_appointment->MJ_hmgt_get_single_appointment($meeting_data->appointment_id);
					$patient_data =	MJ_hmgt_get_user_detail_byid($appointment_data->patient_id);
					$doctor_data =	MJ_hmgt_get_user_detail_byid($appointment_data->doctor_id);
					?>
					<script type="text/javascript">
					$(document).ready(function() {
						$('#meeting_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
						$.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
						$("#start_date").datepicker({
							startDate: '+0d',
							autoclose: true
						});
					} );
					</script>
					<div class="panel-body">   
					        <form name="route_form" action="" method="post" class="form-horizontal" id="meeting_form">
					        <?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
							<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
							<input type="hidden" name="meeting_id" value="<?php echo $_REQUEST['meeting_id'];?>">
							<input type="hidden" name="appointment_id" value="<?php echo $meeting_data->appointment_id;?>">
							<input type="hidden" name="patient_id" value="<?php echo $appointment_data->patient_id;?>">
							<input type="hidden" name="doctor_id" value="<?php echo $appointment_data->doctor_id;?>">
							<input type="hidden" name="appointment_time" value="<?php echo $appointment_data->appointment_time;?>">
							<input type="hidden" name="appointment_time_with_a" value="<?php echo $appointment_data->appointment_time_with_a;?>">
							<input type="hidden" name="department_id" value="<?php echo $appointment_data->department_id;?>">
							<input type="hidden" name="zoom_meeting_id" value="<?php echo $meeting_data->zoom_meeting_id;?>">
							<input type="hidden" name="uuid" value="<?php echo $meeting_data->uuid;?>">
							<input type="hidden" name="meeting_join_link" value="<?php echo $meeting_data->meeting_join_link;?>">
							<input type="hidden" name="meeting_start_link" value="<?php echo $meeting_data->meeting_start_link;?>">
					        <div class="form-group">
					        	<div class="mb-3 row">	
									<label class="col-sm-2 control-label form-label" for="class_name"><?php _e('Patient Name','hospital_mgt');?></label>
									<div class="col-sm-8">
										<input id="class_name" class="form-control" maxlength="50" type="text" value="<?php echo esc_html($patient_data['first_name']." ".$patient_data['last_name']); ?>" name="patient_name" disabled>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="mb-3 row">	
									<label class="col-sm-2 control-label form-label" for="class_section"><?php _e('Doctor Name','hospital_mgt');?></label>
									<div class="col-sm-8">
										<input id="class_section" class="form-control" maxlength="50" type="text" value="<?php echo esc_html($doctor_data['first_name']." ".$doctor_data['last_name']); ?>" name="doctor_name" disabled>
									</div>
								</div>
							</div>		
							<div class="form-group">
								<div class="mb-3 row">	
									<label class="col-sm-2 control-label form-label" for="start_date"><?php _e('Date','hospital_mgt');?><span class="require-field">*</span></label>
									<div class="col-sm-8">
										<input id="start_date" class="form-control validate[required] text-input" type="text" placeholder="<?php esc_html_e('Enter Start Date','school-mgt');?>" name="appointment_date" value="<?php echo date(MJ_hmgt_date_formate(),strtotime($appointment_data->appointment_date)); ?>" readonly>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="mb-3 row">	
									<label class="col-sm-2 control-label form-label" for="agenda"><?php _e('Topic','hospital_mgt');?></label>
									<div class="col-sm-8">
										<textarea name="agenda" class="form-control validate[custom[address_description_validation]]" placeholder="<?php esc_html_e('Enter Agenda','hospital_mgt');?>" maxlength="250" id=""><?php echo $meeting_data->agenda; ?></textarea>
									</div>
								</div>
							</div>
							<div class="form-group">
								<div class="mb-3 row">	
									<label class="col-sm-2 control-label form-label" for="title"><?php _e('Password','hospital_mgt');?></label>
									<div class="col-sm-8">
										<input id="password" class="form-control validate[minSize[8],maxSize[12]]" type="password" value="<?php echo $meeting_data->password; ?>" name="password">
									</div>
								</div>
							</div>
							<?php wp_nonce_field( 'edit_meeting_admin_nonce' ); ?>
							
							<div class="offset-sm-2 col-sm-8">        	
					        	<input type="submit" value="<?php _e('Save Meeting','hospital_mgt');?>" name="edit_meeting" class="btn btn-success" />
					        </div>        
					     </form>
					    </div>
    <?php
				}
				elseif($active_tab == 'view_past_participle_list')
				{
					$past_participle_list = $obj_virtual_appointment->MJ_hmgt_view_past_participle_list_in_zoom($_REQUEST['meeting_uuid']);
					?>

					<script type="text/javascript">
					$(document).ready(function() 
					{
						var table =  jQuery('#past_participle_list').DataTable({
						responsive: true,
						 'order': [0, 'asc'],
					 	dom: 'lBfrtip',
						buttons: [
						{
							extend: 'print',
							text:'<?php _e("Print","hospital_mgt");?>',
							title: '<?php _e("Past Participle List","hospital_mgt");?>',
						}],
						"aoColumns":[
						        {"bSortable": true},
						    	{"bSortable": true},
						    ],
						language:<?php echo MJ_hmgt_datatable_multi_language();?>		
					       });	
					   }); 
					</script>
					<div class="panel-body">
						<form id="frm-example" name="frm-example" method="post">
							<div class="table-responsive">
								<table id="past_participle_list" class="display datatable" cellspacing="0" width="100%">
									<thead>
										<tr>
											<th><?php _e('Name','hospital_mgt');?></th>
											<th><?php _e('Email','hospital_mgt');?></th>
										</tr>
									</thead>
						 
									<tfoot>
										<tr>
											<th><?php _e('Name','hospital_mgt');?></th>
											<th><?php _e('Email','hospital_mgt');?></th>
										</tr>
									</tfoot>
									<tbody>
									<?php 
									if (!empty($past_participle_list->participants))
									{
										foreach($past_participle_list->participants as $retrieved_data)
										{
										?>
											<tr>
												<td><?php echo $retrieved_data->name;?></td>
												<td><?php echo $retrieved_data->user_email;?></td>
											</tr>
										<?php 
										}
									}
									?>
									</tbody>
								</table>
							</div>
						</form>
					</div>
<?php
				}
				?>
		 	</div>
		</div>
	</div>
</div>

<?php ?>