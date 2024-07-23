<?php
MJ_hmgt_browser_javascript_check();
$obj_invoice= new MJ_hmgt_invoice();
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	"use strict";
	<?php
	if (is_rtl())
		{
		?>	
			$('#expense_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#expense_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>
	$.fn.datepicker.defaults.format ="<?php echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
		$('#invoice_date').datepicker({
		autoclose: true
	  }); 
} );
</script>
<?php 	
if($active_tab == 'addexpense')
{
	$expense_id=0;
	if(isset($_REQUEST['expense_id']))
	$expense_id=$_REQUEST['expense_id'];
	$edit=0;
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
	{
		$edit=1;
		$result = $obj_invoice->MJ_hmgt_get_income_data($expense_id);
	}?>
    <div class="panel-body"><!-- PANEL BODY DIV START-->
	    <form name="expense_form" action="" method="post" class="form-horizontal" id="expense_form">
			<?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
			<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
			<input type="hidden" name="expense_id" value="<?php echo esc_attr($expense_id);?>">
			<input type="hidden" name="invoice_type" value="expense">		
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="patient"><?php esc_html_e('Supplier Name','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="party_name" class="form-control validate[required,custom[onlyLetter_specialcharacter]] text-input" maxlength="30" type="text" value="<?php if($edit){ echo esc_attr($result->party_name);}elseif(isset($_POST['party_name'])) echo esc_attr($_POST['party_name']);?>" name="party_name">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="payment_status"><?php esc_html_e('Status','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<select name="payment_status" id="payment_status" class="form-control validate[required] max_width_100">
							<option value="<?php esc_html_e('Paid','hospital_mgt'); ?>"
								<?php if($edit)selected( esc_html__('Paid','hospital_mgt'),$result->payment_status);?> ><?php esc_html_e('Paid','hospital_mgt');?></option>
						   <option value="<?php esc_html_e('Part Paid','hospital_mgt'); ?>"
								<?php if($edit)selected( esc_html__('Part Paid','hospital_mgt'),$result->payment_status);?>><?php esc_html_e('Part Paid','hospital_mgt');?></option>
							<option value="<?php esc_html_e('Unpaid','hospital_mgt'); ?>"
								<?php if($edit)selected( esc_html__('Unpaid','hospital_mgt'),$result->payment_status);?>><?php esc_html_e('Unpaid','hospital_mgt');?></option>
					    </select>
					</div>
				</div>
			</div>
			<?php wp_nonce_field( 'save_expense_nonce' ); ?>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="invoice_date"><?php esc_html_e('Date','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="invoice_date" class="form-control validate[required]" type="text"  value="<?php if($edit){ echo date(MJ_hmgt_date_formate(),strtotime($result->income_create_date));}elseif(isset($_POST['invoice_date'])){ echo $_POST['invoice_date'];}else{ echo date(MJ_hmgt_date_formate());}?>" name="invoice_date">
					</div>
				</div>
			</div>
			<hr>
			<?php 
				if($edit){
					$all_entry=json_decode($result->income_entry);
				}
				else
				{
					if(isset($_POST['income_entry'])){
						
						$all_data=$obj_invoice->MJ_hmgt_get_entry_records($_POST);
						$all_entry=json_decode($all_data);
					}
				}
				if(!empty($all_entry))
				{
					foreach($all_entry as $entry){
					?>
					<div id="expense_entry">
						<div class="form-group">
							<div class="mb-3 row">	
								<label class="col-sm-2 control-label form-label" for="income_entry"><?php esc_html_e('Expense Entry','hospital_mgt');?><span class="require-field">*</span></label>
								<div class="col-sm-2 margin_bottom_5px">
									<input id="income_amount" class="form-control validate[required] text-input"  min="0" type="number" onKeyPress="if(this.value.length==10) return false;" step="0.01" value="<?php echo $entry->amount;?>" name="income_amount[]" >
								</div>
								<div class="col-sm-4 margin_bottom_5px">
									<input id="income_entry" class="form-control validate[required,custom[onlyLetter_specialcharacter]] text-input" maxlength="50" type="text" value="<?php echo esc_attr($entry->entry);?>" name="income_entry[]">
								</div>
								
								<div class="col-sm-2">
								<button type="button" class="btn btn-default" onclick="deleteParentElement(this)">
								<i class="entypo-trash"><?php esc_html_e('Delete','hospital_mgt');?></i>
								</button>
								</div>
							</div>
						</div>	
					</div>
					<?php }
					
				}
				else
				{?>
					<div id="expense_entry">
						<div class="form-group">
							<div class="mb-3 row">	
								<label class="col-sm-2 control-label form-label" for="income_entry"><?php esc_html_e('Expense Entry','hospital_mgt');?><span class="require-field">*</span></label>
								<div class="col-sm-2 margin_bottom_5px">
									<input id="income_amount" class="form-control validate[required] text-input"  min="0" type="number" onKeyPress="if(this.value.length==10) return false;" step="0.01"  value="" name="income_amount[]" placeholder="<?php esc_html_e('Expense Amount','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' )); ?>)">
								</div>
								<div class="col-sm-4">
									<input id="income_entry" class="form-control validate[required,custom[onlyLetter_specialcharacter]] text-input" maxlength="50" type="text" value="" name="income_entry[]" placeholder="<?php esc_html_e('Expense Entry Label','hospital_mgt');?>">
								</div>
								
								<div class="col-sm-2">
								</div>
							</div>	
						</div>
					</div>
			<?php }?>
			
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="expense_entry"></label>
					<div class="col-sm-3">
						<button id="add_new_entry" class="btn btn-default btn-sm btn-icon icon-left" type="button"   name="add_new_entry" onclick="add_entry()"><?php esc_html_e('Add Expense Entry','hospital_mgt'); ?>
						</button>
					</div>
				</div>
			</div>
			<hr>
			<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
				<input type="submit" value="<?php if($edit){ esc_html_e('Save Expense','hospital_mgt'); }else{ esc_html_e('Create Expense Entry','hospital_mgt');}?>" name="save_expense" class="btn btn-success"/>
			</div>
		</form>
    </div><!-- PANEL BODY DIV END-->
    <script>
   	// CREATING BLANK INVOICE ENTRY
   	var blank_income_entry ='';
   	
   	function add_entry()
   	{
   		jQuery("#expense_entry").append('<div class="form-group"><div class="mb-3 row"><label class="col-sm-2 control-label form-label" for="income_entry"><?php esc_html_e('Expense Entry','hospital_mgt');?><span class="require-field">*</span></label><div class="col-sm-2 margin_bottom_5px"><input id="income_amount" class="form-control validate[required] text-input"  min="0" type="number" onKeyPress="if(this.value.length==10) return false;" step="0.01" value="" name="income_amount[]" placeholder="<?php esc_html_e('Expense Amount','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' )); ?>)"></div><div class="col-sm-4 margin_bottom_5px"><input id="income_entry" class="form-control validate[required,custom[onlyLetter_specialcharacter]] text-input" maxlength="50" type="text" value="" name="income_entry[]" placeholder="<?php esc_html_e('Expense Entry Label','hospital_mgt');?>"></div><div class="col-sm-2"><button type="button" class="btn btn-default" onclick="deleteParentElement(this)"><i class="entypo-trash"><?php esc_html_e('Delete','hospital_mgt');?></i></button></div></div></div>');
   	}
   	
   	// REMOVING INVOICE ENTRY
   	function deleteParentElement(n)
	{
		alert("<?php esc_html_e('Do you really want to delete this record ?','hospital_mgt');?>");
   		n.parentNode.parentNode.parentNode.removeChild(n.parentNode.parentNode);
   	}
    </script> 
<?php 
}
?>