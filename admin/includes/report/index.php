<?php
$user_role=MJ_hmgt_get_roles(get_current_user_id());
if($user_role == 'administrator')
{
	$user_access_view=1;
}
else
{
	$user_access=MJ_hmgt_get_access_right_for_management_user_page('report');
	$user_access_view=$user_access['view'];
	
	
	if (isset ( $_REQUEST ['page'] ))
	{	
		if($user_access_view == '0')
		{	
			MJ_hmgt_access_right_page_not_access_message_admin();
			die;
		}
	}
}

$active_tab=isset($_REQUEST['tab'])?$_REQUEST['tab']:'occupandy_report';
$month =MJ_hmgt_month_list();
//------------------ VIEW Occupancy ---------------//
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
	}
	else
	{
		wp_redirect ( admin_url().'admin.php?page=hmgt_report&tab=occupandy_report&message=1');
	}
}
if(isset($_POST['view_operation']))
{
	$start_date =MJ_hmgt_get_format_for_db($_POST['sdate']);
	$end_date =MJ_hmgt_get_format_for_db($_POST['edate']);
	if($end_date >= $start_date)
	{
		global $wpdb;
		$hmgt_ot = $wpdb->prefix."hmgt_ot";
	//	$sql_query = "SELECT EXTRACT(DAY FROM operation_date) as date,count(*) as count FROM ".$hmgt_ot." WHERE operation_date BETWEEN '$start_date' AND '$end_date' group by date(operation_date) ORDER BY operation_date ASC";
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
	}
	else
	{
		wp_redirect ( admin_url().'admin.php?page=hmgt_report&tab=operation_report&message=1');
	}
}
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
	}
	else
	{
		wp_redirect ( admin_url().'admin.php?page=hmgt_report&tab=operation_fail&message=1');
	}
}

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
});
</script>
<div class="page-inner min_height_1631"><!-- PAGE INNER DIV START-->
	<div class="page-title"><!-- PAGE TITLE DIV START-->
		<h3><img src="<?php echo esc_url(get_option( 'hmgt_hospital_logo', 'hospital_mgt' )) ?>" class="img-circle head_logo" width="40" height="40" /><?php echo esc_html(get_option('hmgt_hospital_name','hospital_mgt'));?></h3>
	</div><!-- PAGE TITLE DIV END-->
	<?php
	if(isset($_REQUEST['message']))
	{
		$message =$_REQUEST['message'];
		if($message == 1)
		{?>
			<div id="message" class="updated below-h2 notice is-dismissible">
			<p>
			<?php 
				esc_html_e('End date must be greater or equal than the start date','hospital_mgt');
			?></p></div>
			<?php 		
		}
	}
	?>
	<div id="main-wrapper"><!-- MAIN WRAPPER DIV START-->
		<div class="row"><!-- ROW DIV START-->
			<div class="col-md-12">
				<div class="panel panel-white"><!-- PANEL WHITE DIV START-->
					<div class="panel-body nav_tab_responsive_report"><!-- PANEL BODY DIV START-->
						<h2 class="nav-tab-wrapper">
							<a href="?page=hmgt_report&tab=occupandy_report" class="nav-tab <?php echo $active_tab == 'occupandy_report' ? 'nav-tab-active' : ''?>">
							<?php echo '<span class="dashicons dashicons-menu"></span>'.esc_html__('Bed Occupancy Report', 'hospital_mgt'); ?></a>  
							<a href="?page=hmgt_report&tab=operation_report" class="nav-tab <?php echo $active_tab == 'operation_report' ? 'nav-tab-active' : ''?>">
							<?php echo '<span class="dashicons dashicons-menu"></span>'.esc_html__('Operation Report', 'hospital_mgt'); ?></a>  
							<a href="?page=hmgt_report&tab=operation_fail" class="nav-tab <?php echo $active_tab == 'operation_fail' ? 'nav-tab-active' : ''?>">
							<?php echo '<span class="dashicons dashicons-menu"></span>'.esc_html__('Operation Fail Report', 'hospital_mgt'); ?></a>  
							
							<a href="?page=hmgt_report&tab=income_data" class="nav-tab <?php echo $active_tab == 'income_data' ? 'nav-tab-active' : ''?>">
							<?php echo '<span class="dashicons dashicons-menu"></span>'.esc_html__('Income Report', 'hospital_mgt'); ?></a>   
							
							<a href="?page=hmgt_report&tab=expenses_data" class="nav-tab <?php echo $active_tab == 'expenses_data' ? 'nav-tab-active' : ''?>">
							<?php echo '<span class="dashicons dashicons-menu"></span>'.esc_html__('Expenses Report', 'hospital_mgt'); ?></a>   
							
						</h2>
						<?php 
						if($active_tab != 'income_data' && $active_tab != 'expenses_data')
						{
						?>
						<form name="occupancy_report" action="" id="occupancy_report" method="post">
							<div class="mb-3 row">	
								<div class="form-group col-md-3">
										<label for="sdate"><?php esc_html_e('Start Date','hospital_mgt');?><span class="require-field">*</span></label>
											<input type="text"  class="form-control sdate validate[required]" name="sdate"  value="<?php if(isset($_REQUEST['sdate'])) echo esc_attr($_REQUEST['sdate']);elseif(isset($_POST['sdate'])) echo esc_attr($_POST['sdate']);?>" placeholder="<?php esc_html_e('Please select Start Date','hospital_mgt');?>" readonly>	
								</div>
								<div class="form-group col-md-3">
									<label for="edate"><?php esc_html_e('End Date','hospital_mgt');?><span class="require-field">*</span></label>
										<input type="text"  class="form-control edate validate[required]" name="edate" value="<?php if(isset($_REQUEST['edate'])) echo esc_attr($_REQUEST['edate']);elseif(isset($_POST['edate'])) echo esc_attr($_POST['edate']);?>"  placeholder="<?php esc_html_e('Please select End Date','hospital_mgt');?>" readonly>
											
								</div>
								<?php 
								$button_name ="";
								if($active_tab == 'occupandy_report')
									$button_name ='view_occupancy';
								if($active_tab == 'operation_report')
									$button_name ='view_operation';
								if($active_tab == 'operation_fail')
									$button_name ='view_fail_operation';
								?>
								<div class="form-group col-md-3 button-possition">
									<label for="subject_id">&nbsp;</label>
									<input type="submit" name="<?php echo $button_name;?>" Value="<?php esc_html_e('Go','hospital_mgt');?>"  class="btn btn-info"/>
								</div>	
							</div>
						</form>
						<?php
						}	
							if($active_tab == 'income_data')
							{
								$active_tab = isset($_GET['tab1'])?$_GET['tab1']:'report11'; 
							?>
							<h3>
								<ul id="myTab" class="sub_menu_css line case_nav nav nav-tabs" role="tablist">
									<li role="presentation" class="margin_right_5px <?php echo $active_tab == 'report11' ? 'active' : ''; ?> menucss">
										<a href="?page=hmgt_report&tab=income_data&tab1=report11" class="report_second_tab">
											<?php echo '<span class="dashicons dashicons-chart-bar"></span> '.esc_html__('Data table', 'hospital_mgt'); ?>
										</a>
									</li>
									<li role="presentation" class="<?php echo $active_tab == 'report12' ? 'active' : ''; ?> menucss">
										<a href="?page=hmgt_report&tab=income_data&tab1=report12" class="report_second_tab">
											<?php echo '<span class="dashicons dashicons-chart-bar"></span> '.esc_html__('Graph', 'hospital_mgt'); ?>
										</a>
									</li>
								</ul>	
							</h3>
							<div class="panel-body clearfix">
							 <?php 
							 if($active_tab == 'report11')
							 { 				
								 require_once HMS_PLUGIN_DIR.'/admin/includes/report/data_table_income.php';
							 } 
							 if($active_tab == 'report12')
							 { 				
								 require_once HMS_PLUGIN_DIR.'/admin/includes/report/graph_income.php';
							 } 
							 ?>
							 </div>
							 <?php
							}
							if($active_tab == 'expenses_data')
							{
								$active_tab = isset($_GET['tab1'])?$_GET['tab1']:'report13'; 
							?>
							<h3>
								<ul id="myTab" class="sub_menu_css line case_nav nav nav-tabs" role="tablist">
									<li role="presentation" class="margin_right_5px <?php echo $active_tab == 'report13' ? 'active' : ''; ?> menucss">
										<a href="?page=hmgt_report&tab=expenses_data&tab1=report13" class="report_second_tab">
											<?php echo '<span class="dashicons dashicons-chart-bar"></span> '.esc_html__('Data table', 'hospital_mgt'); ?>
										</a>
									</li>
									<li role="presentation" class="<?php echo $active_tab == 'report14' ? 'active' : ''; ?> menucss">
										<a href="?page=hmgt_report&tab=expenses_data&tab1=report14" class="report_second_tab">
											<?php echo '<span class="dashicons dashicons-chart-bar"></span> '.esc_html__('Graph', 'hospital_mgt'); ?>
										</a>
									</li>
								</ul>	
							</h3>
							<div class="panel-body clearfix">
							<?php 
								if($active_tab == 'report13')
								{ 				
									require_once HMS_PLUGIN_DIR.'/admin/includes/report/data_table_expense.php';
								} 
								if($active_tab == 'report14')
								{ 				
									require_once HMS_PLUGIN_DIR.'/admin/includes/report/graph_expense.php';
								} 
							?>
							</div>
							<?php
							}
							?>
				 <?php if(isset($result) && count($result) >0)
				 {?>
					  <div id="chart_div" class="width_100_height_500"></div>
					  <!-- Javascript --> 
					  <script type="text/javascript" src="https://www.google.com/jsapi"></script> 
					  <script type="text/javascript">
							<?php echo $chart;?>
					  </script>
				  <?php
				  }
				  if(isset($result) && empty($result)) 
				  {?>
				     <div class="clear col-md-12"><?php esc_html_e("There is not enough data to generate report.",'hospital_mgt');?></div>
				    <?php 
				  }?>
				</div><!-- PANEL BODY DIV END-->
				</div><!-- PANEL WHITE DIV END-->
			</div>
		</div><!-- ROW DIV END-->
	</div><!-- MAIN WRAPPER DIV END-->
</div><!-- PAGE INNER END-->

