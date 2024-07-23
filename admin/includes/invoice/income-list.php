<?php
MJ_hmgt_browser_javascript_check();
$obj_invoice= new MJ_hmgt_invoice();
if($active_tab == 'incomelist')
{
	$invoice_id=0;
	if(isset($_REQUEST['income_id']))
		$invoice_id=$_REQUEST['income_id'];
	$edit=0;
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
	{
		$edit=1;
		$result = $obj_invoice->MJ_hmgt_get_invoice_data($invoice_id);
	}?>
	<script type="text/javascript">
	jQuery(document).ready(function() {
		"use strict";
		jQuery('#tblincome').DataTable({
			"responsive": true,
			 "order": [[ 4, "desc" ]],
			 "aoColumns":[
						  {"bSortable": false},
						  {"bSortable": true},
						  {"bSortable": true},
						  {"bSortable": true},
						  {"bSortable": true}, 
						  {"bSortable": true}, 	                                   
						  {"bSortable": true}, 	                                   
						  {"bSortable": false}
					   ],
			language:<?php echo MJ_hmgt_datatable_multi_language();?>
			});
		$('.select_all').on('click', function(e)
		{
			 if($(this).is(':checked',true))  
			 {
				$(".sub_chk").prop('checked', true);  
			 }  
			 else  
			 {  
				$(".sub_chk").prop('checked',false);  
			 } 
		});
	
		$('.sub_chk').on('change',function()
		{ 
			if(false == $(this).prop("checked"))
			{ 
				$(".select_all").prop('checked', false); 
			}
			if ($('.sub_chk:checked').length == $('.sub_chk').length )
			{
				$(".select_all").prop('checked', true);
			}
	  	});
	} );
	</script>
	<form name="wcwm_report" action="" method="post">
	<div class="panel-body"><!-- PANEL BODY DIV START-->
		<div class="table-responsive"><!-- TABLE RESPONSIVE DIV START-->
			<table id="tblincome" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th><input type="checkbox" class="select_all"></th>
						<th><?php esc_html_e( 'Invoice ID', 'hospital_mgt' ) ;?></th>
						<th> <?php esc_html_e( 'Patient Id', 'hospital_mgt' ) ;?></th>
						<th> <?php esc_html_e( 'Patient Name', 'hospital_mgt' ) ;?></th>
						<th> <?php esc_html_e( 'Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
						<th> <?php esc_html_e( 'Date', 'hospital_mgt' ) ;?></th>
						<th> <?php esc_html_e( 'Payment Method', 'hospital_mgt' ) ;?></th>
						<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th></th>
						<th><?php esc_html_e( 'Invoice ID', 'hospital_mgt' ) ;?></th>
						<th> <?php esc_html_e( 'Patient Id', 'hospital_mgt' ) ;?></th>
						<th> <?php esc_html_e( 'Patient Name', 'hospital_mgt' ) ;?></th>
						<th> <?php esc_html_e( 'Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
						<th> <?php esc_html_e( 'Date', 'hospital_mgt' ) ;?></th>
						<th> <?php esc_html_e( 'Payment Method', 'hospital_mgt' ) ;?></th>
						<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
					</tr>
				</tfoot>	 
				<tbody>
				 <?php 				
					foreach ($obj_invoice->MJ_hmgt_get_all_income_data() as $retrieved_data)
					{ 
						$all_entry=json_decode($retrieved_data->income_entry);
						
						$total_amount=0;
						foreach($all_entry as $entry)
						{
							$total_amount+= (int)$entry->amount;
						}
						
						
					?>
					<tr>
						<td class="title"><input type="checkbox" name="selected_id[]" class="sub_chk" value="<?php echo esc_attr($retrieved_data->income_id); ?>"></td>
						<td class=""><?php 
						$obj_invoice= new MJ_hmgt_invoice();
						if(!empty($retrieved_data->invoice_id))
						{
							$invoice_data=$obj_invoice->MJ_hmgt_get_invoice_data($retrieved_data->invoice_id);
							if(isset($invoice_data->invoice_number))
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
								echo "-";
							}
						}
						else
						{
							echo '-'; 
						}
						?></td>
						<td class="patient"><?php echo $patient_id=get_user_meta($retrieved_data->party_name, 'patient_id', true);?></td>
						<td class="patient_name"><?php 
						$user=get_userdata($retrieved_data->party_name); 
						
						if(!empty($user->display_name))
						{
							echo esc_html($user->display_name);
						}
						else
						{
							echo "-";
						}
						?></td>
						<td class="income_amount"><?php echo esc_html($total_amount);?></td>
						<td class="status"><?php echo date(MJ_hmgt_date_formate(),strtotime($retrieved_data->income_create_date));?></td>
						<td class=""><?php echo esc_html__("$retrieved_data->payment_method","hospital_mgt");?></td>
						<td class="action">
						<a  href="javascript:void(0);" class="show-invoice-popup btn btn-default" idtest="<?php echo esc_attr($retrieved_data->income_id); ?>" invoice_type="income">
						<i class="fa fa-eye"></i> <?php esc_html_e('View Income', 'hospital_mgt');?></a>
						<?php
						if(empty($retrieved_data->invoice_id))
						{
							if($user_access_edit == 1)
												{
						?>
							<a href="?page=hmgt_invoice&tab=addincome&action=edit&income_id=<?php echo esc_attr($retrieved_data->income_id);?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
						<?php
						}
						}
						if($user_access_delete == 1)
						{
						?>
						<a href="?page=hmgt_invoice&tab=incomelist&action=delete&income_id=<?php echo esc_attr($retrieved_data->income_id);?>" class="btn btn-danger" 
						onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this record?','hospital_mgt');?>');">
						<?php esc_html_e( 'Delete', 'hospital_mgt' ) ;?> </a>
						<?php 
						} ?>
						</td>
					</tr>
					<?php
					} 						
					?>				 
				</tbody>
			</table>
			<?php
			if($user_access_delete == 1)
						{
							?>
			<div class="print-button pull-left">
				<input  type="submit" value="<?php esc_html_e('Delete Selected','hospital_mgt');?>" name="delete_selected2" class="btn btn-danger"/>
			</div>
				<?php 
						} ?>
		</div><!-- TABLE RESPONSIVE DIV END-->
	</div><!-- PANEL BODY DIV END-->
	 </form>
<?php  
}
?>