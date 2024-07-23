<?php
$obj = new MJ_hmgt_prescription();
?>	
<script type="text/javascript">
jQuery(document).ready(function($) {
	"use strict";
	jQuery('#dispatchlist').DataTable({
		"responsive": true,
		"order": [[ 1, "asc" ]],
		"aoColumns":[
	                  {"bSortable": false},
	                  {"bSortable": true},
	                  {"bSortable": true},
	                  {"bSortable": true},
	                  {"bSortable": true},
	                  {"bSortable": true},
	                  {"bSortable": true},              	                 
	                  {"bSortable": false}],
		language:<?php echo MJ_hmgt_datatable_multi_language();?>			  
		});
} );
</script>
<form name="dispatchlist" action="" method="post">
    <div class="panel-body"><!--PANEL BODY DIV START-->
        <div class="table-responsive"><!--TABLE RESPONSIVE DIV START-->
			<table id="dispatchlist" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th><input type="checkbox" class="select_all"></th>
						<th><?php esc_html_e( 'Patient Name', 'hospital_mgt' ) ;?></th>
						<th><?php esc_html_e( 'Prescription', 'hospital_mgt' ) ;?></th>
						<th><?php esc_html_e( 'Medicine Price', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th> 
						<th><?php esc_html_e( 'Discount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th> 
						<th><?php esc_html_e( 'Tax Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th> 
						<th><?php esc_html_e( 'Sub Total', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th> 
						<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
					</tr>
				</thead>
				<tfoot>
						 <tr>
						 	<th></th>
							<th><?php esc_html_e( 'Patient Name', 'hospital_mgt' ) ;?></th>
							<th><?php esc_html_e( 'Prescription', 'hospital_mgt' ) ;?></th>
							<th><?php esc_html_e( 'Medicine Price', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th> 
							<th><?php esc_html_e( 'Discount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th> 
							<th><?php esc_html_e( 'Tax Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th> 
							<th><?php esc_html_e( 'Sub Total', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th> 
							<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
						</tr>
				</tfoot>		 
				<tbody>
					 <?php 
					$medicinedata=$obj_medicine->MJ_hmgt_get_all_dispatch_medicine();
					
					if(!empty($medicinedata))
					{
						foreach ($medicinedata as $retrieved_data)
						{
							$prescriptiondata = $obj->MJ_hmgt_get_prescription_data($retrieved_data->prescription_id);
						?>
						<tr>
							<td class="title"><input type="checkbox" name="selected_id[]" class="sub_chk" value="<?php echo esc_attr($retrieved_data->id); ?>"></td>
							<td class="">
							<?php
							
							if(!empty(MJ_hmgt_get_display_name($retrieved_data->patient)))
							{	
								echo MJ_hmgt_get_display_name($retrieved_data->patient);	
							}
							else
							{
								echo "-";
							}
							
							?></td>
							<td class=""><?php
							if(isset($prescriptiondata->patient_id))
							{
								echo MJ_hmgt_get_display_name($prescriptiondata->patient_id) .' - '.$prescriptiondata->pris_create_date; 
							}
							else
							{
								echo "-";
							}
							
							?></td>
							<td class=""><?php  echo esc_html($retrieved_data->med_price);	?></td>
							<td class=""><?php  echo esc_html($retrieved_data->discount);	?></td>
							<td class=""><?php  echo esc_html($retrieved_data->total_tax_amount);	?></td>							
							<td class=""><?php echo esc_html($retrieved_data->sub_total);?></td>
							
							<td class="action"> 
							<?php if($user_access_edit == 1)
							{?>
							<a href="?page=hmgt_medicine&tab=dispatch-medicine&&action=edit&dispatch_id=<?php echo esc_attr($retrieved_data->id);?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
							<?php 
							} 
							?>
							<?php if($user_access_delete == 1)
							{?>	
							<a href="?page=hmgt_medicine&tab=medicinelist&action=delete&dispatch_id=<?php echo esc_attr($retrieved_data->id);?>" class="btn btn-danger" 
							onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this record?','hospital_mgt');?>');">
							<?php esc_html_e( 'Delete', 'hospital_mgt' ) ;?> </a>
							<?php 
							 } ?>
							</td>
						   
						</tr>
						<?php 
						} 						
					}
					?>				 
				</tbody>        
            </table>
			 <?php if($user_access_delete == 1)
			{?>	
            <div class="print-button pull-left">
				<input  type="submit" value="<?php esc_html_e('Delete Selected','hospital_mgt');?>" name="delete_selected2" class="btn btn-danger delete_selected "/>
			</div>
			<?php 
			} 
			?>
        </div><!--TABLE RESPONSIVE DIV END-->
    </div><!--PANEL BODY DIV END-->
</form>