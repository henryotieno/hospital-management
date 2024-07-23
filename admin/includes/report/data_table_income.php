<style>
@media print
{
html, body { height: auto; }
#page-wrapper{ margin: 0 0 0 0; border-left:#FFF; padding: 0 10px; min-height:auto;}
.navbar-static-side { margin: 0 0 0 0; margin-top: 10px;}
}
</style>

<script src="https://cdn.datatables.net/buttons/1.7.0/js/buttons.html5.min.js" type="text/javascript" defer></script>
<div class="panel-body clearfix">
<script type="text/javascript">
jQuery(document).ready(function() {
	"use strict";
	jQuery('#transaction_list_income').DataTable({
	"responsive": true,		
	"order": [[ 1, "asc" ]],
	dom: 'Bfrtip',
   buttons: [
       {
	    extend: 'print',
		title: 'Income Report',
	   },
	   {
	  	 extend: 'pdf',
	  	 title: 'Income Report',
         download: 'open'
	   } 
    ],	
	"aoColumns":[
				  {"bSortable": false},
				  {"bSortable": true},
				  {"bSortable": true},
				  {"bSortable": true},
				  {"bSortable": true},              	                 
				  {"bSortable": true}],
				  language:<?php echo MJ_hmgt_datatable_multi_language();?>
				  });
	 $('#table_income_report').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
	var start = new Date();
		var end = new Date(new Date().setYear(start.getFullYear()+1));
		$.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
		$('.sdate').datepicker({
			//startDate : start,
			endDate   : end,
			autoclose: true
		}).on('changeDate', function (selected) {
        var minDate = new Date(selected.date.valueOf());
        $('.edate').datepicker('setStartDate', minDate);
    });

 
		$.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
		$('.edate').datepicker({
			endDate   : end,
			autoclose: true
		}).on('changeDate', function (selected) {
            var maxDate = new Date(selected.date.valueOf());
            $('.sdate').datepicker('setEndDate', maxDate);
        });		
} );
</script>
	<div class="panel-body clearfix">
		<form name="table_income_report" action="" id="table_income_report" method="post" autocomplete="off">
			<div class="mb-3 row">	  
				<div class="form-group col-md-3">
					<label for="sdate"><?php esc_html_e('Start Date','hospital_mgt');?><span class="require-field">*</span></label>
						<input type="text"  class="form-control sdate validate[required]" name="sdate"  value="<?php if(isset($_REQUEST['sdate'])) echo esc_attr($_REQUEST['sdate']);elseif(isset($_POST['sdate'])) echo esc_attr($_POST['sdate']);?>" placeholder="<?php esc_html_e('Please select Start Date','hospital_mgt');?>" readonly>	
				</div>
				<div class="form-group col-md-3">
					<label for="edate"><?php esc_html_e('End Date','hospital_mgt');?><span class="require-field">*</span></label>
					<input type="text"  class="form-control edate validate[required]" name="edate" value="<?php if(isset($_REQUEST['edate'])) echo esc_attr($_REQUEST['edate']);elseif(isset($_POST['edate'])) echo esc_attr($_POST['edate']);?>" placeholder="<?php esc_html_e('Please select End Date','hospital_mgt');?>" readonly>
							
				</div>
				<div class="form-group col-md-3 button-possition">
					<label for="subject_id">&nbsp;</label>
					<input type="submit" name="view_income_data" Value="<?php esc_html_e('Go','hospital_mgt');?>"  class="btn btn-info"/>
				</div>
			</div>		
		</form>
	</div>		
	<?php
	if(isset($_REQUEST['view_income_data']))
	{
		
        $start_date =MJ_hmgt_get_format_for_db($_POST['sdate']);
		$end_date =MJ_hmgt_get_format_for_db($_POST['edate']);
		if($end_date >= $start_date)
		{
			global $wpdb;
			$table_name=$wpdb->prefix.'hmgt_income_expense';
			$result1 = $wpdb->get_results("select *from $table_name where income_create_date BETWEEN '$start_date' AND '$end_date' AND invoice_type='income'");
		?>
    	<div class="panel-body">
          	<div class="table-responsive">
                <form id="frm-example" name="frm-example" method="post">
            		<table id="transaction_list_income" class="display" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th><?php esc_html_e( 'Invoice ID', 'hospital_mgt' ) ;?></th>
								<th> <?php esc_html_e( 'Patient Id', 'hospital_mgt' ) ;?></th>
								<th> <?php esc_html_e( 'Patient Name', 'hospital_mgt' ) ;?></th>
								<th> <?php esc_html_e( 'Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
								<th> <?php esc_html_e( 'Date', 'hospital_mgt' ) ;?></th>
								<th> <?php esc_html_e( 'Payment Method', 'hospital_mgt' ) ;?></th>
								
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th><?php esc_html_e( 'Invoice ID', 'hospital_mgt' ) ;?></th>
								<th> <?php esc_html_e( 'Patient Id', 'hospital_mgt' ) ;?></th>
								<th> <?php esc_html_e( 'Patient Name', 'hospital_mgt' ) ;?></th>
								<th> <?php esc_html_e( 'Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
								<th> <?php esc_html_e( 'Date', 'hospital_mgt' ) ;?></th>
								<th> <?php esc_html_e( 'Payment Method', 'hospital_mgt' ) ;?></th>
								
							</tr>
						</tfoot>	 
			 
						<tbody>
						 <?php 
						 if(!empty($result1))
						 {
							foreach ($result1 as $retrieved_data)
							{
							
							$all_entry=json_decode($retrieved_data->income_entry);
								$total_amount=0;
								foreach($all_entry as $entry)
								{
									$total_amount+=$entry->amount;
								}
								$obj_invoice= new MJ_hmgt_invoice();
								if(!empty($retrieved_data->invoice_id))
								{
									$invoice_data=$obj_invoice->MJ_hmgt_get_invoice_data($retrieved_data->invoice_id);
								}
								$idtest=($retrieved_data->income_id);
								$type=($retrieved_data->invoice_type);
								
								
						 ?>
							<tr>
								<td class=""><?php 
							    if(!empty($invoice_data->invoice_number))
								{
									if($invoice_data->invoice_number == 0)
									{ 
										echo '-'; 
									}
									else
									{
										echo esc_html($invoice_data->invoice_number); 	
									}
								}
								else
								{
									echo '-'; 
								}
								?></td>
								<td class="patient"><?php echo $patient_id=get_user_meta($retrieved_data->party_name, 'patient_id', true);?></td>
								<td class="patient_name"><?php $user=get_userdata($retrieved_data->party_name); echo esc_html($user->display_name);?></td>
								<td class="income_amount"><?php echo esc_html($total_amount);?></td>
								<td class="status"><?php echo date(MJ_hmgt_date_formate(),strtotime($retrieved_data->income_create_date));?></td>
								<td class="aa"><?php echo esc_html($retrieved_data->payment_method);?></td>
							</tr>
							<?php 
							} 
						}?>
						</tbody>
					</table>
       			</form>
    		</div>
    	</div>
    <?php	
   	}
	}
?>
</div>