<?php
MJ_hmgt_browser_javascript_check();
$obj_invoice= new MJ_hmgt_invoice();
?>
<script type="text/javascript">
jQuery(document).ready(function($) 
{
	"use strict";
	<?php
	if (is_rtl())
		{
		?>	
			$('#tax_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#tax_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>
});
</script>
 <?php 	
if($active_tab == 'addtax')
{
	$tax_id=0;
	if(isset($_REQUEST['tax_id']))
	$tax_id=$_REQUEST['tax_id'];
	$edit=0;
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
	{
		$edit=1;
		$result = $obj_invoice->MJ_hmgt_get_single_tax_data($tax_id);
	}
	?>
    <div class="panel-body"><!-- PANEL BODY DIV START-->
        <form name="tax_form" action="" method="post" class="form-horizontal" id="tax_form">
			<?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
			<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
			<input type="hidden" name="tax_id" value="<?php echo esc_attr($tax_id);?>">						
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for=""><?php esc_html_e("Tax Name","hospital_mgt");?><span class="require-field">*</span></label>
					<div class="col-sm-8">
					<input id="" maxlength="30" class="form-control validate[required,custom[address_description_validation]] text-input" type="text" value="<?php if($edit){ echo esc_attr($result->tax_title);}elseif(isset($_POST['tax_title'])) echo esc_attr($_POST['tax_title']);?>" name="tax_title">
				   </div>
				</div>
			</div>  
			<?php wp_nonce_field( 'save_tax_nonce' ); ?>
			<div class="form-group margin_bottom_5px">
				<div class="mb-3 row">	
				   <label class="col-sm-2 control-label form-label" for=""><?php esc_html_e("Tax Value","hospital_mgt");?>(%)<span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="tax" class="form-control validate[required,custom[number]] text-input" onkeypress="if(this.value.length==6) return false;" step="0.01" type="number" value="<?php if($edit){ echo esc_attr($result->tax_value);}elseif(isset($_POST['tax_value'])) echo esc_attr($_POST['tax_value']);?>" name="tax_value" min="0" max="100">
					</div>
				</div>
			</div>
			<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
				<input type="submit" value="<?php if($edit){ esc_html_e('Save','hospital_mgt'); }else{ esc_html_e('Save','hospital_mgt');}?>" name="save_tax" class="btn btn-success"/>
			</div>
        </form>
    </div><!-- PANEL BODY DIV END-->    
<?php 
}
?>