<?php
MJ_hmgt_browser_javascript_check();
$obj_invoice= new MJ_hmgt_invoice();
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
	$user_access=MJ_hmgt_get_access_right_for_management_user_page('invoice');
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
			if (isset ( $_REQUEST ['page'] ) && 'invoice' == $user_access['page_link'] && ($_REQUEST['action']=='edit'))
			{
				if($user_access['edit']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}			
			}
			if (isset ( $_REQUEST ['page'] ) && 'invoice' == $user_access['page_link'] && ($_REQUEST['action']=='delete'))
			{
				if($user_access['delete']=='0')
				{	
					MJ_hmgt_access_right_page_not_access_message_admin();
					die;
				}	
			}
			if (isset ( $_REQUEST ['page'] ) && 'invoice' == $user_access['page_link'] && ($_REQUEST['action']=='insert')) 
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
$active_tab = isset($_GET['tab'])?$_GET['tab']:'invoicelist';
?>
<!-- POP up code -->
<div class="popup-bg">
    <div class="overlay-content">
		<div class="modal-content">
			<div class="invoice_data"></div>			
		</div>
    </div>     
</div>
<!-- End POP-UP Code -->
<div class="page-inner min_height_1631"><!-- PAGE INNER DIV START-->
	<div class="page-title"><!-- PAGE TITLE DIV START-->
		<h3><img src="<?php echo esc_url(get_option( 'hmgt_hospital_logo', 'hospital_mgt' )) ?>" class="img-circle head_logo" width="40" height="40" /><?php echo esc_html(get_option('hmgt_hospital_name','hospital_mgt'));?></h3>
	</div><!-- PAGE TITLE DIV END-->
	<?php 
	//save tax
	if(isset($_POST['save_tax']))
	{	
		$nonce = $_POST['_wpnonce'];
		if (wp_verify_nonce( $nonce, 'save_tax_nonce' ) )
		{
			if($_REQUEST['action']=='edit')
			{				
				$result=$obj_invoice->MJ_hmgt_add_tax($_POST);
				if($result)	
				{			
					wp_redirect ( admin_url() . 'admin.php?page=hmgt_invoice&tab=taxlist&message=2');
				}				
			}
			else
			{		
				$result=$obj_invoice->MJ_hmgt_add_tax($_POST);
				
				if($result)
				{				
					wp_redirect ( admin_url() . 'admin.php?page=hmgt_invoice&tab=taxlist&message=1');
				}
			}
		}
	}
	if(isset($_POST['save_invoice']))
	{
		$nonce = $_POST['_wpnonce'];
		if (wp_verify_nonce( $nonce, 'save_invoice_nonce' ) )
		{
			global $wpdb;
			$table_hmgt_patient_transation = $wpdb->prefix . 'hmgt_patient_transation';
			if($_REQUEST['action']=='edit'){				
				$result=$obj_invoice->MJ_hmgt_add_invoice($_POST);
				if($result)	
				{			
					if(isset($_POST['transationdata']) && !empty($_POST['transationdata']))
					{
						foreach($_POST['transationdata'] as $transactionid)
						{					
							$wpdb->update($table_hmgt_patient_transation,array('status'=>'Paid'),array('id'=>$transactionid));
						}
					}				
					wp_redirect ( admin_url() . 'admin.php?page=hmgt_invoice&tab=invoicelist&message=2');
				}				
			}
			else
			{		
				$result=$obj_invoice->MJ_hmgt_add_invoice($_POST);
				
				if($result)
				{	
					if(isset($_POST['transationdata']) && !empty($_POST['transationdata']))
					{
						foreach($_POST['transationdata'] as $transactionid)
						{						
							$wpdb->update($table_hmgt_patient_transation,array('status'=>'Paid'),array('id'=>$transactionid));
						}
					}					
					wp_redirect ( admin_url() . 'admin.php?page=hmgt_invoice&tab=invoicelist&message=1');
				}
			}
		}
	}
		
	if(isset($_REQUEST['action'])&& $_REQUEST['action']=='delete')
	{
		if(isset($_REQUEST['invoice_id']))
		{
			$result=$obj_invoice->MJ_hmgt_delete_invoice($_REQUEST['invoice_id']);
			if($result)
			{
				wp_redirect ( admin_url() . 'admin.php?page=hmgt_invoice&tab=invoicelist&message=3');
			}
		}
		
		if(isset($_REQUEST['income_id']))
		{
			$result=$obj_invoice->MJ_hmgt_delete_income($_REQUEST['income_id']);
			if($result)
			{
				wp_redirect ( admin_url() . 'admin.php?page=hmgt_invoice&tab=incomelist&message=3');
			}
		}
			
		if(isset($_REQUEST['expense_id']))
		{
			$result=$obj_invoice->MJ_hmgt_delete_expense($_REQUEST['expense_id']);
			if($result)
			{
				wp_redirect ( admin_url() . 'admin.php?page=hmgt_invoice&tab=expenselist&message=3');
			}
		}
		if(isset($_REQUEST['tax_id']))
		{
			$result=$obj_invoice->MJ_hmgt_delete_tax($_REQUEST['tax_id']);
			if($result)
			{
				wp_redirect ( admin_url() . 'admin.php?page=hmgt_invoice&tab=taxlist&message=3');
			}
		}				
	}
	if(isset($_REQUEST['delete_selected']))
	{		
		if(!empty($_REQUEST['selected_id']))
		{
			
			foreach($_REQUEST['selected_id'] as $id)
			{
				$result=$obj_invoice->MJ_hmgt_delete_invoice($id);
			}
			if($result)
			{
				wp_redirect ( admin_url() . 'admin.php?page=hmgt_invoice&tab=invoicelist&message=3');
			}
		}
		else
		{
			echo '<script language="javascript">';
			echo 'alert("'.esc_html__('Please select at least one record.','hospital_mgt').'")';
			echo '</script>';
		}
	}
	if(isset($_REQUEST['delete_selected2']))
	{		
		if(!empty($_REQUEST['selected_id']))
		{
			
			foreach($_REQUEST['selected_id'] as $id)
			{
				$result=$obj_invoice->MJ_hmgt_delete_income($id);
			}
			if($result)
			{
				wp_redirect ( admin_url() . 'admin.php?page=hmgt_invoice&tab=incomelist&message=3');
			}
		}
		else
		{
			echo '<script language="javascript">';
			echo 'alert("'.esc_html__('Please select at least one record.','hospital_mgt').'")';
			echo '</script>';
		}
	}
	if(isset($_REQUEST['delete_selected3']))
	{		
		if(!empty($_REQUEST['selected_id']))
		{
			
			foreach($_REQUEST['selected_id'] as $id)
			{
				$result=$obj_invoice->MJ_hmgt_delete_expense($id);
			}
			if($result)
			{
				wp_redirect ( admin_url() . 'admin.php?page=hmgt_invoice&tab=expenselist&message=3');
			}
		}
		else
		{
			echo '<script language="javascript">';
			echo 'alert("'.esc_html__('Please select at least one record.','hospital_mgt').'")';
			echo '</script>';
		}
	}
	if(isset($_REQUEST['delete_selected4']))
	{		
		if(!empty($_REQUEST['selected_id']))
		{
			
			foreach($_REQUEST['selected_id'] as $id)
			{
				$result=$obj_invoice->MJ_hmgt_delete_tax($id);
			}
			if($result)
			{
				wp_redirect ( admin_url() . 'admin.php?page=hmgt_invoice&tab=taxlist&message=3');
			}
		}
		else
		{
			echo '<script language="javascript">';
			echo 'alert("'.esc_html__('Please select at least one record.','hospital_mgt').'")';
			echo '</script>';
		}
	}
	//--------save income-------------
	if(isset($_POST['save_income']))
	{
		$nonce = $_POST['_wpnonce'];
		if (wp_verify_nonce( $nonce, 'save_income_nonce' ) )
		{
			if($_REQUEST['action']=='edit')
			{
				$result=$obj_invoice->MJ_hmgt_add_income_admin($_POST);
				if($result)
				{
					wp_redirect ( admin_url() . 'admin.php?page=hmgt_invoice&tab=incomelist&message=2');
				}
			}
			else
			{
				$result=$obj_invoice->MJ_hmgt_add_income_admin($_POST);
				if($result)
				{
					wp_redirect ( admin_url() . 'admin.php?page=hmgt_invoice&tab=incomelist&message=1');
				}
			}
		}		
	}
	
	//--------save Expense-------------
	if(isset($_POST['save_expense']))
	{
		$nonce = $_POST['_wpnonce'];
		if (wp_verify_nonce( $nonce, 'save_expense_nonce' ) )
		{		
			if($_REQUEST['action']=='edit')
			{
					
				$result=$obj_invoice->MJ_hmgt_add_expense($_POST);
				if($result)
				{
					wp_redirect ( admin_url() . 'admin.php?page=hmgt_invoice&tab=expenselist&message=2');
				}
			}
			else
			{
				$result=$obj_invoice->MJ_hmgt_add_expense($_POST);
				if($result)
				{
					wp_redirect ( admin_url() . 'admin.php?page=hmgt_invoice&tab=expenselist&message=1');
				}
			}
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
		{?>
			<div id="message" class="updated below-h2 notice is-dismissible"><p><?php
				esc_html_e("Record updated successfully",'hospital_mgt');
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
	<div id="main-wrapper"><!-- MAIN WRAPER DIV START-->
		<div class="row"><!-- ROW DIV START-->
			<div class="col-md-12">
				<div class="panel panel-white"><!-- PANEL WHITE DIV START-->
					<div class="panel-body nav_tab_responsive_invoice"><!-- PANEL BODY DIV START-->
						<h2 class="nav-tab-wrapper">
							<a href="?page=hmgt_invoice&tab=invoicelist" class="nav-tab <?php echo $active_tab == 'invoicelist' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-menu"></span> '.esc_html__('Invoice List', 'hospital_mgt'); ?></a>
							
							<?php  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && isset($_REQUEST['invoice_id']))
							{?>
							<a href="?page=hmgt_invoice&tab=addinvoice&action=edit&invoice_id=<?php echo $_REQUEST['invoice_id'];?>" class="nav-tab <?php echo $active_tab == 'addinvoice' ? 'nav-tab-active' : ''; ?>">
							<?php esc_html_e('Edit Invoice', 'hospital_mgt'); ?></a>  
							<?php 
							}
							else
							{
							if($user_access_add == 1)
							{?>
								<a href="?page=hmgt_invoice&tab=addinvoice" class="nav-tab <?php echo $active_tab == 'addinvoice' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-plus-alt"></span> '.esc_html__('Add Invoice', 'hospital_mgt'); ?></a>  
							<?php 
							} 
							}?>
							<a href="?page=hmgt_invoice&tab=incomelist" class="nav-tab <?php echo $active_tab == 'incomelist' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-menu"></span> '.esc_html__('Income List', 'hospital_mgt'); ?></a>
							 <?php  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && isset($_REQUEST['income_id']))
							{?>
							<a href="?page=hmgt_invoice&tab=addincome&action=edit&income_id=<?php echo $_REQUEST['income_id'];?>" class="nav-tab <?php echo $active_tab == 'addincome' ? 'nav-tab-active' : ''; ?>">
							<?php esc_html_e('Edit Income', 'hospital_mgt'); ?></a>  
							<?php 
							}
							else
							{
							if($user_access_add == 1)
							{?>
							<a href="?page=hmgt_invoice&tab=addincome" class="nav-tab <?php echo $active_tab == 'addincome' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-plus-alt"></span> '.esc_html__('Add Income', 'hospital_mgt'); ?></a>  
							<?php 
							}
							}
							?>
							<a href="?page=hmgt_invoice&tab=expenselist" class="nav-tab <?php echo $active_tab == 'expenselist' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-menu"></span> '.esc_html__('Expense List', 'hospital_mgt'); ?></a>
							 <?php  
							 if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && isset($_REQUEST['expense_id']))
							{?>
							<a href="?page=hmgt_invoice&tab=addexpense&action=edit&expense_id=<?php echo $_REQUEST['expense_id'];?>" class="nav-tab <?php echo $active_tab == 'addexpense' ? 'nav-tab-active' : ''; ?>">
							<?php esc_html_e('Edit Expense', 'hospital_mgt'); ?></a>  
							<?php 
							}
							else
							{
							if($user_access_add == 1)
							{
							?>
							<a href="?page=hmgt_invoice&tab=addexpense" class="nav-tab <?php echo $active_tab == 'addexpense' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-plus-alt"></span> '.esc_html__('Add Expense', 'hospital_mgt'); ?></a>  
							<?php
							}
							}
							?>
							<a href="?page=hmgt_invoice&tab=taxlist" class="nav-tab <?php echo $active_tab == 'taxlist' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-menu"></span> '.esc_html__('Tax List', 'hospital_mgt'); ?></a>
							 <?php  
							 if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && isset($_REQUEST['tax_id']))
							{?>
							<a href="?page=hmgt_invoice&tab=addtax&action=edit&tax_id=<?php echo $_REQUEST['tax_id'];?>" class="nav-tab <?php echo $active_tab == 'addtax' ? 'nav-tab-active' : ''; ?>">
							<?php esc_html_e('Edit Tax', 'hospital_mgt'); ?></a>  
							<?php 
							}
							else
							{if($user_access_add == 1)
							{?>
								<a href="?page=hmgt_invoice&tab=addtax" class="nav-tab <?php echo $active_tab == 'addtax' ? 'nav-tab-active' : ''; ?>">
							<?php echo '<span class="dashicons dashicons-plus-alt"></span> '.esc_html__('Add Tax', 'hospital_mgt'); ?></a>  
							<?php
							} }
							?>
						</h2>
						
						<?php 						
						if($active_tab == 'invoicelist')
						{ 
						
						?>	
						<script type="text/javascript">
							jQuery(document).ready(function($) {
								"use strict";
								<?php
								if (is_rtl())
									{
									?>	
										$('#occupancy_report').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
									<?php
									}
									else{
										?>
										$('#occupancy_report').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
										<?php
									}
								?>
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

								$("body").on("click", ".hidetable_btn", function()
								{
									$(".hidetable").hide()
								});
							});
						</script>
						 <script type="text/javascript">
							jQuery(document).ready(function() {
								"use strict";
								jQuery('#tblinvoice').DataTable({
									"responsive": true,
									 "order": [[ 7, "desc" ]],
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
						<form name="wcwm_report" action="" method="post" id="invoice_list">
							<div class="panel-body"><!-- PANEL BODY DIV START-->
								<div class="table-responsive overflow_hidden"><!-- TABLE RESPONSIVE DIV START-->
									<div class="mb-3 row">	
										<div class="form-group col-md-3">
											<label for="sdate"><?php esc_html_e('Start Date','hospital_mgt');?></label>
											<input type="text"  class="form-control sdate " name="sdate"  value="<?php if(isset($_REQUEST['sdate'])) echo $_REQUEST['sdate'];elseif(isset($_POST['sdate'])) echo $_POST['sdate'];?>">	
										</div>
										<div class="form-group col-md-3">
											<label for="edate"><?php esc_html_e('End Date','hospital_mgt');?></label>
											<input type="text"  class="form-control edate" name="edate" value="<?php if(isset($_REQUEST['edate'])) echo $_REQUEST['edate'];elseif(isset($_POST['edate'])) echo $_POST['edate'];?>">
										</div>
										<div class="form-group col-md-3 button-possition">
											<label for="subject_id">&nbsp;</label>
											<input type="submit" name="range_invoice" Value="<?php esc_html_e('Go','hospital_mgt');?>"  class="btn btn-info hidetable_btn"/>
										</div>
									</div>
									<table id="tblinvoice" class="display hidetable" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th><input type="checkbox" class="select_all"></th>
												<th><?php esc_html_e( 'Invoice ID', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e( 'Title', 'hospital_mgt' ) ;?></th>
												<th> <?php esc_html_e( 'Patient', 'hospital_mgt' ) ;?></th>
												<th> <?php esc_html_e( 'Total Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
												<th> <?php esc_html_e( 'Adjustment Amount ', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
												<th> <?php esc_html_e( 'Paid Amount ', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
												<th> <?php esc_html_e( 'Due Amount ', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
												<th> <?php esc_html_e( 'Status', 'hospital_mgt' ) ;?></th>
												<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>												
											</tr>
										</thead>
										<tfoot>
											<tr>
												<th></th>
												<th><?php esc_html_e( 'Invoice ID', 'hospital_mgt' ) ;?></th>
												<th><?php esc_html_e( 'Title', 'hospital_mgt' ) ;?></th>
												<th> <?php esc_html_e( 'Patient', 'hospital_mgt' ) ;?></th>
												<th> <?php esc_html_e( 'Total Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
												<th> <?php esc_html_e( 'Adjustment Amount ', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
												<th> <?php esc_html_e( 'Paid Amount ', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
												<th> <?php esc_html_e( 'Due Amount ', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
												<th> <?php esc_html_e( 'Status', 'hospital_mgt' ) ;?></th>
												<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
											
											</tr>
										</tfoot>
										
									<?php	if(isset($_POST['range_invoice']))
											{	
											$start_date =MJ_hmgt_get_format_for_db($_POST['sdate']);
											$end_date =MJ_hmgt_get_format_for_db($_POST['edate']);
											if($end_date >= $start_date)
											{
											global $wpdb;
											$table_name=$wpdb->prefix.'hmgt_invoice';
											$result1 = $wpdb->get_results("select *from $table_name where invoice_create_date BETWEEN '$start_date' AND '$end_date' "); 
						
										?>
										<tbody>
										 <?php
											if(!empty($result1))
											{
											foreach ($result1 as $retrieved_data)
											{
												if(empty($retrieved_data->adjustment_amount))
												{
													$due_amount=$retrieved_data->invoice_amount-$retrieved_data->paid_amount;
												}												
												else
												{
													$due_amount=$retrieved_data->invoice_amount-$retrieved_data->adjustment_amount-$retrieved_data->paid_amount;
												}
											?>
												<tr>
													<td class="title"><input type="checkbox" name="selected_id[]" class="sub_chk" value="<?php echo esc_attr($retrieved_data->invoice_id); ?>"></td>
												<td class="title"><?php echo esc_html($retrieved_data->invoice_number); ?></td>
												<td class="title"><a href="?page=hmgt_invoice&tab=addinvoice&action=edit&invoice_id=<?php echo esc_attr($retrieved_data->invoice_id);?>"><?php echo esc_html($retrieved_data->invoice_title); ?></a></td>
													<td class="patient"><?php echo $patient_id=get_user_meta($retrieved_data->patient_id, 'patient_id', true);?></td>

													<td class="vat_percentage"><?php echo number_format($retrieved_data->invoice_amount, 2, '.', '');?></td>
													
													<td class="adjustment_amount"><?php if(!empty($retrieved_data->adjustment_amount)) { echo number_format($retrieved_data->adjustment_amount, 2, '.', ''); }else{ echo '0.00'; } ?></td>
													<td class=""><?php echo number_format($retrieved_data->paid_amount, 2, '.', ''); ?></td>
													<td class=""><?php echo number_format($due_amount, 2, '.', ''); ?></td>
													<td class=""><?php echo esc_html__("$retrieved_data->status","hospital_mgt");?></td>												
													<td class="action">
													<a  href="javascript:void(0);" class="show-invoice-popup btn btn-default" idtest="<?php echo $retrieved_data->invoice_id; ?>" invoice_type="invoice">
													<i class="fa fa-eye"></i> <?php esc_html_e('View Invoice', 'hospital_mgt');?></a>
													<?php
													if($retrieved_data->status != 'Paid')
													{
														if($user_access_add == 1)
														{
													  ?>	
														<a href="?page=hmgt_invoice&tab=addincome&patient_id=<?php echo esc_attr($retrieved_data->patient_id);?>&due_amount=<?php echo number_format($due_amount, 2, '.', ''); ?>&invoice_id=<?php echo esc_attr($retrieved_data->invoice_id);?>" class="btn btn-success margin_bottom_5px"><?php  esc_html_e( 'Add Income', 'hospital_mgt' ) ;?> </a>
													   <?php
													     }
													}
													if($retrieved_data->paid_amount>0)
													{
													?>
														<a href="?page=payment_receipt&print=print&invoice_id=<?php echo esc_attr($retrieved_data->invoice_id); ?>&invoice_type=payment_receipt" target="_blank" class="btn btn-info"> <?php esc_html_e('Print Payment Receipt', 'hospital_mgt' ) ;?>
															</a> 
													<?php													
													}
													?>
													<?php if($user_access_edit == 1)
													{?>
													<a href="?page=hmgt_invoice&tab=addinvoice&action=edit&invoice_id=<?php echo esc_attr($retrieved_data->invoice_id);?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
													<?php 
													} 
													?>
													<?php if($user_access_delete == 1)
													{?>	
													<a href="?page=hmgt_invoice&tab=invoicelist&action=delete&invoice_id=<?php echo esc_attr($retrieved_data->invoice_id);?>" class="btn btn-danger" 
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
									
									<?php
										}
									}
									else
									{
									?>
										<tbody>
										 <?php
											
											foreach ($obj_invoice->MJ_hmgt_get_all_invoice_data() as $retrieved_data)
											{
												if(empty($retrieved_data->adjustment_amount))
												{
													$due_amount=$retrieved_data->invoice_amount-$retrieved_data->paid_amount;
												}												
												else
												{
													$due_amount=$retrieved_data->invoice_amount-$retrieved_data->adjustment_amount-$retrieved_data->paid_amount;
												}
											?>
												<tr>
													<td class="title"><input type="checkbox" name="selected_id[]" class="sub_chk" value="<?php echo esc_attr($retrieved_data->invoice_id); ?>"></td>
												<td class="title"><?php echo esc_html($retrieved_data->invoice_number); ?></td>
												<td class="title"><a href="?page=hmgt_invoice&tab=addinvoice&action=edit&invoice_id=<?php echo esc_attr($retrieved_data->invoice_id);?>"><?php echo esc_html($retrieved_data->invoice_title); ?></a></td>
													<td class="patient"><?php echo $patient_id=get_user_meta($retrieved_data->patient_id, 'patient_id', true);?></td>
													<td class="vat_percentage"><?php echo number_format($retrieved_data->invoice_amount, 2, '.', '');?></td>
													<td class="adjustment_amount"><?php if(!empty($retrieved_data->adjustment_amount)) { echo number_format($retrieved_data->adjustment_amount, 2, '.', ''); }else{ echo '0.00'; } ?></td>
													<td class=""><?php echo number_format($retrieved_data->paid_amount, 2, '.', ''); ?></td>
													<td class=""><?php echo number_format($due_amount, 2, '.', ''); ?></td>
													<td class=""><?php echo esc_html__("$retrieved_data->status","hospital_mgt");?></td>												
													<td class="action">
													<a  href="javascript:void(0);" class="show-invoice-popup btn btn-default" idtest="<?php echo $retrieved_data->invoice_id; ?>" invoice_type="invoice">
													<i class="fa fa-eye"></i> <?php esc_html_e('View Invoice', 'hospital_mgt');?></a>
													<?php
													if($retrieved_data->status != 'Paid')
													{
														 if($user_access_add == 1)
														 {
														 ?>	
															<a href="?page=hmgt_invoice&tab=addincome&patient_id=<?php echo esc_attr($retrieved_data->patient_id);?>&due_amount=<?php echo number_format($due_amount, 2, '.', ''); ?>&invoice_id=<?php echo esc_attr($retrieved_data->invoice_id);?>" class="btn btn-success margin_bottom_5px"><?php  esc_html_e( 'Add Income', 'hospital_mgt' ) ;?> </a>
														<?php
														 }
													}
													if($retrieved_data->paid_amount>0)
													{
													?>
														<a href="?page=payment_receipt&print=print&invoice_id=<?php echo esc_attr($retrieved_data->invoice_id); ?>&invoice_type=payment_receipt" target="_blank" class="btn btn-info"> <?php esc_html_e('Print Payment Receipt', 'hospital_mgt' ) ;?>
															</a> 
													<?php													
													}
													?>
													<?php if($user_access_edit == 1)
													{?>
													<a href="?page=hmgt_invoice&tab=addinvoice&action=edit&invoice_id=<?php echo esc_attr($retrieved_data->invoice_id);?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
													<?php 
													} 
													?>
													<?php if($user_access_delete == 1)
													{?>	
													<a href="?page=hmgt_invoice&tab=invoicelist&action=delete&invoice_id=<?php echo esc_attr($retrieved_data->invoice_id);?>" class="btn btn-danger" 
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
									<?php 
									if($user_access_delete == 1)
									{?>
									<div class="print-button pull-left">
										<input  type="submit" value="<?php esc_html_e('Delete Selected','hospital_mgt');?>" name="delete_selected" class="btn btn-danger delete_selected "/>
									</div>
										<?php 
													} ?>
								</div><!-- TABLE RESPONSIVE DIV END-->
							</div><!-- PANEL BODY DIV END-->
						</form>
						<?php 
						}
						if($active_tab == 'addinvoice')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/invoice/add_invoice.php';
						}
						if($active_tab == 'incomelist')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/invoice/income-list.php';
						}
						if($active_tab == 'addincome')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/invoice/add_income.php';
						}
						if($active_tab == 'expenselist')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/invoice/expense-list.php';
						}
						if($active_tab == 'addexpense')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/invoice/add_expense.php';
						}
						if($active_tab == 'taxlist')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/invoice/tax_list.php';
						}
						if($active_tab == 'addtax')
						{
							require_once HMS_PLUGIN_DIR. '/admin/includes/invoice/add_tax.php';
						}
						?>
                    </div><!-- PANEL BODY DIV END-->
	            </div><!-- PANEL WHITE DIV END-->
	        </div>
        </div><!-- ROW DIV END-->
    </div><!-- END MAIN WRAPER DIV -->