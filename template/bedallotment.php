<div class="datas"> </div>
<?php
MJ_hmgt_browser_javascript_check();
$obj_bed = new MJ_hmgt_bedmanage();
$obj_hospital = new MJ_hmgt_Hospital_Management(get_current_user_id());
$user_role = $obj_hospital->role;
//access right
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
 //SAVE BAD ALLOTMENT DATA   
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
					wp_redirect ( home_url() . '?dashboard=user&page=bedallotment&tab=bedallotlist&message=2');
				}
				else 
				{
					wp_redirect ( home_url() . '?dashboard=user&page=bedallotment&tab=bedallotlist&message=1');
				}
				
				
			}
		}
	}
}
//SAVE BAS Transfer BED
if(isset($_POST['bed_transfar'])){
	if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'transfer')){	
			$result = $obj_bed->MJ_hmgt_patient_bed_transfar($_POST);
			if($result){		
				wp_redirect ( home_url() . '?dashboard=user&page=bedallotment&tab=bedallotlist&message=4');				
			}
		}
}
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete')
{
	$result = $obj_bed->MJ_hmgt_delete_bedallocate_record(MJ_hmgt_id_decrypt($_REQUEST['allotment_id']));
	if($result)
	{
		wp_redirect ( home_url() . '?dashboard=user&page=bedallotment&tab=bedallotlist&message=3');
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
	elseif($message == 4)
	{?>
		<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
		</button><p><?php
			esc_html_e(" Bed Transfered successfully",'hospital_mgt');
			?></p>
		</div>
		<?php
	}
}	
$active_tab = isset($_GET['tab'])?$_GET['tab']:'bedallotlist';
?>
<div class="panel-body panel-white"><!-- START PANEL BODY DIV-->
	<ul class="nav nav-tabs panel_tabs" role="tablist">
		<li class="<?php if($active_tab=='bedallotlist'){?>active<?php }?>">
			  <a href="?dashboard=user&page=bedallotment&tab=bedallotlist">
				 <i class="fa fa-align-justify"></i> <?php esc_html_e('Assigned Bed List', 'hospital_mgt'); ?></a>
			  </a>
		</li>    
		<li class="<?php if($active_tab=='bedassign'){?>active<?php }?>">
		  <?php  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
			{?>
				<a href="?dashboard=user&page=bedallotment&tab=bedassign&&action=edit&allotment_id=<?php echo $_REQUEST['allotment_id'];?>" class="tab <?php echo $active_tab == 'bedassign' ? 'active' : ''; ?>">
				<i class="fa fa"></i> <?php esc_html_e('Edit Assign Bed', 'hospital_mgt'); ?></a>
			 <?php 
			}
			else
			{
				if($user_access['add']=='1')
				{			
				?>				
					<a href="?dashboard=user&page=bedallotment&tab=bedassign&&action=insert" class="tab <?php echo $active_tab == 'bedassign' ? 'active' : ''; ?>">
					<i class="fa fa-plus-circle"></i> <?php esc_html_e('Assign Bed', 'hospital_mgt'); ?></a>
				<?php
				}
			}
			?>	  
		</li>	  
		<?php
		if($active_tab=="transfer"){
		  ?> 
		  
		   <li class="<?php if($active_tab=='transfer'){?>active<?php }?>">
			  <a href="?dashboard=user&page=bedallotment&tab=transfer&action=transfer&allotment_id=<?php echo $_REQUEST['allotment_id'];?>">
				 <i class="fa fa-plus-circle"></i> <?php esc_html_e('Transfer Bed', 'hospital_mgt'); ?></a>
			  </a>
		  </li>
		  <?php }  ?>
		  
	</ul>
	<div class="tab-content opacity_div"><!-- START TAB CONTENT DIV-->
		<?php 
		if($active_tab=='bedallotlist'){
		?>
		<script type="text/javascript">
		jQuery(document).ready(function() {
			"use strict";
		jQuery('#bedallotmentlist').DataTable({
			"responsive": true,
			 "order": [[ 4, "desc" ]],
			 "aoColumns":[
						  {"bSortable": true},
						  {"bSortable": true},
						  {"bSortable": true},
						  {"bSortable": true},
						  {"bSortable": true},
						  {"bSortable": true}                 
						  <?php  
							if($user_access['edit']=='1' || $user_access['delete']=='1' || $user_access['add']=='1')
							{
								?>
							  ,{"bSortable": false}
							 <?php  
							 } 
							 ?> 	
							],
				language:<?php echo MJ_hmgt_datatable_multi_language();?>
			});
		} );
		</script>
		<div class="tab-pane fade active in"  id="bedallotlist"><!-- START TAB PANE DIV-->
			<div class="panel-body"><!-- START PANEL BODY DIV-->
				<div class="table-responsive"><!-- START TABLE RESPONSIVE DIV-->
					<table id="bedallotmentlist" class="display dataTable " cellspacing="0" width="100%"><!-- START BAD Allotment LIST TABLE-->
						<thead>
							<tr>
								<th><?php esc_html_e( 'Bed Type', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Bed Number', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Patient', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Nurse', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Allotment Date', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Expected Discharge Date', 'hospital_mgt' ) ;?></th>
								<?php  
								if($user_access['edit']=='1' || $user_access['delete']=='1' || $user_access['add']=='1')
								{ ?>
									<th> <?php esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
								<?php
								}
								?>		
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th><?php esc_html_e( 'Bed Type', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Bed Number', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Patient', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Nurse', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Allotment Date', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Expected Discharge Date', 'hospital_mgt' ) ;?></th>
								<?php  
								if($user_access['edit']=='1' || $user_access['delete']=='1' || $user_access['add']=='1')
								{ ?>
									<th> <?php esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
								<?php
								}
								?>		
							</tr>
						</tfoot>
						<tbody>
						 <?php						
						if($obj_hospital->role == 'laboratorist' || $obj_hospital->role == 'pharmacist' || $obj_hospital->role == 'accountant' || $obj_hospital->role == 'receptionist') 
						{
							$own_data=$user_access['own_data'];
							if($own_data == '1')
							{
								$bedallotment_data=$obj_bed->MJ_hmgt_get_all_bedallotment_by_allotment_by();
							}
							else
							{
								$bedallotment_data=$obj_bed->MJ_hmgt_get_all_bedallotment();
							}
						}
						elseif($obj_hospital->role == 'doctor') 
						{
							$own_data=$user_access['own_data'];
							if($own_data == '1')
							{
								$bedallotment_data=$obj_bed->MJ_hmgt_get_doctor_all_bedallotment_by_allotment_by();
							}
							else
							{
								$bedallotment_data=$obj_bed->MJ_hmgt_get_all_bedallotment();
							}
						}
						elseif($obj_hospital->role == 'nurse') 
						{
							$own_data=$user_access['own_data'];
							if($own_data == '1')
							{
								$bedallotment_data=$obj_bed->MJ_hmgt_get_nurse_all_bedallotment_by_allotment_by();
							}
							else
							{
								$bedallotment_data=$obj_bed->MJ_hmgt_get_all_bedallotment();
							}
						}
						elseif($obj_hospital->role == 'patient') 
						{
							$own_data=$user_access['own_data'];
							if($own_data == '1')
							{
								$bedallotment_data=$obj_bed->MJ_hmgt_get_all_bedallotment_by_patient();
							}
							else
							{
								$bedallotment_data=$obj_bed->MJ_hmgt_get_all_bedallotment();
							}
						}
												
						if(!empty($bedallotment_data))
						{
							foreach ($bedallotment_data as $retrieved_data)
							{ 
								$patient_data =	MJ_hmgt_get_user_detail_byid($retrieved_data->patient_id);
						?>
								<tr>
									<td class="bed_type"><?php echo $obj_bed->MJ_hmgt_get_bedtype_name($retrieved_data->bed_type_id);	?></td>
									<td class="bed_number"><?php
									if(!empty($retrieved_data->bed_number))
									{  
										echo $obj_bed->MJ_hmgt_get_bed_number(esc_html($retrieved_data->bed_number));
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
									<td class="allotment_time"><?php echo date(MJ_hmgt_date_formate(),strtotime($retrieved_data->allotment_date));?></td>
									<td class="discharge_time"><?php echo date(MJ_hmgt_date_formate(),strtotime( $retrieved_data->discharge_time));?></td>
									<?php  
									if($user_access['edit']=='1' || $user_access['delete']=='1' || $user_access['add']=='1')
									{ ?>
										<td class="action"> 
											<?php
											if($user_access['add']=='1')
											{
											?>					
												<a href="?dashboard=user&page=bedallotment&tab=transfer&action=transfer&allotment_id=<?php echo esc_attr($retrieved_data->bed_allotment_id);?>" class="btn btn-success"> <?php esc_html_e('Transfer Bed', 'hospital_mgt' ) ;?></a>
											<?php
											}
											if($user_access['edit']=='1')
											{
											?>
												<a href="?dashboard=user&page=bedallotment&tab=bedassign&action=edit&allotment_id=<?php echo MJ_hmgt_id_encrypt(esc_attr($retrieved_data->bed_allotment_id));?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
											<?php
											}
											if($user_access['delete']=='1')
											{
											?>	
												<a href="?dashboard=user&page=bedallotment&tab=bedallotlist&action=delete&allotment_id=<?php echo MJ_hmgt_id_encrypt(esc_attr($retrieved_data->bed_allotment_id));?>" class="btn btn-danger" 
												onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this record?','hospital_mgt');?>');">
												<?php esc_html_e( 'Delete', 'hospital_mgt' ) ;?> </a>
										   <?php
											}
										   ?>
										</td>
										<?php
									}
										 ?>
								</tr>
							<?php 
							}
						}?>
						</tbody>
					</table><!-- END BEDALLOTMENT TABLE-->
				</div><!-- END TABLE RESPONSIVE DIV-->
			</div><!-- END PANEL BODY DIV-->
		</div><!-- END PANE TAB DIV-->
		<?php }
		 if($active_tab=='bedassign'){
		?>
		<div class="tab-pane fade active in"  id="bedallot"><!-- END TAB PANE DIV-->
		<?php 
		$obj_bed = new MJ_hmgt_bedmanage();
		?>
	<script type="text/javascript">
	jQuery(document).ready(function($) 
	{
		"use strict";
		<?php
	if (is_rtl())
		{
		?>	
			$('#patient_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#patient_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>
			var start = new Date();
			var end = new Date(new Date().setYear(start.getFullYear()+1));
			 $.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
			$('#allotment_date').datepicker({
				startDate : start,
				
				autoclose: true
			}).on('changeDate', function(){
				//$('#discharge_time').datepicker('setStartDate', new Date($(this).val()));
				$('#discharge_time').datepicker('setStartDate', $(this).val());
			}); 
			$.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
			$('#discharge_time').datepicker({
				startDate : start,
				autoclose: true
			}).on('changeDate', function(){
				//$('#allotment_date').datepicker('setEndDate', new Date($(this).val()));
				$('#allotment_date').datepicker('setEndDate', $(this).val());
			});
		 $('#nurse').multiselect(
		{
			nonSelectedText :'<?php esc_html_e('Select Nurse','hospital_mgt');?>',
			includeSelectAllOption: true,
		    selectAllText : '<?php esc_html_e('Select all','hospital_mgt'); ?>',
			templates: {
		            button: '<button class="multiselect btn btn-default dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="multiselect-selected-text"></span><b class="caret"></b></button>',
		        },
				buttonContainer: '<div class="dropdown" />'
		});
		 $('#patient_id').select2();
	} );
	</script>
		 <?php 	
			$edit=0;
			if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit'){
				$edit=1;
				$result = $obj_bed->MJ_hmgt_get_single_bedallotment(MJ_hmgt_id_decrypt($_REQUEST['allotment_id']));
			}?>
	
		<div class="panel-body"><!-- START PANEL BODY DIV-->
			<form name="patient_form" action="" method="post" class="form-horizontal" id="patient_form"><!-- START BED ALLOTMENT FORM-->
			<?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
			<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
			<input type="hidden" name="allotment_id" value="<?php if(isset($_REQUEST['allotment_id'])) echo MJ_hmgt_id_decrypt(esc_attr($_REQUEST['allotment_id']));?>"  />
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="patient_id"><?php esc_html_e('Patient','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<select name="patient_id" id="patient_id" class="form-control validate[required] ">
							<option value=""><?php esc_html_e('Select Patient','hospital_mgt');?></option>
							<?php 
							if($edit)
							{
								$patient_id1 = $result->patient_id;
							}
							elseif(isset($_REQUEST['patient_id']))
							{
								$patient_id1 = $_REQUEST['patient_id'];
							}
							else 
							{
								$patient_id1 = "";
							}
							$own_data=$user_access['own_data'];
							if($own_data == '1')
							{ 
								$patients =$obj_hospital->patient;
											
								if(!empty($patients))
								{
									foreach($patients as $patient)
									{
										$patient_id = get_user_meta($patient->ID,'patient_id',true);
										
										echo '<option value="'.$patient->ID.'" '.selected($patient_id1,$patient->ID).'>'.$patient_id.' - '.$patient->display_name.'</option>';
									
									}
								}
							}
							else
							{
								$patients = MJ_hmgt_inpatient_list();
								if(!empty($patients))
								{
									foreach($patients as $patient)
									{
										echo '<option value="'.$patient['id'].'" '.selected($patient_id1,$patient['id']).'>'.$patient['patient_id'].' - '.$patient['first_name'].' '.$patient['last_name'].'</option>';
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
					<div class="col-sm-8" >
						<?php 
						$patient_status = "";
						if($edit){ $patient=MJ_get_inpatient_status($result->patient_id);
							if(!empty($patient)){
						 $patient_status=$patient->patient_status;}}elseif(isset($_POST['patient_status'])){$patient_status=$_POST['patient_status'];}else{$patient_status=' ';} ?>
						<select name="patient_status" class="form-control validate[required]" >
						<option value=""><?php esc_html_e('select Patient Status','hospital_mgt');?></option>
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
					<label class="col-sm-2 control-label form-label" for="bed_type_id"><?php esc_html_e('Select Bed Type','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
					<?php 
						if(isset($_REQUEST['bed_type_id']))
						{
							$bed_type1 = $_REQUEST['bed_type_id'];
						}
						elseif($edit)
						{
							$bed_type1 = $result->bed_type_id;
						}
						else
						{						
							$bed_type1 = "";
						}
						?>
						<select name="bed_type_id" class="form-control validate[required]" id="bed_type_id">
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
						<select name="bed_number" class="form-control validate[required]" id="bednumber">
							<option value=""><?php esc_html_e('Select Bed Number','hospital_mgt');?></option>
							<?php 
							if($edit)
							{
								$bedtype_data = $obj_bed->MJ_hmgt_get_bed_by_bedtype($result->bed_type_id);
								if(!empty($bedtype_data))
								{
									foreach ($bedtype_data as $retrieved_data)
									{
										echo '<option value="'.$retrieved_data->bed_id.'" '.selected($result->bed_number,$retrieved_data->bed_id).'>'.$retrieved_data->bed_number.'</option>';
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
					<div class="col-sm-2"></div>
					<div class="col-sm-8" id="bedlocation">
					<?php 
					if($edit)
					{
						$obj_bed = new MJ_hmgt_bedmanage();
						$beddata = $obj_bed->MJ_hmgt_get_single_bed($result->bed_number);
						
					?>	
						<p class="bg-info bed_location"><strong>Bed Location : </strong><?php print $beddata->bed_location  ?></p>
					<?php
					}
					?>	
					</div>
					<div class="col-sm-2 bed_location"></div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="allotment_date"><?php esc_html_e('Allotment Date','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="allotment_date" class="form-control validate[required]" type="text"   value="<?php if($edit){ echo date(MJ_hmgt_date_formate(),strtotime($result->allotment_date));}elseif(isset($_POST['allotment_date'])) echo esc_attr($_POST['allotment_date']);?>" name="allotment_date">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="discharge_time"><?php esc_html_e('Expected Discharge Date','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="discharge_time" class="form-control validate[required]" type="text"  value="<?php if($edit){ echo date(MJ_hmgt_date_formate(),strtotime($result->discharge_time));}elseif(isset($_POST['discharge_time'])) echo esc_attr($_POST['discharge_time']);?>" name="discharge_time">
					</div>
				</div>
			</div>
			<?php wp_nonce_field( 'bedallotment_nonce' ); ?>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="doctor"><?php esc_html_e('Select Nurse','hospital_mgt');?></label>
					<div class="col-sm-8">
					<?php $allnurse = MJ_hmgt_getuser_by_user_role('nurse');
							$nurse_data = array();
							if($edit)
							{
								$nurse_list = $obj_bed->MJ_hmgt_get_nurse_by_bedallotment_id(MJ_hmgt_id_decrypt($_REQUEST['allotment_id']));
								
								foreach($nurse_list as $assign_id)
								{
									$nurse_data[]=$assign_id->child_id;
								}
							}
							elseif(isset($_REQUEST['doctor']))
							{
								$nurse_list = $_REQUEST['doctor'];
								foreach($nurse_list as $assign_id)
								{
									$nurse_data[]=$assign_id;
								}
							}
							
							?>
						<select name="nurse[]" class="form-control" multiple="multiple" id="nurse">
						<?php 
							if(!empty($allnurse))
							{
								foreach($allnurse as $nurse)
								{
									$selected = "";
									if(in_array($nurse['id'],$nurse_data))
										$selected = "selected";
									echo '<option value='.$nurse['id'].' '.$selected.'>'.$nurse['first_name'].'</option>';
								}
							}
							?>
						</select>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="middle_name"><?php esc_html_e('Description','hospital_mgt');?></label>
					<div class="col-sm-8">
						<textarea class="form-control validate[custom[address_description_validation]]" maxlength="150" name="allotment_description" id="allotment_description"><?php if($edit){ echo esc_attr($result->allotment_description);}elseif(isset($_POST['allotment_description'])) echo esc_attr($_POST['allotment_description']);?></textarea>
					</div>
				</div>
			</div>
			<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
				<input id="save_allow" type="submit" value="<?php if($edit){ esc_html_e('Save','hospital_mgt'); }else{ esc_html_e('Save','hospital_mgt');}?>" name="bedallotment" class="btn btn-success"/>
			</div>
			</form><!-- END BED Allotment FORM-->
		</div><!-- END PANEL BODY DIV-->
		
	</div><!-- END PANE TAB DIV-->
	<?php } 
	if($active_tab=="transfer")
	{ 
		require_once HMS_PLUGIN_DIR. '/template/transfer.php';
	}
	?>
    </div><!-- END TAB CONTENT DIV-->
</div><!-- END PANE BODY DIV-->