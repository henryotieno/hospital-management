<?php
if($active_tab == 'assigned_instrumentlist')
{ ?>	
    <script type="text/javascript">
	jQuery(document).ready(function() {
		"use strict";
		jQuery('#instrument_list').DataTable({
			"responsive": true,
			"order": [[ 1, "asc" ]],
			"dom": 'Bfrtip',
			"buttons": [
				'colvis'
			], 
			"aoColumns":[
						  {"bSortable": false},
						  {"bSortable": true},
						  {"bSortable": true},
						  {"bSortable": true},
						  {"bSortable": true},              	                 
						  {"bSortable": true},              	                 
						  {"bSortable": true},              	                 
						  {"bSortable": true},              	                 
						  {"bSortable": false}],
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
	});
	</script>
    <form name="wcwm_report" action="" method="post">    
        <div class="panel-body"><!-- PANEL BODY DIV START -->		
        	<div class="table-responsive"><!-- TABLE RESPONSIVE DIV START-->		
				<table id="instrument_list" class="display" cellspacing="0" width="100%">
					<thead>
						<tr>
						<th><input type="checkbox" class="select_all"></th>
						<th><?php esc_html_e( 'Patient', 'hospital_mgt' ) ;?></th>
						 <th><?php esc_html_e( 'Instrument Code & Name', 'hospital_mgt' ) ;?></th>
						 <th><?php esc_html_e( 'Assigned Date', 'hospital_mgt' ) ;?></th>
						 <th><?php esc_html_e( 'Expected Return Date', 'hospital_mgt' ) ;?></th>
						  <th><?php esc_html_e( 'Charges Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
						  <th><?php esc_html_e( 'Tax Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
						  <th><?php esc_html_e( 'Total Charges Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
						  <th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
						</tr>
					</thead>
					<tfoot>
						<tr>
						<th></th>
						 <th><?php esc_html_e( 'Patient', 'hospital_mgt' ) ;?></th>
						<th><?php esc_html_e( 'Instrument Code & Name', 'hospital_mgt' ) ;?></th>
						 <th><?php esc_html_e( 'Assigned Date', 'hospital_mgt' ) ;?></th>
						 <th><?php esc_html_e( 'Expected Return Date', 'hospital_mgt' ) ;?></th>
						 <th><?php esc_html_e( 'Charges Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
						 <th><?php esc_html_e( 'Tax Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
						 <th><?php esc_html_e( 'Total Charges Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
						 <th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
						</tr>
					</tfoot>
			 
					<tbody>
					 <?php 
						$assigned_instrumentdata=$obj_instrument->MJ_hmgt_get_all_assigned_instrument();
					
						if(!empty($assigned_instrumentdata))
						{
							foreach ($assigned_instrumentdata as $retrieved_data)
							{ 
								$patientdata=get_userdata($retrieved_data->patient_id);
					?>		
								<tr>
									<td class="title"><input type="checkbox" name="selected_id[]" class="sub_chk" value="<?php echo esc_attr($retrieved_data->id); ?>"></td>
									<td class="bed_number"><a href="?page=hmgt_instrument_mgt&tab=assigninstrument&action=edit&assign_instument_id=<?php echo $retrieved_data->id;?>">
									<?php  
									if(isset($patientdata->display_name))
									{
										echo esc_html($patientdata->display_name); 
									}
									else
									{
										echo "-";
									}
									?>
									</a></td>
									<td class="bed_type"><?php 
									$instrumentdata=$obj_instrument->MJ_hmgt_get_single_instrument($retrieved_data->instrument_id);
									if(isset($instrumentdata->instrument_name))
									{
										$intrument_data=$instrumentdata->instrument_code .'=>'.esc_html($instrumentdata->instrument_name);
									}
									else
									{
										$intrument_data='-';
										
									}
									echo $intrument_data;
									?></td>
									
									<td class="descrition"><?php  echo date(MJ_hmgt_date_formate(),strtotime($retrieved_data->start_date));	?></td>
									
									<td class="descrition"><?php if($retrieved_data->end_date!="0000-00-00" && "00/00/0000" && "00/00/0000"){ echo date(MJ_hmgt_date_formate(),strtotime($retrieved_data->end_date)); }?></td>
									<td class="charge">	<?php echo number_format($retrieved_data->charges_amount, 2, '.', ''); ?></td>
									<td class="charge">	<?php echo number_format($retrieved_data->total_tax, 2, '.', ''); ?></td>
									<td class="charge">	<?php echo number_format($retrieved_data->total_charge_amount, 2, '.', ''); ?></td>
									<td class="action"> 
									<a href="javascript:void(0);" class="view_details_popup btn btn-default" id="<?php echo esc_attr( $retrieved_data->id); ?>" type="<?php echo 'assigned_instrument';?>"><i class="fa fa-eye"> </i> <?php esc_html_e('View', 'hospital_mgt' ) ;?> </a>
									<?php if($user_access_edit == 1)
														{?>
									<a href="?page=hmgt_instrument_mgt&tab=assigninstrument&action=edit&assign_instument_id=<?php echo esc_attr($retrieved_data->id);?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
									<?php 
												} 
												?>
												<?php if($user_access_delete == 1)
												{?>	
									<a href="?page=hmgt_instrument_mgt&tab=instrumentlist&action=delete&assign_instument_id=<?php echo esc_attr($retrieved_data->id);?>" class="btn btn-danger" 
									onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this record?','hospital_mgt');?>');">
									<?php esc_html_e( 'Delete', 'hospital_mgt' ) ;?> </a>  
<?php 
												 } ?>										
									</td>
								   
								</tr>
						<?php 
							} 
					}?>
					</tbody>
				</table>
				<?php 
									if($user_access_delete == 1)
									{?>
				<div class="print-button pull-left">
					<input  type="submit" value="<?php esc_html_e('Delete Selected','hospital_mgt');?>" name="delete_selected2" class="btn btn-danger delete_selected "/>
				</div>
				<?php 
										} ?>
			</div><!-- TABLE RESPONSIVE DIV END -->		
        </div> <!-- PANEL BODY DIV END -->		      
	</form>
    <?php 
} ?>