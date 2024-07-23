<?php
$obj_bed = new MJ_hmgt_bedmanage();
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
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
	var date = new Date();
      date.setDate(date.getDate()-0);
	  $.fn.datepicker.defaults.format =" <?php echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
        $('#discharge_time').datepicker({
	    startDate: date,
        autoclose: true
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
} );
</script>
    <?php 	
	if($active_tab == 'transfer')
	{
		$transfar=0;
			if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'transfer'){
				$transfar=1;
				$result = $obj_bed->MJ_hmgt_get_single_bedallotment($_REQUEST['allotment_id']);
				
			}?>
		
       <div class="panel-body"><!-- STRAT PANEL BODY DIV-->
			<form name="patient_form" action="" method="post" class="form-horizontal" id="patient_form"><!-- START TRANSFER BED FORM-->
				 <?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
				<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
				<input type="hidden" name="transfar_allotment_id" value="<?php if(isset($_REQUEST['allotment_id'])) echo esc_attr($_REQUEST['allotment_id']);?>"  />
				<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="patient_id"><?php esc_html_e('Patient','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<select name="transfar_patient_id" id="patient_id" class="form-control validate[required] max_width_100" disabled="disabled">
							<option value=""><?php esc_html_e('Select Patient','hospital_mgt');?></option>
							<?php 
							if($transfar)
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
							$patients = MJ_hmgt_inpatient_list();
							//print_r($patient);
							if(!empty($patients))
							{
								foreach($patients as $patient)
								{
									echo '<option value="'.$patient['id'].'" '.selected($patient_id1,$patient['id']).'>'.$patient['patient_id'].' - '.$patient['first_name'].' '.$patient['last_name'].'</option>';
								}
							}
							?>
						</select>
						<input type="hidden" name="transfar_patient_id" value="<?php print esc_attr($result->patient_id);?>">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="patient_status"><?php esc_html_e('Patient Status','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8" >
						<?php if($transfar){ $patient=MJ_get_inpatient_status($patient_id1);
						$patient_status="";
							if(!empty($patient)){
						$patient_status=$patient->patient_status; } }elseif(isset($_POST['patient_status'])){$patient_status=$_POST['patient_status'];}else{$patient_status='';} 
						?>
						
						<select name="patient_status" class="form-control validate[required] max_width_100" disabled="disabled" >
						<option value=""><?php esc_html_e('select Patient Status','hospital_mgt');?></option>
						<?php foreach(MJ_hmgt_admit_reason() as $reason)
						{?>
							<option value="<?php echo esc_attr($reason);?>" <?php selected($patient_status,$reason);?>><?php echo esc_html($reason);?></option>
						<?php }?>				
						</select>
						<input type="hidden" name="transfar_patient_status" value="<?php print esc_attr($patient_status); ?>">
					</div>	
				</div>
			</div>
			
			<div class="form-group">
				<div class="mb-3 row">
					<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12 bg-info bed_transfer_from"> <?php esc_html_e('Bed Transfer From :','hospital_mgt');  ?>
					</div>
				</div>
			</div>
			
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="bed_type_id"><?php esc_html_e('Select Bed Type','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
					<?php if(isset($_REQUEST['bed_type_id']))
						{	
							$bed_type1 = $_REQUEST['bed_type_id'];
						}
						elseif($transfar)
						{
							$bed_type1 = $result->bed_type_id;
						}
						else 
						{
							$bed_type1 = "";
						}
						?>
						<select name="" class="form-control" id="" disabled="disabled">
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
						<input type="hidden" name="old_bed_type_id" value="<?php print esc_attr($bed_type1); ?>" >
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="bednumber"><?php esc_html_e('Bed Number','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<select name="" class="form-control validate[required] max_width_100" id="" disabled="disabled">
						<option value=""><?php esc_html_e('Select Bed Number','hospital_mgt');?></option>
						<?php 
						if($transfar){					
							$bedtype_data = $obj_bed->MJ_hmgt_get_bed_by_bedtype($result->bed_type_id);
							if(!empty($bedtype_data)){
								foreach ($bedtype_data as $retrieved_data){
									echo '<option value="'.$retrieved_data->bed_id.'" '.selected($result->bed_number,$retrieved_data->bed_id).'>'.$retrieved_data->bed_number.'</option>';
								}
							}
						}
						?>
						</select>	
						<input type="hidden" name="old_bednumber" value="<?php print esc_attr($retrieved_data->bed_id);?>"> 
					</div>
				</div>
			</div>
				
			<div id=""></div>
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="allotment_date"><?php esc_html_e('Allotment Date','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="" class="form-control validate[required]" type="text"  value="<?php if($transfar){   echo date(MJ_hmgt_date_formate(),strtotime($result->allotment_date));}elseif(isset($_POST['allotment_date'])) echo esc_attr($_POST['allotment_date']);?>" name="allotment_date_old" readonly>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="discharge_time"><?php esc_html_e('Expected Discharge Date','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="" class="form-control validate[required]" type="text"   value="<?php if($transfar){ echo date(MJ_hmgt_date_formate(),strtotime($result->discharge_time));}elseif(isset($_POST['discharge_time'])) echo esc_attr($_POST['discharge_time']);?>" name="" readonly>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">
					<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12 bg-info bed_transfer_from"> <?php esc_html_e('Bed Transfer To :','hospital_mgt')  ?></div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="bed_type_id"><?php esc_html_e('Select Bed Type','hospital_mgt');?><span class="require-field max_width_100">*</span></label>
					<div class="col-sm-8">
					<?php 
						if(isset($_REQUEST['bed_type_id']))
						{
							$bed_type1 = $_REQUEST['bed_type_id'];
						}
						elseif($transfar)
						{
							$bed_type1 = $result->bed_type_id;
						}
						else 
						{
							$bed_type1 = "";
						}
						?>
						<select name="transfar_bed_type_id" class="form-control validate[required] bed_type_transfer" id="bed_type_id" >
							<option value = ""><?php esc_html_e('Bed type','hospital_mgt');?></option>
							<?php 
							$bedtype_data=$obj_bed->MJ_hmgt_get_all_bedtype();
							if(!empty($bedtype_data))
							{
								foreach ($bedtype_data as $retrieved_data)
								{
									echo '<option value="'.$retrieved_data->ID.'">'.$retrieved_data->post_title.'</option>';
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
						<select name="transfar_bed_number" class="form-control validate[required] max_width_100" id="bednumber" >
							<option value=""><?php esc_html_e('Select Bed Number','hospital_mgt');?></option>
							<?php 
							if($transfar)
							{					
								$bedtype_data = $obj_bed->MJ_hmgt_get_bed_by_bedtype($result->bed_type_id);
								if(!empty($bedtype_data))
								{
									foreach ($bedtype_data as $retrieved_data)
									{
										echo '<option value="'.$retrieved_data->bed_id.'">'.$retrieved_data->bed_number.'</option>';
									}
								}
							}
							?>
						</select>			
					</div>
				</div>
			</div>
			
			
			<div class="col-sm-8" id="bedlocation">	</div>
			<div class="col-sm-4 flot_width_100 float_left_width_100"></div>
			
			<div id=""></div>
			
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="allotment_date"><?php esc_html_e('Allotment Date','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="allotment_date" class="form-control validate[required]" type="text"   value="<?php print date(MJ_hmgt_date_formate());?>" name="transfar_allotment_date"  readonly>
					</div>
				</div>
			</div>
			
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="discharge_time"><?php esc_html_e('Expected Discharge Date','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="discharge_time" class="form-control validate[required]" type="text" value="" name="transfar_discharge_time">
					</div>
				</div>
			</div>
			<?php wp_nonce_field( 'bed_transfar_nonce' ); ?>
			
			<div class="form-group">
				<div class="mb-3 row">
					<label class="col-sm-2 control-label form-label" for="doctor"><?php esc_html_e('Select Nurse','hospital_mgt');?></label>
					<div class="col-sm-8">
					<?php $allnurse = MJ_hmgt_getuser_by_user_role('nurse');
							$nurse_data = array();
							if($transfar){						
								$nurse_list = $obj_bed->MJ_hmgt_get_nurse_by_bedallotment_id($_REQUEST['allotment_id']);						
								foreach($nurse_list as $assign_id){
									$nurse_data[]=$assign_id->child_id;						
								}
							}
							elseif(isset($_REQUEST['doctor'])){
								$nurse_list = $_REQUEST['doctor'];
								foreach($nurse_list as $assign_id){
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
									echo '<option value='.$nurse['id'].' '.$selected.'>'.$nurse['first_name'].' '.$nurse['last_name'].'</option>';
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
						<textarea class="form-control" name="allotment_description" id="allotment_description"><?php if($transfar){ echo esc_textarea($result->allotment_description);}elseif(isset($_POST['allotment_description'])) echo esc_textarea($_POST['allotment_description']);?></textarea>
					</div>
				</div>
			</div>
			<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
					<input id="save_allow" type="submit" value="<?php  esc_html_e('Transfer Bed','hospital_mgt')?>" name="bed_transfar" class="btn btn-success"/>
				</div>
			</form><!-- END BED Transfer FORM-->
	</div>  <!-- END PANEL BODY DIV-->      
<?php 
}
?>