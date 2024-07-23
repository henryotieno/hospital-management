<?php
$obj_bloodbank=new MJ_hmgt_bloodbank();
if($active_tab == 'addbloodgroop')	
{
	MJ_hmgt_browser_javascript_check();
	$edit=0;
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
	{
		$edit=1;
		$result = $obj_bloodbank->MJ_hmgt_get_single_bloodgroup($_REQUEST['bloodgroup_id']);	
	} 
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	"use strict";
	<?php
	if (is_rtl())
		{
		?>	
			$('#bloodgroup_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#bloodgroup_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>
});
</script>
    <div class="panel-body"><!-- PANEL BODY DIV START-->
        <form name="bloodgroup_form" action="" method="post" class="form-horizontal" id="bloodgroup_form">
			<?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
			<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
			<input type="hidden" name="bloodgroup_id" value="<?php if(isset($_REQUEST['bloodgroup_id'])) echo esc_attr($_REQUEST['bloodgroup_id']);?>"  />
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="blood_group"><?php esc_html_e('Blood Group','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<?php if($edit){ $userblood=$result->blood_group; }elseif(isset($_POST['blood_group'])){$userblood=$_POST['blood_group'];}else{$userblood='';}?>
						<select id="blood_group" class="form-control validate[required] max_width_100" name="blood_group">
						<option value=""><?php esc_html_e('Select Blood Group','hospital_mgt');?></option>
						<?php foreach(MJ_hmgt_blood_group() as $blood){ ?>
								<option value="<?php echo esc_attr($blood);?>" <?php selected($userblood,$blood);  ?>><?php echo esc_html($blood); ?> </option>
						<?php } ?>
					</select>
					</div>
				</div>
			</div>
			<?php wp_nonce_field( 'save_bloodgroup_nonce' ); ?>
			<div class="form-group margin_bottom_5px">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="blood_status"><?php esc_html_e('Number Of Bags','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="blood_status" class="form-control validate[required] text-input" type="number" min="0" onKeyPress="if(this.value.length==4) return false;" value="<?php if($edit){ echo esc_attr($result->blood_status);}elseif(isset($_POST['blood_status'])) echo esc_attr($_POST['blood_status']);?>" name="blood_status">
					</div>
				</div>
			</div>
			<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
				<input type="submit" value="<?php if($edit){ esc_html_e('Save','hospital_mgt'); }else{ esc_html_e('Save','hospital_mgt');}?>" name="save_bloodgroup" class="btn btn-success"/>
			</div>
	    </form>
    </div><!-- PANEL BODY DIV END-->
<?php 
}
?>