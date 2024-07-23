<?php
$month =MJ_hmgt_month_list();
//VIEW Occupancy DATA
if(isset($_POST['view_occupancy']))
{	
	$start_date =MJ_hmgt_get_format_for_db($_POST['sdate']);
	$end_date =MJ_hmgt_get_format_for_db($_POST['edate']);
	
	if($end_date >= $start_date)
	{		
		global $wpdb;
		$hmgt_bed_allotment = $wpdb->prefix."hmgt_bed_allotment";
		$hmgt_bed = $wpdb->prefix."hmgt_bed";
		$posts = $wpdb->prefix."posts";
		$sql_query = "SELECT post.post_title as bedtype,count(allotment_date) as count FROM $hmgt_bed_allotment as bed_altmt
		inner join $hmgt_bed as bed on bed.bed_id =  bed_altmt.bed_number inner join $posts as post on post.id = bed.bed_type_id WHERE
		bed_altmt.allotment_date BETWEEN '$start_date' AND '$end_date' GROUP BY bed.bed_type_id";
		$result=$wpdb->get_results($sql_query);

		$chart_array = array();
		$chart_array[] = array(esc_html__('Bed Number','hospital_mgt'),esc_html__('Bed Count','hospital_mgt'));
		foreach($result as $r)
		{
			$chart_array[]=array( "$r->bedtype",(int)$r->count);
		}


		$options = Array(
				'title' => esc_html__('Bed Occupancy Report','hospital_mgt'),
				'pieHole' => 0.2,
				'pieSliceText' => 'value'
		);
		require_once HMS_PLUGIN_DIR.'/lib/chart/GoogleCharts.class.php';
		$GoogleCharts = new GoogleCharts;
		if(isset($chart_array))
		{
			$chart = $GoogleCharts->load( 'pie' , 'chart_div' )->get( $chart_array , $options );
		}
		?>
		<script type="text/javascript">
		jQuery(document).ready(function($) 
		{
			"use strict";
			$('#message').css("display", "none");
		});	
		</script>
		<?php
	}
	else
	{		
		wp_redirect ( home_url() . '?dashboard=user&page=report&tab=occupandy_report&message=1');
	}
}
//VIEW Operation DAT
if(isset($_POST['view_operation']))
{	
	$start_date =MJ_hmgt_get_format_for_db($_POST['sdate']);
	$end_date =MJ_hmgt_get_format_for_db($_POST['edate']);
	
	if($end_date >= $start_date)
	{
		global $wpdb;
		$hmgt_ot = $wpdb->prefix."hmgt_ot";
		$sql_query = "SELECT operation_date as date,count(*) as count FROM ".$hmgt_ot." WHERE operation_date BETWEEN '$start_date' AND '$end_date' group by date(operation_date) ORDER BY operation_date ASC";
		$result=$wpdb->get_results($sql_query);


		$chart_array = array();
		$chart_array[] = array(esc_html__('Date','hospital_mgt'),esc_html__('Number Of Operation','hospital_mgt'));
		foreach($result as $r)
		{
			$chart_array[]=array( "$r->date",(int)$r->count);
		}


		$options = Array(
				'title' => esc_html__('Operation Report','hospital_mgt'),
				'titleTextStyle' => Array('color' => '#222','fontSize' => 14,'bold'=>true,'italic'=>false,'fontName' =>'open sans'),
				'legend' =>Array('position' => 'right',
						'textStyle'=> Array('color' => '#222','fontSize' => 14,'bold'=>true,'italic'=>false,'fontName' =>'open sans')),

				'hAxis' => Array(
						'title' =>  esc_html__('Date','hospital_mgt'),
						'titleTextStyle' => Array('color' => '#222','fontSize' => 14,'bold'=>true,'italic'=>false,'fontName' =>'open sans'),
						'textStyle' => Array('color' => '#222','fontSize' => 10),
						'maxAlternation' => 2


				),
				'vAxis' => Array(
						'title' =>  esc_html__('No of Operation','hospital_mgt'),
					 'minValue' => 0,
						'maxValue' => 5,
					 'format' => '#',
						'titleTextStyle' => Array('color' => '#222','fontSize' => 14,'bold'=>true,'italic'=>false,'fontName' =>'open sans'),
						'textStyle' => Array('color' => '#222','fontSize' => 12)
				)
		);
		require_once HMS_PLUGIN_DIR.'/lib/chart/GoogleCharts.class.php';
		$GoogleCharts = new GoogleCharts;
		if(isset($chart_array))
		{
			$chart = $GoogleCharts->load( 'column' , 'chart_div' )->get( $chart_array , $options );
		}
		?>
		<script type="text/javascript">
		$(document).ready(function() 
		{
			"use strict";
			$('#message').css("display", "none");
		});	
		</script>
		<?php
	}
	else
	{		
		wp_redirect ( home_url() . '?dashboard=user&page=report&tab=operation_report&message=1');
	}
}
//VIEW FAIL Operation DATA
if(isset($_POST['view_fail_operation']))
{
	$start_date =MJ_hmgt_get_format_for_db($_POST['sdate']);
	$end_date =MJ_hmgt_get_format_for_db($_POST['edate']);
	
	if($end_date >= $start_date)
	{
		global $wpdb;
		$hmgt_ot = $wpdb->prefix."hmgt_ot";
		$sql_query = "SELECT EXTRACT(DAY FROM operation_date) as date,count(*) as count FROM ".$hmgt_ot."
		WHERE operation_date BETWEEN '$start_date' AND '$end_date' AND out_come_status = 'Fail' group by date(operation_date)  ORDER BY operation_date ASC";
		$result=$wpdb->get_results($sql_query);

		$chart_array = array();
		$chart_array[] = array(esc_html__('Date','hospital_mgt'),esc_html__('Number Of Fail Operation','hospital_mgt'));
		foreach($result as $r)
		{
			$chart_array[]=array( "$r->date",(int)$r->count);
		}


		$options = Array(
				'title' => esc_html__('Operation Fail Report','hospital_mgt'),
				'titleTextStyle' => Array('color' => '#222','fontSize' => 14,'bold'=>true,'italic'=>false,'fontName' =>'open sans'),
				'legend' =>Array('position' => 'right',
						'textStyle'=> Array('color' => '#222','fontSize' => 14,'bold'=>true,'italic'=>false,'fontName' =>'open sans')),

				'hAxis' => Array(
						'title' =>  esc_html__('Date','hospital_mgt'),
						'titleTextStyle' => Array('color' => '#222','fontSize' => 14,'bold'=>true,'italic'=>false,'fontName' =>'open sans'),
						'textStyle' => Array('color' => '#222','fontSize' => 10),
						'maxAlternation' => 2


				),
				'vAxis' => Array(
						'title' =>  esc_html__('No of Fail Operation','hospital_mgt'),
					 'minValue' => 0,
						'maxValue' => 5,
					 'format' => '#',
						'titleTextStyle' => Array('color' => '#222','fontSize' => 14,'bold'=>true,'italic'=>false,'fontName' =>'open sans'),
						'textStyle' => Array('color' => '#222','fontSize' => 12)
				)
		);
		require_once HMS_PLUGIN_DIR.'/lib/chart/GoogleCharts.class.php';
		$GoogleCharts = new GoogleCharts;
		if(isset($chart_array))
		{
			$chart = $GoogleCharts->load( 'column' , 'chart_div' )->get( $chart_array , $options );
		}
		?>
		<script type="text/javascript">
		$(document).ready(function() 
		{
			"use strict";
			$('#message').css("display", "none");
		});	
		</script>
		<?php
	}
	else
	{		
		wp_redirect ( home_url() . '?dashboard=user&page=report&tab=operation_fail&message=1');
	}
}
//VIEW INCOME GRAPH REPORT
if(isset($_REQUEST['tab']) && $_REQUEST['tab'] == "income_data" && isset($_REQUEST['tab1']) && $_REQUEST['tab1'] == "graph")
{
	$month =array('1'=>"January ",'2'=>"February",'3'=>"March",'4'=>"April",'5'=>"May",'6'=>"June",'7'=> "July",'8'=>"August",'9'=>"September",'10'=>"October",'11'=>"November",'12'=>"December",);        
	$year =isset($_POST['year'])?$_POST['year']:date('Y');
	$currency=MJ_hmgt_get_currency_symbol();
	global $wpdb;
	$table_name = $wpdb->prefix."hmgt_income_expense";
	$result1 = $wpdb->get_results("SELECT * FROM $table_name where invoice_type='income'");
   $total_amount=array();
	foreach($result1 as $result)
	{
		$all_entry=json_decode($result->income_entry);
		
		foreach($all_entry as $entry)
		{
			$total_amount[]=$entry->amount;
		}
	}
	$test=array_sum($total_amount);
	$q="SELECT EXTRACT(MONTH FROM income_create_date) as date,$test as count FROM ".$table_name." WHERE invoice_type = 'income' AND YEAR(income_create_date) =".$year." group by month(income_create_date) ORDER BY income_create_date ASC";
	$result=$wpdb->get_results($q); 
	
	$sumArray = array(); 

	foreach ($result as $value) 
	{ 
		if(isset($sumArray[$value->date]))
		{
			$sumArray[$value->date] = $sumArray[$value->date] + (int)$value->count;
		}
		else
		{
			$sumArray[$value->date] = (int)$value->count; 
		}       
	}
	$chart_array = array();
	$chart_array[] = array(esc_html__('Month','hospital_mgt'),esc_html__('Income','hospital_mgt'));
	$i=1;
	foreach($sumArray as $month_value=>$count)
	{
		$chart_array[]=array( $month[$month_value],(int)$count);
	}

	$options = Array(
		'title' => esc_html__('Income Report By Month','hospital_mgt'),
		'titleTextStyle' => Array('color' => '#4e5e6a','fontSize' => 16,'bold'=>false,'italic'=>false,'fontName' =>'-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", sans-serif;'),
		'legend' =>Array('position' => 'right',
				
		'textStyle'=> Array('color' => '#4e5e6a','fontSize' => 13,'bold'=>false,'italic'=>false,'fontName' =>'-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", sans-serif;')),
		'hAxis' => Array(
			'title' => esc_html__('Month','hospital_mgt'),
				'format' => '#',
			'titleTextStyle' => Array('color' => '#4e5e6a','fontSize' => 16,'bold'=>false,'italic'=>false,'fontName' =>'-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", sans-serif;'),
			'textStyle'=> Array('color' => '#4e5e6a','fontSize' => 13,'bold'=>false,'italic'=>false,'fontName' =>'-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", sans-serif;'),
			'maxAlternation' => 2
			
			),
		'vAxis' => Array(
			'title' => esc_html__('Income','hospital_mgt'),
			'minValue' => 0,
			'maxValue' => 6,
			'format' => html_entity_decode($currency),
			'titleTextStyle' => Array('color' => '#4e5e6a','fontSize' => 16,'bold'=>false,'italic'=>false,'fontName' =>'-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", sans-serif;'),
			'textStyle'=> Array('color' => '#4e5e6a','fontSize' => 13,'bold'=>false,'italic'=>false,'fontName' =>'-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", sans-serif;')
			),
	'colors' => array('#22BAA0')
		);
	require_once HMS_PLUGIN_DIR.'/lib/chart/GoogleCharts.class.php';
	$GoogleCharts = new GoogleCharts;
	if(isset($chart_array))
	{
		$chart = $GoogleCharts->load( 'column' , 'chart_div' )->get( $chart_array , $options );
	}
	?>
	<script type="text/javascript">
	$(document).ready(function() 
	{
		"use strict";
		$('#message').css("display", "none");
	});	
	</script><?php
}
//VIEW EXXPENSE GRAPH REPORT
if(isset($_REQUEST['tab']) && $_REQUEST['tab'] == "expenses_data" && isset($_REQUEST['tab1']) && $_REQUEST['tab1'] == "graph")
{
	$month =array('1'=>"January ",'2'=>"February",'3'=>"March",'4'=>"April",'5'=>"May",'6'=>"June",'7'=> "July",'8'=>"August",'9'=>"September",'10'=>"October",'11'=>"November",'12'=>"December",);        
	$year =isset($_POST['year'])?$_POST['year']:date('Y');
	$currency=MJ_hmgt_get_currency_symbol();
	global $wpdb;
	$table_name = $wpdb->prefix."hmgt_income_expense";
	$result1 = $wpdb->get_results("SELECT * FROM $table_name where invoice_type='expense'");
	$total_amount=array();
	foreach($result1 as $result)
	{
		
		$all_entry=json_decode($result->income_entry);
		
		foreach($all_entry as $entry)
		{
			$total_amount[]=$entry->amount;
		}
	}
	$test=array_sum($total_amount);
	$q="SELECT EXTRACT(MONTH FROM income_create_date) as date,$test as count FROM ".$table_name." WHERE invoice_type = 'expense' AND YEAR(income_create_date) =".$year." group by month(income_create_date) ORDER BY income_create_date ASC";
	$result=$wpdb->get_results($q); 
	
	$sumArray = array(); 
	foreach ($result as $value) 
	{
		if(isset($sumArray[$value->date]))
		{
			$sumArray[$value->date] = $sumArray[$value->date] + (int)$value->count;
		}
		else
		{
			$sumArray[$value->date] = (int)$value->count; 
		}       
	}
	$chart_array = array();
	$chart_array[] = array(esc_html__('Month','hospital_mgt'),esc_html__('Expense','hospital_mgt'));
	$i=1;
	foreach($sumArray as $month_value=>$count)
	{
		$chart_array[]=array( $month[$month_value],(int)$count);
	}

	$options = Array(
		'title' => esc_html__('Expense Report By Month','hospital_mgt'),
		'titleTextStyle' => Array('color' => '#4e5e6a','fontSize' => 16,'bold'=>false,'italic'=>false,'fontName' =>'-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", sans-serif;'),
		'legend' =>Array('position' => 'right',
				
		'textStyle'=> Array('color' => '#4e5e6a','fontSize' => 13,'bold'=>false,'italic'=>false,'fontName' =>'-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", sans-serif;')),
		'hAxis' => Array(
			'title' => esc_html__('Month','hospital_mgt'),
				'format' => '#',
			'titleTextStyle' => Array('color' => '#4e5e6a','fontSize' => 16,'bold'=>false,'italic'=>false,'fontName' =>'-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", sans-serif;'),
			'textStyle'=> Array('color' => '#4e5e6a','fontSize' => 13,'bold'=>false,'italic'=>false,'fontName' =>'-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", sans-serif;'),
			'maxAlternation' => 2
			
			),
		'vAxis' => Array(
			'title' => esc_html__('Expense','hospital_mgt'),
			'minValue' => 0,
			'maxValue' => 6,
			'format' => html_entity_decode($currency),
			'titleTextStyle' => Array('color' => '#4e5e6a','fontSize' => 16,'bold'=>false,'italic'=>false,'fontName' =>'-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", sans-serif;'),
			'textStyle'=> Array('color' => '#4e5e6a','fontSize' => 13,'bold'=>false,'italic'=>false,'fontName' =>'-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", sans-serif;')
			),
	'colors' => array('#22BAA0')
		);
	require_once HMS_PLUGIN_DIR.'/lib/chart/GoogleCharts.class.php';
	$GoogleCharts = new GoogleCharts;
	if(isset($chart_array))
	{
		$chart = $GoogleCharts->load( 'column' , 'chart_div' )->get( $chart_array , $options );
	}
	?>
	<script type="text/javascript">
	$(document).ready(function() 
	{
		"use strict";
		$('#message').css("display", "none");
	});	
	</script><?php
}
$active_tab = isset($_REQUEST['tab'])?$_REQUEST['tab']:'occupandy_report';
?>
<script type="text/javascript">
$(document).ready(function() {
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
<?php
if(isset($_REQUEST['message']))
{
	$message =$_REQUEST['message'];
	if($message == 1)
	{?>
			<div id="message" class="updated below-h2 ">
			<p>
			<?php 
				esc_html_e('End date must be greater or equal than the start date','hospital_mgt');
			?></p></div>
			<?php 		
	}
}
?>
<div class="panel-body panel-white"><!-- START PANEL BODY DIV-->
	<ul class="nav nav-tabs panel_tabs" role="tablist">
		  <li class="<?php if($active_tab == 'occupandy_report') echo "active";?>">
			  <a href="?dashboard=user&page=report&tab=occupandy_report" >
				 <i class="fa fa-align-justify"></i> <?php esc_html_e('Bed Occupation Report', 'hospital_mgt'); ?></a>
			  </a>
		  </li>
		  <li class="<?php if($active_tab == 'operation_report') echo "active";?>">
			  <a href="?dashboard=user&page=report&tab=operation_report" >
				 <i class="fa fa-align-justify"></i> <?php esc_html_e('Operation Report', 'hospital_mgt'); ?></a>
			  </a>
		  </li>
		  <li class="<?php if($active_tab == 'operation_fail') echo "active";?>">
			  <a href="?dashboard=user&page=report&tab=operation_fail" >
				 <i class="fa fa-align-justify"></i> <?php esc_html_e('Operation Fail Report', 'hospital_mgt'); ?></a>
			  </a>
		  </li> 
		  <li class="<?php if($active_tab == 'income_data') echo "active";?>">
			  <a href="?dashboard=user&page=report&tab=income_data&tab1=data_table" >
				 <i class="fa fa-align-justify"></i> <?php esc_html_e('Income Report', 'hospital_mgt'); ?></a>
			  </a>
		  </li> 
		  <li class="<?php if($active_tab == 'expenses_data') echo "active";?>">
			  <a href="?dashboard=user&page=report&tab=expenses_data&tab1=data_table" >
				 <i class="fa fa-align-justify"></i> <?php esc_html_e('Expenses Report', 'hospital_mgt'); ?></a>
			  </a>
		  </li> 
	</ul>
	<ul class="nav nav-tabs panel_tabs" role="tablist">
		<?php
		if($active_tab == 'income_data')
		{
			?>
			<li class="<?php if($_REQUEST['tab1'] == 'data_table') echo "active";?>">
				<a href="?dashboard=user&page=report&tab=income_data&tab1=data_table" >
					<i class="fa fa-align-justify"></i> <?php esc_html_e('Data Table', 'hospital_mgt'); ?></a>
				</a>
			</li> 
			<li class="<?php if($_REQUEST['tab1'] == 'graph') echo "active";?>">
				<a href="?dashboard=user&page=report&tab=income_data&tab1=graph" >
					<i class="fa fa-align-justify"></i> <?php esc_html_e('Graph', 'hospital_mgt'); ?></a>
				</a>	
			</li> 
			<?php
		}
		if($active_tab == 'expenses_data')
		{
			?>
			<li class="<?php if($_REQUEST['tab1'] == 'data_table') echo "active";?>">
				<a href="?dashboard=user&page=report&tab=expenses_data&tab1=data_table" >
					<i class="fa fa-align-justify"></i> <?php esc_html_e('Data Table', 'hospital_mgt'); ?></a>
				</a>
			</li> 
			<li class="<?php if($_REQUEST['tab1'] == 'graph') echo "active";?>">
				<a href="?dashboard=user&page=report&tab=expenses_data&tab1=graph" >
					<i class="fa fa-align-justify"></i> <?php esc_html_e('Graph', 'hospital_mgt'); ?></a>
				</a>	
			</li> 
			<?php
		}
		?>
		 
	</ul>
	<div class="tab-content opacity_div"><!-- START TAB CONTENT DIV-->
    	<div class="tab-pane fade active in"  id="birthreport"><!-- START TAB PANE DIV-->         
			<div class="panel-body"><!-- START PANEL BODY DIV-->        
			<?php
			if(@$_REQUEST['tab1'] != 'graph')
			{
				?>
				<form name="occupancy_report" id="occupancy_report" action="" method="post"><!-- STRAT Occupancy FORM-->	
					<div class="mb-3 row">
						<div class="form-group col-md-3">
							<label for="sdate"><?php esc_html_e('Start Date','hospital_mgt');?><span class="require-field">*</span></label>
							   <input type="text"  class="form-control sdate validate[required]" name="sdate"  value="<?php if(isset($_REQUEST['sdate'])) echo esc_attr($_REQUEST['sdate']);elseif(isset($_POST['sdate'])) echo $_POST['sdate'];?>">
						</div>
						<div class="form-group col-md-3">
							<label for="edate"><?php esc_html_e('End Date','hospital_mgt');?> <span class="require-field">*</span></label>
								<input type="text"  class="form-control edate validate[required]" name="edate"  value="<?php if(isset($_REQUEST['edate'])) echo esc_attr($_REQUEST['edate']);elseif(isset($_POST['edate'])) echo $_POST['edate'];?>">
						</div>
						 <?php 
						$button_name ="";
						if($active_tab == 'occupandy_report')
							$button_name ='view_occupancy';
						if($active_tab == 'operation_report')
							$button_name ='view_operation';
						if($active_tab == 'operation_fail')
							$button_name ='view_fail_operation';
						if($active_tab == 'expenses_data')
							$button_name ='view_expenses_data';
						if($active_tab == 'income_data')
							$button_name ='view_income_data';	
						
						?>
						<div class="form-group col-md-3 button-possition">
							<label for="subject_id">&nbsp;</label>
							<input type="submit" name="<?php echo esc_attr($button_name);?>" Value="<?php esc_html_e('Go','hospital_mgt');?>"  class="btn btn-info"/>
						</div>	
					</div>
				</form>
				<div class="clearfix"></div>
				<?php
			}
			?>
				
					<?php
						if(isset($_POST['view_expenses_data']))
						{	
							$start_date =MJ_hmgt_get_format_for_db($_POST['sdate']);
							$end_date =MJ_hmgt_get_format_for_db($_POST['edate']);
							if($end_date >= $start_date)
							{
								global $wpdb;
								$table_name=$wpdb->prefix.'hmgt_income_expense';
								$result1 = $wpdb->get_results("select *from $table_name where income_create_date BETWEEN '$start_date' AND '$end_date' AND invoice_type='expense'");  ?>
								
								<script type="text/javascript">
								$(document).ready(function()
								{
									"use strict";
									jQuery('#transaction_list').DataTable({
										"responsive":true,
										language:<?php echo MJ_hmgt_datatable_multi_language();?>,
										"order": [[ 0, "asc" ]],
										"aoColumns":[
													  {"bSortable": true},
													  {"bSortable": true},
													  {"bSortable": true},
													  {"bSortable": true},
													  
													]
										});
								} );
								</script>
								<div class="panel-body">
								<div class="table-responsive">
									<table id="transaction_list" class="display" cellspacing="0" width="100%">
										<thead>
											<tr>
												<th> <?php esc_html_e( 'Supplier Name', 'hospital_mgt' ) ;?></th>
												<th> <?php esc_html_e( 'Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
												<th> <?php esc_html_e( 'Status', 'hospital_mgt' ) ;?></th>
												<th> <?php esc_html_e( 'Date', 'hospital_mgt' ) ;?></th>
												
											</tr>
										</thead>
										<tfoot>
											<tr>
												<th> <?php esc_html_e( 'Supplier Name', 'hospital_mgt' ) ;?></th>
												<th> <?php esc_html_e( 'Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
												<th> <?php esc_html_e( 'Status', 'hospital_mgt' ) ;?></th>
												<th> <?php esc_html_e( 'Date', 'hospital_mgt' ) ;?></th>
												
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
												$idtest=($retrieved_data->income_id);
												$type=($retrieved_data->invoice_type);
										 ?>
											<tr>
												<td class="name"><?php 
												echo esc_html($retrieved_data->party_name);
												?></td>
												<td class="total_amount"><?php echo esc_html($total_amount);?> </td>
												<td class="Status"><?php echo esc_html($retrieved_data->payment_status);?> </td>
												<td class="method"><?php echo date(MJ_hmgt_date_formate(),strtotime($retrieved_data->income_create_date));?> </td>
											</tr>
											<?php 
											} 
										}?>
										</tbody>
									</table>
								</div>
								</div><!-- PANEL BODY DIV END-->
								
					<?php  	}
							
						}
						if(isset($_POST['view_income_data']))
						{	
							$start_date =MJ_hmgt_get_format_for_db($_POST['sdate']);
							$end_date =MJ_hmgt_get_format_for_db($_POST['edate']);
							if($end_date >= $start_date)
							{
								global $wpdb;
								$table_name=$wpdb->prefix.'hmgt_income_expense';
								$result1 = $wpdb->get_results("select *from $table_name where income_create_date BETWEEN '$start_date' AND '$end_date' AND invoice_type='income'");  ?>
								
								<script type="text/javascript">
								$(document).ready(function()
								{
									"use strict";
									jQuery('#transaction_list').DataTable({
										"responsive":true,
										language:<?php echo MJ_hmgt_datatable_multi_language();?>,
										"order": [[ 0, "asc" ]],
										"aoColumns":[
													  {"bSortable": true},
													  {"bSortable": true},
													  {"bSortable": true},
													  {"bSortable": true},
													  {"bSortable": true},
													  {"bSortable": true},
													  ]
										});
								} );
								</script>
								<div class="panel-body">
								<div class="table-responsive">
									<table id="transaction_list" class="display" cellspacing="0" width="100%">
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
												if($invoice_data->invoice_number == 0)
												{ 
													echo '-'; 
												}
												else
												{
													echo esc_html($invoice_data->invoice_number); 	
												}
												?></td>
												<td class="patient"><?php echo $patient_id=get_user_meta($retrieved_data->party_name, 'patient_id', true);?></td>
												<td class="patient_name"><?php $user=get_userdata($retrieved_data->party_name); echo esc_html($user->display_name);?></td>
												<td class="income_amount"><?php echo esc_html($total_amount);?></td>
												<td class="status"><?php echo date(MJ_hmgt_date_formate(),strtotime($retrieved_data->income_create_date));?></td>
												<td class=""><?php echo esc_html($retrieved_data->payment_method);?></td>
											</tr>
											<?php 
											} 
										}?>
										</tbody>
									</table>
								</div>
								</div><!-- PANEL BODY DIV END-->
								
					<?php  	}
							
						}
	?>
				 <?php if(isset($result) && count($result) >0){?>
				  <div id="chart_div" class="width_100_height_500"></div>
				  
				  <!-- Javascript --> 
				  <script type="text/javascript" src="https://www.google.com/jsapi"></script> 
				  <script type="text/javascript">
							<?php echo $chart;?>
						</script>
				  <?php }
				 if(isset($result) && empty($result)) {?>
				  <div class="clear col-md-12"><?php esc_html_e("There is not enough data to generate report.",'hospital_mgt');?></div>
				  <?php }?>
			</div><!-- END PANEL BODY DIV-->
		</div>	<!-- END TAB PANE DIV-->
	</div><!-- END TAB CONTENT BODY DIV-->
</div><!-- END PANEL BODY DIV-->