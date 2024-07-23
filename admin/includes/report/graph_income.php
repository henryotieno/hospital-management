<?php
?>
<div class="panel-body clearfix">
<?php	
       $month =array('1'=>"January ",'2'=>"February",'3'=>"March",'4'=>"April",'5'=>"May",'6'=>"June",'7'=> "July",'8'=>"August",'9'=>"September",'10'=>"October",'11'=>"November",'12'=>"December",);        
        $year =isset($_POST['year'])?$_POST['year']:date('Y');
        $currency=MJ_hmgt_get_currency_symbol();
        global $wpdb;
        $table_name = $wpdb->prefix."hmgt_income_expense";
        $result1 = $wpdb->get_results("SELECT * FROM $table_name where invoice_type='income'");
    
       if(!empty($result1))
       {
            foreach($result1 as $result)
            {
                $all_entry=json_decode($result->income_entry);
                
                foreach($all_entry as $entry)
                {
                    $total_amount[]=$entry->amount;
                }
            }
            $test=array_sum($total_amount);
        }
       
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
        require_once HMS_PLUGIN_DIR. '/lib/chart/GoogleCharts.class.php';
        $GoogleCharts = new GoogleCharts;
        if(!empty($result))
        {
            $chart = $GoogleCharts->load( 'column' , 'chart_div_payment' )->get( $chart_array , $options );
        }
    if(isset($result) && count($result) >0)
    {
        
    ?>
        <div id="chart_div_payment" class="chart_css"></div>
  
      <!-- Javascript --> 
      <script type="text/javascript" src="https://www.google.com/jsapi"></script> 
      <script type="text/javascript">
                <?php echo $chart;?>
        </script>
  <?php 
    }
 if(isset($result) && empty($result))
 {?>
    <div class="clear col-md-12 error_msg"><?php esc_html_e("No data available",'hospital_mgt');?></div>
<?php }?>
