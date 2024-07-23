<?php
MJ_hmgt_browser_javascript_check();
$obj_dignosis = new MJ_hmgt_dignosis();
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
$active_tab=isset($_REQUEST['tab'])?$_REQUEST['tab']:'diagnosislist';
if(isset($_POST['save_diagnosis']))
{
	$nonce = $_POST['_wpnonce'];
	if (wp_verify_nonce( $nonce, 'save_diagnosis_nonce' ) )
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
				<div id="message" class="updated below-h2 ">
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
						wp_redirect ( home_url() . '?dashboard=user&page=diagnosis&tab=diagnosislist&message=2');
						exit();			
						
					}
					else 
					{
						wp_redirect( home_url() . '?dashboard=user&page=diagnosis&tab=diagnosislist&message=1');
						exit();
					}
				}
			}
		}
		else
		{
			$result = $obj_dignosis->MJ_hmgt_add_dignosis($_POST);
			
			if($result)
			{	
				if(isset($_REQUEST['action']) &&  $_REQUEST['action'] == 'edit')
				{
					wp_redirect ( home_url() . '?dashboard=user&page=diagnosis&tab=diagnosislist&message=2');
					exit();
				}
				else 
				{
					wp_redirect ( home_url() . '?dashboard=user&page=diagnosis&tab=diagnosislist&message=1');
					exit();
				}
			}
		}
	}	
}
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete')
{
	$result = $obj_dignosis->MJ_hmgt_delete_dignosis(MJ_hmgt_id_decrypt($_REQUEST['diagnosis_id']));
	if($result)
	{
		wp_redirect ( home_url() . '?dashboard=user&page=diagnosis&tab=diagnosislist&message=3');
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
			?></p>
		</div>
			<?php
	}
	elseif($message == 2)
	{?>
		<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
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
		</button><p>
		<?php 
			esc_html_e('Report status updated successfully','hospital_mgt');
		?></div></p><?php	
	}
}	
$active_tab = isset($_GET['tab'])?$_GET['tab']:'diagnosislist';
$edit=0;
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
{					
	$edit=1;
	$result = $obj_dignosis->MJ_hmgt_get_single_dignosis_report(MJ_hmgt_id_decrypt($_REQUEST['diagnosis_id']));
}
?>

<script type="text/javascript">
jQuery(document).ready(function($) {
	"use strict";
		$('#diagnosis1').DataTable({
			"responsive": true,
		 "order": [[ 0, "desc" ]],
		 "aoColumns":[
					  {"bSortable": true},
					  {"bSortable": true},
					  {"bSortable": true},
					  {"bSortable": true},
					  {"bSortable": true},
					  {"bSortable": true},
					  {"bSortable": true},
					  {"bVisible": true}                 
					  <?php  
						if($user_access['edit']=='1' || $user_access['delete']=='1')
						{
							?>
						  ,{"bSortable": false}
						 <?php  
						 } 
						 ?> 	
						],
			language:<?php echo MJ_hmgt_datatable_multi_language();?>
		}); 
		 $('#diagnosis_request_list').DataTable({
			 "responsive": true,
		 "order": [[ 0, "desc" ]],
		 "aoColumns":[
	                  {"bSortable": true},
	                  {"bSortable": true},
	                  {"bSortable": true},
	                  {"bVisible": true},	                 
	                  {"bVisible": true},	                 
	                  {"bSortable": false}
	               ],
			language:<?php echo MJ_hmgt_datatable_multi_language();?>
		}); 
		
		
	<?php
	if (is_rtl())
		{
		?>	
			$('#diagnosis_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
			$('#doctor_form_outpatient_popup_form_percription').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#diagnosis_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			$('#doctor_form_outpatient_popup_form_percription').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>
	$('#symptoms').multiselect(
	{
		nonSelectedText :'<?php esc_html_e('Select Symptoms','hospital_mgt');?>',
		includeSelectAllOption: true,
	    selectAllText : '<?php esc_html_e('Select all','hospital_mgt'); ?>',
		templates: {
            button: '<button class="multiselect btn btn-default dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="multiselect-selected-text"></span><b class="caret"></b></button>',
        },
		buttonContainer: '<div class="dropdown" />'
	});
	var date = new Date();
       date.setDate(date.getDate()-0);
	    $.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
        $('.birth_date').datepicker({
	    startDate: date,
        autoclose: true
   }); 
				
		//add outpatient pop up//
	 
	    $('#doctor_form_outpatient_popup_form_percription').on('submit', function(e) {
		e.preventDefault();
		
		var valid = $('#doctor_form_outpatient_popup_form_percription').validationEngine('validate');
		if (valid == true) 
		{
			var form = new FormData(this);
		
		 $.ajax({
			type:"POST",
			url: $(this).attr('action'),
			data:form,
			cache: false,
            contentType: false,
            processData: false,
			success: function(data)
			{
				 if(data!=""){ 
				   var json_obj = $.parseJSON(data);
				    
					$('#doctor_form_outpatient_popup_form_percription').trigger("reset");
					$('#patient').append(json_obj[0]);
					
					$('#upload_user_avatar_preview').html('<img alt="" src="<?php echo get_option( 'hmgt_patient_thumb' ); ?>">');
					$('#hmgt_user_avatar_url').val('');
					
					$('.modal').modal('hide');
				}  
			},
			error: function(data){
			}
		})
		
		}
	}); 
	$('#report_type').multiselect(
	{
		nonSelectedText :'<?php esc_html_e('Select Report Name','hospital_mgt');?>',
		includeSelectAllOption: true,
	    selectAllText : '<?php esc_html_e('Select all','hospital_mgt'); ?>',
		templates: {
            button: '<button class="multiselect btn btn-default dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="multiselect-selected-text"></span><b class="caret"></b></button>',
        },
		buttonContainer: '<div class="dropdown" />'
	});
	$('.tax_charge').multiselect(
	{
		nonSelectedText :'<?php esc_html_e('Select Tax','hospital_mgt');?>',
		includeSelectAllOption: true,
	    selectAllText : '<?php esc_html_e('Select all','hospital_mgt'); ?>',
		templates: {
            button: '<button class="multiselect btn btn-default dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="multiselect-selected-text"></span><b class="caret"></b></button>',
        },
		buttonContainer: '<div class="dropdown" />'
	});
	
	$(".symptoms_alert").on("click",function()
	{	
		checked = $(".multiselect_validation_symtoms .dropdown-menu input:checked").length;
		if(!checked)
		{
		  alert("<?php esc_html_e('Please select atleast one Symptoms','hospital_mgt');?>");
		  return false;
		}	
	});

	  $('#dagnosis_report_form').on('submit', function(e)
		{
			e.preventDefault();
			var form = $(this).serialize();
			var valid = $("#dagnosis_report_form").validationEngine('validate');
			if (valid == true)
			{	
				$.ajax(
				{
					type:"POST",
					url: $(this).attr('action'),
					data:form,												
					success: function(data)
					{														
						$('#dagnosis_report_form').trigger("reset");
						$('.modal').modal('hide');
					
						window.location.href = window.location.href + "&message=4";								
					},
					error: function(data){
					}
				})
				
			}
		}); 	
	
	$("body").on("click", ".update_dagnosis_report", function(event)
	{
		var report_status  = $(this).attr('report_status');
		var pescription_id  = $(this).attr('priscription_id');
		$(".report_status").val(report_status);
		$(".pescription").val(pescription_id);
		
	});
	
	$("body").on("click",".save_diagnosis",function()
	 {
		var checked = $(".multiselect_validation_Report .dropdown-menu input:checked").length;

		if(!checked)
		{
			alert("<?php esc_html_e('Please select atleast one report type','hospital_mgt');?>");
			return false;
		}		
	});

	$("body").on("click", ".save_diagnosis", function()
	{
		var patient_name = $("#patient");
		if (patient_name.val() == "") {
			
			alert("<?php esc_html_e('Please select atleast one patient','hospital_mgt');?>");
			return false;
		}
		return true;
	});
	
	$('#patient').select2();
});
</script>
<!-- POP up code -->
<div class="popup-bg zindex_100000">
    <div class="overlay-content overlay_content_css">
		<div class="modal-content">
			<div class="notice_content"></div>    
			<div class="category_list">
			 </div>
        </div>
   </div> 
</div>
<!-- End POP-UP Code -->
<div class="panel-body panel-white"><!-- START PANEL BODY DIV-->
	<ul class="nav nav-tabs panel_tabs" role="tablist">
		    <li class="<?php if($active_tab=='diagnosislist'){?>active<?php }?>">
				<a href="?dashboard=user&page=diagnosis&tab=diagnosislist" class="tab <?php echo $active_tab == 'diagnosislist' ? 'active' : ''; ?>">
				 <i class="fa fa-align-justify"></i> <?php esc_html_e('Diagnosis Report List', 'hospital_mgt'); ?></a>
			  </a>
		    </li>
		    <li class="<?php if($active_tab=='adddiagnosis'){?>active<?php }?>">
			  <?php  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && isset($_REQUEST['diagnosis_id']))
				{?>
				<a href="?dashboard=user&page=diagnosis&tab=adddiagnosis&action=edit&diagnosis_id=<?php if(isset($_REQUEST['diagnosis_id'])) echo $_REQUEST['diagnosis_id'];?>" class="tab <?php echo $active_tab == 'adddiagnosis' ? 'active' : ''; ?>">
				 <i class="fa fa"></i> <?php esc_html_e('Edit Diagnosis Report', 'hospital_mgt'); ?></a>
				 <?php }
				else
				{	
					if($user_access['add']=='1')
					{		
					?>
						<a href="?dashboard=user&page=diagnosis&tab=adddiagnosis&action=insert" class="tab <?php echo $active_tab == 'adddiagnosis' ? 'active' : ''; ?>">
						<i class="fa fa-plus-circle"></i> <?php esc_html_e('Add Diagnosis Report', 'hospital_mgt'); ?></a>
				<?php 
					}
				}
			?>
			</li>
			<?php 
			if($obj_hospital->role == 'laboratorist')
			{
			?>
				<li class="<?php if($active_tab=='new_diagnosis_report_request_list'){?>active<?php }?>">
					<a href="?dashboard=user&page=diagnosis&tab=new_diagnosis_report_request_list" class="tab <?php echo $active_tab == 'new_diagnosis_report_request_list' ? 'active' : ''; ?>">
					 <i class="fa fa-align-justify"></i> <?php esc_html_e('New Diagnosis Report Request', 'hospital_mgt'); ?></a>
				  </a>
				</li>
			<?php 
			} ?>
	</ul>
<?php if($active_tab=='diagnosislist')
{
?>
	<div class="tab-content"><!--START TAB CONTENT DIV-->
		<div class="panel-body"><!--STRAT PANEL BODY DIV-->
            <div class="table-responsive">	<!--START TABLE RESPONSIVE DIV-->
				<table id="diagnosis1" class="display dataTable" cellspacing="0" width="100%"><!--START DIGNOSIS LIST TABLE-->
				    <thead>
						<tr>
						    <th><?php  esc_html_e( 'Date', 'hospital_mgt' ) ;?></th>
						    <th> <?php esc_html_e( 'Patient ID-Name', 'hospital_mgt' ) ;?></th>
						    <th> <?php esc_html_e( 'Report Type & Amount', 'hospital_mgt' ) ;?></th>
							<th> <?php esc_html_e( 'Description', 'hospital_mgt' ) ;?></th>
							<th> <?php esc_html_e( 'Report', 'hospital_mgt' ) ;?></th>
							<th><?php esc_html_e( 'Report Cost', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
							<th><?php esc_html_e( 'Tax Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
							<th><?php esc_html_e( 'Total Report Cost', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
							<?php  
							if($user_access['edit']=='1' || $user_access['delete']=='1')
							{ ?>
								<th> <?php esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
							<?php
							}
							?>
						</tr>
				    </thead>
			        <tfoot>
						<tr>
							<th><?php  esc_html_e( 'Date', 'hospital_mgt' ) ;?></th>
							<th> <?php esc_html_e( 'Patient ID-Name', 'hospital_mgt' ) ;?></th>
							<th> <?php esc_html_e( 'Report Type & Amount', 'hospital_mgt' ) ;?></th>
						    <th> <?php esc_html_e( 'Description', 'hospital_mgt' ) ;?></th>
							<th> <?php esc_html_e( 'Report', 'hospital_mgt' ) ;?></th>
							<th><?php esc_html_e( 'Report Cost', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
							<th><?php esc_html_e( 'Tax Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
							<th><?php esc_html_e( 'Total Report Cost', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
							<?php  
							if($user_access['edit']=='1' || $user_access['delete']=='1')
							{ ?>
								<th> <?php esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
							<?php
							}
							?>
						</tr>
				    </tfoot>
					<tbody>
					<?php 
					$current_user_id=get_current_user_id();
					if($role == 'patient')
					{
						$own_data=$user_access['own_data'];
						if($own_data == '1')
						{ 
							$dignosis_data = $obj_hospital->MJ_hmgt_get_current_patint_diagnosis_report($current_user_id);
						 
						}
						else
						{
						   $dignosis_data=$obj_dignosis->MJ_hmgt_get_all_dignosis_report();
						}
					}
					elseif($role == 'doctor')
					{
						$own_data=$user_access['own_data'];
						if($own_data == '1')
						{ 
							$dignosis_data=$obj_dignosis->MJ_hmgt_get_doctor_last_diagnosis_created_by($current_user_id);
						}
						else
						{
						   $dignosis_data=$obj_dignosis->MJ_hmgt_get_all_dignosis_report();
						}
					}
					elseif($role == 'nurse')
					{
						$own_data=$user_access['own_data'];
						if($own_data == '1')
						{ 
							$dignosis_data=$obj_dignosis->MJ_hmgt_get_nurse_last_diagnosis_created_by($current_user_id);
						}
						else
						{
						   $dignosis_data=$obj_dignosis->MJ_hmgt_get_all_dignosis_report();
						}
					}
					elseif($obj_hospital->role == 'laboratorist' || $obj_hospital->role == 'pharmacist' || $obj_hospital->role == 'accountant' || $obj_hospital->role == 'receptionist') 
					{
						$own_data=$user_access['own_data'];
						if($own_data == '1')
						{ 
							$dignosis_data=$obj_dignosis->MJ_hmgt_get_last_diagnosis_created_by($current_user_id);
						}
						else
						{
						   $dignosis_data=$obj_dignosis->MJ_hmgt_get_all_dignosis_report();
						}
					}
					if(!empty($dignosis_data))
					{
						foreach ($dignosis_data as $retrieved_data)
						{ 
					 ?>
							<tr>
								<td class="date"><?php echo date(MJ_hmgt_date_formate(),strtotime($retrieved_data->diagnosis_date));?></td>
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
								<td class="description"><?php echo $retrieved_data->diagno_description;?></td>		
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
										echo '<a href="'.content_url().'/uploads/hospital_assets/'.$retrieved_data->attach_report.'" class="btn btn-default" target="_blank"
										><i class="fa fa-eye"></i>  '. esc_html__( "Download", "hospital_mgt" ) .' </a>';
									}	
									else
									{		
										echo esc_html__('No any Report','hospital_mgt');
									}	
								?>
								</td>	
								<td class=""><?php echo number_format($retrieved_data->report_cost, 2, '.', ''); ?></td>
								<td class=""><?php echo number_format($retrieved_data->total_tax, 2, '.', ''); ?></td>
								<td class=""><?php echo number_format($retrieved_data->total_cost, 2, '.', ''); ?></td>	
								<?php  
								if($user_access['edit']=='1' || $user_access['delete']=='1')
								{ ?>								
									<td class="action"> 
									<?php
									if($user_access['edit']=='1')
									{
										if($retrieved_data->total_cost!="")
										{
										?>
										<a href="?dashboard=user&page=diagnosis&tab=adddiagnosis&action=edit&diagnosis_id=<?php echo MJ_hmgt_id_encrypt(esc_attr($retrieved_data->diagnosis_id));?>" 
										class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
										<?php
										}							
									}
									if($user_access['delete']=='1')
									{
									?>					
										<a href="?dashboard=user&page=diagnosis&tab=diagnosislist&action=delete&diagnosis_id=<?php echo MJ_hmgt_id_encrypt(esc_attr($retrieved_data->diagnosis_id));?>" 
										class="btn btn-danger" 
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
				</table><!--END DIGNOSIS LIST TABLE-->
            </div><!--END TABLE RESPONSIVE DIV-->
        </div><!--END PANEL BODY DIV-->
	<?php
	}
	if($active_tab=='adddiagnosis')
	{ 
   ?>
	    <div class="panel-body"><!--START PANEL BODY DIV-->
			<form name="diagnosis_form" action="" method="post" class="form-horizontal" id="diagnosis_form" enctype="multipart/form-data"><!--START DIGNOSIS FORM-->
			<?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
			<input type="hidden" id="action_name" name="action" value="<?php echo esc_attr($action);?>">
			<input type="hidden" id="diagnosisid" name="diagnosisid" value="<?php if(isset($_REQUEST['diagnosis_id'])) echo MJ_hmgt_id_decrypt(esc_attr($_REQUEST['diagnosis_id']));?>"  />
			
			<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-sm-2 control-label form-label" for="patient_id"><?php esc_html_e('Patient','hospital_mgt'); ?><span class="require-field">*</span></label>
						<div class="col-sm-8 margin_bottom_5px">
							<select name="patient_id" id="patient" class="form-control  max_width_100">
								<option value=""><?php esc_html_e('Select Patient','hospital_mgt'); ?></option>
								<?php 
								if($edit)
									$patient_id1 = $result->patient_id;
								elseif(isset($_REQUEST['patient_id']))
									$patient_id1 = $_REQUEST['patient_id'];
								else 
									$patient_id1 = "";
								$own_data=$user_access['own_data'];
								if($own_data == '1')
								{
									if($obj_hospital->role == 'laboratorist') 
									{ 
										$current_user_id=get_current_user_id();
										$patients =MJ_hmgt_get_patient_list_for_laboratorist($current_user_id);
													
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
										$patients =$obj_hospital->patient;
													
										if(!empty($patients))
										{
											foreach($patients as $patient)
											{
												$patient_id = get_user_meta($patient->ID,'patient_id',true);
												
												echo '<option value="'.$patient->ID.'" '.selected($patient_id1,$patient->ID).'>'.$patient->display_name.' - '.$patient_id.'</option>';
											
											}
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
											echo '<option value="'.$patient['id'].'" '.selected($patient_id1,$patient['id']).'> '.$patient['first_name'].' '.$patient['last_name'].' - '.$patient['patient_id'].'</option>';
										}
									}
								}
								?>
							</select>						
						</div>
						<!--ADD OUT PATIENT POPUP BUTTON -->
						<!-- <div class="col-sm-2">		
							<button type="button" class="btn btn-default" data-bs-toggle="modal" data-bs-target="#myModal_add_outpatient"> <?php esc_html_e('Add Outpatient','hospital_mgt');?></button>				
						</div> -->
					</div>
				</div>
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-sm-2 control-label form-label" for="Report"><?php esc_html_e('Report Name','hospital_mgt');?><span class="require-field">*</span></label>
						<div class="col-sm-8 multiselect_validation_Report margin_bottom_5px">
							<select class="form-control validate[required] reportlist_list report_type dignosis_upload " multiple="multiple" name="report_type[]"
							id="report_type">
							<?php 
							$report_type=new MJ_hmgt_dignosis();
							$operation_array =$report_type->MJ_hmgt_get_all_report_type();
							if(!empty($operation_array))
							{
								foreach ($operation_array as $retrive_data)
								{
									$report_type_data=$retrive_data->post_title;
									$report_type_array=json_decode($report_type_data);			
									$report_type=explode(",",$result->report_type);
									?>
									<option value="<?php echo esc_attr($retrive_data->ID); ?>" <?php  if(in_array($retrive_data->ID,$report_type)){ echo 'selected'; } ?>><?php echo esc_html($report_type_array->category_name); ?></option>
									<?php
								}
							}
							?>						
							</select>
							<br>
						</div>
						<div class="col-sm-2"><button id="addremove" model="report_type"><?php esc_html_e('Add Or Remove','hospital_mgt');?></button></div>
					</div>
				</div>
				<?php
				if($edit)
				{
					?>
					<div class="add_document_div_main_class">		
						<div class="form-group">	
							<div class="mb-3 row">		   
								<label class="offset-sm-2 col-lg-2 col-md-2 col-sm-2 col-xs-2 control-label form-label upload_document_text_align"  for="document"><?php esc_html_e('Report Name','hospital_mgt');?></label>
								<label class="col-sm-3 control-label form-label upload_document_text_align"  for="document"><?php esc_html_e('Upload Report','hospital_mgt');?></label>
								<label class="col-sm-2 control-label form-label upload_document_text_align"  for="document"><?php esc_html_e('View Report','hospital_mgt');?></label>
								<label class="col-sm-1 control-label form-label upload_document_text_align"  for="document"><?php esc_html_e('Amount','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(); ?>)</label>
							</div>
						</div>
						<?php
						$dignosis_array=json_decode($result->attach_report);
						
						if(!empty($result->attach_report))
						{
							foreach($dignosis_array as $key=>$value)
							{
								$report_type=new MJ_hmgt_dignosis();
								$report_data=$report_type->MJ_hmgt_get_report_by_id($value->report_id);
								$report_type_array=json_decode($report_data);
								?>
							<div class="form-group">
								<div class="mb-3 row">				
									<div class="offset-sm-2 col-lg-2 col-md-2 col-sm-2 col-xs-2">
									<input type="hidden" name="report_id[]" value="<?php echo esc_attr($value->report_id); ?>">
									<input type="text" class="form-control file report_name text_align_center" value="<?php echo esc_attr($report_type_array->category_name); ?>" name="report_name[]" readonly>
									</div>
									
									<div class="col-sm-3">
										<input type="file" class="form-control file document text_align_center" name="document[]" value="<?php echo esc_attr($value->attach_report); ?>">
										
									</div>		
									<div class="col-sm-2">
										<?php
										echo '<a href="'.content_url().'/uploads/hospital_assets/'.$value->attach_report.'" class="btn btn-default" target="_blank"><i class="fa fa-eye"></i> '.$report_type_array->category_name.' '.esc_html__('Report','hospital_mgt').'</a>';
										?>
										<input type="hidden" name="hidden_attach_report[]" value="<?php echo $value->attach_report; ?>" >
									</div>	
									<div class="col-sm-1">
										<input type="text" class="form-control file diagnosis_total_amount text_align_center" value="<?php echo esc_attr($value->report_amount); ?>" name="diagnosis_total_amount[]" readonly>
									</div>		
								</div>	 
							</div>	
						<?php
							}		
						}
						?>
					</div>	
					<?php	
				}
				else
				{
				?>		
				<div class="add_document_div_main_class">		
					
				</div>
				<?php
				}	
				?>		
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-sm-2 control-label form-label" for="report_cost"><?php esc_html_e('Report Total Cost','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' )); ?>)</label>
						<div class="col-sm-8">
							<input type="hidden" name="cost" class="cost" value="<?php if($edit){ echo esc_attr($result->report_cost); } ?>">
							<input type="hidden" name="report_tax" class="report_tax" value="<?php if($edit){ echo esc_attr($result->total_tax);}  ?>">
							<input id="report_cost" class="form-control  text-input report_cost" type="number" min="0" onKeyPress="if(this.value.length==8) return false;" step="0.01" value="<?php if($edit){ echo esc_attr($result->total_cost);}elseif(isset($_POST['report_cost'])) echo esc_attr($_POST['report_cost']);?>" name="report_cost" readonly>
						</div>
					</div>
				</div>	
				<?php wp_nonce_field( 'save_diagnosis_nonce' ); ?>
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-sm-2 control-label form-label" for="description"><?php esc_html_e('Description','hospital_mgt');?></label>
						<div class="col-sm-8">
							<textarea id="diagno_description" maxlength="150" class="form-control validate[custom[address_description_validation]]" name="diagno_description"><?php if($edit)echo esc_textarea($result->diagno_description); elseif(isset($_POST['diagno_description'])) echo esc_textarea($_REQUEST['diagno_description']); else echo "";?> </textarea>
						</div>
					</div>
				</div>				
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-sm-2 control-label form-label" for="enable"><?php esc_html_e('Send Email to Patient ','hospital_mgt');?></label>
						<div class="col-sm-8 send_mail_checkbox">
							 <div class="checkbox">
								<label>
									<input  type="checkbox" value="1"  name="hmgt_send_mail_to_patient">
								</label>
							</div>						 
						</div>
					</div>
				</div>				
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-sm-2 control-label form-label" for="enable"><?php esc_html_e('Send Email to Doctor ','hospital_mgt');?></label>
						<div class="col-sm-8 send_mail_checkbox">
							 <div class="checkbox">
								<label>
									<input  type="checkbox" value="1" name="hmgt_send_mail_to_doctor" checked>
								</label>
							</div>						 
						</div>
					</div>
				</div>
			<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
				<input type="submit" id="dignosisreport" value="<?php if($edit){ esc_html_e('Save','hospital_mgt'); }else{ esc_html_e('Save','hospital_mgt');}?>" name="save_diagnosis" class="btn btn-success save_diagnosis"/>
			</div>
			</form><!--END DIGNOSI SFORM -->
        </div><!--END PANEL BODY DIV-->
	<?php } ?>
	<?php
	if($active_tab=='new_diagnosis_report_request_list')
    {
    ?>
	<div class="panel-body"><!--START PANEL BODY DIV-->
            <div class="table-responsive"><!--START TABLE RESONSIVE DIV-->	
				<table id="diagnosis_request_list" class="display dataTable" cellspacing="0" width="100%"><!--START DIGNOSIS REPORT REQUEST TABLE-->
				    <thead>
						<tr>
						    <th><?php  esc_html_e( 'Date', 'hospital_mgt' ) ;?></th>
						    <th> <?php esc_html_e( 'Patient ID-Name', 'hospital_mgt' ) ;?></th>
						    <th> <?php esc_html_e( 'Report Type', 'hospital_mgt' ) ;?></th>
							<th> <?php esc_html_e( 'Description', 'hospital_mgt' ) ;?></th>
							<th> <?php esc_html_e( 'Status', 'hospital_mgt' ) ;?></th>
							<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
						</tr>
				    </thead>
			        <tfoot>
						<tr>
							<th><?php  esc_html_e( 'Date', 'hospital_mgt' ) ;?></th>
							<th> <?php esc_html_e( 'Patient ID-Name', 'hospital_mgt' ) ;?></th>
							<th> <?php esc_html_e( 'Report Type', 'hospital_mgt' ) ;?></th>
						    <th> <?php esc_html_e( 'Description', 'hospital_mgt' ) ;?></th>
							<th> <?php esc_html_e( 'Status', 'hospital_mgt' ) ;?></th>
							<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
						</tr>
				    </tfoot>
					<tbody>
					<?php
					$obj_var=new MJ_hmgt_prescription();
					$alldiagnosis_requst_data=$obj_var->MJ_hmgt_get_all_diagnosis_requst();
					foreach($alldiagnosis_requst_data as $diagnosis_requst_data)
					{
					?>
						<tr>
							<td class="name"><?php echo date(MJ_hmgt_date_formate(),strtotime($diagnosis_requst_data->pris_create_date));?>
							</td>
							<td class="patient">
								<?php 
									$patient = MJ_hmgt_get_user_detail_byid( $diagnosis_requst_data->patient_id);
									$patinet_full_name=$patient['first_name']." ".$patient['last_name'];
									$patient_id=get_user_meta($diagnosis_requst_data->patient_id, 'patient_id', true);
									echo $patient_id .'-'. $patinet_full_name;
								?>
							</td>
						<?php 
						  $report_type=new MJ_hmgt_dignosis();
						  $report_type_data=explode(",",$diagnosis_requst_data->report_type);
						?>
						<td class="report_type">
						<?php
						  $i=1;
						  $report_amount=0;
						  $total_tax=0;
						  foreach ($report_type_data as $report_id)
						  {
							$report_data=$report_type->MJ_hmgt_get_report_by_id($report_id);
							$report_type_array=json_decode($report_data);
							
							echo '('.$i .') '.$report_type_array->category_name.','.$report_type_array->report_cost.' ';
							$i++;
							
							$report_amount += $report_type_array->report_cost;
							
							$diagnosis_tax_array = explode(",",$report_type_array->diagnosis_tax);
				
							if(!empty($diagnosis_tax_array))
							{	
								foreach($diagnosis_tax_array  as $tax_id)
								{				
									$tax_percentage=MJ_hmgt_tax_percentage_by_tax_id($tax_id);
									$tax_amount=$report_type_array->report_cost * $tax_percentage / 100;
									$total_tax=$total_tax + $tax_amount;
								}	
							}
						  }
						  $total_report_amount=$report_amount+$total_tax;
						?>
						 </td>
						  <td class="description"><?php echo esc_html($diagnosis_requst_data->report_description);?></td>
						  <td class="description"> <?php echo esc_html__("$diagnosis_requst_data->status","hospital_mgt");?></td>
						   <td class="action">
								<a href="?dashboard=user&page=diagnosis&tab=adddiagnosis&action=insert&patient_id=<?php echo esc_attr($diagnosis_requst_data->patient_id);?>&report_type=<?php echo esc_attr($diagnosis_requst_data->report_type);?>&cost=<?php echo esc_attr($report_amount);?>&total_tax=<?php echo esc_attr($total_tax);?>&report_cost=<?php echo esc_attr($total_report_amount);?>" class="btn btn-success margin_bottom_5px"><?php  esc_html_e( 'Upload Diagnosis Report', 'hospital_mgt' ) ;?> </a>

								<button type="button" class="btn btn-info update_dagnosis_report" priscription_id="<?php echo esc_attr($diagnosis_requst_data->priscription_id);?>" report_status="<?php echo esc_attr($diagnosis_requst_data->status); ?>" data-bs-toggle="modal" data-bs-target="#myModal_dagnosis_report"> <?php esc_html_e('Update Status ','hospital_mgt');?></button>

								<!-- <a href="javascript:void(0);" class="btn btn-info update_dagnosis_report" priscription_id="<?php echo esc_attr($diagnosis_requst_data->priscription_id);?>" report_status="<?php echo esc_attr($diagnosis_requst_data->status); ?>" data-toggle="modal"  data-target="#myModal_dagnosis_report"> <?php esc_html_e('Update Status ','hospital_mgt');?></a>	 -->
							</td>
						   </tr>
						<?php 
					}
       				?>
					</tbody>
				</table><!--END DIGNOSIS REPORT REQUEST TABLE-->
            </div><!--END TABLE RESONSIVE DIV-->
        </div><!--END PANEL BODY DIV-->
		<!----------Update status PopUP------------->
		<style>
			.modal-backdrop {
				position: unset;
			}
			.modal-dialog
			{
				margin-top: 6%;
			}
		</style>
		<div class="modal fade" id="myModal_dagnosis_report" tabindex="-1" aria-labelledby="myModal_dagnosis_report" aria-hidden="true" role="dialog" style="background-color: rgb(8, 7, 7);">
			<div class="modal-dialog modal-lg"><!--START MODAL DiaLOG DIV-->
				<div class="modal-content"><!--START MODAL CONTENT DIV-->
					<div class="modal-header float_left_width_100">
						<h3 class="modal-title float_left"><?php esc_html_e('Update Report Status','hospital_mgt');?></h3>
						<button type="button" class="close btn-close float_right" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
					</div>
					<div class="modal-body"><!--START MODAL BODY DIV-->
						<form name="dagnosis_report_form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" class="form-horizontal" id="dagnosis_report_form"><!--START DIGNOSIS STATUS FROM-->
							<input type="hidden" name="pescription_id" class="pescription" value=""  />
							<input type="hidden" name="action" value="MJ_hmgt_update_diagnosis_report_status_function">
								<div class="form-group">
									<div class="mb-3 row">	
										<label class="col-sm-2 control-label" for="installment_amount"><?php esc_html_e('Report Status','hospital_mgt');?><span class="require-field"></span></label>
										<div class="col-sm-8">
											<select name="report_status" id="" class="form-control report_status">
											<option value="Pending" class="validate[required]"><?php esc_html_e('Pending','hospital_mgt');?></option>
											<option value="Processing" class="validate[required]"><?php esc_html_e('Processing','hospital_mgt');?></option>
											<option value="Completed" class="validate[required]"><?php esc_html_e('Completed','hospital_mgt');?></option>
											</select>
										</div>
									</div>
								</div>									
							<div class="offset-sm-2 col-sm-8">
								<input type="submit" value="<?php if($edit){ esc_html_e('Update Status','hospital_mgt'); }else{ esc_html_e('Update Status','hospital_mgt');}?>" name="update_status" class="btn btn-success"/>
							</div>
					</form><!--END Diagnosis STATUS FORM-->
					</div><!--END MODAL BODY DIV-->
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-bs-dismiss="modal"><?php esc_html_e('Close','hospital_mgt'); ?></button>
					</div>
				</div><!--END CONTENT FADE DIV-->
			</div><!--END DiaLOG FADE DIV-->
		</div><!--END MODAL FADE DIV-->
		<?php }
    ?>
	</div>
</div>
<!----------ADD Outpatient------------->
<div class="modal fade overflow_scroll" id="myModal_add_outpatient" role="dialog"><!--START MODAL FADE DIV-->
    <div class="modal-dialog modal-lg"><!--START MODAL DiaLOG DIV-->
      <div class="modal-content"><!--START MODAL CONTENT DIV-->
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h3 class="modal-title"><?php esc_html_e('Add Outpatient','hospital_mgt');?></h3>
        </div>
			<div class="modal-body"><!--START MODAL BODY DIV-->
			 <?php 
				  $role='patient';
				   $patient_type='outpatient';
				   
					$newpatient=MJ_hmgt_get_lastpatient_id($role);
			   ?>
				<form name="out_patient_form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" class="form-horizontal" id="doctor_form_outpatient_popup_form_percription" enctype="multipart/form-data">
				<input type="hidden" name="action" value="MJ_hmgt_save_outpatient_popup_form_template">
								
				<input type="hidden" name="role" value="<?php echo esc_attr($role);?>"  />
				<input type="hidden" name="patient_type" value="<?php echo esc_attr($patient_type);?>"  />
				<div class="header">	
					<h3 class="first_hed"><?php esc_html_e('Personal Information','hospital_mgt');?></h3>
					<hr>
				</div>	
				<div class="form-group">
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label" for="roll_id"><?php esc_html_e('Patient Id','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="patient_id" class="form-control validate[required]" type="text" 
						value="<?php  echo esc_attr($newpatient);?>" readonly name="patient_id">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label" for="first_name"><?php esc_html_e('First Name','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="first_name" class="form-control validate[required,custom[onlyLetter_specialcharacter]] text-input" maxlength="50" type="text" value="" name="first_name">
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label" for="middle_name"><?php esc_html_e('Middle Name','hospital_mgt');?></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="middle_name" class="form-control validate[custom[onlyLetter_specialcharacter]]" type="text" maxlength="50" value="" name="middle_name">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label" for="last_name"><?php esc_html_e('Last Name','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="last_name" class="form-control validate[required,custom[onlyLetter_specialcharacter]] text-input" maxlength="50" type="text"  value="" name="last_name">
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label" for="birth_date"><?php esc_html_e('Date of birth','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input class="form-control validate[required] birth_date" type="text"  name="birth_date" 
						value="" readonly>
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label" for="blood_group"><?php esc_html_e('Blood Group','hospital_mgt');?></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<select id="blood_group" class="form-control" name="blood_group">
							<option value=""><?php esc_html_e('Select Blood Group','hospital_mgt');?></option>
							<?php
							$userblood=0;
							foreach(MJ_hmgt_blood_group() as $blood)
							{ ?>
								<option value="<?php echo esc_attr($blood);?>" <?php selected($userblood,$blood);  ?>><?php echo esc_html($blood); ?> </option>
							<?php 
							} ?>
						</select>
					</div>
				</div>	
				<div class="form-group">
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label" for="gender"><?php esc_html_e('Gender','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
					<?php $genderval = "male" ?>
						<label class="radio-inline">
						 <input type="radio" value="male" class="tog" name="gender"  <?php  checked( 'male', $genderval);  ?>/><?php esc_html_e('Male','hospital_mgt');?>
						</label>
						<label class="radio-inline">
						  <input type="radio" value="female" class="tog" name="gender"  <?php  checked( 'female', $genderval);  ?>/><?php esc_html_e('Female','hospital_mgt');?> 
						</label>
					</div>
				</div>
				<div class="header">
					<h3><?php esc_html_e('HomeTown Address Information','hospital_mgt');?></h3>
					<hr>
				</div>
				<div class="form-group">
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label" for="address"><?php esc_html_e('Home Town Address','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="address" class="form-control validate[required,custom[address_description_validation]]" type="text" maxlength="150"  name="address" 
						value="">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label" for="city_name"><?php esc_html_e('City','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="city_name" class="form-control validate[required,custom[city_state_country_validation]]" type="text" maxlength="50" name="city_name" 
						value="">
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label" for="state_name"><?php esc_html_e('State','hospital_mgt');?></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="state_name" class="form-control validate[custom[city_state_country_validation]]" type="text" maxlength="50" name="state_name" 
						value="">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label" for="state_name"><?php esc_html_e('Country','hospital_mgt');?></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="country_name" class="form-control validate[custom[city_state_country_validation]]" type="text" maxlength="50" name="country_name" 
						value="">
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label" for="zip_code"><?php esc_html_e('Zip Code','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="zip_code" class="form-control  validate[required,custom[onlyLetterNumber]]" type="text" maxlength="15" name="zip_code" 
						value="">
					</div>
				</div>
				<div class="header">
					<h3><?php esc_html_e('Contact Information','hospital_mgt');?></h3>
					<hr>
				</div>
				<div class="form-group">
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label  " for="mobile"><?php esc_html_e('Mobile Number','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 margin_bottom_5px">
					<input type="text" value="<?php if(isset($_POST['phonecode'])){ echo esc_attr($_POST['phonecode']); }else{ ?>+<?php echo MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }?>"  class="form-control  validate[required] onlynumber_and_plussign" name="phonecode" maxlength="5">
					</div>
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 has-feedback">
						<input id="mobile" class="form-control validate[required,custom[phone_number]] text-input" minlength="6" maxlength="15" type="text" value="" name="mobile">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label " for="phone"><?php esc_html_e('Phone','hospital_mgt');?></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="phone" class="form-control validate[custom[phone_number]] text-input" minlength="6" maxlength="15" type="text" value="" name="phone">
					</div>
				</div>
				<div class="header">
					<h3><?php esc_html_e('Login Information','hospital_mgt');?></h3>
					<hr>
		        </div>
				<div class="form-group">
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label " for="email"><?php esc_html_e('Email','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="email" class="form-control validate[required,custom[email]] text-input" maxlength="100" type="text"  name="email" value="">
					</div>
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label" for="username"><?php esc_html_e('User Name','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="username" class="form-control validate[required,custom[username_validation]]" type="text" maxlength="30" name="username" value="">
					</div>
				</div>
				<div class="form-group">
					<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label" for="password"><?php esc_html_e('Password','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<input id="password" class="form-control validate[required,minSize[8]]" type="password"  maxlength="12" name="password" value="">
					</div>
				</div>
				<div class="header">
					<h3><?php esc_html_e('Other Information','hospital_mgt');?></h3>
					<hr>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="doctor"><?php esc_html_e('Assign Doctor','hospital_mgt');?></label>
					<div class="col-sm-3">
						<select name="doctor" id="doctor" class="form-control">
							<option ><?php esc_html_e('select Doctor','hospital_mgt');?></option>
							<?php
							$doctorid=0;
							$get_doctor = array('role' => 'doctor');
								$doctordata=get_users($get_doctor);
								if(!empty($doctordata))
								{
									foreach($doctordata as $retrieved_data)
									{?>
										<option value="<?php echo esc_attr($retrieved_data->ID); ?>" <?php selected($doctorid,$retrieved_data->ID);?>><?php echo esc_html($retrieved_data->display_name);?> - <?php echo MJ_hmgt_doctor_specialization_title(esc_html($retrieved_data->ID)); ?></option>
									<?php 
									}
								}
								?>
						</select>
					 </div>
				</div>
				<div class="form-group">
					<label class="col-sm-2 control-label" for="symptoms"><?php esc_html_e('Symptoms','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-3 multiselect_validation_symtoms margin_bottom_5px">
							<select class="form-control symptoms_list" multiple="multiple" name="symptoms[]" id="symptoms">
							<?php 
							$user_object=new MJ_hmgt_user();
							$symptoms_category = $user_object->MJ_hmgt_getPatientSymptoms();
							if(!empty($symptoms_category))
							{
								foreach ($symptoms_category as $retrive_data)
								{
									?>
									<option value="<?php echo esc_attr($retrive_data->ID); ?>"><?php echo esc_html($retrive_data->post_title); ?></option>
									<?php
								}
							}
							?>					
							</select>
							<br>					
					</div>
					<div class="col-sm-2">
						<button id="addremove" model="symptoms"><?php esc_html_e('Add Or Remove','hospital_mgt');?></button>
					</div>
				</div>					
				<div class="form-group">
					<label class="col-sm-2 control-label" for="photo"><?php esc_html_e('Image','hospital_mgt');?></label>
						<div class="col-sm-3">
							<input type="hidden" id="hmgt_user_avatar_url" class="form-control" name="hmgt_user_avatar_url" readonly 
							/>
							<input type="hidden" name="hidden_upload_user_avatar_image" 
							>
							 <input id="upload_user_avatar_image" name="upload_user_avatar_image" type="file" class="form-control file" value="<?php esc_html_e( 'Upload image', 'hospital_mgt' ); ?>" />
						</div>
					<div class="clearfix"></div>
					
					<div class="col-sm-offset-2 col-sm-8">
						 <div id="upload_user_avatar_preview" >
							<img class="image_preview_css" alt="" src="<?php echo esc_url(get_option( 'hmgt_patient_thumb' )); ?>">
						</div>
					</div>
				</div>
				<div class="col-sm-offset-2 col-sm-8">
					<input type="submit" value="<?php esc_html_e('Save Patient','hospital_mgt');?>" name="save_outpatient" class="btn btn-success symptoms_alert"/>
				</div>
			    </form>
            </div><!--END MODAL BODY DIV-->
        <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?php  esc_html_e('Close','hospital_mgt');?></button>
        </div>
    </div><!--END MODAL CONTENT DIV-->
 </div><!--END MODAL DiaLOG DIV-->
</div><!--END MODAL FADE DIV-->