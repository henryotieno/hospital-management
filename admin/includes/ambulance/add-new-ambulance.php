<?php
	MJ_hmgt_browser_javascript_check();
	if($active_tab == 'add_ambulance')	
	{
		$edit=0;
		if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
		{					
			$edit=1;
			$result= $obj_ambulance->MJ_hmgt_get_single_ambulance($_REQUEST['amb_id']);			
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
				$('#patient_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
			<?php
			}
			else{
				?>
				$('#patient_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
				<?php
			}
			?>
		});
		</script>
        <div class="panel-body"><!-- PANEL BODY DIV START-->
			<form name="patient_form" action="" method="post" class="form-horizontal" id="patient_form">
				 <?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
				<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
				<input type="hidden" name="amb_id" value="<?php if(isset($_REQUEST['amb_id'])) echo esc_attr($_REQUEST['amb_id']);?>"  />
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-sm-2 control-label form-label" for="ambulance_id"><?php esc_html_e('Ambulance Id','hospital_mgt');?><span class="require-field">*</span></label>
						<div class="col-sm-8">
							<input id="ambulance_id" class="form-control validate[required]" type="text" readonly value="<?php if($edit){ echo esc_attr($result->ambulance_id);}elseif(isset($_POST['ambulance_id'])) echo esc_attr($_POST['ambulance_id']); else echo esc_attr($obj_ambulance->MJ_hmgt_generate_ambulance_id());?>" name="ambulance_id">
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-sm-2 control-label form-label" for="amb_Reg_number"><?php esc_html_e('Registration Number','hospital_mgt');?><span class="require-field">*</span></label>
						<div class="col-sm-8">
							<input id="amb_Reg_number" class="form-control validate[required]" min="0" type="number" onKeyPress="if(this.value.length==6) return false;" value="<?php if($edit){ echo esc_attr($result->registerd_no);}elseif(isset($_POST['registerd_no'])) echo esc_attr($_POST['registerd_no']);?>" name="registerd_no">
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-sm-2 control-label form-label" for="amb_driver_name"><?php esc_html_e('Driver Name','hospital_mgt');?><span class="require-field">*</span></label>
						<div class="col-sm-8">
							<input id="amb_driver_name" class="form-control validate[required,custom[onlyLetter_specialcharacter]]" type="text"  maxlength="50" value="<?php if($edit){ echo esc_attr($result->driver_name);}elseif(isset($_POST['driver_name'])) echo esc_attr($_POST['driver_name']);?>" name="driver_name">
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-sm-2 control-label form-label" for="amb_driver_address"><?php esc_html_e('Driver Address','hospital_mgt');?><span class="require-field">*</span></label>
						<div class="col-sm-8">
							<input id="amb_driver_address" class="form-control validate[required,custom[address_description_validation]]" type="text"  maxlength="150" value="<?php if($edit){ echo esc_attr($result->driver_address);}elseif(isset($_POST['driver_address'])) echo esc_attr($_POST['driver_address']);?>" name="driver_address">
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-sm-2 control-label form-label" for="amb_phone_number"><?php esc_html_e('Driver Phone Number','hospital_mgt');?><span class="require-field">*</span></label>
						<div class="col-sm-8">
							<input id="amb_phone_number" class="form-control validate[required,custom[phone_number]] text-input" minlength="6" maxlength="15" type="text" value="<?php if($edit){ echo esc_attr($result->driver_phoneno);}elseif(isset($_POST['driver_phoneno'])) echo esc_attr($_POST['driver_phoneno']);?>" name="driver_phoneno">
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-sm-2 control-label form-label" for="discription"><?php esc_html_e('Description','hospital_mgt');?></label>
						<div class="col-sm-8">
							<input id="discription" class="form-control validate[custom[address_description_validation]]" maxlength="150" type="text"  value="<?php if($edit){ echo esc_attr($result->description);}elseif(isset($_POST['description'])) echo esc_attr($_POST['description']);?>" name="description">
						</div>
					</div>
				</div>
				<?php wp_nonce_field( 'save_ambulance_nonce' ); ?>
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-sm-2 control-label form-label" for="driver_image"><?php esc_html_e('Driver Image','hospital_mgt');?></label>
						<div class="col-sm-2 margin_bottom_5px">
							 <input type="text" id="hmgt_user_avatar_url" class="form-control" name="driver_image" value="<?php if($edit)echo esc_url( $result->driver_image ); ?>" readonly />
							 </div>
							<div class="col-sm-4">
								 <input id="upload_user_avatar_button" type="button" class="button" value="<?php esc_html_e( 'Upload image', 'hospital_mgt' ); ?>" />
								 <span class="description"><?php esc_html_e('Upload image', 'hospital_mgt' ); ?></span>
							   </div>
						<div class="clearfix"></div>					
						<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12 margin_bottom_5px">  
							<div id="upload_user_avatar_preview">
							 <br>
							 <?php 
								if($edit) 
								{
									if($result->driver_image == "")
									{
										?><img class="image_preview_css" alt="" src="<?php echo esc_url(get_option( 'hmgt_driver_thumb' )) ?>" height="100px" width="100px"><?php 
									}
									else
									{
									?>
										<img  class="image_preview_css" src="<?php if($edit)echo esc_url( $result->driver_image ); ?>" />
								<?php 
									}
								}
								else 
								{
								?>
									<img class="image_preview_css" alt="" src="<?php echo esc_url(get_option( 'hmgt_driver_thumb' )) ?>" height="150px" width="150px">
										<?php 
								}?>  
							</div>
						</div>	
					</div>						
				</div>
				<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
					<input type="submit" value="<?php if($edit){ esc_html_e('Save','hospital_mgt'); }else{ esc_html_e('Save','hospital_mgt');}?>" name="save_ambulance" class="btn btn-success"/>
				</div>
			</form>
        </div><!-- PANEL BODY DIV END-->
    <?php 
	}
	?>