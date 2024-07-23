<?php
//Treatment
$obj_treatment = new MJ_hmgt_treatment();
$user_role=MJ_hmgt_get_roles(get_current_user_id());
if($user_role == 'administrator')
{
	$user_access_add=1;
	$user_access_edit=1;
	$user_access_delete=1;
	$user_access_view=1;
}
else
{
	$user_access=MJ_hmgt_get_access_right_for_management_user_page('treatment');
	$user_access_add=$user_access['add'];
	$user_access_edit=$user_access['edit'];
	$user_access_delete=$user_access['delete'];
	$user_access_view=$user_access['view'];
	
	
if (isset ( $_REQUEST ['page'] ))
{	
	if($user_access_view == '0')
	{	
		MJ_hmgt_access_right_page_not_access_message_admin();
		die;
	}
	if(!empty($_REQUEST['action']))
	{
		if (isset ( $_REQUEST ['page'] ) && 'treatment' == $user_access['page_link'] && ($_REQUEST['action']=='edit'))
		{
			if($user_access['edit']=='0')
			{	
				MJ_hmgt_access_right_page_not_access_message_admin();
				die;
			}			
		}
		if (isset ( $_REQUEST ['page'] ) && 'treatment' == $user_access['page_link'] && ($_REQUEST['action']=='delete'))
		{
			if($user_access['delete']=='0')
			{	
				MJ_hmgt_access_right_page_not_access_message_admin();
				die;
			}	
		}
		if (isset ( $_REQUEST ['page'] ) && 'treatment' == $user_access['page_link'] && ($_REQUEST['action']=='insert')) 
		{
			if($user_access['add']=='0')
			{	
				MJ_hmgt_access_right_page_not_access_message_admin();
				die;
			}	
		} 
	}
  }
}
$active_tab = isset($_GET['tab'])?$_GET['tab']:'treatmentlist';
?>
<div class="page-inner min_height_1631">  <!--PAGE INNNER DIV START-->
     <!--PAGE TITLE START-->
    <div class="page-title">
		<h3><img src="<?php echo esc_url(get_option( 'hmgt_hospital_logo', 'hospital_mgt' )) ?>" class="img-circle head_logo" width="40" height="40" /><?php echo esc_html(get_option('hmgt_hospital_name','hospital_mgt'));?></h3>
	</div>  <!--PAGE TITLE END-->
	<?php 
	//-------------------- SAVE TREATMENT ------------------//
	if(isset($_REQUEST['save_treatment']))
	{
		$nonce = $_POST['_wpnonce'];
		if (wp_verify_nonce( $nonce, 'save_treatment_nonce' ) )
		{	
			if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'insert' || $_REQUEST['action'] == 'edit'))
			{
		
				$result = $obj_treatment->MJ_hmgt_add_treatment($_POST);
				if($result)
				{
					if($_REQUEST['action'] == 'edit')
					{
						wp_redirect ( admin_url().'admin.php?page=hmgt_treatment&tab=treatmentlist&message=2');
					}
					else
					{
						wp_redirect ( admin_url().'admin.php?page=hmgt_treatment&tab=treatmentlist&message=1');
					}	
				}
			}
		}
	}
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'delete')
	{
		$result = $obj_treatment->MJ_hmgt_delete_treatment($_REQUEST['treatment_id']);
		if($result)
		{
			wp_redirect ( admin_url().'admin.php?page=hmgt_treatment&tab=treatmentlist&message=3');
		}
	}
	if(isset($_REQUEST['delete_selected']))
	{		
		if(!empty($_REQUEST['selected_id']))
		{
			
			foreach($_REQUEST['selected_id'] as $id)
			{
				$result=$obj_treatment->MJ_hmgt_delete_treatment($id);
			}
			if($result)
			{
				wp_redirect ( admin_url().'admin.php?page=hmgt_treatment&tab=treatmentlist&message=3');
			}
		}
		else
		{
			echo '<script language="javascript">';
			echo 'alert("'.esc_html__('Please select at least one record.','hospital_mgt').'")';
			echo '</script>';
		}
	}
	if(isset($_REQUEST['message']))
	{
		$message =$_REQUEST['message'];
		if($message == 1)
		{?>
				<div id="message" class="updated below-h2 notice is-dismissible">
				<p>
				<?php 
					esc_html_e('Record inserted successfully','hospital_mgt');
				?></p></div>
				<?php 
			
		}
		elseif($message == 2)
		{?><div id="message" class="updated below-h2 notice is-dismissible"><p><?php
					esc_html_e("Record updated successfully.",'hospital_mgt');
					?></p>
					</div>
				<?php 
			
		}
		elseif($message == 3) 
		{?>
		<div id="message" class="updated below-h2 notice is-dismissible"><p>
		<?php 
			esc_html_e('Record deleted successfully','hospital_mgt');
		?></div></p><?php
				
		}
	}
	?>
	 <!--WRAPPER DIV START-->
	<div id="main-wrapper">
		<div class="row"><!--ROW DIV START-->
			<div class="col-md-12">
			 <!--PANEL WHITE DIV START-->
				<div class="panel panel-white">
				<!--PANEL BODY DIV START-->
					<div class="panel-body">
						<h2 class="nav-tab-wrapper">
							<a href="?page=hmgt_treatment&tab=treatmentlist" class="nav-tab <?php echo $active_tab == 'treatmentlist' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-menu"></span> '.esc_html__('Treatment List', 'hospital_mgt'); ?></a>
							
							<?php  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
							{?>
							<a href="?page=hmgt_treatment&tab=addtreatment&&action=edit&treatment_id=<?php echo $_REQUEST['treatment_id'];?>" class="nav-tab <?php echo $active_tab == 'addtreatment' ? 'nav-tab-active' : ''; ?>">
							<?php esc_html_e('Edit Treatment', 'hospital_mgt'); ?></a>  
							<?php 
							}
							else
							{	if($user_access_add == 1)
							{?>
								<a href="?page=hmgt_treatment&tab=addtreatment" class="nav-tab <?php echo $active_tab == 'addtreatment' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-plus-alt"></span> '.esc_html__('Add New Treatment', 'hospital_mgt'); ?></a>  
							<?php  } }?>
						   
						</h2>
						 <?php 
						//Report 1 
						if($active_tab == 'treatmentlist')
						{ 
						
						?>	
						<script type="text/javascript">
						jQuery(document).ready(function() {
							"use strict";
							jQuery('#treatment_list').DataTable({
								"responsive": true,
								"order": [[ 1, "asc" ]],
								"aoColumns":[
											  {"bSortable": false},
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
						} );
						</script>
						<form name="treatment" action="" method="post">						
							<div class="panel-body"><!-- START PANEL BODY DIV-->		
								<div class="table-responsive">	<!-- START TABLE RESPONSIVE DIV-->		
									<table id="treatment_list" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th><input type="checkbox" class="select_all"></th>
												<th><?php esc_html_e( 'Treatment Name', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e( 'Price', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
												<th><?php  esc_html_e( 'Tax', 'hospital_mgt' ) ;?></th>
												<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
											</tr>
									    </thead>
							 
										<tfoot>
											<tr>
												<th></th>
												<th><?php esc_html_e( 'Treatment Name', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e( 'Price', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
												<th><?php  esc_html_e( 'Tax', 'hospital_mgt' ) ;?></th>
												<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
											</tr>
										</tfoot>
							 
										<tbody>
										 <?php 
										$treatment_data=$obj_treatment->MJ_hmgt_get_all_treatment();
										if(!empty($treatment_data))
										{
										foreach ($treatment_data as $retrieved_data)
										{ 
										 ?>
											<tr>
												<td class="title"><input type="checkbox" name="selected_id[]" class="sub_chk" value="<?php echo esc_attr($retrieved_data->treatment_id); ?>"></td>
												<td class="treatment_name"><?php echo esc_html($retrieved_data->treatment_name);?></td>
												<td class="treatment_price"><?php echo esc_html($retrieved_data->treatment_price);?></td>                
												<td class="tax"><?php
												if(!empty($retrieved_data->tax))
												{
													echo MJ_hmgt_tax_name_array_by_tax_id_array($retrieved_data->tax);
												}
												else
												{
													echo '-';	
												}	
												?></td>                
												<td class="action"> 
												<?php if($user_access_edit == 1)
														{?>
													<a href="?page=hmgt_treatment&tab=addtreatment&action=edit&treatment_id=<?php echo esc_attr($retrieved_data->treatment_id);?>" class="btn btn-info"> 
													<?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
													<?php 
														} 
														?>
                                                        <?php if($user_access_delete == 1)
														{?>		
													<a href="?page=hmgt_treatment&tab=treatmentlist&action=delete&treatment_id=<?php echo esc_attr($retrieved_data->treatment_id);?>" class="btn btn-danger" 
													onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this record?','hospital_mgt');?>');">
													<?php esc_html_e( 'Delete', 'hospital_mgt' ) ;?> </a>
													<?php 
														 } ?>
												</td>
											</tr>
											<?php } 
										}?>
										</tbody>
									
									</table>
									<?php 
									if($user_access_delete == 1)
									{?>
									<div class="print-button pull-left">
										<input  type="submit" value="<?php esc_html_e('Delete Selected','hospital_mgt');?>" name="delete_selected" class="btn btn-danger delete_selected "/>
									</div>
									<?php 
										} ?>
							    </div>	<!-- END TABLE RESPONSIVE DIV-->		
							</div>	<!-- END PANEL BODY DIV-->							   
					    </form>
						 <?php 
						}
						if($active_tab == 'addtreatment')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/treatment/add_treatment.php';
						}
						?>
                    </div>	<!-- END PANEL BODY DIV-->		
		        </div><!-- END PANEL WHITE DIV-->
	        </div>
        </div><!-- END ROW DIV-->
	</div>
	<!-- END WRAPPER DIV-->
<?php  ?>