<?php
MJ_hmgt_browser_javascript_check();
$obj_invoice= new MJ_hmgt_invoice();
if($active_tab == 'taxlist')
{
	?>
	<script type="text/javascript">
	jQuery(document).ready(function()
	{
		"use strict";
		jQuery('#tbltax').DataTable({
			"responsive": true,
			"order": [[ 1, "asc" ]],		
			 "aoColumns":[
						  {"bSortable": false},
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
			<table id="tbltax" class="display" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th><input type="checkbox" class="select_all"></th>
						<th><?php esc_html_e( 'Tax Name', 'hospital_mgt' ) ;?></th>
						<th> <?php esc_html_e( 'Tax Value', 'hospital_mgt' ) ;?> (%)</th>
						<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
						<th></th>
						<th><?php esc_html_e( 'Tax Name', 'hospital_mgt' ) ;?></th>
						<th> <?php esc_html_e( 'Tax Value', 'hospital_mgt' ) ;?> (%)</th>
						<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
					</tr>
				</tfoot>	 
				<tbody>
					<?php 
					foreach ($obj_invoice->MJ_hmgt_get_all_tax_data() as $retrieved_data)
					{ 							
					?>
					<tr>
						<td class="title"><input type="checkbox" name="selected_id[]" class="sub_chk" value="<?php echo esc_attr($retrieved_data->tax_id); ?>"></td>
						<td class=""><?php echo esc_html($retrieved_data->tax_title); ?></td>
						<td class=""><?php echo esc_html($retrieved_data->tax_value); ?></td>							
						<td class="action">		
							<?php if($user_access_edit == 1)
							{?>						
							<a href="?page=hmgt_invoice&tab=addtax&action=edit&tax_id=<?php echo esc_attr($retrieved_data->tax_id);?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>							
							<?php 
							} 
							?>
							<?php if($user_access_delete == 1)
							{?>	
							<a href="?page=hmgt_invoice&tab=taxlist&action=delete&tax_id=<?php echo esc_attr($retrieved_data->tax_id);?>" class="btn btn-danger" onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this record?','hospital_mgt');?>');"><?php esc_html_e( 'Delete', 'hospital_mgt' ) ;?> </a>
							<?php 
							} ?>
						</td>
					</tr>
					<?php
					} 						
					?>				 
				</tbody>
			</table>
			<?php if($user_access_delete == 1)
							{?>	
			<div class="print-button pull-left">
				<input  type="submit" value="<?php esc_html_e('Delete Selected','hospital_mgt');?>" name="delete_selected4" class="btn btn-danger"/>
			</div>
			<?php 
							} ?>
		</div><!-- TABLE RESPONSIVE DIV END -->
    </div><!-- PANEL BODY DIV END-->
    </form>
<?php 
} 
?>