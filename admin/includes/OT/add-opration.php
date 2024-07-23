<?php
MJ_hmgt_browser_javascript_check();
$obj_bed = new MJ_hmgt_bedmanage();
$edit = 0;
if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
{
	$edit = 1;
	$ot_id = $_REQUEST['ot_id'];
	$result = $obj_ot->MJ_hmgt_get_single_operation($ot_id);
}
?>
<script type="text/javascript">
jQuery(document).ready(function($) 
{
	"use strict";
	<?php
	if (is_rtl())
		{
		?>	
			$('#operation_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
			$('#doctor_form_popup_add_percription').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
			$('#doctor_form_outpatient_popup_form_percription').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
			$('#bed_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#operation_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			$('#doctor_form_popup_add_percription').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			$('#doctor_form_outpatient_popup_form_percription').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			$('#bed_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
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
	$.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
    $('.birth_date').datepicker({
		endDate: '+0d',
		autoclose: true,
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
		$("body").on("click",".doctor_submit",function()
		 {
			var checked = $(".multiselect_validation_doctor .dropdown-menu input:checked").length;

			if(!checked)
			{
				alert("<?php esc_html_e('Please select atleast one doctor','hospital_mgt');?>");
				return false;
			}		
		});
	 
	 $('.timepicker').timepicki({
		show_meridian:false,
		min_hour_value:0,
		max_hour_value:23,
		step_size_minutes:15,
		overflow_minutes:true,
		increase_direction:'up',
		disable_keyboard_mobile: true});
	 
	  $('#add_doctor_form_popup_active').hide();
	    $('#adddoctor_label').hide();
	
	    $("body").on("click", '#add_precription_doctor', function(){	
	  
	       $('#add_doctor_form_popup_active').show();
	       $('#add_outpatient_form_popup_active').hide();
		   $('#add_doctor_tab').addClass("nav-tab-active");
		   $('#add_outpatient_tab').removeClass("nav-tab-active");
		   $('#adddoctor_label').show();
		   $('#outpatient_label').hide();
		  
		     });  
		
		     $('#add_doctor_tab').on("click",function(){
		     $('#add_doctor_tab').addClass("nav-tab-active");
		     $('#add_doctor_form_popup_active').show();
			 $('#add_outpatient_form_popup_active').hide();
			 $('#add_outpatient_tab').removeClass("nav-tab-active");
			 $('#adddoctor_label').show();
			 $('#outpatient_label').hide();
	      });
	   
	   $('#add_outpatient_tab').on("click",function(){
		 $('#add_doctor_tab').removeClass("nav-tab-active");
		 $('#add_doctor_form_popup_active').hide();
		  $('#add_outpatient_tab').addClass("nav-tab-active");
		  $('#add_outpatient_form_popup_active').show();
		  $('#adddoctor_label').hide();
			  $('#outpatient_label').show();
	   });
	    //add doctor popup ajax//
	    $('#doctor_form_popup_add_percription').on('submit', function(e) {
		e.preventDefault();
		var valid = $('#doctor_form_popup_add_percription').validationEngine('validate');
		if (valid == true) 
		{		
		var form = new FormData(this);
		var x = $("#doctor_cv")[0].files;
		var y = $("#education_certificate")[0].files;
		var z = $("#experience_cert")[0].files;
        form.append('doctor_cv',x);
        form.append('doctor_cv',y);
        form.append('doctor_cv',z);  
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
					$('#doctor_form_popup_add_percription').trigger("reset");
					$('#doctor').append(json_obj[0]);
					
					$('#add_doctor_tab').removeClass("nav-tab-active");
					 $('#add_doctor_form_popup_active').hide();
					 $('#add_outpatient_form_popup_active').show();
					  $('#add_outpatient_tab').addClass("nav-tab-active");
					  $('.upload_user_avatar_preview').html('<img alt="" src="<?php echo get_option( 'hmgt_doctor_thumb' ); ?>">');
					$('.hmgt_user_avatar_url').val('');
					
				} 
			},
			error: function(data){
			}
		})
		}
	}); 
	
	 //ADD OUPATIENT FUNCTION//	 
	$('#doctor_form_outpatient_popup_form_percription').on('submit', function(e) 
	{
		e.preventDefault();
		var valid = $('#doctor_form_outpatient_popup_form_percription').validationEngine('validate');
		if (valid == true) {
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
				if(data!="")
				{
					if(data == 2)
					{	
						alert('<?php esc_html_e('Sorry, only JPG,JPEG,PNG,GIF,DOC,PDF and ZIP files are allowed.','hospital_mgt'); ?>');
					}
					else
					{	
				
					   var json_obj = $.parseJSON(data);
						$('#doctor_form_outpatient_popup_form_percription').trigger("reset");
						$('#patient').append(json_obj[0]);
						$('.upload_user_avatar_preview').html('<img alt="" src="<?php echo get_option( 'hmgt_patient_thumb' ); ?>">');
						$('.hmgt_user_avatar_url').val('');
						
						$('.modal').modal('hide');
					}
				}  
			},
			error: function(data){
			}
		})
		}
	});  
	$("body").on("click", ".add_more_report", function()
	{
		$(".diagnosissnosis_div").append('<div class="form-group"><label class="col-sm-2 control-label" for="diagnosis"><?php esc_html_e('Diagnosis Report','hospital_mgt');?></label><div class="col-sm-3"><input type="file" class="dignosisreport form-control file" name="diagnosis[]"></div><div class="col-sm-2"><input type="button" value="<?php esc_html_e('Delete','hospital_mgt') ?>" onclick="deleteParentElement(this)" class="remove_cirtificate btn btn-default"></div></div>');
	});				
	$("body").on("click", ".remove_cirtificate", function()
	{
	    alert("<?php esc_html_e('Do you really want to delete this record ?','hospital_mgt');?>");
		$(this).parent().parent().remove();
	});	
	$(".symptoms_alert").on("click",function()
	{	
		checked = $(".multiselect_validation_symtoms .dropdown-menu input:checked").length;
		if(!checked)
		{
		  alert("<?php esc_html_e('Please select atleast one Symtoms','hospital_mgt');?>");
		  return false;
		}	
	});
	 //add bad ajax
	$('#bed_form').on('submit', function(e)
    {
		e.preventDefault();
		var form = $(this).serialize(); 
		var valid = $('#bed_form').validationEngine('validate');
		if (valid == true) {
		$.ajax({
			type:"POST",
			url: $(this).attr('action'),
			data:form,
			success: function(data)
			{
				 if(data!=""){ 
				   var json_obj = $.parseJSON(data);
					$('#bed_form').trigger("reset");
					//$('#bed_type_id').append(json_obj[0]);
					$('.modal').modal('hide');
				} 
			},
			error: function(data){
			}
			
		})
		}
	});
	$('#patient').select2();
	$("body").on("click", "#save_allow", function()
	{
            var patient_name = $("#patient");
            if (patient_name.val() == "") {
                //If the "Please Select" option is selected display error.
                alert("<?php esc_html_e('Please select a patient','hospital_mgt');?>");
                return false;
            }
            return true;
	});
} );

</script>	
<?php 	
if($active_tab == 'addoperation')
{
?>
    <div class="panel-body"><!-- PANEL BODY DIV START-->
		<form name="operation_form" action="" method="post" class="form-horizontal" id="operation_form">
			<?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
			<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
			<input type="hidden" name="operation_id" value="<?php if(isset($_REQUEST['ot_id']))echo esc_attr($_REQUEST['ot_id']);?>"  />
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="patient"><?php esc_html_e('Patient','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8 col-md-8 margin_bottom_5px">
						<select name="patient_id" id="patient" class="form-control ">
							<option value=""><?php esc_html_e('Select Patient','hospital_mgt');?></option>
							<?php 
							if($edit)
								$patient_id1 = $result->patient_id;
							elseif(isset($_REQUEST['patient_id']))
								$patient_id1 = $_REQUEST['patient_id'];
							else 
								$patient_id1 = "";
							$patients = MJ_hmgt_inpatient_list();
							if(!empty($patients))
							{
							foreach($patients as $patient)
							{
								echo '<option value="'.$patient['id'].'" '.selected($patient_id1,$patient['id']).'>'.$patient['patient_id'].' - '.$patient['first_name'].' '.$patient['last_name'].'</option>';
							}
							}
							?>
						</select>
					</div>
					
					<!--ADD OUT PATIENT POPUP BUTTON -->
					<div class="col-sm-2">				
						<button type="button" class="btn btn-default" data-bs-toggle="modal" data-bs-target="#myModal_add_outpatient"> <?php esc_html_e('Add Outpatient','hospital_mgt');?></button>	
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
								$doctor_list = $obj_ot->MJ_hmgt_get_doctor_by_oprationid($_REQUEST['ot_id']);
								
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
					<div class="col-sm-2">
					 	<button type="button" class="btn btn-default" data-bs-toggle="modal" data-bs-target="#myModal_add_bad"> <?php esc_html_e('Add Bed','hospital_mgt');?></button>				
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
				<input type="submit" id="save_allow" value="<?php if($edit){ esc_html_e('Save','hospital_mgt'); }else{ esc_html_e('Save','hospital_mgt');}?>" name="save_operation" class="btn btn-success"/>
			</div>
		</form>
     </div><!-- PANEL BODY DIV END-->
        
     <?php 
	} ?>
  <!----------ADD OUT PATIENT POPUP------------->
  <div class="modal fade" id="myModal_add_outpatient" tabindex="-1" aria-labelledby="myModal_add_outpatient" aria-hidden="true" role="dialog">
	 <div class="modal-dialog modal-lg"><!-- MODAL DIALOG DIV START -->
      <div class="modal-content"><!-- MODAL CONTENT DIV START -->
		<div class="modal-header float_left_width_100">
			<h3 class="modal-title float_left"><?php esc_html_e('Add Outpatient','hospital_mgt');?></h3>
			<h3 class="modal-title float_left">&nbsp;<?php esc_html_e('OR Add Doctor','hospital_mgt');?></h3>
			<button type="button" class="close btn-close float_right" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
		</div>
		<!----------MODAL BODY DIV------------->
		<div class="modal-body"><!-- MODAL BODY DIV START -->
			<h2 class="nav-tab-wrapper pos_top">
			<a class="nav-tab nav-tab-active" id="add_outpatient_tab">
			<?php echo '<span class="dashicons dashicons-plus-alt"></span> '.esc_html__('Add Outpatient', 'hospital_mgt'); ?></a>
			<a class="nav-tab" id="add_doctor_tab" >
			<?php echo '<span class="dashicons dashicons-plus-alt"></span> '.esc_html__('Add Doctor', 'hospital_mgt'); ?></a>  
		</h2>
		  <!----------OUTPATIENT FORM ACTIVE DIV------------->
			<div id="add_outpatient_form_popup_active">
			    <?php 
				    $role='patient';
				    $patient_type='outpatient';
				    $newpatient=MJ_hmgt_get_lastpatient_id($role);
			    ?>
				 <!----------PANEL BODY DIV START------------->
				<div class="panel-body">	
					<form name="out_patient_form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" class="form-horizontal" id="doctor_form_outpatient_popup_form_percription" enctype="multipart/form-data">
					<input type="hidden" name="action" value="MJ_hmgt_save_outpatient_popup_form">		
					<input type="hidden" name="role" value="<?php echo esc_attr($role);?>"  />
					<input type="hidden" name="patient_type" value="<?php echo esc_attr($patient_type);?>"  />
					<div class="header">	
							<h3 class="first_hed"><?php esc_html_e('Personal Information','hospital_mgt');?></h3>
							<hr>
					</div>
					
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="roll_id"><?php esc_html_e('Patient Id','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="patient_id" class="form-control validate[required]" type="text" 
								value="<?php  echo esc_attr($newpatient);?>" readonly name="patient_id">
							</div>
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="first_name"><?php esc_html_e('First Name','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="first_name" class="form-control validate[required,custom[onlyLetter_specialcharacter]] text-input" maxlength="50" type="text" value="" name="first_name">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="middle_name"><?php esc_html_e('Middle Name','hospital_mgt');?></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="middle_name" class="form-control validate[custom[onlyLetter_specialcharacter]]" type="text" maxlength="50" value="" name="middle_name">
							</div>
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="last_name"><?php esc_html_e('Last Name','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="last_name" class="form-control validate[required,custom[onlyLetter_specialcharacter]] text-input" maxlength="50" type="text"  value="" name="last_name">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="birth_date"><?php esc_html_e('Date of birth','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input class="form-control validate[required] birth_date " type="text"   name="birth_date" 
								value="" readonly>
							</div>
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="blood_group"><?php esc_html_e('Blood Group','hospital_mgt');?></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								
								<select id="blood_group" class="form-control" name="blood_group">
								<option value=""><?php esc_html_e('Select Blood Group','hospital_mgt');?></option>
								<?php
								$userblood=0;
								foreach(MJ_hmgt_blood_group() as $blood){ ?>
										<option value="<?php echo $blood;?>" <?php selected($userblood,$blood);  ?>><?php echo esc_html($blood); ?> </option>
								<?php } ?>
							</select>
							</div>
						</div>
					</div>		
				
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="gender"><?php esc_html_e('Gender','hospital_mgt');?><span class="require-field">*</span></label>
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
					</div>
					<div class="header">
						<h3><?php esc_html_e('HomeTown Address Information','hospital_mgt');?></h3>
						<hr>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="address"><?php esc_html_e('Home Town Address','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="address" class="form-control validate[required,custom[address_description_validation]]" type="text" maxlength="150" name="address" 
								value="">
							</div>
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="city_name"><?php esc_html_e('City','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="city_name" class="form-control validate[required,custom[city_state_country_validation]]" type="text" maxlength="50"  name="city_name" 
								value="">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="state_name"><?php esc_html_e('State','hospital_mgt');?></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="state_name" class="form-control validate[custom[city_state_country_validation]]" type="text" maxlength="50" name="state_name" 
								value="">
							</div>
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="state_name"><?php esc_html_e('Country','hospital_mgt');?></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="country_name" class="form-control validate[custom[city_state_country_validation]]" type="text" maxlength="50" name="country_name" 
								value="">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="zip_code"><?php esc_html_e('Zip Code','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="zip_code" class="form-control  validate[required,custom[onlyLetterNumber]]" type="text" maxlength="15"  name="zip_code" 
								value="">
							</div>
						</div>
					</div>
					<div class="header">
						<h3><?php esc_html_e('Contact Information','hospital_mgt');?></h3>
						<hr>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="mobile"><?php esc_html_e('Mobile Number','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 margin_bottom_5px">
							<input type="text" value="<?php if(isset($_POST['phonecode'])){ echo $_POST['phonecode']; }else{ ?>+<?php echo MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }?>"  class="form-control  validate[required] onlynumber_and_plussign" name="phonecode" maxlength="5">
							</div>
							<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 has-feedback">
								<input id="mobile" class="form-control validate[required,custom[phone_number]] text-input" minlength="6" maxlength="15" type="text" value="" name="mobile">
							</div>
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="phone"><?php esc_html_e('Phone','hospital_mgt');?></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="phone" class="form-control validate[custom[phone_number]] text-input" minlength="6" maxlength="15" type="text" value="" name="phone">
							</div>
						</div>
					</div>
					<div class="header">
						<h3><?php esc_html_e('Login Information','hospital_mgt');?></h3>
						<hr>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="email"><?php esc_html_e('Email','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="email" class="form-control validate[required,custom[email]] text-input" maxlength="100" type="text"  name="email" 
								value="">
							</div>
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="username"><?php esc_html_e('User Name','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="username" class="form-control validate[required,custom[username_validation]]" type="text" maxlength="30" name="username" 
								value="">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="password"><?php esc_html_e('Password','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="password" class="form-control validate[required,minSize[8]]" type="password" maxlength="12"  name="password" value="">
							</div>
						</div>
					</div>
					<div class="header">
						<h3><?php esc_html_e('Other Information','hospital_mgt');?></h3>
						<hr>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="doctor"><?php esc_html_e('Assign Doctor','hospital_mgt');?></label>
							<div class="col-sm-3 margin_bottom_5px">
								
								<select name="doctor" id="doctor" class="form-control">
								
								<option ><?php esc_html_e('select Doctor','hospital_mgt');?></option>
								<?php
								 $doctorid=0;
								$get_doctor = array('role' => 'doctor');
									$doctordata=get_users($get_doctor);
									 if(!empty($doctordata))
									 {
										foreach($doctordata as $retrieved_data){?>
										<option value="<?php echo esc_attr($retrieved_data->ID); ?>" <?php selected($doctorid,$retrieved_data->ID);?>><?php echo esc_html($retrieved_data->display_name);?> - <?php echo MJ_hmgt_doctor_specialization_title($retrieved_data->ID); ?></option>
										<?php }
									 }?>
									 
								</select>
							</div>
							<!-- Adddoctor Button -->
							 <div class="col-sm-2">						
								<a href="javascript:void(0);" class="btn btn-default"  id="add_precription_doctor" data-toggle="modal" data-target="#myModal_add_doctor"> <?php esc_html_e('Add Doctor','hospital_mgt');?></a>						
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="symptoms"><?php esc_html_e('Symptoms','hospital_mgt');?><span class="require-field">*</span></label>
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
								<div class="col-sm-3"><button id="addremove" model="symptoms"><?php esc_html_e('Add Or Remove','hospital_mgt');?></button></div>
							</div>
					</div>			
				
					<div class="diagnosissnosis_div">
						<div class="form-group">
							<div class="mb-3 row">	
								<label class="col-sm-2 control-label form-label" for="diagnosis"><?php esc_html_e('Diagnosis Report','hospital_mgt');?></label>
								<div class="col-sm-3">
									<input type="file" class="form-control file dignosisreport" name="diagnosis[]">
								</div>
							</div>
						</div>	
					</div>
					<div class="form-group">
						<div class="mb-3 row">				
							<div class="col-sm-2">
							</div>
							<div class="col-sm-2">
								<input type="button" value="<?php esc_html_e('Add More Report','hospital_mgt') ?>" name="add_more_report" class="add_more_report btn btn-default">
							</div>
						</div>
					</div>
					
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-sm-2 control-label form-label" for="photo"><?php esc_html_e('Image','hospital_mgt');?></label>
						<div class="col-sm-3 margin_bottom_5px">
							<input type="text"  class="form-control hmgt_user_avatar_url" name="hmgt_user_avatar" readonly 
							 />
						</div>	
						<div class="col-sm-4">
								 <input  type="button" class="button upload_user_avatar_button" value="<?php esc_html_e( 'Upload image', 'hospital_mgt' ); ?>" />
								 <br>
								 <span class="description"><?php esc_html_e('Upload only JPG, JPEG, PNG & GIF image', 'hospital_mgt' ); ?></span>
						</div>
						<div class="clearfix"></div>
						
						<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
								 <div class="upload_user_avatar_preview" >									 
									<img class="image_preview_css" alt="" src="<?php echo esc_url(get_option( 'hmgt_patient_thumb' )); ?>">
								</div>
					 	</div>
					 </div>
					</div>
					<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
						
						<input type="submit" value="<?php esc_html_e('Save Patient','hospital_mgt');?>" name="save_outpatient" class="btn btn-success symptoms_alert"/>
					</div>
                    </form>
		        </div>
				<!------ END POPUP DIV------------->
            </div>
			<!------ END OUTPATIENT FORM ACTIVE DIV------------->

			 <!-- start add doctor popup foem active --><!---rinkal change prescription-->
			<div id="add_doctor_form_popup_active">
				<div class="modal-body"><!----MODEL BODY div START ----->				
					<form name="doctor_form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" class="form-horizontal" id="doctor_form_popup_add_percription" class="doctor_form" enctype="multipart/form-data">
			   
					<input type="hidden" name="action" value="MJ_hmgt_save_doctor_popup_form">
					<input type="hidden" name="role" value="doctor"  />
					<div class="header">	
						<h3 class="first_hed"><?php esc_html_e('Personal Information','hospital_mgt') ?></h3>
						<hr>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="first_name"><?php esc_html_e('First Name','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="first_name" class="form-control validate[required,custom[onlyLetter_specialcharacter]] text-input" maxlength="50" type="text" value="" name="first_name">
							</div>
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="middle_name"><?php esc_html_e('Middle Name','hospital_mgt');?></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="middle_name" class="form-control validate[custom[onlyLetter_specialcharacter]] text-input" maxlength="50" type="text"  value="" name="middle_name">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="last_name"><?php esc_html_e('Last Name','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="last_name" class="form-control validate[required,custom[onlyLetter_specialcharacter]] text-input" maxlength="50" type="text"  value="" name="last_name">
							</div>
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="birth_date"><?php esc_html_e('Date of birth','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input  class="form-control validate[required] birth_date"  type="text"   name="birth_date" 
								value="" readonly>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="gender"><?php esc_html_e('Gender','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-sm-8">
							
								<label class="radio-inline">
								 <input type="radio" value="male" class="tog" name="gender"  <?php  checked( 'male', $genderval);  ?>/><?php esc_html_e('Male','hospital_mgt');?>
								</label>
								<label class="radio-inline">
								  <input type="radio" value="female" class="tog" name="gender"  <?php  checked( 'female', $genderval);  ?>/><?php esc_html_e('Female','hospital_mgt');?> 
								</label>
							</div>
						</div>
					</div>
					
				
					<div class="header">
						<h3><?php esc_html_e('Office Address Information','hospital_mgt') ?></h3>
						<hr>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="address"><?php esc_html_e('Office Address','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="" class="form-control validate[required,custom[address_description_validation]]" maxlength="150" type="text"  name="office_address" 
								value="">
							</div>
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="city_name"><?php esc_html_e('City','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="" class="form-control validate[required,custom[city_state_country_validation]]" maxlength="50" type="text"  name="city_name" 
								value="">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="state_name"><?php esc_html_e('State','hospital_mgt');?></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="" class="form-control validate[custom[city_state_country_validation]]" type="text" maxlength="50" name="state_name" 
								value="">
							</div>
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="country_name"><?php esc_html_e('Country','hospital_mgt');?></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="" class="form-control validate[custom[city_state_country_validation]]" maxlength="50" type="text"  name="country_name" 
								value="">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="zip_code"><?php esc_html_e('Zip Code','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="" class="form-control  validate[required,custom[onlyLetterNumber]]" type="text" maxlength="15" name="zip_code" 
								value="">
							</div>
						</div>
					</div>
					<div class="header">
						<h3><?php esc_html_e('HomeTown Address Information','hospital_mgt') ?></h3>
						<hr>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="address"><?php esc_html_e('Home Town Address','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="address" class="form-control validate[required,custom[address_description_validation]]" type="text" maxlength="150" name="address" 
								value="">
							</div>
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="home_city_name"><?php esc_html_e('City','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="city_name" class="form-control validate[required,custom[city_state_country_validation]]" maxlength="50" type="text"  name="home_city_name" 
								value="">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="home_state_name"><?php esc_html_e('State','hospital_mgt');?></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="home_state_name" class="form-control validate[custom[city_state_country_validation]]" maxlength="50" type="text"  name="home_state_name" 
								value="">
							</div>
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="country_name"><?php esc_html_e('Country','hospital_mgt');?></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="country_name" class="form-control validate[custom[city_state_country_validation]]" maxlength="50" type="text"  name="country_name" 
								value="">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="zip_code"><?php esc_html_e('Zip Code','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="zip_code" class="form-control  validate[required,custom[onlyLetterNumber]]" type="text" maxlength="15"  name="home_zip_code" 
								value="<?php if($edit){ echo $user_info->home_zip_code;}elseif(isset($_POST['home_zip_code'])) echo $_POST['home_zip_code'];?>">
							</div>
						</div>
					</div>
					<div class="header">
						<h3><?php esc_html_e('Education Information','hospital_mgt') ?></h3>
						<hr>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="birth_date"><?php esc_html_e('Degree','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="doc_degree" class="form-control validate[required,custom[popup_category_validation]]" maxlength="500" type="text"  name="doc_degree" 
								value="">
							</div>
						</div>
					</div>	
					<div class="header">
						<h3><?php esc_html_e('Contact Information','hospital_mgt') ?></h3>
						<hr>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="mobile"><?php esc_html_e('Mobile Number','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-1 col-md-1 col-sm-2 col-xs-12 margin_bottom_5px">					
								<input type="text" value="<?php if(isset($_POST['phonecode'])){ echo $_POST['phonecode']; }else{ ?>+<?php echo MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }?>"  class="form-control  validate[required] onlynumber_and_plussign" name="phonecode" maxlength="5">
							</div>
							<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 has-feedback">
								<input id="mobile" class="form-control validate[required,custom[phone_number]] text-input" minlength="6" maxlength="15" type="text" value="" name="mobile">
							</div>
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="phone"><?php esc_html_e('Phone','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="phone" class="form-control validate[custom[phone_number]] text-input" minlength="" maxlength="15" type="text" value="" name="phone">
							</div>
						</div>
					</div>
					<div class="header">
						<h3><?php esc_html_e('Login Information','hospital_mgt');?></h3>
						<hr>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="email"><?php esc_html_e('Email','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="email" class="form-control validate[required,custom[email]] text-input" maxlength="100" type="text"  name="email" 
								value="">
							</div>
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="username"><?php esc_html_e('User Name','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="username" class="form-control validate[required,custom[username_validation]]" type="text" maxlength="30"  name="username" 
								value="">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="password"><?php esc_html_e('Password','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="password" class="form-control validate[required,minSize[8]]" type="password"    maxlength="12" name="password" value="">
							</div>
						</div>
					</div>
					<div class="header">
						<h3><?php esc_html_e('Other Information','hospital_mgt');?></h3>
						<hr>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="department"><?php esc_html_e('Department','hospital_mgt');?></label>
							<div class="col-sm-3 margin_bottom_5px">
								<select name="department" class="form-control" id="department">
								<option><?php esc_html_e('select Department','hospital_mgt');?></option>
								<?php 
								$departmentid=0;
								$department_array = $user_object->MJ_hmgt_get_staff_department();
								 if(!empty($department_array))
								 {
									foreach ($department_array as $retrieved_data){?>
										<option value="<?php echo $retrieved_data->ID; ?>" <?php selected($departmentid,$retrieved_data->ID);?>><?php echo $retrieved_data->post_title;?></option>
									<?php }
								 }
								?>
								</select>
							</div>
							<div class="col-sm-2"><button id="addremove" model="department"><?php esc_html_e('Add Or Remove','hospital_mgt');?></button></div>
						</div>
					</div>	
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="birth_date"><?php esc_html_e('Specialization','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-sm-3 margin_bottom_5px">
								<select class="form-control validate[required]" 
								id="specialization" name="specialization" >
									<option value=""><?php esc_html_e('Select Specialization','hospital_mgt');?></option>
									<?php 
									$specializeid=0;
									$specialize_array = $user_object->MJ_hmgt_get_doctor_specilize();
									 if(!empty($specialize_array))
									 {
										foreach ($specialize_array as $retrieved_data){?>
											<option value="<?php echo esc_attr($retrieved_data->ID); ?>" <?php selected($specializeid,$retrieved_data->ID);?>><?php echo esc_html($retrieved_data->post_title);?></option>
										<?php }
									 }?>
									</select>
							</div>
							<div class="col-sm-2"><button id="addremove" model="specialization"><?php esc_html_e('Add Or Remove','hospital_mgt');?></button></div>
						</div>
					</div>	
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="visiting_fees"><?php esc_html_e('Visiting Charge','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' )); ?>)</label>
							<div class="col-sm-3">
								<input id="doc_degree" class="form-control" type="number" min="0" onKeyPress="if(this.value.length==8) return false;"name="visiting_fees" step="0.01" value="">
							</div>
							<div class="col-sm-2 padding_left_0 add_bed_1">
								<?php esc_html_e('/ Per Session','hospital_mgt');?>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for=""><?php esc_html_e('Visiting Charge Tax','hospital_mgt');?></label>
							<div class="col-sm-3">
								<select  class="form-control tax_charge"  name="visiting_fees_tax[]" multiple="multiple">		<?php										
									$obj_invoice= new MJ_hmgt_invoice();
									$hmgt_taxs=$obj_invoice->MJ_hmgt_get_all_tax_data();	
									
									if(!empty($hmgt_taxs))
									{
										foreach($hmgt_taxs as $entry)
										{							
											?>
											<option value="<?php echo esc_attr($entry->tax_id); ?>"><?php echo esc_html($entry->tax_title);?>-<?php echo esc_html($entry->tax_value);?></option>
										<?php 
										}
									}
									?>
								</select>		
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for=""><?php esc_html_e('Consulting Charge','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' )); ?>)</label>
							<div class="col-sm-3">
								<input id="" class="form-control" type="number" min="0" onKeyPress="if(this.value.length==8) return false;"  name="consulting_fees" step="0.01"
								value="">
							</div>
							<div class="col-sm-2 padding_left_0 add_bed_1">
								<?php esc_html_e('/ Per Session','hospital_mgt');?>
							</div>
						</div>
					</div>	
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="visiting_fees"><?php esc_html_e('Consulting Charge Tax','hospital_mgt');?></label>
							<div class="col-sm-3">
								<select  class="form-control tax_charge" name="consulting_fees_tax[]" multiple="multiple">		<?php
									$obj_invoice= new MJ_hmgt_invoice();
									$hmgt_taxs=$obj_invoice->MJ_hmgt_get_all_tax_data();	
									
									if(!empty($hmgt_taxs))
									{
										foreach($hmgt_taxs as $entry)
										{							
											?>
											<option value="<?php echo esc_attr($entry->tax_id); ?>"><?php echo esc_html($entry->tax_title);?>-<?php echo esc_html($entry->tax_value);?></option>
											<?php 
										}
									}
									?>
								</select>		
							</div>
						</div>
					</div>
				
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="photo"><?php esc_html_e('Image','hospital_mgt');?></label>
							<div class="col-sm-3 margin_bottom_5px">
								<input type="text"  class="form-control hmgt_user_avatar_url" name="hmgt_user_avatar" readonly 
								 />
							</div>	
							<div class="col-sm-4">
								 <input  type="button" class="button upload_user_avatar_button" value="<?php esc_html_e( 'Upload image', 'hospital_mgt' ); ?>" />
								 <br>
								 <span class="description"><?php esc_html_e('Upload only JPG, JPEG, PNG & GIF image', 'hospital_mgt' ); ?></span>
							</div>
							<div class="clearfix"></div>
							
							<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
								<div class="upload_user_avatar_preview" >	                     
									<img class="image_preview_css" alt="" src="<?php echo esc_url(get_option( 'hmgt_doctor_thumb' )); ?>">
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="document"><?php esc_html_e('Curriculum Vitae','hospital_mgt');?></label>
							<div class="col-sm-3">
								<input type="file" class="form-control file" id="doctor_cv" name="doctor_cv" >
								<input type="hidden" name="hidden_cv" value="">
								<p class="help-block"><?php esc_html_e('Upload document in PDF','hospital_mgt');?></p> 
							</div>
							<div class="col-sm-2">
								<?php if(isset($user_info->doctor_cv) && $user_info->doctor_cv!=""){?>
								<a href="<?php echo content_url().'/uploads/hospital_assets/'.$user_info->doctor_cv;?>" class="btn btn-default"><i class="fa fa-download"></i> <?php esc_html_e('Curriculum Vitae','hospital_mgt');?></a>
								<?php } ?>
								 
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="document"><?php esc_html_e('Education Certificate','hospital_mgt');?></label>
							<div class="col-sm-3">
								<input type="file" class="form-control file" name="education_certificate" id="education_certificate">
								<input type="hidden" name="hidden_education_certificate" value="">
								<p class="help-block"><?php esc_html_e('Upload document in PDF','hospital_mgt');?></p> 
							</div>
							<div class="col-sm-2">
								<?php if(isset($user_info->edu_certificate) && $user_info->edu_certificate!=""){?>
								<a href="<?php echo content_url().'/uploads/hospital_assets/'.$user_info->edu_certificate;?>" class="btn btn-default"><i class="fa fa-download"></i> <?php esc_html_e('Education Certificate','hospital_mgt');?></a>
								<?php } ?>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="document"><?php esc_html_e('Experience Certificate','hospital_mgt');?></label>
							<div class="col-sm-3">
								<input type="file" class="form-control file" name="experience_cert" id="experience_cert" >
								<input type="hidden" name="hidden_exp_certificate" value="">
								<p class="help-block"><?php esc_html_e('Upload document in PDF','hospital_mgt');?></p> 
							</div>
							<div class="col-sm-2">
								<?php if(isset($user_info->exp_certificate) && $user_info->exp_certificate!=""){?>
								<a href="<?php echo content_url().'/uploads/hospital_assets/'.$user_info->exp_certificate;?>" class="btn btn-default"><i class="fa fa-download"></i> <?php esc_html_e('Experience Certificate','hospital_mgt');?></a>
								<?php } ?>
								 
							</div>
						</div>
					</div>
					<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
						<input type="submit" value="<?php esc_html_e('Save Doctor','hospital_mgt');?>" name="save_doctor" class="btn btn-success"/>
					</div>
				</form>	
				</div><!-- PAGE BODY DIV END-->			 
			</div>
			<!-- end doctor popup DIV -->
        </div>
		 <!---- -MODAL BODY DIV END ------>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-bs-dismiss="modal"><?php esc_html_e('Close','hospital_mgt'); ?></button>
			</div>
        </div><!-- MODAL CONTENT DIV END -->
    </div><!-- MODAL DIALOG DIV END-->
</div>	
<!-----   ADD BAD POPUP FORM --->
<div class="modal fade" id="myModal_add_bad" tabindex="-1" aria-labelledby="myModal_add_bad" aria-hidden="true" role="dialog">
    <div class="modal-dialog modal-lg"><!-- MODAL DIALOG DIV START-->
       <div class="modal-content"><!-- MODAL CONTENT DIV START-->
       		<div class="modal-header float_left_width_100">
				<h3 class="modal-title float_left"><?php esc_html_e('Add Bed','hospital_mgt');?></h3>
				<button type="button" class="close btn-close float_right" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body"><!-- MODAL BODY DIV START-->
				<form name="bed_form"action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" class="form-horizontal" id="bed_form">
					<input type="hidden" name="action" value="MJ_hmgt_asignbad_addbad_popup_form">
					<input type="hidden" name="bad_id" value="" />
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="bed_type_id"><?php esc_html_e('Select Bed Category','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-sm-8 margin_bottom_5px">
								<select name="bed_type_id" class="form-control validate[required] max_width_100" id="bedtype">
								<option value = ""><?php esc_html_e('Select Bed Category','hospital_mgt');?></option>
								<?php 
								$bed_type1=0;
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
							<div class="col-sm-2"><button id="addremove" model="bedtype"><?php esc_html_e('Add Or Remove','hospital_mgt');?></button></div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="bed_number"><?php esc_html_e('Bed Number','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-sm-8">						
								<input id="bed_number" class="form-control validate[required,custom[popup_category_validation]] text-input" maxlength="10" type="text"  value="" name="bed_number">
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="bed_charges"><?php esc_html_e('Charges','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' )); ?>)<span class="require-field">*</span></label>
							<div class="col-sm-8">
								<input id="bed_charges" class="form-control validate[required] " min="0" type="number" onKeyPress="if(this.value.length==10) return false;"  step="0.01" 
								value="" name="bed_charges">						
							</div>
							<div class="col-sm-2 padding_left_0 add_bed_1">
								<?php esc_html_e('/ Per Day','hospital_mgt');?>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="visiting_fees"><?php esc_html_e('Tax','hospital_mgt');?></label>
							<div class="col-sm-2">
								<select  class="form-control tax_charge max_width_100" id="" name="tax[]" multiple="multiple">					
									<?php
									$obj_invoice= new MJ_hmgt_invoice();
									$hmgt_taxs=$obj_invoice->MJ_hmgt_get_all_tax_data();	
									
									if(!empty($hmgt_taxs))
									{
										foreach($hmgt_taxs as $entry)
										{									
											?>
											<option value="<?php echo esc_attr($entry->tax_id); ?>"><?php echo esc_html($entry->tax_title);?>-<?php echo esc_html($entry->tax_value);?></option>
										<?php 
										}
									}
									?>
								</select>		
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="bed_location"><?php esc_html_e('Location','hospital_mgt');?></label>
							<div class="col-sm-8">
								<textarea id="bed_location" class="form-control"  name="bed_location"></textarea>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="bed_description"><?php esc_html_e('Description','hospital_mgt');?></label>
							<div class="col-sm-8">
								<textarea id="bed_description" class="form-control"  name="bed_description"></textarea>
								
							</div>
						</div>
					</div>
					<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
						<input type="submit" value="<?php  esc_html_e('Save','hospital_mgt');?>" name="save_bed" class="btn btn-success"/>
					</div>
				</form>			
			</div><!-- MODAL BODY DIV END-->
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-bs-dismiss="modal"><?php esc_html_e('Close','hospital_mgt'); ?></button>
			</div>
		</div><!-- MODAL CONTENT DIV END-->
	</div><!-- MODAL DIALOG DIV END-->
</div><!-- MODAL DIV END-->