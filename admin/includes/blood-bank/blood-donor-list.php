<?php
$obj_bloodbank= new MJ_hmgt_bloodbank();
$active_tab = isset($_GET['tab'])?$_GET['tab']:'bloodbanklist';
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	"use strict";
	jQuery('#bood_doner').DataTable({
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
});
</script>
	<div class="panel-body"><!-- PANEL BODY DIV START-->
		<form name="wcwm_report" action="" method="post">
			<div class="panel-body"><!-- PANEL BODY DIV START-->
				<div class="table-responsive"><!-- TABLE RESPONSIVE DIV START-->
					<table id="bood_doner" class="display" cellspacing="0" width="100%">
						<thead>
							<tr>
								<th><input type="checkbox" class="select_all"></th>
								<th><?php esc_html_e('Name', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Blood Group', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Age', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Gender', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Number Of Bags', 'hospital_mgt' ) ;?></th> 
								<th><?php esc_html_e( 'Last Donation Date', 'hospital_mgt' ) ;?></th> 
								<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th></th>
								<th><?php esc_html_e( 'Name', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Blood Group', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Age', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Gender', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Number Of Bags', 'hospital_mgt' ) ;?></th> 
								<th><?php esc_html_e( 'Last Donation Date', 'hospital_mgt' ) ;?></th> 
								<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
							</tr>
						</tfoot>
			 
						<tbody>
						 <?php 
						$blooddonordata=$obj_bloodbank->MJ_hmgt_get_all_blooddonors();
						if(!empty($blooddonordata))
						{
							foreach($blooddonordata as $retrieved_data)
							{ 
							?>
							<tr>
								<td class="title"><input type="checkbox" name="selected_id[]" class="sub_chk" value="<?php echo esc_attr($retrieved_data->bld_donor_id); ?>"></td>
							
								<td class="name"><a href="?page=hmgt_bloodbank&tab=addbloodbank&action=edit&blooddonor_id=<?php echo $retrieved_data->bld_donor_id;?>"><?php echo esc_html($retrieved_data->donor_name);?></a></td>
								<td class="bloodgroup">
								<?php 										
										echo esc_html__("$retrieved_data->blood_group","hospital_mgt");
								?></td>
								<td class="age"><?php echo esc_html($retrieved_data->donor_age);?></td>
								<td class="age"><?php echo esc_html__($retrieved_data->donor_gender,"hospital_mgt");?></td>
								<td class="subject_name"><?php 
								if(!empty($retrieved_data->blood_status))
								{   
									echo esc_html($retrieved_data->blood_status);
								}
								else
								{
									echo '-'; 
								}
								?></td>
							  <td class="lastdonate_date"><?php if(!empty($retrieved_data-> last_donet_date!='0000-00-00')) { echo date(MJ_hmgt_date_formate(),strtotime($retrieved_data-> last_donet_date)); }else{ echo '-'; } ?></td>
								<td class="action"> 
								<a href="javascript:void(0);" class="view_details_popup btn btn-default" id="<?php echo esc_attr( $retrieved_data->bld_donor_id); ?>" type="<?php echo 'view_blooddonor';?>"><i class="fa fa-eye"> </i> <?php esc_html_e('View', 'hospital_mgt' ) ;?> </a>
								<?php if($user_access_edit == 1)
								{?>
								<a href="?page=hmgt_bloodbank&tab=addblooddonor&action=edit&blooddonor_id=<?php echo esc_attr($retrieved_data->bld_donor_id);?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
								<?php 
								} 
								?>
								<?php if($user_access_delete == 1)
								{?>	
								<a href="?page=hmgt_bloodbank&tab=bloodbanklist&action=delete&blooddonor_id=<?php echo esc_attr($retrieved_data->bld_donor_id);?>" class="btn btn-danger" 
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
					<?php if($user_access_delete == 1)
								{?>	
					<div class="print-button pull-left">
						<input  type="submit" value="<?php esc_html_e('Delete Selected','hospital_mgt');?>" name="delete_selected2" class="btn btn-danger delete_selected "/>
					</div>
					<?php 
								} ?>
				</div><!-- TABLE RESPONSIVE DIV END-->
			</div><!-- PANEL BODY DIV END-->
	    </form>
    </div><!-- PANEL BODY DIV END-->