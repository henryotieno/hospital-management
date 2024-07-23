<?php
$month =MJ_hmgt_month_list();
//VIEW FAIL Operation DATA
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
jQuery(document).ready(function($) {
	"use strict";
	$('.sdate').datepicker({dateFormat: "yy-mm-dd"}); 
	$('.edate').datepicker({dateFormat: "yy-mm-dd"}); 
});
</script>
<div class="panel-body panel-white"><!--START PANEL BODY DIV-->
    <ul class="nav nav-tabs panel_tabs" role="tablist">
      <li class="active">
          <a href="javascript:void(0);" >
             <i class="fa fa-align-justify"></i> <?php esc_html_e('Operation Fail Report', 'hospital_mgt'); ?></a>
          </a>
      </li>
     
    </ul>
	<div class="tab-content"><!--START TAB CONTENT DIV-->
    	<div class="tab-pane fade active in"  id="birthreport"><!--STRAT TAB PANE DIV-->
		    <div class="panel-body"><!--START PANEL BODY DIV-->
			    <form name="occupancy_report" action="" method="post"><!--START OCCUPAMCY REPORT FORM-->
					<div class="form-group col-md-3">
						<label for="sdate"><?php esc_html_e('Strat Date','hospital_mgt');?></label>
						<input type="text"  class="form-control sdate" name="sdate" data-date-format="yyyy-mm-dd" value="<?php if(isset($_REQUEST['sdate'])) echo esc_attr($_REQUEST['sdate']);else echo date('Y-m-d');?>">
					</div>
					<div class="form-group col-md-3">
						<label for="edate"><?php esc_html_e('End Date','hospital_mgt');?></label>
							<input type="text"  class="form-control edate" name="edate" data-date-format="yyyy-mm-dd" value="<?php if(isset($_REQUEST['edate'])) echo esc_attr($_REQUEST['edate']);else echo date('Y-m-d');?>">
								
					</div>
					<div class="form-group col-md-3 button-possition">
						<label for="subject_id">&nbsp;</label>
						<input type="submit" name="view_fail_operation" Value="<?php esc_html_e('Go','hospital_mgt');?>"  class="btn btn-info"/>
	 				</div>	
		        </form><!--END OCCUPAMCY REPORT FORM-->
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
		    </div><!--END PANEL BODY DIV-->
		</div><!--END TAB PANE DIV-->
	
	</div><!--END TAB CONTENT DIV-->
</div><!--END PANEL BODY DIV-->