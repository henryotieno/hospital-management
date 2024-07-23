<?php
$role='nurse';
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	"use strict";
	<?php
	if (is_rtl())
		{
		?>	
			$('#nurse_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#nurse_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>
	$.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
     	$('#birth_date').datepicker({
       	endDate: '+0d',
        autoclose: true,
   }); 
	//username not  allow space validation
	$('#username').keypress(function( e ) 
	{
       if(e.which === 32) 
         return false;
    });
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
} );
</script>
<?php 	
if($active_tab == 'addnurse')
{
	MJ_hmgt_browser_javascript_check();
	$nurse_id=0;
	$edit=0;
	if(isset($_REQUEST['nurse_id']))
		$nurse_id=$_REQUEST['nurse_id'];
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
	{			
		$edit=1;
		$user_info = get_userdata($nurse_id);
	}
	?>
	<!--PANEL BODY START-->
    <div class="panel-body">
        <form name="nurse_form" action="" method="post" class="form-horizontal" id="nurse_form">	
         <?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
		<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
		<input type="hidden" name="role" value="<?php echo esc_attr($role);?>"  />
		<input type="hidden" name="user_id" value="<?php echo esc_attr($nurse_id);?>"  />
		<div class="header">	
			<h3 class="first_hed"><?php esc_html_e('Personal Information','hospital_mgt');?></h3>
			<hr>
		</div>		
		<div class="form-group">
			<div class="mb-3 row">	
				<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="first_name"><?php esc_html_e('First Name','hospital_mgt');?><span class="require-field">*</span></label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
					<input id="first_name" class="form-control validate[required,custom[onlyLetter_specialcharacter]] text-input" maxlength="50" type="text" value="<?php if($edit){ echo esc_attr($user_info->first_name);}elseif(isset($_POST['first_name'])) echo esc_attr($_POST['first_name']);?>" name="first_name">
				</div>
				<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="middle_name"><?php esc_html_e('Middle Name','hospital_mgt');?></label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
					<input id="middle_name" class="form-control validate[custom[onlyLetter_specialcharacter]]" type="text" maxlength="50" value="<?php if($edit){ echo esc_attr($user_info->middle_name);}elseif(isset($_POST['middle_name'])) echo esc_attr($_POST['middle_name']);?>" name="middle_name">
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="mb-3 row">	
				<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="last_name"><?php esc_html_e('Last Name','hospital_mgt');?><span class="require-field">*</span></label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
					<input id="last_name" class="form-control validate[required,custom[onlyLetter_specialcharacter]] text-input" maxlength="50" type="text"  value="<?php if($edit){ echo esc_attr($user_info->last_name);}elseif(isset($_POST['last_name'])) echo esc_attr($_POST['last_name']);?>" name="last_name">
				</div>
				<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="birth_date"><?php esc_html_e('Date of birth','hospital_mgt');?><span class="require-field">*</span></label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
					<input id="birth_date" class="form-control validate[required]" type="text"   name="birth_date" 
					value="<?php if($edit){ echo date(MJ_hmgt_date_formate(),strtotime($user_info->birth_date));}elseif(isset($_POST['birth_date'])) echo esc_attr($_POST['birth_date']);?>" readonly>
				</div>
			</div>
		</div>
		<?php wp_nonce_field( 'save_nurse_nonce' ); ?>
		<div class="form-group">
			<div class="mb-3 row">	
				<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="gender"><?php esc_html_e('Gender','hospital_mgt');?><span class="require-field">*</span></label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
				<?php $genderval = "male"; if($edit){ $genderval=$user_info->gender; }elseif(isset($_POST['gender'])) {$genderval=$_POST['gender'];}?>
					<label class="radio-inline">
					<input type="radio" value="male" class="tog" name="gender"  <?php  checked( 'male', $genderval);  ?>/>&nbsp;<?php esc_html_e('Male','hospital_mgt');?>
				    </label>&nbsp;&nbsp;
				    <label class="radio-inline">
				   <input type="radio" value="female" class="tog" name="gender"  <?php  checked( 'female', $genderval);  ?>/>&nbsp;<?php esc_html_e('Female','hospital_mgt');?>
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
					value="<?php if($edit){ echo esc_attr($user_info->address);}elseif(isset($_POST['address'])) echo esc_attr($_POST['address']);?>">
				</div>
				<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="city_name"><?php esc_html_e('City','hospital_mgt');?><span class="require-field">*</span></label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
					<input id="city_name" class="form-control validate[required,custom[city_state_country_validation]]" type="text" maxlength="50" name="city_name" 
					value="<?php if($edit){ echo esc_attr($user_info->city_name);}elseif(isset($_POST['city_name'])) echo esc_attr($_POST['city_name']);?>">
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="mb-3 row">	
				<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="state_name"><?php esc_html_e('State','hospital_mgt');?></label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
					<input id="state_name" class="form-control validate[custom[city_state_country_validation]]" type="text" maxlength="50" name="state_name" 
					value="<?php if($edit){ echo esc_attr($user_info->state_name);}elseif(isset($_POST['state_name'])) echo esc_attr($_POST['state_name']);?>">
				</div>
				<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="country_name"><?php esc_html_e('Country','hospital_mgt');?></label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
					<input id="country_name" class="form-control validate[custom[city_state_country_validation]]" type="text" maxlength="50" name="country_name" 
					value="<?php if($edit){ echo esc_attr($user_info->country_name);}elseif(isset($_POST['country_name'])) echo esc_attr($_POST['country_name']);?>">
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="mb-3 row">	
				<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="zip_code"><?php esc_html_e('Zip Code','hospital_mgt');?><span class="require-field">*</span></label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
					<input id="zip_code" class="form-control  validate[required,custom[onlyLetterNumber]]" type="text" maxlength="15"  name="zip_code" 
					value="<?php if($edit){ echo esc_attr($user_info->zip_code);}elseif(isset($_POST['zip_code'])) echo esc_attr($_POST['zip_code']);?>">
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
				<input type="text" value="<?php if($edit) { if(!empty($user_info->phonecode)){ echo esc_attr($user_info->phonecode); }else{ ?>+<?php echo MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }  }elseif(isset($_POST['phonecode'])){ echo esc_attr($_POST['phonecode']); }else{ ?>+<?php echo MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }?>"  class="form-control  validate[required] onlynumber_and_plussign" name="phonecode" maxlength="5">
				</div>
				<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 has-feedback">
					<input id="mobile" class="form-control validate[required,custom[phone_number]] text-input" minlength="6" maxlength="15" type="text" value="<?php if($edit){ echo esc_attr($user_info->mobile);}elseif(isset($_POST['mobile'])) echo esc_attr($_POST['mobile']);?>" name="mobile">
				</div>
				<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="phone"><?php esc_html_e('Phone','hospital_mgt');?></label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
					<input id="phone" class="form-control validate[custom[phone_number]] text-input" minlength="6" maxlength="15" type="text" value="<?php if($edit){ echo esc_attr($user_info->phone);}elseif(isset($_POST['phone'])) echo esc_attr($_POST['phone']);?>" name="phone">
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
					<input id="email" class="form-control validate[required,custom[email]] text-input" type="text" maxlength="100" name="email" 
					value="<?php if($edit){ echo esc_attr($user_info->user_email);}elseif(isset($_POST['email'])) echo esc_attr( $_POST['email']);?>">
				</div>
				<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="username"><?php esc_html_e('User Name','hospital_mgt');?><span class="require-field">*</span></label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
					<input id="username" class="form-control validate[required,custom[username_validation]]" type="text" maxlength="30" name="username" 
					value="<?php if($edit){ echo esc_attr($user_info->user_login);}elseif(isset($_POST['username'])) echo esc_attr($_POST['username']);?>" <?php if($edit) echo "readonly";?>>
				</div>
			</div>
		</div>
		<div class="form-group">
			<div class="mb-3 row">	
				<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="password"><?php esc_html_e('Password','hospital_mgt');?><?php if(!$edit) {?><span class="require-field">*</span><?php }?></label>
				<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
					<input id="password" class="form-control <?php if(!$edit){ echo 'validate[required,minSize[8]]'; }else{ echo 'validate[minSize[8]]'; }?>" type="password"  maxlength="12" name="password" value="">
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
				<?php if($edit){ $departmentid=$user_info->department; }elseif(isset($_POST['department'])){$departmentid=$_POST['department'];}else{$departmentid='';}?>
					<select name="department" class="form-control validate[required]" id="department">
					<option value=""><?php esc_html_e('Select Department','hospital_mgt');?></option>
					<?php 
					
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
					<input id="charge" class="form-control validate[required] text-input" step="0.01" min="0" type="number" onKeyPress="if(this.value.length==8) return false;"  name="charge" 
					value="<?php if($edit){ echo esc_attr($user_info->charge);}elseif(isset($_POST['charge'])) echo esc_attr($_POST['charge']);?>">
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
					<select  class="form-control" id="tax_charge" name="tax[]" multiple="multiple">					
						<?php					
						if($edit)
						{
							$tax_id=explode(',',$user_info->tax);
						}
						else
						{	
							$tax_id[]='';
						}
						$obj_invoice= new MJ_hmgt_invoice();
						$hmgt_taxs=$obj_invoice->MJ_hmgt_get_all_tax_data();	
						
						if(!empty($hmgt_taxs))
						{
							foreach($hmgt_taxs as $entry)
							{
								$selected = "";
								if(in_array($entry->tax_id,$tax_id))
									$selected = "selected";
								?>
								<option value="<?php echo esc_attr($entry->tax_id); ?>" <?php echo esc_attr($selected); ?> ><?php echo esc_html($entry->tax_title);?>-<?php echo esc_html($entry->tax_value);?></option>
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
					<input type="text" id="hmgt_user_avatar_url" class="form-control" name="hmgt_user_avatar"  
					value="<?php if($edit)echo esc_url( $user_info->hmgt_user_avatar );elseif(isset($_POST['hmgt_user_avatar'])) echo $_POST['hmgt_user_avatar']; ?>" readonly />
				</div>	
					<div class="col-sm-3">
	       				 <input id="upload_user_avatar_button" type="button" class="button" value="<?php esc_html_e( 'Upload image', 'hospital_mgt' ); ?>" />
						 <br>
	       				 <span class="description"><?php esc_html_e('Upload only JPG, JPEG, PNG & GIF image', 'hospital_mgt' ); ?></span>
	       		
				</div>
				<div class="clearfix"></div>
				
				<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
	                     <div id="upload_user_avatar_preview" >
		                     <?php 
								if($edit) 
		                     	{
									if($user_info->hmgt_user_avatar == "")
									{?>
									<img class="image_preview_css" alt="" src="<?php echo esc_url(get_option( 'hmgt_nurse_thumb' )); ?>">
									<?php
									}
									else 
									{
										?>
										<img class="image_preview_css" src="<?php if($edit)echo esc_url( $user_info->hmgt_user_avatar ); ?>" />
									<?php 
									}
		                     	}
						        else 
								{
						        	?>
						        	<img class="image_preview_css" alt="" src="<?php echo esc_url(get_option( 'hmgt_nurse_thumb' )); ?>">
						        	<?php 
						        }?>
	    				</div>
	   		 </div>
	   		</div>
		</div>

		<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
        	<input type="submit" value="<?php if($edit){ esc_html_e('Save Nurse','hospital_mgt'); }else{ esc_html_e('Save Nurse','hospital_mgt');}?>" name="save_nurse" class="btn btn-success"/>
        </div>
        </form>
    </div>
    <!--END PANEL BODY-->    
<?php 
}
?>