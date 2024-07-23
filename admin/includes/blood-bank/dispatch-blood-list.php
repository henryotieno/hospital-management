<?php
$obj_bloodbank= new MJ_hmgt_bloodbank();
$active_tab = isset($_GET['tab'])?$_GET['tab']:'dispatchbloodlist';
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	"use strict";
	jQuery('#dispatch_bood').DataTable({
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
	<div class="panel-body"><!-- PANEL BODY DIV START-->
		<form name="wcwm_report" action="" method="post">
			<div class="panel-body"><!-- PANEL BODY DIV START-->
				<div class="table-responsive"><!-- TABLE RESPONSIVE DIV START-->
					<table id="dispatch_bood" class="display" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th><input type="checkbox" class="select_all"></th>
								<th><?php esc_html_e('Patient Name', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Blood Group', 'hospital_mgt' ) ;?></th>								
								<th><?php esc_html_e( 'Number Of Bags', 'hospital_mgt' ) ;?></th> 
								<th><?php esc_html_e( 'Date', 'hospital_mgt' ) ;?></th> 
								<th><?php esc_html_e( 'Charges', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
								<th><?php esc_html_e( 'Tax Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
								<th><?php esc_html_e( 'Total Charges', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
								<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th></th>
								<th><?php esc_html_e('Patient Name', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Blood Group', 'hospital_mgt' ) ;?></th>								
								<th><?php esc_html_e( 'Number Of Bags', 'hospital_mgt' ) ;?></th> 
								<th><?php esc_html_e( 'Date', 'hospital_mgt' ) ;?></th> 
								<th><?php esc_html_e( 'Charges', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
								<th><?php esc_html_e( 'Tax Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
								<th><?php esc_html_e( 'Total Charges', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
								<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
							</tr>
						</tfoot>			 
						<tbody>
						 <?php 
						$dispatch_blooddata=$obj_bloodbank->MJ_hmgt_get_all_dispatch_blood_data();
						 if(!empty($dispatch_blooddata))
						 {
							foreach($dispatch_blooddata as $retrieved_data)
							{ 
								$patient_data =	MJ_hmgt_get_user_detail_byid($retrieved_data->patient_id);
								?>
								<tr>
									<td class="title"><input type="checkbox" name="selected_id[]" class="sub_chk" value="<?php echo esc_attr($retrieved_data->dispatchblood_id); ?>"></td>							
									<td class="patient"><?php echo esc_html($patient_data['first_name']." ".$patient_data['last_name']."(".$patient_data['patient_id'].")");?></td>
									<td class="bloodgroup">
									<?php 							
										echo esc_html__("$retrieved_data->blood_group","hospital_mgt");
									?>
									</td>							
									<td class="subject_name"><?php  echo esc_html($retrieved_data->blood_status);?></td>
								  <td class=""><?php echo date(MJ_hmgt_date_formate(),strtotime(esc_html($retrieved_data->date))); ?></td>
									<td class=""><?php  echo esc_html($retrieved_data->blood_charge);?></td>
									<td class=""><?php  echo esc_html($retrieved_data->total_tax);?></td>
									<td class=""><?php  echo esc_html($retrieved_data->total_blood_charge);?></td>
									<td class="action">
									<?php if($user_access_edit == 1)
									{?>
									<a href="?page=hmgt_bloodbank&tab=adddispatchblood&action=edit&dispatchblood_id=<?php echo esc_attr($retrieved_data->dispatchblood_id);?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
									<?php 
									} 
									?>
									<?php if($user_access_delete == 1)
									{?>
									<a href="?page=hmgt_bloodbank&tab=dispatchbloodlist&action=delete&dispatchblood_id=<?php echo esc_attr($retrieved_data->dispatchblood_id);?>" class="btn btn-danger" 
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
						<input  type="submit" value="<?php esc_html_e('Delete Selected','hospital_mgt');?>" name="delete_selected3" class="btn btn-danger delete_selected "/>
					</div>
					<?php 
					} ?>
				</div><!-- TABLE RESPONSIVE DIV END-->
			</div><!-- PANEL BODY DIV END-->
	    </form>
    </div><!-- PANEL BODY DIV END-->