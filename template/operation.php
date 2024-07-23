<?php
MJ_hmgt_browser_javascript_check();
$obj_ot = new MJ_hmgt_operation();
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
//SAVE Opearation DATA
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
					wp_redirect ( home_url().'?dashboard=user&page=operation&tab=operationlist&message=2');
				}
				else 
				{
					wp_redirect ( home_url().'?dashboard=user&page=operation&tab=operationlist&message=1'); 
				}
			}
		}
		
	}
}
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete')
{			
	$result = $obj_ot->MJ_hmgt_delete_oprationtheater(MJ_hmgt_id_decrypt($_REQUEST['ot_id']));
	if($result)
	{
		wp_redirect ( home_url().'?dashboard=user&page=operation&tab=operationlist&message=3');
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
				esc_html_e("Record updated successfully",'hospital_mgt');
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
$active_tab = isset($_GET['tab'])?$_GET['tab']:'operationlist';
?>

<script type="text/javascript">
jQuery(document).ready(function($) {
	"use strict";
	$('#hmgt_operation').DataTable({
		"responsive": true,
		 "order": [[ 3, "desc" ]],
		 "aoColumns":[
	                  {"bSortable": true},
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
	<?php
	if (is_rtl())
		{
		?>	
			$('#operation_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#operation_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>
	$('#tax_charge').multiselect(
	{
		nonSelectedText :'<?php esc_html_e('Select Tax','hospital_mgt');?>',
		includeSelectAllOption: true,
	    selectAllText : '<?php esc_html_e('Select all','hospital_mgt'); ?>',
		templates: {
            button: '<button class="multiselect btn btn-default dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="multiselect-selected-text"></span><b class="caret"></b></button>',
        },
		buttonContainer: '<div class="dropdown" />'
	});
	$('.timepicker').timepicki({
		show_meridian:false,
		min_hour_value:0,
		max_hour_value:23,
		step_size_minutes:15,
		overflow_minutes:true,
		increase_direction:'up',
		disable_keyboard_mobile: true
		});
		
		var date = new Date();
		date.setDate(date.getDate()-0);
		$.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
		$('#operation_date').datepicker({
		startDate: date,
		autoclose: true
	   }); 
	
	$('#doctor').multiselect(
	{
		nonSelectedText :'<?php esc_html_e('Select Doctor','hospital_mgt');?>',
		includeSelectAllOption: true,
	    selectAllText : '<?php esc_html_e('Select all','hospital_mgt'); ?>',
		templates: {
            button: '<button class="multiselect btn btn-default dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="multiselect-selected-text"></span><b class="caret"></b></button>',
        },
		buttonContainer: '<div class="dropdown" />'
	});  
	 
	 $(".doctor_submit").on("click",function()
		 {	
		  checked = $(".multiselect_validation_doctor .dropdown-menu input:checked").length;
		if(!checked)
		{
		  alert("<?php esc_html_e('Please select atleast one doctor','hospital_mgt');?>");
		  return false;
		}	
		});
		$('#patient').select2();
} );
</script>
<!-- POP up code -->
<div class="popup-bg">
    <div class="overlay-content overlay_content_css">
		<div class="modal-content">
			<div class="category_list">
			</div>
		</div>
    </div> 
</div>
<!-- End POP-UP Code -->
<div class="panel-body panel-white"><!-- START PANEL BODY DIV-->
	 <ul class="nav nav-tabs panel_tabs" role="tablist">
		  <li class="<?php if($active_tab == 'operationlist'){?>active<?php }?>">
			  <a href="?dashboard=user&page=operation&tab=operationlist">
				 <i class="fa fa-align-justify"></i> <?php esc_html_e('Operation List', 'hospital_mgt'); ?></a>
			  </a>
		  </li>
		  <li class="<?php if($active_tab=='addoperation'){?>active<?php }?>">
			  <?php  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && isset($_REQUEST['ot_id']))
				{?>
				<a href="?dashboard=user&page=operation&tab=addoperation&action=edit&ot_id=<?php if(isset($_REQUEST['ot_id'])) echo $_REQUEST['ot_id'];?>" class="tab <?php echo $active_tab == 'addoperation' ? 'active' : ''; ?>">
				 <i class="fa fa"></i> <?php esc_html_e('Edit Operation List', 'hospital_mgt'); ?></a>
				 <?php }
				else
				{				
					if($user_access['add']=='1')
					{
					?>
						<a href="?dashboard=user&page=operation&tab=addoperation&&action=insert" class="tab <?php echo $active_tab == 'addoperation' ? 'active' : ''; ?>">
						<i class="fa fa-plus-circle"></i> <?php esc_html_e('Add Operation', 'hospital_mgt'); ?></a>
		  <?php 	}
				}
			?>
		  
		</li>
	</ul>
	<div class="tab-content opacity_div"> <!-- START TAB CONTENT DIV-->
		<?php if($active_tab == 'operationlist'){?>
		<div class="tab-pane fade active in"  id="eventlist"><!-- START TAB PANE DIV-->
			
			<div class="panel-body"><!-- START PANEL BODY DIV-->
				<div class="table-responsive"><!-- START TABLE RESPONSIVE DIV-->
					<table id="hmgt_operation" class="display dataTable " cellspacing="0" width="100%"><!-- START OPERATION LIST TABLE-->
						<thead>
							<tr>
								<th><?php esc_html_e( 'Operation Name', 'hospital_mgt' ) ;?></th>
								 <th><?php esc_html_e( 'Patient', 'hospital_mgt' ) ;?></th>
								 <th><?php esc_html_e( 'Surgeon', 'hospital_mgt' ) ;?></th>
								 <th><?php esc_html_e( 'Date', 'hospital_mgt' ) ;?></th>
								 <th><?php esc_html_e( 'Operation Charge', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
								 <th><?php esc_html_e( 'Tax Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
								<th><?php esc_html_e( 'Total Operation Charge', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
								<th><?php esc_html_e( 'Operation Status', 'hospital_mgt' ) ;?></th>
								 <th><?php esc_html_e( 'Out Come Status', 'hospital_mgt' ) ;?></th>
								<th> <?php esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th><?php esc_html_e( 'Operation Name', 'hospital_mgt' ) ;?></th>
								 <th><?php esc_html_e( 'Patient', 'hospital_mgt' ) ;?></th>
								 <th><?php esc_html_e( 'Surgeon', 'hospital_mgt' ) ;?></th>
								 <th><?php esc_html_e( 'Date', 'hospital_mgt' ) ;?></th>
								  <th><?php esc_html_e( 'Operation Charge', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
								  <th><?php esc_html_e( 'Tax Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
								<th><?php esc_html_e( 'Total Operation Charge', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
								 <th><?php esc_html_e( 'Operation Status', 'hospital_mgt' ) ;?></th>
								  <th><?php esc_html_e( 'Out Come Status', 'hospital_mgt' ) ;?></th>
									<th> <?php esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
							</tr>
						</tfoot>
						<tbody>
						 <?php	
						$userid=get_current_user_id();			 
						if($obj_hospital->role == 'laboratorist' || $obj_hospital->role == 'pharmacist' || $obj_hospital->role == 'accountant' || $obj_hospital->role == 'receptionist') 
						{
							$own_data=$user_access['own_data'];
							if($own_data == '1')
							{ 							
							  $ot_data=$obj_ot->MJ_hmgt_get_operation_by_created_by($userid);
							}
							else
							{
							   $ot_data=$obj_ot->MJ_hmgt_get_all_operation();
							}
							
						}
						elseif($obj_hospital->role == 'doctor') 
						{
							$own_data=$user_access['own_data'];
							if($own_data == '1')
							{ 							
								$ot_data=$obj_ot->MJ_hmgt_get_doctor_operation_by_created_by($userid);
							}
							else
							{
							   $ot_data=$obj_ot->MJ_hmgt_get_all_operation();
							}
							
						}
						elseif($obj_hospital->role == 'nurse') 
						{
							$own_data=$user_access['own_data'];
							if($own_data == '1')
							{ 							
							  $ot_data=$obj_ot->MJ_hmgt_get_nurse_operation_by_created_by($userid);
							}
							else
							{
							   $ot_data=$obj_ot->MJ_hmgt_get_all_operation();
							}
							
						}
						elseif($obj_hospital->role == 'patient') 
						{
							$own_data=$user_access['own_data'];
							if($own_data == '1')
							{ 	
								
							  $ot_data=$obj_ot->MJ_hmgt_get_operation_by_patient($userid);
							}
							else
							{
							   $ot_data=$obj_ot->MJ_hmgt_get_all_operation();
							}							
						}
						
						 if(!empty($ot_data))
						 {
							foreach ($ot_data as $retrieved_data){ 
							$patient_data =	MJ_hmgt_get_user_detail_byid($retrieved_data->patient_id);
							?>
							<tr>
								<td class="operation_name"><?php echo esc_html($obj_ot->MJ_hmgt_get_operation_name($retrieved_data->operation_title));?></td>
								<td class="patient"><?php echo esc_html($patient_data['first_name']." ".$patient_data['last_name']."(".$patient_data['patient_id'].")");?></td>
								<td class="surgen">
								<?php 
									$surgenlist =  $obj_ot->MJ_hmgt_get_doctor_by_oprationid($retrieved_data->operation_id) ;
									$surgenlist_names = '';
									foreach($surgenlist as $assign_id)
									{
										$doctory_data =	MJ_hmgt_get_user_detail_byid($assign_id->child_id);
										//echo '<a href="javascript:void(0);">';
										$surgenlist_names.=$doctory_data['first_name']." ".$doctory_data['last_name'].",";
									}
									echo rtrim($surgenlist_names, ',');
								?></td>
								<td class="date"><?php echo date(MJ_hmgt_date_formate(),strtotime($retrieved_data->operation_date));?></td>
								<td class=""><?php echo number_format($retrieved_data->ot_charge, 2, '.', ''); ?></td>
							   <td class=""><?php echo number_format($retrieved_data->ot_tax, 2, '.', ''); ?></td>
							   <td class=""><?php echo number_format($retrieved_data->operation_charge, 2, '.', ''); ?></td>
								<td class=""><?php if(!empty($retrieved_data->operation_status)) { esc_html_e(''.$retrieved_data->operation_status.'','hospital_mgt'); }else{ echo '-'; }?></td>
								<td class=""><?php if(!empty($retrieved_data->out_come_status)) { esc_html_e(''.$retrieved_data->out_come_status.'','hospital_mgt'); }else{ echo '-'; } ?></td>
								 
									<td class="action"> 
									<a href="javascript:void(0);" class="view_details_popup btn btn-default" id="<?php echo esc_attr( $retrieved_data->operation_id); ?>" type="<?php echo 'view_ot';?>"><i class="fa fa-eye"> </i> <?php esc_html_e('View', 'hospital_mgt' ) ;?> </a>
									
									<?php
									if($user_access['edit']=='1')
									{
									?>
										<a href="?dashboard=user&page=operation&tab=addoperation&action=edit&ot_id=<?php echo MJ_hmgt_id_encrypt(esc_attr($retrieved_data->operation_id));?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
									<?php
									}
									if($user_access['delete']=='1')
									{
									?>	
										<a href="?dashboard=user&page=operation&tab=operationlist&action=delete&ot_id=<?php echo MJ_hmgt_id_encrypt(esc_attr($retrieved_data->operation_id));?>" class="btn btn-danger" 
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
					</table><!-- END Operation LIST TABLE-->
				</div><!-- END TABLE RESPONSIVE DIV-->
			</div><!-- END PANEL BODY DIV-->
		</div><!-- END TAB CONTENT DIV-->
		<?php }
		 if($active_tab == 'addoperation')
		 {
			 
			$obj_bed = new MJ_hmgt_bedmanage();
			$edit = 0;
			if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
			{
				$edit = 1;
				$ot_id = MJ_hmgt_id_decrypt($_REQUEST['ot_id']);
				$result = $obj_ot->MJ_hmgt_get_single_operation($ot_id);
			}
			?>
		
		<div class="panel-body"><!-- START PANEL BODY DIV-->
			<form name="operation_form" action="" method="post" class="form-horizontal" id="operation_form"><!-- START Operation FORM-->
				<?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
				<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
				<input type="hidden" name="operation_id" value="<?php if(isset($_REQUEST['ot_id']))echo MJ_hmgt_id_decrypt($_REQUEST['ot_id']);?>"  />
				<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="patient"><?php esc_html_e('Patient','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8 col-md-8 margin_bottom_5px">
						<select name="patient_id" id="patient" class="form-control ">
							<option value=""><?php esc_html_e('Select Patient','hospital_mgt');?></option>
							<?php if($edit){ $patient_id1=$result->patient_id; }elseif(isset($_REQUEST['patient_id'])){$patient_id1=$_REQUEST['patient_id'];}else{ $patient_id1="";}
							$own_data=$user_access['own_data'];
							if($own_data == '1')
							{ 
								if($obj_hospital->role == 'doctor') 
								{
									$get_doctor = get_current_user_id();
									$patients =MJ_hmgt_get_patient_list_for_doctor($get_doctor);
								}
								else
								{
									$patients =$obj_hospital->patient;
								}	
								if(!empty($patients))
								{
									foreach($patients as $patient)
									{
										$patient_id = get_user_meta($patient->ID,'patient_id',true);
									
										echo '<option value="'.$patient->ID.'" '.selected($patient_id1,$patient->ID).'>'.$patient->display_name.' - '.$patient_id.'</option>';
									
									}
								}
							}
							else
							{
								$patients = MJ_hmgt_patientid_list();
								if(!empty($patients))
								{
									foreach($patients as $patient)
									{
										echo '<option value="'.$patient['id'].'" '.selected($patient_id1,$patient['id']).'>'.$patient['first_name'].' '.$patient['last_name'].' - '.$patient['patient_id'].'</option>';
									
									}
								}
							}
							?>
						</select>
					</div>
				</div>
			</div>

			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="patient_status"><?php esc_html_e('Patient Status','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<?php 
						$patient_status = "";
						if($edit){ $patient=MJ_get_inpatient_status($patient_id1);
							if(!empty($patient)){
						$patient_status=$patient->patient_status; } }elseif(isset($_POST['patient_status'])){$patient_status=$_POST['patient_status'];}else{$patient_status='';} ?>
						<select name="patient_status" class="form-control validate[required]" >
						<option value=""><?php esc_html_e('Select Patient Status','hospital_mgt');?></option>
						<?php foreach(MJ_hmgt_admit_reason() as $reason)
						{?>
							<option value="<?php echo esc_attr($reason);?>" <?php selected($patient_status,$reason);?>><?php echo esc_html($reason);?></option>
						<?php }?>				
						</select>				
					</div>	
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="patient"><?php esc_html_e('Operation','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8 margin_bottom_5px">
					<?php if($edit){ $operation=$result->operation_title; }elseif(isset($_POST['operation'])){$operation=$_POST['operation'];}else{$operation='';}?>
						<select name="operation_title" id="operation" class="form-control validate[required] ">
							<option value=""><?php esc_html_e('Select Operation','hospital_mgt');?></option>
							<?php 
							$operation_type=new MJ_hmgt_operation();
							$operation_array =$operation_type->MJ_hmgt_get_all_operationtype();
							if($edit)
								$operation1 = $result->operation_title;
							elseif(isset($_REQUEST['operation_title']))
							$operation1 = $_REQUEST['operation_title'];
							else
								$operation1 = "";
							 if(!empty($operation_array))
							 {
								foreach ($operation_array as $retrieved_data)
								{
									$operation_type_data=$retrieved_data->post_title;
									$operation_type_array=json_decode($operation_type_data);
								?>
									<option value="<?php echo esc_attr($retrieved_data->ID); ?>" <?php selected($operation1,$retrieved_data->ID);?>><?php echo esc_html($operation_type_array->category_name);?></option>
								<?php
								}
							 }
							?>
						</select>
					</div>
					<div class="col-sm-2"><button id="addremove" model="operation"><?php esc_html_e('Add Or Remove','hospital_mgt');?></button></div>
				</div>
			</div>
			
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="doctor"><?php esc_html_e('Select Doctor','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8 multiselect_validation_doctor">
						<?php
							$doctors = MJ_hmgt_getuser_by_user_role('doctor');
							$doctory_data = array();
							if($edit)
							{
								$doctor1 = $result->operation_title;
								$doctor_list = $obj_ot->MJ_hmgt_get_doctor_by_oprationid($ot_id);
								
								foreach($doctor_list as $assign_id)
								{
									$doctory_data[]=$assign_id->child_id;
								
								}
							}
							elseif(isset($_REQUEST['doctor']))
							{
								$doctor_list = $_REQUEST['doctor'];
								foreach($doctor_list as $assign_id)
								{
									$doctory_data[]=$assign_id;
								
								}
							}								
							?>	
						<select name="doctor[]" class="form-control validate[required]" multiple="multiple" id="doctor">
						<?php						
							if(!empty($doctors))
							{
								foreach($doctors as $doctor)
								{
									$selected = "";
									if(in_array($doctor['id'],$doctory_data))
										$selected = "selected";
									echo '<option value='.$doctor['id'].' '.$selected.'>'.$doctor['first_name']. ' '.$doctor['last_name'].' - '.MJ_hmgt_doctor_specialization_title($doctor['id']).'</option>';
								}
							}
							?>
						</select>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="bed_type_id"><?php esc_html_e('Select Bed Type','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
					<?php if(isset($_REQUEST['bed_type_id']))
							$bed_type1 = $_REQUEST['bed_type_id'];
						elseif($edit)
							$bed_type1 = $result->bed_type_id;
						else 
							$bed_type1 = "";
						?>
						<select name="bed_type_id" class="form-control validate[required] operation_bed_type" id="bed_type_id">
						<option value = ""><?php esc_html_e('Bed type','hospital_mgt');?></option>
						<?php 
						
						$bedtype_data=$obj_bed->MJ_hmgt_get_all_bedtype();
						if(!empty($bedtype_data))
						{
							foreach ($bedtype_data as $retrieved_data)
							{
								echo '<option value="'.$retrieved_data->ID.'" '.selected($bed_type1,$retrieved_data->ID).'>'.$retrieved_data->post_title.'</option>';
							}
						}
						?>
						</select>
					</div>
				</div>				
			</div>
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="bednumber"><?php esc_html_e('Bed Number','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<select name="bed_number" class="form-control validate[required] margin_bottom_5px" id="bednumber">
						<option value=""><?php esc_html_e('Select Bed Number','hospital_mgt');?></option>
						<?php 
						if($edit)
						{
							$bedtype_data = $obj_bed->MJ_hmgt_get_bed_by_bedtype($result->bed_type_id);
						
							if(!empty($bedtype_data))
							{
								foreach ($bedtype_data as $retrieved_data)
								{
									echo '<option value="'.$retrieved_data->bed_id.'" '.selected($retrieved_data->bed_id,$result->bednumber).'>'.$retrieved_data->bed_number.'</option>';
								}
							}
						}
						?>
						</select>
					</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">
					<div class="col-sm-2"></div>
					<div class="col-sm-8" id="bedlocation">	</div>
					<div class="col-sm-2 opration_1"></div>
				</div>
			</div>
			<div id=""></div>			
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="request_date"><?php esc_html_e('Operation Date','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="operation_date" class="form-control validate[required]" type="text" 
						value="<?php if($edit){ echo date(MJ_hmgt_date_formate(),strtotime($result->operation_date));}elseif(isset($_POST['operation_date'])) echo esc_attr($_POST['operation_date']);?>"  name="operation_date">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="operation_time"><?php esc_html_e('Operation Time','hospital_mgt');?></label>
					<div class="col-sm-8">
						<input id="operation_time" class="form-control timepicker" type="text"  placeholder="<?php esc_html_e('HH:MM','hospital_mgt');?>" 
						value="<?php if($edit){ echo esc_attr($result->operation_time);}elseif(isset($_POST['operation_time'])) echo esc_attr($_POST['operation_time']);?>" name="operation_time">
					</div>
				</div>
			</div>
			
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="ot_description"><?php esc_html_e('Description','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<textarea id="ot_description" class="form-control validate[required,custom[address_description_validation]]" maxlength="150" name="ot_description"><?php if($edit){ echo esc_textarea($result->ot_description);}elseif(isset($_POST['ot_description'])) echo esc_textarea($_POST['ot_description']);?></textarea>				
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="operation_charge"><?php esc_html_e('Total Operation Charge','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' )); ?>)<span class="require-field">*</span></label>
					<div class="col-sm-8">	
						<input type="hidden" name="ot_charge" id="ot_charge" value="<?php if($edit){ echo $result->ot_charge;} ?>">
						<input type="hidden" name="ot_tax" id="ot_tax" value="<?php if($edit){ echo $result->ot_tax;} ?>">
						<input id="operation_charge" class="form-control validate[required]" min="0" type="number" onKeyPress="if(this.value.length==10) return false;"  step="0.01"
						value="<?php if($edit){ echo esc_attr($result->operation_charge);}elseif(isset($_POST['operation_charge'])) echo esc_attr($_POST['operation_charge']);?>" name="operation_charge" readonly>				
					</div>
				</div>
			</div>			
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="patient_status"><?php esc_html_e('Operation Status','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8 margin_bottom_5px" >
						<?php 
						$operation_status = "";
						if($edit)
						{ 
							$operation_status=$result->operation_status;
							if($operation_status == 'Completed')
							{
								?>
								<style>
								.out_come_status
								{
									display:block;
								}
								</style>
								<?php
							}	
						}
						elseif(isset($_POST['operation_status']))
						{
							$operation_status=$_POST['operation_status'];}else{$operation_status='';
						} ?>
						<select name="operation_status" class="form-control validate[required] operation_status" >
						<option value=""><?php esc_html_e('Select Operation Status','hospital_mgt');?></option>
						<option value="Inprogress" <?php echo selected($operation_status,'Inprogress');?>>
						  <?php esc_html_e('Inprogress','hospital_mgt');?></option>
						<option value="Completed" <?php echo selected($operation_status,'Completed');?>>
						  <?php esc_html_e('Completed','hospital_mgt');?></option>
						 <option value="Scheduled" <?php echo selected($operation_status,'Scheduled');?>>
						  <?php esc_html_e('Scheduled','hospital_mgt');?></option> 						
						</select>				
					</div>	
				</div>
			</div>
			<?php wp_nonce_field( 'save_operation_nonce' ); ?>
			<div class="form-group out_come_status">
				<div class="mb-3 row">
						<label class="col-sm-2 control-label form-label" for="hmgt_currency_code"><?php esc_html_e('Out Come Status','hospital_mgt');?></label>
						<div class="col-sm-8 margin_bottom_5px">
						<?php
						$out_come_status = "";
						if($edit)
						{ 
							$out_come_status=$result->out_come_status;
						}
						elseif(isset($_POST['out_come_status']))
						{
							$out_come_status=$_POST['out_come_status'];}else{ $out_come_status='';
						}
						?>
						<select name="out_come_status" class="form-control text-input">					  
						  <option value="Success" <?php echo selected($out_come_status,'Success');?>>
						  <?php esc_html_e('Success','hospital_mgt');?></option>
						  <option value="Fail" <?php echo selected($out_come_status,'Fail');?>>
						  <?php esc_html_e('Fail','hospital_mgt');?></option>
						</select>
					</div>	
				</div>		
			</div>			
				<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12 doctor_submit">
					<input type="submit" value="<?php if($edit){ esc_html_e('Save','hospital_mgt'); }else{ esc_html_e('Save','hospital_mgt');}?>" name="save_operation" class="btn btn-success"/>
				</div>
			</form><!-- END Operation FORM-->
		</div><!-- END PANEL BODY DIV-->
		<?php }?>
	</div>	<!-- END TAB CONTENT DIV-->
</div><!-- END PANEL BODY DIV-->