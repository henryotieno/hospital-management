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
			$('#bed_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
			$('#nurse_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#patient_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			$('#bed_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			$('#nurse_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>
      var start = new Date();
		var end = new Date(new Date().setYear(start.getFullYear()+1));
		 $.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
		$('.allotmentdate').datepicker({
			startDate : start,
			autoclose: true
		}).on('changeDate', function()
		{
			//$(".allotment_date_dischargdate").datepicker("update",'2021-09-29');
			$('.allotment_date_dischargdate').datepicker('setStartDate', $(this).val());
		}); 
		$.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
		$('.allotment_date_dischargdate').datepicker({
			startDate : start,
			autoclose: true
		}).on('changeDate', function(){
			//$('.allotmentdate').datepicker('setEndDate', new Date($(this).val()));
			$('.allotmentdate').datepicker('setEndDate', $(this).val());
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
	 //add bad ajax
	  $('#bed_form').on('submit', function(e) {
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
					$('#bed_type_id').append(json_obj[0]);
					$('.modal').modal('hide');
				} 
			},
			error: function(data){
			}
		})
		}
	});
	$.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
      $('#birth_date').datepicker({
     endDate: '+0d',
        autoclose: true,
   }); 
	   //add nurse  Ajax
	  $('#nurse_form').on('submit', function(e) {
		e.preventDefault();
		
		var form = $(this).serialize(); 
		var valid = $('#nurse_form').validationEngine('validate');
		var nurse = $('#nurse').multiselect(); 
		if (valid == true) {
		$.ajax({
			type:"POST",
			url: $(this).attr('action'),
			data:form,
			success: function(data)
			{
				 if(data!=""){ 
				   var json_obj = $.parseJSON(data);
					$('#nurse_form').trigger("reset");
					$('#nurse').append(json_obj[0]);
					nurse.multiselect('rebuild'); 
					$('.upload_user_avatar_preview').html('<img alt="" src="<?php echo get_option( 'hmgt_nurse_thumb' ); ?>">');
					$('.hmgt_user_avatar_url').val('');
					$('.modal').modal('hide');
					
				} 
			},
			error: function(data){
			}
		})
		}
	});
	$('#patient_id').select2();
	
	$("body").on("click", "#save_allow", function()
	{
            var patient_name = $("#patient_id");
            if (patient_name.val() == "") {
                //If the "Please Select" option is selected display error.
                alert("<?php esc_html_e('Please select a patient','hospital_mgt');?>");
                return false;
            }
            return true;
	});
	
});
</script>
 <?php 	
if($active_tab == 'bedassign')
{
	MJ_hmgt_browser_javascript_check();
	$edit=0;
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
	{
		$edit=1;
		$result = $obj_bed->MJ_hmgt_get_single_bedallotment($_REQUEST['allotment_id']);
	}?>
	<div class="panel-body"><!-- PANEL BODY DIV START-->
		<form name="patient_form" action="" method="post" class="form-horizontal" id="patient_form">
			 <?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
			<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
			<input type="hidden" name="allotment_id" value="<?php if(isset($_REQUEST['allotment_id'])) echo esc_attr($_REQUEST['allotment_id']);?>"  />
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="patient_id"><?php esc_html_e('Patient','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<select name="patient_id" id="patient_id" class="form-control max_width_100">
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
								$patient_id1 ="";
							}
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
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="patient_status"><?php esc_html_e('Patient Status','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8" >
						<?php if($edit){ $patient=MJ_get_inpatient_status($patient_id1);
						$patient_status="";
							if(!empty($patient)){
						$patient_status=$patient->patient_status; } }elseif(isset($_POST['patient_status'])){$patient_status=$_POST['patient_status'];}else{$patient_status='';} 
						?>
						
						<select name="patient_status" class="form-control validate[required] max_width_100" >
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
					<label class="col-sm-2 control-label form-label" for="bed_type_id"><?php esc_html_e('Select Bed Type','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
					<?php if(isset($_REQUEST['bed_type_id']))
							$bed_type1 = $_REQUEST['bed_type_id'];
						elseif($edit)
							$bed_type1 = $result->bed_type_id;
						else 
							$bed_type1 = "";
						?>
						<select name="bed_type_id" class="form-control validate[required] max_width_100 bed_type_assign" id="bed_type_id">
						<option value = ""><?php esc_html_e('Bed Type','hospital_mgt');?></option>
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
					<div class="col-sm-8 margin_bottom_5px">
						<select name="bed_number" class="form-control validate[required] max_width_100" id="bednumber">
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
					<!--- ADD bad  in asign bad  -->
					 <div class="col-sm-2">
					 	<button type="button" class="btn btn-default" data-bs-toggle="modal" data-bs-target="#myModal_add_bad"> <?php esc_html_e('Add Bed','hospital_mgt');?></button>				
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
						<p class="bg-info bed_location"><strong><?php esc_html_e('Bed Location : ' ,'hospital_mgt')?></strong><?php print esc_html($beddata->bed_location); ?></p>
					<?php
					}
					?>
					</div>
					<div class="col-sm-2 bed_location"></div>
				</div>
			</div>
			<div id=""></div>
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="allotment_date"><?php esc_html_e('Allotment Date','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input class="form-control validate[required] allotmentdate" type="text"   value="<?php if($edit){ echo date(MJ_hmgt_date_formate(),strtotime($result->allotment_date));}elseif(isset($_POST['allotment_date'])) echo $_POST['allotment_date'];?>" name="allotment_date">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="discharge_time"><?php esc_html_e('Expected Discharge Date','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input class="form-control validate[required] allotment_date_dischargdate" type="text"   value="<?php if($edit){ echo date(MJ_hmgt_date_formate(),strtotime(esc_attr($result->discharge_time)));}elseif(isset($_POST['discharge_time'])) echo esc_attr($_POST['discharge_time']);?>" name="discharge_time">
					</div>
				</div>
			</div>
			<?php wp_nonce_field( 'bedallotment_nonce' ); ?>
			
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="doctor"><?php esc_html_e('Select Nurse','hospital_mgt');?></label>
					<div class="col-sm-8 margin_bottom_5px">
					<?php $allnurse = MJ_hmgt_getuser_by_user_role('nurse');
							$nurse_data = array();
							if($edit)
							{
								$nurse_list = $obj_bed->MJ_hmgt_get_nurse_by_bedallotment_id($_REQUEST['allotment_id']);
								
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
						<select name="nurse[]" class="form-control max_width_100" multiple="multiple" id="nurse">
						<?php
							if(!empty($allnurse))
							{
								foreach($allnurse as $nurse)
								{
									$selected = "";
									if(in_array($nurse['id'],$nurse_data))
										$selected = "selected";
									echo '<option value='.$nurse['id'].' '.$selected.'>'.$nurse['first_name'].' '.$nurse['last_name'].'</option>';
								}
							}
							?>
						</select>
					</div>
					
					<!--- add nurce in asign bad   -->
					<div class="col-sm-2">	
						<button type="button" class="btn btn-default" data-bs-toggle="modal" data-bs-target="#myModal_add_nurce"> <?php esc_html_e('Add Nurse','hospital_mgt');?></button>			
					</div>
				</div>
			</div>
			<div class="form-group margin_bottom_5px">
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
		</form>
	</div> <!-- PANEL BODY DIV END-->
<?php 
}
?>
<!-----   ADD BAD POPUP FORM --->
<!-- MODAL DIV START-->
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
 <!-- ADD NURSE POPUP FORM------------->
<!-- MODAL DIV START-->
	<div class="modal fade" id="myModal_add_nurce" tabindex="-1" aria-labelledby="myModal_add_nurce" aria-hidden="true" role="dialog">
	<div class="modal-dialog modal-lg"><!-- MODAL DIALOG DIV START-->
		<div class="modal-content"><!-- MODAL CONTENT DIV START-->
			<div class="modal-header float_left_width_100">
					<h3 class="modal-title float_left"><?php esc_html_e('Add Nurse','hospital_mgt');?></h3>
					<button type="button" class="close btn-close float_right" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
			<div class="modal-body"><!-- MODAL BODY DIV START-->
			   <form name="nurse_form" action="<?php echo admin_url('admin-ajax.php'); ?>" method="post" class="form-horizontal" id="nurse_form" enctype="multipart/form-data">	
					<input type="hidden" name="action" value="MJ_hmgt_save_nurce_popup_form">
					<input type="hidden" name="role" value="nurse"/>
					<div class="header">	
						<h3 class="first_hed"><?php esc_html_e('Personal Information','hospital_mgt');?></h3>
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
								<input id="middle_name" class="form-control validate[custom[onlyLetter_specialcharacter]]" type="text" maxlength="50" value="" name="middle_name">
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
								<input id="birth_date" class="form-control validate[required] allotment_date" type="text"  name="birth_date" 
								value="" readonly>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="gender"><?php esc_html_e('Gender','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
							<?php $genderval = "male"; ?>
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
								<input id="address" class="form-control validate[required,custom[address_description_validation]]" maxlength="150" type="text"  name="address" 
								value="">
							</div>
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="city_name"><?php esc_html_e('City','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="city_name" class="form-control validate[required,custom[city_state_country_validation]]" maxlength="50" type="text"  name="city_name" 
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
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="country_name"><?php esc_html_e('Country','hospital_mgt');?></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="country_name" class="form-control validate[custom[city_state_country_validation]]" type="text" maxlength="50"  name="country_name" 
								value="">
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="zip_code"><?php esc_html_e('Zip Code','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
								<input id="zip_code" class="form-control  validate[required,custom[onlyLetterNumber]]" type="text" maxlength="15" name="zip_code" 
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
								<input id="password" class="form-control validate[required,minSize[8]]" type="password"  maxlength="12" name="password" value="">
							</div>
						</div>
					</div>
					<div class="header">
						<h3><?php esc_html_e('Other Information','hospital_mgt');?></h3>
						<hr>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="department"><?php esc_html_e('Department','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-sm-3 margin_bottom_5px">
							
								<select name="department" class="form-control validate[required] max_width_100" id="department">
								<option value=""><?php esc_html_e('select Department','hospital_mgt');?></option>
								<?php 
								$user_object=new MJ_hmgt_user();
								   $departmentid=0;
									$department_array = $user_object->MJ_hmgt_get_staff_department();
									 if(!empty($department_array))
									 {
										foreach ($department_array as $retrieved_data){?>
											<option value="<?php echo esc_attr($retrieved_data->ID); ?>" <?php selected($departmentid,$retrieved_data->ID);?>><?php echo esc_html($retrieved_data->post_title);?></option>
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
							<label class="col-sm-2 control-label form-label" for="chage"><?php esc_html_e('Charge','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' )); ?>)<span class="require-field">*</span></label>
							<div class="col-sm-3">
								<input id="charge" class="form-control validate[required] text-input" min="0" step="0.01" type="number" onKeyPress="if(this.value.length==8) return false;" name="charge" 
								value="">
							</div>
							<div class="col-sm-2 padding_left_0 add_bed_1">
								<?php esc_html_e('/ Per Day','hospital_mgt');?>
							</div>
						</div>
					</div>
					<div class="form-group">
						<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="visiting_fees"><?php esc_html_e('Tax','hospital_mgt');?></label>
							<div class="col-sm-3">
								<select  class="form-control tax_charge" id="" name="tax[]" multiple="multiple">					
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
							<label class="col-sm-2 control-label form-label" for="photo"><?php esc_html_e('Image','hospital_mgt');?></label>
							<div class="col-sm-3 margin_bottom_5px">
								<input type="text"  class="form-control hmgt_user_avatar_url" name="hmgt_user_avatar"  readonly
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
									<img  class="image_preview_css" alt="" src="<?php echo get_option( 'hmgt_nurse_thumb' ); ?>">
								</div>
						 </div>
						</div>
					</div>
					<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
						<input type="submit" value="<?php if($edit){ esc_html_e('Save Nurse','hospital_mgt'); }else{ esc_html_e('Save Nurse','hospital_mgt');}?>" name="save_nurse" class="btn btn-success"/>
					</div>
				</form>
			</div><!-- MODAL BODY DIV END-->
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-bs-dismiss="modal"><?php esc_html_e('Close','hospital_mgt'); ?></button>
			</div>
		</div><!-- MODAL CONTENT DIV END-->
    </div><!-- MODAL DIALOG DIV END-->
</div><!-- MODAL  DIV END-->