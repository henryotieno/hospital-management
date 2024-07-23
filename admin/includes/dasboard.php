<?php
$obj_appointment = new MJ_hmgt_appointment();
$active_tab = "addnotice";
//--------- Get appointment data ----------//
$appointment_data=$obj_appointment->MJ_hmgt_get_all_appointment();
$cal_array = array ();
if (! empty ( $appointment_data )) 
{
	foreach ( $appointment_data as $appointment )
	{		
		$patient_data =	MJ_hmgt_get_user_detail_byid($appointment->patient_id);
		$patient_name = $patient_data['first_name']." ".$patient_data['last_name']."(".$patient_data['patient_id'].")";
		$doctor_data =	MJ_hmgt_get_user_detail_byid($appointment->doctor_id);
		$doctor_name = $doctor_data['first_name']." ".$doctor_data['last_name'];
		$appointment_time_with_a=$appointment->appointment_time_with_a;
		$d = new DateTime($appointment_time_with_a); 
		 $starttime=date_format($d,'H:i:s'); 
		 $appointment_start_date=date('Y-m-d',strtotime($appointment->appointment_time_string));
		$appointment_start_date_new=$appointment_start_date." ".$starttime;
		
		$appointment_enddate = date('Y-m-d H:i:s',strtotime($appointment_start_date_new) + 900);
		
		$i=1;
			
		 $cal_array[] = array (
				'type' =>  'appointment',
				'title' =>  esc_html__( 'Appointment', 'hospital_mgt' ) ,
				'start' => $appointment_start_date_new,
				'end' =>$appointment_enddate,
				'patient_name' =>$patient_name,
				'doctor_name' =>$doctor_name,
				'appointment_time'=> $appointment->appointment_time_with_a 
			); 	
	}
}
//-------- get event data ----------//
$args['post_type'] = array('hmgt_event');
$args['posts_per_page'] = -1;
$args['post_status'] = 'public';
$q = new WP_Query();
$retrieve_class = $q->query( $args );
//$cal_array = array ();
if(!empty($retrieve_class))
{
	foreach ($retrieve_class as $retrieved_data)
	{
		
		$event_for_array=explode(",",get_post_meta( $retrieved_data->ID, 'notice_for',true));
		$i=1;
		  $cal_array[] = array (	
					'type' =>  'event',
					'title' => $retrieved_data->post_title,
					'start' => get_post_meta($retrieved_data->ID,'start_date',true),
					//'end' => get_post_meta($retrieved_data->ID,'end_date',true),
					//'end' => date('Y-m-d ',strtotime(get_post_meta($retrieved_data->ID,'end_date',true).' +'.$i.' days')) ,
					'end' => date('Y-m-d',strtotime(get_post_meta($retrieved_data->ID,'end_date',true).' +'.$i.' days')) ,
					'event_for' =>MJ_hmgt_get_role_name_in_event($event_for_array),
					'event_comment' =>$retrieved_data->post_content,
					'backgroundColor' => 'green'						
				);  
	} 
}
//--------- get notice data ----------//
$args['post_type'] = array('hmgt_notice');
$args['posts_per_page'] = -1;
$args['post_status'] = 'public';
$q = new WP_Query();
$retrieve_class1 = $q->query( $args );

if(!empty($retrieve_class1))
{
	foreach ($retrieve_class1 as $retrieved_data)
	{
		
		$notice_for_array=explode(",",get_post_meta( $retrieved_data->ID, 'notice_for',true));
		$i=1;		
	} 
}
?>

<script>
	var $ = jQuery.noConflict();
    var calendar_laungage ="<?php echo MJ_hmgt_calander_laungage();?>";
    document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
	
     var calendar = new FullCalendar.Calendar(calendarEl, {
			   height:600,
			   locale: calendar_laungage,
			   dayMaxEventRows: true,	
	           headerToolbar: {
	        left: 'prev,today,next',
	        center: 'title',
	        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
	      },
			editable: false,
			
			timeFormat: 'h(:mm)A',
			eventLimit: 1, 
			slotDuration:'00:15:00',
			events:<?php echo json_encode($cal_array);?>,			
			eventRender: function(event, element)
			{
				if(event.type=='appointment')
				{   
					element.find('.fc-title').append("<?php  esc_html__( 'Doctor', 'hospital_mgt' ) ?> :" + event.doctor_name +"  <?php esc_html__( 'Patient', 'hospital_mgt' ) ?> :" + event.patient_name + ", ");							
				}	
				if(event.type=='event')
				{ 
					element.find('.fc-content').css("background-color", "green");	
				}
				if(event.type=='notice')
				{ 
					element.find('.fc-content').css("background-color", "#F25656");	
				}				
			},		

			eventMouseover: function(event, element)
			{
				if(event.type=='appointment')
				{	
					var date = new Date(event.start);			
					var time = event.appointment_time;				
					var month = date.getMonth() + 1;
					var day = date.getDate();
					var year = date.getFullYear();						
					var full_date = year + "-" + month + "-" + day;				
					var tooltip = '<div class="tooltipevent tooltip_1"><?php  esc_html_e( 'Doctor Name', 'hospital_mgt' ) ?>  : ' + event.doctor_name + '<br> <?php  esc_html_e( 'Patient Name', 'hospital_mgt' ) ?> :' + event.patient_name +' <br>  <?php  esc_html_e( 'Date', 'hospital_mgt' ) ?>  :' + full_date +'<br> <?php  esc_html_e( 'Time', 'hospital_mgt' ) ?>  :'+ time +' </div>';
					var $tool = $(tooltip).appendTo('body');					
					
					jQuery(this).mouseover(function(e)
					{
						$(this).css('z-index', 10000);
						$tool.fadeIn('500');
						$tool.fadeTo('10', 1.9);
					}).mousemove(function(e) 
					{
						$tool.css('top', e.pageY + 5);
						$tool.css('left', e.pageX + 5);							
					});
				}
				else
				{							
					var date = new Date(event.start);
					var month = date.getMonth() + 1;
					var day = date.getDate();
					var year = date.getFullYear();						
					var full_start_date = year + "-" + month + "-" + day;		
					
					var newdate = event.end;				
					var newdate1 = new Date(newdate);
					newdate1.setDate(newdate1.getDate() - 1);
					
					var date1 = new Date(newdate1);
					var month1 = date1.getMonth() + 1;
					var day1 = date1.getDate();
					var year1 = date1.getFullYear();						
					var full_end_date = year1 + "-" + month1 + "-" + day1;		
					
					if(event.type=='event')
					{
						var tooltip = '<div class="tooltipevent tooltip_1"><?php  esc_html_e( 'Event Name', 'hospital_mgt' ) ?>  : ' + event.title + '<br> <?php  esc_html_e( 'Start Date', 'hospital_mgt' ) ?> :' + full_start_date +' <br>  <?php  esc_html_e( 'End Date', 'hospital_mgt' ) ?>  :' + full_end_date +'<br> <?php  esc_html_e( 'Event For', 'hospital_mgt' ) ?>  :'+ event.event_for +' <br> <?php  esc_html_e( 'Comment', 'hospital_mgt' ) ?>  :'+ event.event_comment +'</div>';
					}
					else
					{
						var tooltip = '<div class="tooltipevent tooltip_1"><?php  esc_html_e( 'Notice Name', 'hospital_mgt' ) ?>  : ' + event.title + '<br> <?php  esc_html_e( 'Start Date', 'hospital_mgt' ) ?> :' + full_start_date +' <br>  <?php  esc_html_e( 'End Date', 'hospital_mgt' ) ?>  :' + full_end_date +'<br> <?php  esc_html_e( 'Notice For', 'hospital_mgt' ) ?>  :'+ event.event_for +' <br> <?php  esc_html_e( 'Comment', 'hospital_mgt' ) ?>  :'+ event.event_comment +'</div>';
					}
					var $tool = $(tooltip).appendTo('body');	
					
					jQuery(this).mouseover(function(e)
					{
						$(this).css('z-index', 10000);
						$tool.fadeIn('500');
						$tool.fadeTo('10', 1.9);
					}).mousemove(function(e) 
					{	
						$tool.css('top', e.pageY + 5);
						$tool.css('left', e.pageX + 5);
					});
				}
			},
			eventMouseout: function(event, element) 
			{				
				$(this).css('z-index', 8);
				$('.tooltipevent').remove();
			}
		});	
		//calendar.setOption('locale', 'hi');
		calendar.render();	
	});
	
</script>
	
<?php MJ_hmgt_datatable_multi_language(); ?>
<!-- task-event POP up code -->
  	<div class="popup-bg">
	    <div class="overlay-content content_width">
			<div class="modal-content dashboad_1">
				<div class="task_event_list">
				</div>     
			</div>
	    </div>     
  	</div>
 <!-- End task-event POP-UP Code -->
<div class="page-inner min_height_1088">
	<!--  Page title div -->
	<div class="page-title">
		<h3><img src="<?php echo esc_url(get_option( 'hmgt_hospital_logo', 'hospital_mgt' )) ?>" class="img-circle head_logo" width="40" height="40" /><?php echo esc_html(get_option('hmgt_hospital_name','hospital_mgt'));?></h3>
	</div>
	<!--  End Page title div -->
	<!-- main-wrapper div START-->  
	<div id="main-wrapper">
	  <!-- row div START--> 
		<div class="row">
			<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
				<a href="<?php echo admin_url().'admin.php?page=hmgt_patient';?>">
					<div class="panel info-box panel-white">
						<div class="panel-body patient">
							<div class="info-box-stats">
								<p class="counter"><?php echo count(get_users(array('role'=>'patient')));?></p>
								<span class="info-box-title"><?php echo esc_html( esc_html__( 'Patient', 'hospital_mgt' ) );?>
								</span>
							</div>
							<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/patient.png"?>" class="dashboard_background">
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
				<a href="<?php echo admin_url().'admin.php?page=hmgt_doctor';?>">
					<div class="panel info-box panel-white">
						<div class="panel-body doctor">
							<div class="info-box-stats">
								<p class="counter"><?php echo count(get_users(array('role'=>'doctor')));?></p>
								<span class="info-box-title"><?php echo esc_html( esc_html__( 'Doctor', 'hospital_mgt' ) );?></span>
							</div>
							<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/doctor.png"?>" class="dashboard_background">
	                        
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
				<a href="<?php echo admin_url().'admin.php?page=hmgt_nurse';?>">
					<div class="panel info-box panel-white">
						<div class="panel-body nurse">
							<div class="info-box-stats">
								<p class="counter"><?php echo count(get_users(array('role'=>'nurse')));?></p>
								<span class="info-box-title"><?php echo esc_html( esc_html__( 'Nurse', 'hospital_mgt' ) );?></span>
							</div>
							<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/nurse.png"?>" class="dashboard_background">
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
				<a href="<?php echo admin_url().'admin.php?page=hmgt_receptionist';?>">
					<div class="panel info-box panel-white">
						<div class="panel-body receptionist">
							<div class="info-box-stats">
								<p class="counter"><?php echo count(get_users(array('role'=>'receptionist')));?></p>
								<span class="info-box-title"><?php echo esc_html( esc_html__( 'Support Staff', 'hospital_mgt' ) );?></span>
							</div>
							<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/support-staft.png"?>" class="dashboard_background">
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
				<a href="<?php echo admin_url().'admin.php?page=hmgt_message';?>">
					<div class="panel info-box panel-white">
						<div class="panel-body message">
							<div class="info-box-stats">
								<p class="counter"><?php 
								$obj_message = new MJ_hmgt_message();
								$message = $obj_message->MJ_hmgt_count_inbox_item(get_current_user_id());
								echo count($message);
								?></p>
								<span class="info-box-title"><?php echo esc_html( esc_html__( 'Message', 'hospital_mgt' ) );?></span>
							</div>
							<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/message.png"?>" class="dashboard_background">
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
			<a href="<?php echo admin_url().'admin.php?page=hmgt_gnrl_settings';?>">
				<div class="panel info-box panel-white">
					<div class="panel-body setting">
						<div class="info-box-stats">
							<p class="counter"> &nbsp;</p>
							<span class="info-box-title"><?php echo esc_html( esc_html__( 'Settings', 'hospital_mgt' ) );?></span>
						</div>
						<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/setting-image.png"?>" class="dashboard_background">
					</div>
				</div>
				</a>
			</div>
			<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
			<a href="<?php echo admin_url().'admin.php?page=hmgt_appointment';?>">
				<div class="panel info-box panel-white">
					<div class="panel-body appointment">
						<div class="info-box-stats">
							<p class="counter"><?php MJ_hmgt_tables_rows('hmgt_appointment');?></p>
							<span class="info-box-title"><?php echo esc_html( esc_html__( 'Appointment', 'hospital_mgt' ) );?></span>
						</div>
						<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/appointment-image.png"?>" class="dashboard_background">
					</div>
				</div>
			</a>
			</div>
			<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
				<a href="<?php echo admin_url().'admin.php?page=hmgt_prescription';?>">
					<div class="panel info-box panel-white">
						<div class="panel-body prescription">
							<div class="info-box-stats">
								<p class="counter"><?php MJ_hmgt_tables_rows('hmgt_priscription');?></p>
								<span class="info-box-title"><?php echo esc_html( esc_html__( 'Prescription', 'hospital_mgt' ) );?></span>
							</div>
							<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/preseription-image.png"?>" class="dashboard_background"> 
							
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
				<a href="<?php echo admin_url().'admin.php?page=hmgt_bedallotment';?>">
					<div class="panel info-box panel-white">
						<div class="panel-body assignbed">
							<div class="info-box-stats">
								<p class="counter rtl_text_align_left"><?php MJ_hmgt_tables_rows('hmgt_bed_allotment');?></p>
								
								<span class="info-box-title width_10px assign_bed_nurse"><?php echo esc_html__('Assign Bed/Nurse', 'hospital_mgt' );?></span>
							</div>
							 <img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/assign-bed-image.png"?>" class="dashboard_background"> 
							
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
				<a href="<?php echo admin_url().'admin.php?page=hmgt_treatment';?>">
					<div class="panel info-box panel-white">
						<div class="panel-body treatment">
							<div class="info-box-stats">
								<p class="counter"><?php MJ_hmgt_tables_rows('hmgt_treatment');?></p>
								
								<span class="info-box-title"><?php echo esc_html__('Treatment', 'hospital_mgt' );?></span>
							</div>
							 <img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/tretment-image.png"?>" class="dashboard_background">
							
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
				<a href="<?php echo admin_url().'admin.php?page=hmgt_event';?>">
					<div class="panel info-box panel-white">
						<div class="panel-body eventnotice">
							<div class="info-box-stats">
								<p class="counter">
								<?php 
								$args['post_type'] = array('hmgt_event','hmgt_notice');
								$args['posts_per_page'] = -1;
								$args['post_status'] = 'public';
								$q = new WP_Query();
								$retrieve_class = $q->query( $args );
									echo count($retrieve_class);
								?></p>
								
								<span class="info-box-title width_10px"><?php echo esc_html__('Events/ Notice', 'hospital_mgt' );?></span>
							</div>
							<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/notice-event-image.png"?>" class="dashboard_background">
							
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
				<a href="<?php echo admin_url().'admin.php?page=hmgt_report';?>">
					<div class="panel info-box panel-white">
						<div class="panel-body operation_report">
							<div class="info-box-stats">
								<p class="counter">&nbsp;</p>
								
								<span class="info-box-title"><?php echo esc_html( esc_html__( 'Reports', 'hospital_mgt' ) );?></span>
							</div>
							<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/report.png"?>" class="dashboard_background">
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
				<a href="<?php echo admin_url().'admin.php?page=hmgt_pharmacist';?>">
					<div class="panel info-box panel-white">
						<div class="panel-body pharmacist">
							<div class="info-box-stats">
								<p class="counter"><?php echo count(get_users(array('role'=>'pharmacist')));?></p>
								
								<span class="info-box-title"><?php echo esc_html( esc_html__( 'Pharmacist', 'hospital_mgt' ) );?></span>
							</div>
							<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/pharmacist.png"?>" class="dashboard_background">
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
				<a href="<?php echo admin_url().'admin.php?page=hmgt_medicine';?>">
					<div class="panel info-box panel-white">
						<div class="panel-body medicine">
							<div class="info-box-stats">
								<p class="counter"><?php MJ_hmgt_tables_rows('hmgt_medicine');?></p>
								
								<span class="info-box-title"><?php echo esc_html( esc_html__( 'Medicines', 'hospital_mgt' ) );?></span>
							</div>
							<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/medicine.png"?>" class="dashboard_background"> 
							
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
				<a href="<?php echo admin_url().'admin.php?page=hmgt_laboratorist';?>">
					<div class="panel info-box panel-white">
						<div class="panel-body laboratorist">
							<div class="info-box-stats">
								<p class="counter"><?php echo count(get_users(array('role'=>'laboratorist')));?></p>
								
								<span class="info-box-title width_10px"><?php echo esc_html__('Laboratory Staff', 'hospital_mgt' );?></span>
							</div>
							<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/laboratorist.png"?>" class="dashboard_background">
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
				<a href="<?php echo admin_url().'admin.php?page=hmgt_diagnosis';?>">
					<div class="panel info-box panel-white">
						<div class="panel-body diagnosis">
							<div class="info-box-stats">
								<p class="counter"><?php MJ_hmgt_tables_rows('hmgt_diagnosis');?></p>
								
								<span class="info-box-title width_10px"><?php echo esc_html__('Diagnosis Reports', 'hospital_mgt' );?></span>
							</div>
							<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/diagnosis-image.png"?>" class="dashboard_background">
							
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
				<a href="<?php echo admin_url().'admin.php?page=hmgt_accountant';?>">
					<div class="panel info-box panel-white">
						<div class="panel-body accountant">
							<div class="info-box-stats">
								<p class="counter"><?php echo count(get_users(array('role'=>'accountant')));?></p>
								<span class="info-box-title"><?php echo esc_html( esc_html__( 'Accountant', 'hospital_mgt' ) );?></span>
							</div>					
	                        <img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/accountant.png"?>" class="dashboard_background">
						</div>
					</div>
				</a>
			</div>
			<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
				<a href="<?php echo admin_url().'admin.php?page=hmgt_invoice';?>">
					<div class="panel info-box panel-white">
						<div class="panel-body invoice">
							<div class="info-box-stats">
								<p class="counter"><?php MJ_hmgt_tables_rows('hmgt_invoice');?></p>
								
								<span class="info-box-title"><?php echo esc_html( esc_html__( 'Invoice', 'hospital_mgt' ) );?></span>
							</div>
							<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/invoice.png"?>" class="dashboard_background"> 
						</div>
					</div>
				</a>
			</div>		
		</div>		
		<!-- Rinkal changes --> 
		<div class="row dashboard_panel_heading_border">
			<div class="col-md-6 no-paddingR">
				<!--  Start Prescription Box -->
				<div class="panel panel-white event priscription">
					<div class="panel-heading ">					
					<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/Prescription.png"?>" >
						<h3 class="panel-title"><?php esc_html_e('Prescription','hospital_mgt');?></h3>						
					</div>
					<div class="panel-body">
						<div class="events">
							<?php 
							$obj_var=new MJ_hmgt_prescription();
							$prescriptiondata=$obj_var->MJ_hmgt_get_prescription_on_admin_dashboard();
							if(!empty($prescriptiondata))
							{
								foreach ($prescriptiondata as $retrieved_data)
								{ 
								?>								
									<div class="calendar-event"> 
										<p class="remainder_title_pr Bold viewpriscription show_task_event" id="<?php echo esc_attr($retrieved_data->priscription_id); ?>" model="Prescription Details" >  <?php esc_html_e('Patient Name','hospital_mgt');?> : 
										<?php 	$patient = MJ_hmgt_get_user_detail_byid( $retrieved_data->patient_id);
												echo esc_html($patient['first_name']." ".$patient['last_name']);
											?>
										</p>
										<p class="remainder_date_pr"> <?php  echo date(MJ_hmgt_date_formate(),strtotime($retrieved_data->pris_create_date));?> </p>
										<p class="remainder_title_pr viewpriscription" > <?php esc_html_e('Description','hospital_mgt');?>	 : <?php
											if($retrieved_data->prescription_type=='report')
											{		
												echo esc_html($retrieved_data->report_description); 
											}
											else
											{
												echo esc_html($retrieved_data->case_history); 
											}											
										?></p>
									</div>	
							<?php
								}
							}	
							else
							{
								?>
								<div class="calendar-event"> 
									<p class="remainder_title_pr Bold">  <?php esc_html_e('No Data Available','hospital_mgt');?>
									</p>
								</div>	
								<?php
							}	
							?>	
						</div>                       
					</div>
				</div>
				<!-- End Prescription Box -->
						
				<!--  Start Operation Box -->
				<div class="panel panel-white event operation">
					<div class="panel-heading ">
					<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/Operation-List.png"?>" >
					<h3 class="panel-title"><?php esc_html_e('Operation','hospital_mgt');?></h3>						
					</div>
					<div class="panel-body">
						<div class="events">
							<?php
							$obj_ot = new MJ_hmgt_operation();
							$ot_data=$obj_ot->MJ_hmgt_get_operation_on_admin_dashboard();
							if(!empty($ot_data))
							{
								foreach ($ot_data as $retrieved_data)
								{		
									$patient_data =	MJ_hmgt_get_user_detail_byid($retrieved_data->patient_id);		
								?>
									<div class="calendar-event"> 
										<p class="remainder_title_pr Bold viewoperation show_task_event" id="<?php echo esc_attr($retrieved_data->operation_id); ?>" model="Operation Details">	<?php esc_html_e('Patient Name','hospital_mgt');?> : <?php echo esc_html($patient_data['first_name']." ".$patient_data['last_name']); ?></p>
										<p class="remainder_date_pr"> <?php  echo date(MJ_hmgt_date_formate(),strtotime($retrieved_data->operation_date));	?> 	</p>
										
										<p class="remainder_title_pr  viewoperation"  > <?php esc_html_e('Operation Name','hospital_mgt');?>: <?php echo $obj_ot->MJ_hmgt_get_operation_name($retrieved_data->operation_title);?></p>
									</div>	
								<?php
								}
							}	
							else
							{
								?>
								<div class="calendar-event"> 
									<p class="remainder_title_pr Bold">  <?php esc_html_e('No Data Available','hospital_mgt');?>
									</p>
								</div>	
								<?php
							}	
							?>		
						</div>                       
					</div>
				</div>
				<!-- End Operation Box -->
				<div class="panel panel-white">
				   <div class="panel-heading margin_bottom_15 cal_font">
						<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/calender.png"?>" >
						<h3 class="panel-title"><?php esc_html_e('Calendar','hospital_mgt');?></h3>			
					</div>
					<div class="panel-body">
						<div id="calendar" class="x_content full_calender"></div>
					</div>
				</div>
			 </div>
			<!-- Start Appointment Box -->
				<div class="col-md-6">
					<div class="panel panel-white Appoinment">
						<div class="panel-heading">
							<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/Appointment.png"?>" >
							<h3 class="panel-title"><?php esc_html_e('Appointment','hospital_mgt');?></h3>
						</div>
						<div class="panel-body">
							<div class="events">
								<?php								
								$appointment_data=$obj_appointment->MJ_hmgt_get_appointment_on_admin_dashboard();
								
								if(!empty($appointment_data))
								{
									foreach ($appointment_data as $retrieved_data)
									{		
										$patient_data =	MJ_hmgt_get_user_detail_byid($retrieved_data->patient_id);
									?>									
									<div class="calendar-event"> 
										<p class="remainder_title Bold save1 show_task_event" id="<?php echo esc_attr($retrieved_data->appointment_id); ?>" model="Appointment Details">
										<?php esc_html_e('Patient Name','hospital_mgt');?> : <?php  echo esc_html($patient_data['first_name']." ".$patient_data['last_name']); ?>  </p>
										<p class="remainder_date width_160">
										<?php 
										echo date(MJ_hmgt_date_formate(),strtotime($retrieved_data->appointment_date)); ?>		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $retrieved_data->appointment_time_with_a; ?>
										</p>
									</div>	
									<?php
									}
								}
								else
								{
									?>
									<div class="calendar-event"> 
										<p class="remainder_title_pr Bold">  <?php esc_html_e('No Data Available','hospital_mgt');?>
										</p>
									</div>	
									<?php
								}	
								?>
								
							</div>					
						</div>
					</div>
				<!--  End Appoinment box -->
				 
				<!--  Start assigned bed Box -->
				<div class="panel panel-white event assignbed">
					<div class="panel-heading">
						<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/Assign--Bed-nurse.png"?>" >
						<h3 class="panel-title"><?php esc_html_e('Assigned Bed','hospital_mgt');?></h3>						
					</div>
					<div class="panel-body">
						<div class="events">
							<?php
							$obj_bed = new MJ_hmgt_bedmanage();
							$bedallotment_data=$obj_bed->MJ_hmgt_get_bedallotment_on_admin_dashboard();
							if(!empty($bedallotment_data))
							{
								foreach ($bedallotment_data as $retrieved_data)
								{
								?>									
									<div class="calendar-event"> 
										<p class="remainder_title Bold viewbedlist show_task_event" id="<?php echo esc_attr($retrieved_data->bed_allotment_id); ?>" model="Assigned Bed Details" > <?php esc_html_e('Patient Name','hospital_mgt'); ?> : 	  
											<?php
											$patient_data =	MJ_hmgt_get_user_detail_byid($retrieved_data->patient_id);	
											echo esc_html($patient_data['first_name']." ".$patient_data['last_name']);
											?>
										</p>
										<p class="remainder_date">	<?php  echo date(MJ_hmgt_date_formate(),strtotime($retrieved_data->allotment_date));?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php  echo date(MJ_hmgt_date_formate(),strtotime($retrieved_data->discharge_time));?> </p>
									</div>		
								<?php
								}
							}
							else
							{
								?>
								<div class="calendar-event"> 
									<p class="remainder_title_pr Bold">  <?php esc_html_e('No Data Available','hospital_mgt');?>
									</p>
								</div>	
								<?php
							}		
							?>	
						</div>                       
					</div>
				</div>
				<!-- End assigned bed Box -->
				<!--  Start Event Box -->
				<div class="panel panel-white event">
					<div class="panel-heading ">
						<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/event.png"?>" >
						<h3 class="panel-title"><?php esc_html_e('Events','hospital_mgt');?></h3>						
					</div>					
					<div class="panel-body">
						<div class="events">	
						<?php         
						$args = array(
						  'numberposts' => 3,
						  'post_type'   => 'hmgt_event',
						  'order'     => 'DESC',
						  'orderby'   => 'ID'
						);
						$retrieve_class = get_posts($args);
						
						if(!empty($retrieve_class))
						{ 
							foreach ($retrieve_class as $retrieved_data)
							{ 
							?>
								<div class="calendar-event">
									<p class="remainder_title Bold viewdetail show_task_event" id="<?php echo esc_attr($retrieved_data->ID); ?>" model="Event Details">
										<?php echo esc_html($retrieved_data->post_title); ?>
									</p>									
									<p class="remainder_date">
										<?php echo date(MJ_hmgt_date_formate(),strtotime(get_post_meta($retrieved_data->ID,'start_date',true))); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo date(MJ_hmgt_date_formate(),strtotime(get_post_meta($retrieved_data->ID,'end_date',true))); ?>
									</p>
									<p class="remainder_title viewdetail">	
										<?php echo esc_html($retrieved_data->post_content); ?>
									</p>
								</div>
							<?php
							}
						}
						else
						{
							?>
							<div class="calendar-event"> 
								<p class="remainder_title_pr Bold">  <?php esc_html_e('No Data Available','hospital_mgt');?>
								</p>
							</div>	
							<?php
						}	
						?>
						</div>                       
					</div>
				</div>
				<!-- End Event Box -->
				<!--  Start Notice box -->
				<div class="panel panel-white nt">
					<div class="panel-heading">
					<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/notice1.png"?>" >
					<h3 class="panel-title"><?php esc_html_e('Notice','hospital_mgt');?></h3>						
					</div>
					<div class="panel-body">
						<div class="events">
							<?php         
							$args = array(
							  'numberposts' => 3,
							  'post_type'   => 'hmgt_notice',
							  'order'     => 'DESC',
							  'orderby'   => 'ID'
							);
							
							$retrieve_class = get_posts($args);
							
							if(!empty($retrieve_class))
							{ 
								foreach ($retrieve_class as $retrieved_data)
								{ 
								?>						
									<div class="calendar-event"> 
										<p class="remainder_title Bold viewdetail show_task_event" id="<?php echo esc_attr($retrieved_data->ID); ?>" model="Notice Details">	
											<?php echo esc_html($retrieved_data->post_title); ?>	
										</p>
										<p class="remainder_date">	<?php echo date(MJ_hmgt_date_formate(),strtotime(get_post_meta($retrieved_data->ID,'start_date',true))); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo date(MJ_hmgt_date_formate(),strtotime(get_post_meta($retrieved_data->ID,'end_date',true))); ?></p>
										<p class="remainder_title viewdetail"><?php echo esc_html($retrieved_data->post_content); ?></p>
									</div>	
							<?php
							}
						}
						else
						{
							?>
							<div class="calendar-event"> 
								<p class="remainder_title_pr Bold">  <?php esc_html_e('No Data Available','hospital_mgt');?>
								</p>
							</div>	
							<?php
						}	
						?>					
						</div>
					</div>
				</div>
				<div class="panel panel-white">
					<div class="panel-heading income_report">
						<h3 class="panel-title"><i class="fa fa-shopping-cart" aria-hidden="true"></i> <?php esc_html_e('Income Report','hospital_mgt');?></h3>						
					</div>
					<div class="panel-body">
							<?php 
							$total_amount=0;
							$month =array('1'=>esc_html__('January','hospital_mgt'),'2'=>esc_html__('February','hospital_mgt'),'3'=>esc_html__('March','hospital_mgt'),'4'=>esc_html__('April','hospital_mgt'),'5'=>esc_html__('May','hospital_mgt'),'6'=>esc_html__('June','hospital_mgt'),'7'=>esc_html__('July','hospital_mgt'),'8'=>esc_html__('August','hospital_mgt'),'9'=>esc_html__('September','hospital_mgt'),'10'=>esc_html__('October','hospital_mgt'),'11'=>esc_html__('November','hospital_mgt'),'12'=>esc_html__('December','hospital_mgt'),);			 
							$year =isset($_POST['year'])?$_POST['year']:date('Y');
							$currency=MJ_hmgt_get_currency_symbol();
							global $wpdb;
							$table_name = $wpdb->prefix."hmgt_income_expense";
							$result1 = $wpdb->get_results("select * from $table_name WHERE invoice_type='income'"); 	
							if(!empty($result1))
							 {
								
								foreach ($result1 as $retrieved_data)
								{
								$all_entry=json_decode($retrieved_data->income_entry);
									
									foreach($all_entry as $entry)
									{
										$total_amount+= (int)$entry->amount;
									}
								}
							}	
							//$q="SELECT EXTRACT(MONTH FROM income_create_date) as date,sum(".$total_amount.") as count FROM ".$table_name." WHERE YEAR(income_create_date) =".$year." group by month(income_create_date) ORDER BY income_create_date AND invoice_type='income' ASC";
                            $q="SELECT EXTRACT(MONTH FROM income_create_date) as date,$total_amount as count FROM ".$table_name." WHERE invoice_type = 'income' AND YEAR(income_create_date) =".$year." group by month(income_create_date) ORDER BY income_create_date ASC";
							
							$result=$wpdb->get_results($q);
				
							$result_merge_array=array_merge($result);

							$sumArray = array(); 
							foreach ($result_merge_array as $value) 
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
							$chart_array[] = array(esc_html__('Month','hospital_mgt'),esc_html__('Income Payment','hospital_mgt'));
							$i=1;

							foreach($sumArray as $month_value=>$count)
							{
								$chart_array[]=array($month[$month_value],(int)$count);
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
						if(!empty($result_merge_array))
						{
							$chart = $GoogleCharts->load( 'column' , 'chart_div_payment' )->get( $chart_array , $options );
						}
						if(isset($result_merge_array) && count($result_merge_array) >0)
						{
							
						?>
							<div id="chart_div_payment" class="chart_dashboard"></div>
						  <!-- Javascript --> 
						  <script type="text/javascript" src="https://www.google.com/jsapi"></script> 
						  <script type="text/javascript">
									<?php echo $chart;?>
							</script>
					  <?php 
						}
						if(isset($result_merge_array) && empty($result_merge_array))
						{?>
							<div class="calendar-event"> 
								<p class="remainder_title_pr Bold">  <?php esc_html_e('No Data Available','hospital_mgt');?>
								</p>
							</div>	
						   <!-- <div class="clear col-md-12 error_msg"><?php //esc_html_e("No data available",'hospital_mgt');?></div> -->
					   <?php }?>
				 
					</div>
				</div>
			<div class="clear"></div>	
			<div class="panel panel-white">
				<div class="panel-heading income_report">
					<h3 class="panel-title"><i class="fa fa-shopping-cart" aria-hidden="true"></i> <?php esc_html_e('Expenses Report','hospital_mgt');?></h3>						
				</div>
				<div class="panel-body">
					<?php 
					$month =array('1'=>esc_html__('January','hospital_mgt'),'2'=>esc_html__('February','hospital_mgt'),'3'=>esc_html__('March','hospital_mgt'),'4'=>esc_html__('April','hospital_mgt'),'5'=>esc_html__('May','hospital_mgt'),'6'=>esc_html__('June','hospital_mgt'),'7'=>esc_html__('July','hospital_mgt'),'8'=>esc_html__('August','hospital_mgt'),'9'=>esc_html__('September','hospital_mgt'),'10'=>esc_html__('October','hospital_mgt'),'11'=>esc_html__('November','hospital_mgt'),'12'=>esc_html__('December','hospital_mgt'),);			 
					$year =isset($_POST['year'])?$_POST['year']:date('Y');
					$currency=MJ_hmgt_get_currency_symbol();
					global $wpdb;
					$table_name = $wpdb->prefix."hmgt_income_expense";
					$result1 = $wpdb->get_results("select * from $table_name WHERE invoice_type='expense'"); 	
					
					if(!empty($result1))
					 {
						 $total_amount=0;
						foreach ($result1 as $retrieved_data)
						{
						$all_entry=json_decode($retrieved_data->income_entry);
							
							foreach($all_entry as $entry)
							{
								$total_amount+=$entry->amount;
							}
						}
					}

					//$test=array_sum($total_amount);
					$q="SELECT EXTRACT(MONTH FROM income_create_date) as date,$total_amount as count FROM ".$table_name." WHERE invoice_type = 'expense' AND YEAR(income_create_date) =".$year." group by month(income_create_date) ORDER BY income_create_date ASC";
					
					//$q="SELECT EXTRACT(MONTH FROM income_create_date) as date,sum(".$total_amount.") as count FROM ".$table_name." WHERE YEAR(income_create_date) =".$year." group by month(income_create_date) ORDER BY income_create_date AND invoice_type='expense' ASC";
					$result=$wpdb->get_results($q);
					$result_merge_array1=array_merge($result);
					$sumArray = array(); 
					foreach ($result_merge_array1 as $value) 
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
					$chart_array[] = array(esc_html__('Month','hospital_mgt'),esc_html__('Expense Payment','hospital_mgt'));
					$i=1;

					foreach($sumArray as $month_value=>$count)
					{
						$chart_array[]=array($month[$month_value],(int)$count);
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
									'title' => __('Expense','hospital_mgt'),
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
						if(!empty($result_merge_array1))
						{
							$chart = $GoogleCharts->load( 'column' , 'chart_div_payment1' )->get( $chart_array , $options );
						}
						if(isset($result_merge_array1) && count($result_merge_array1) >0)
						{
						?>
							<div id="chart_div_payment1" class="chart_dashboard"></div>
					  
						  <!-- Javascript --> 
						  <script type="text/javascript" src="https://www.google.com/jsapi"></script> 
						  <script type="text/javascript">
							<?php echo $chart;?>
						  </script>
					  <?php 
						}
					 if(isset($result_merge_array1) && empty($result_merge_array1))
					 {?>
						<div class="calendar-event"> 
								<p class="remainder_title_pr Bold">  <?php esc_html_e('No Data Available','hospital_mgt');?>
								</p>
							</div>	
					<?php }?>
				</div>
			</div>
				<!--  End Notice box -->
			</div>
		</div><!--  end ROW DIV -->	
	</div><!--  end MAIN WRAPPER DIV -->	
</div><!--  end PAGE INNER DIV -->	
<?php ?>