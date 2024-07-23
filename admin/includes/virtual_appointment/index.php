<?php 
require_once HMS_PLUGIN_DIR. '/lib/vendor/autoload.php';
$obj_virtual_appointment = new MJ_hmgt_virtual_appointment;
$obj_appointment = new MJ_hmgt_appointment;
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
	$user_access=MJ_hmgt_get_access_right_for_management_user_page('virtual_appointment');
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
			if (isset ( $_REQUEST ['page'] ) && 'virtual_appointment' == $user_access['page_link'] && ($_REQUEST['action']=='edit'))
			{
				if($user_access['edit']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}			
			}
			if (isset ( $_REQUEST ['page'] ) && 'virtual_appointment' == $user_access['page_link'] && ($_REQUEST['action']=='delete'))
			{
				if($user_access['delete']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}	
			}
			if (isset ( $_REQUEST ['page'] ) && 'virtual_appointment' == $user_access['page_link'] && ($_REQUEST['action']=='insert')) 
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

$active_tab = isset($_GET['tab'])?$_GET['tab']:'meeting_list';
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
			wp_redirect ( admin_url().'admin.php?page=hmgt_virtual_appointment&tab=meeting_list&message=2');
		}		
	}
}
// DELETE STUDENT IN ZOOM
if(isset($_REQUEST['action'])&& $_REQUEST['action']=='delete')
{
	$result= $obj_virtual_appointment->MJ_hmgt_delete_meeting_in_zoom($_REQUEST['meeting_id']);
	if($result)
	{
		wp_redirect ( admin_url().'admin.php?page=hmgt_virtual_appointment&tab=meeting_list&message=3');
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
		wp_redirect ( admin_url().'admin.php?page=hmgt_virtual_appointment&tab=meeting_list&message=3');
	}
}
?>
<!-- POP up code -->
<div class="popup-bg">
    <div class="overlay-content">
	    <div class="modal-content">
		    <div class="view_meeting_detail_popup">
		    </div>
		</div>
	</div>
</div>
<script type="text/javascript">
jQuery(document).ready(function() {
	var table =  jQuery('#meeting_list').DataTable({
	responsive: true,
	 'order': [1, 'asc'],
	 "aoColumns":[
	 				  {"bSortable": false},
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
<!-- End POP-UP Code -->
<div class="page-inner">
	<div class="page-title"><!-- PANEL TITLE DIV START-->
		<h3><img src="<?php echo get_option( 'hmgt_hospital_logo', 'hospital_mgt' ) ?>" class="img-circle head_logo" width="40" height="40" /><?php echo get_option('hmgt_hospital_name','hospital_mgt');?></h3>
	</div><!-- PANEL TITLE DIV END-->
	<div  id="main-wrapper" class="class_list">
	<?php
		$message = isset($_REQUEST['message'])?$_REQUEST['message']:'0';
		switch($message)
		{
			case '1':
				$message_string = __('Virtual Appointment Added Successfully.','hospital_mgt');
				break;
			case '2':
				$message_string = __('Virtual Appointment Updated Successfully.','hospital_mgt');
				break;
			case '3':
				$message_string = __('Virtual Appointment Deleted Successfully.','hospital_mgt');
				break;
			case '4':
				$message_string = __('Your Access Token Is Updated.','hospital_mgt');
				break;
			case '5':
				$message_string = __('Something Wrong.','hospital_mgt');
				break;
			case '6':
				$message_string = __('First Start Your Virtual Appointment.','hospital_mgt');
				break;
		}
		
		if($message)
		{ ?>
		<div id="message" class="alert updated below-h2 notice is-dismissible alert-dismissible">
			<p><?php echo $message_string;?></p>
			<button type="button" class="notice-dismiss" data-dismiss="alert"><span class="screen-reader-text">Dismiss this notice.</span></button>
		</div>
		<?php 
		} 
		?>
		<div class="panel panel-white">
			<div class="panel-body">		
				<h2 class="nav-tab-wrapper">
			    	<a href="?page=hmgt_virtual_appointment&tab=meeting_list" class="nav-tab margin_bottom <?php echo $active_tab == 'meeting_list' ? 'nav-tab-active' : ''; ?>">
					<?php echo '<span class="dashicons dashicons-menu"></span>'. __('Virtual Appointment List', 'hospital_mgt'); ?></a>
			        <?php
			        if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
					{?>
			         	<a href="?page=hmgt_virtual_appointment&tab=edit_meeting&&action=edit&meeting_id=<?php echo $_REQUEST['meeting_id'];?>" class="nav-tab <?php echo $active_tab == 'edit_meeting' ? 'nav-tab-active' : ''; ?>"><?php _e('Edit Virtual Appointment', 'hospital_mgt'); ?></a>  
					<?php 
					}
					if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'view')
					{?>
			         	<a href="?page=hmgt_virtual_appointment&tab=view_past_participle_list&&action=view" class="nav-tab <?php echo $active_tab == 'view_past_participle_list' ? 'nav-tab-active' : ''; ?>"><?php _e('View Past Participle List', 'hospital_mgt'); ?></a>  
					<?php 
					}
					?>
			    </h2>
			    <?php
				if($active_tab == 'meeting_list')
				{	
						$meeting_list_data = $obj_virtual_appointment->MJ_hmgt_get_all_meeting_data_in_zoom();
					?>	
				<div class="panel-body">
					<form id="frm-example" name="frm-example" method="post">
						<div class="table-responsive">
							<table id="meeting_list" class="display datatable" cellspacing="0" width="100%">
								<thead>
									<tr>
										<th style="width: 20px;"><input name="select_all" value="all" id="checkbox-select-all" type="checkbox" /></th>
										<th><?php _e('Date','hospital_mgt');?></th>
										<th><?php _e('Patient','hospital_mgt');?></th>
										<th><?php _e('Doctor','hospital_mgt');?></th>
										<th ><?php _e('Topic','hospital_mgt');?></th>
										<th><?php _e('Action','hospital_mgt');?></th>
									</tr>
								</thead>
					 
								<tfoot>
									<tr>
										<th></th>
										<th><?php _e('Date','hospital_mgt');?></th>
										<th><?php _e('Patient','hospital_mgt');?></th>
										<th><?php _e('Doctor','hospital_mgt');?></th>
										<th ><?php _e('Topic','hospital_mgt');?></th>
										<th><?php _e('Action','hospital_mgt');?></th>
									</tr>
								</tfoot>
								<tbody>
								<?php 
								foreach ($meeting_list_data as $retrieved_data)
								{
									$appointment_data = $obj_appointment->MJ_hmgt_get_single_appointment($retrieved_data->appointment_id);
									
								?>
									<tr>
										<td><input type="checkbox" class="select-checkbox" name="id[]" value="<?php echo $retrieved_data->meeting_id;?>"></td>
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
										<a href="<?php echo $retrieved_data->meeting_start_link;?>" class="btn btn-primary" target="_blank"><i class="fa fa-video-camera" aria-hidden="true"></i> <?php _e('Start Virtual Appointment','hospital_mgt');?> </a>
										<?php if($user_access_edit == 1)
											{?>
										<a href="?page=hmgt_virtual_appointment&tab=edit_meeting&action=edit&meeting_id=<?php echo $retrieved_data->meeting_id;?>" class="btn btn-info"><?php _e('Edit','hospital_mgt');?> </a>
										<?php 
										} 
										?>
										<?php if($user_access_delete == 1)
										{?>	
										<a href="?page=hmgt_virtual_appointment&tab=meeting_list&action=delete&meeting_id=<?php echo $retrieved_data->meeting_id;?>" class="btn btn-danger" onclick="return confirm('<?php _e('Are you sure you want to delete this record?','hospital_mgt');?>');"> <?php _e('Delete','hospital_mgt');?></a>
										<?php 
												 } ?>												
										<a href="?page=hmgt_virtual_appointment&tab=view_past_participle_list&action=view&meeting_uuid=<?php echo $retrieved_data->uuid;?>" class="btn btn-success"><?php _e('View Past Participle List','hospital_mgt');?> </a>
										</td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
						<?php if($user_access_delete == 1)
										{?>	
						<div class="print-button pull-left">
							<input id="delete_selected" type="submit" value="<?php _e('Delete Selected','hospital_mgt');?>" name="delete_selected" class="btn btn-danger delete_selected"/>
						</div>
						<?php } ?>
					</form>
		        </div>
			    <?php 
				}
				if($active_tab == 'edit_meeting')
				{
					require_once HMS_PLUGIN_DIR. '/admin/includes/virtual_appointment/edit_meeting.php';
				}
				elseif($active_tab == 'view_past_participle_list')
				{
					require_once HMS_PLUGIN_DIR. '/admin/includes/virtual_appointment/view_past_participle_list.php';
				}
				?>
		 	</div>
		</div>
	</div>
</div>

<?php ?>