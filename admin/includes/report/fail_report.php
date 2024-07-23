<?php
$month =MJ_hmgt_month_list();
//--------------- FAIL REPOTRS --------------//
if(isset($_POST['view_fail_operation']))
{
	$start_date = $_POST['sdate'];
	$end_date = $_POST['edate'];
	global $wpdb;
	$hmgt_ot = $wpdb->prefix."hmgt_ot";	
	$sql_query = "SELECT EXTRACT(DAY FROM operation_date) as date,count(*) as count FROM ".$hmgt_ot." 
			WHERE operation_date BETWEEN '$start_date' AND '$end_date' AND patient_status = 'Death' group by date(operation_date)  ORDER BY operation_date ASC";
	$result=$wpdb->get_results($sql_query);
	
	$chart_array = array();
	$chart_array[] = array('Date','Number Of Fail Operation');
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
}

require_once HMS_PLUGIN_DIR.'/lib/chart/GoogleCharts.class.php';
$GoogleCharts = new GoogleCharts;
if(isset($chart_array))
{
$chart = $GoogleCharts->load( 'column' , 'chart_div' )->get( $chart_array , $options );
}
?>
<script type="text/javascript">
jQuery(document).ready(function($) 
{
"use strict";	
	$('.sdate').datepicker({dateFormat: "yy-mm-dd"}); 
	$('.edate').datepicker({dateFormat: "yy-mm-dd"}); 
} );
</script>
<div class="page-inner min_height_1631"><!-- PAGE INNNER DIV START-->
	<div class="page-title"><!-- PAGE TITLE DIV START-->
		<h3><img src="<?php echo esc_url(get_option( 'hmgt_hospital_logo', 'hospital_mgt' )) ?>" class="img-circle head_logo" width="40" height="40" /><?php echo esc_html(get_option('hmgt_hospital_name','hospital_mgt'));?></h3>
	</div><!-- PAGE TITLE DIV END-->
	<!-- Main-wrapper DIV START-->
	<div id="main-wrapper">
		<div class="row"><!--ROW DIV START-->
			<div class="col-md-12">
				<div class="panel panel-white"><!-- PANEL WHITE DIV START-->
				<!-- PANEL BODY DIV -->
					<div class="panel-body"><!-- PANEL BODY DIV START-->
						<h2 class="nav-tab-wrapper">
						<a href="?page=occupancy_report" class="nav-tab nav-tab-active">
						<?php echo '<span class="dashicons dashicons-menu"></span>'.esc_html__('Opearion Fail Report', 'hospital_mgt'); ?></a>    	
						
					    </h2>					
						<form name="occupancy_report" action="" method="post">							 
							<div class="form-group col-md-3">
								<label for="sdate"><?php esc_html_e('Strat Date','hospital_mgt');?></label>
									<input type="text"  class="form-control sdate" name="sdate" value="<?php if(isset($_REQUEST['sdate'])) echo esc_attr($_REQUEST['sdate']);else echo date('Y-m-d');?>">	
							</div>
							<div class="form-group col-md-3">
								<label for="edate"><?php esc_html_e('End Date','hospital_mgt');?></label>
									<input type="text"  class="form-control edate" name="edate" value="<?php if(isset($_REQUEST['edate'])) echo esc_attr($_REQUEST['edate']);else echo date('Y-m-d');?>">	
							</div>
							<div class="form-group col-md-3 button-possition">
								<label for="subject_id">&nbsp;</label>
								<input type="submit" name="view_fail_operation" Value="<?php esc_html_e('Go','hospital_mgt');?>"  class="btn btn-info"/>
							</div>	
						</form>
						<div class="clearfix"></div>
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
                    </div>   <!--PANEL BODY DIV END-->      
                </div>  <!--PANEL WHITE DIV END-->       
            </div>
        </div> <!--ROW DIV END-->         
    </div><!--MAIN WRAPPER DIV END--> 
</div><!--PAGE INNER DIV END--> 