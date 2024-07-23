<?php
//-------------- Paytm Success -----------------//
if(isset($_REQUEST['STATUS']) && $_REQUEST['STATUS'] == 'TXN_SUCCESS')
{
	$paymentdata['invoice_id']=$_REQUEST['MERC_UNQ_REF'];
	$paymentdata['invoice_type']="income";
	$paymentdata['party_name']=$_REQUEST['user_id'];
	$paymentdata['invoice_date']=date("Y-m-d");
	$paymentdata['income_entry']=array("income");
	$paymentdata['income_amount']=array($_REQUEST['TXNAMOUNT']);
	$paymentdata['payment_method']='Paytm';	
	$paymentdata['payment_description']='Paytm';	
	$paymentdata['transaction_id']="";

	$obj_invoice= new MJ_hmgt_invoice();
	$PaymentSucces =$obj_invoice->MJ_hmgt_add_income($paymentdata);
	if($PaymentSucces)
	{ 
		wp_redirect ( home_url() . '/?dashboard=user&page=invoice&&tab=invoicelist&action=success');
		exit;
	}	
} 
//--------------PAYPAL Success ---------------//
if(isset($_POST['payer_status']) && $_POST['payer_status'] == 'VERIFIED' && (isset($_POST['payment_status'])) && $_POST['payment_status']=='Completed' && isset($_REQUEST['half']) && $_REQUEST['half']=='yes' )
{

	$transaction_id  = $_POST["txn_id"];
	$custom_array = explode("_",$_POST['custom']);
	$paymentdata['invoice_id']=$custom_array[1];
	$paymentdata['invoice_type']=$_POST['item_name1'];
	$paymentdata['party_name']=$custom_array[0];
	$paymentdata['invoice_date']=date("Y-m-d");
	$paymentdata['income_entry']=array($custom_array[3]);
	$paymentdata['income_amount']=array($_POST['mc_gross_1']);
	$paymentdata['payment_method']='paypal';	
	$paymentdata['payment_description']='paypal';	
	$paymentdata['transaction_id']=$transaction_id ;

	$obj_invoice= new MJ_hmgt_invoice();
	$PaymentSucces =$obj_invoice->MJ_hmgt_add_income($paymentdata);
	if($PaymentSucces)
	{ 
		wp_redirect ( home_url() . '/?dashboard=user&page=invoice&&tab=invoicelist&action=success');
		exit;
	}		
}
require_once(ABSPATH.'wp-admin/includes/user.php' );
$obj_hospital = new MJ_hmgt_Hospital_Management(get_current_user_id());

if (! is_user_logged_in ()) 
{
	$page_id = get_option ( 'hmgt_login_page' );
	wp_redirect ( home_url () . "?page_id=" . $page_id );
}
global $current_user;
$user_roles = $current_user->roles;
$user_role = array_shift($user_roles);
if (is_super_admin ()OR $user_role == 'management')
{
	wp_redirect ( admin_url () . 'admin.php?page=hmgt_hospital' );
}
// Get appointment data//
$obj_appointment = new MJ_hmgt_appointment();
$obj_virtual_appointment = new MJ_hmgt_virtual_appointment;
$appointment_data = $obj_hospital->appointment;
$cal_array = array ();
if (! empty ( $appointment_data ))
{
	foreach ( $appointment_data as $appointment )
	{
		//var_dump($appointment);
		$meeting_data = $obj_virtual_appointment->MJ_hmgt_get_singal_meeting_data_in_zoom_with_appointment_id($appointment->appointment_id);
		//var_dump($meeting_data);
		if(!empty($meeting_data->meeting_join_link))
		{
			$meeting_join_link=$meeting_data->meeting_join_link;
		}
		else
		{
			$meeting_join_link="";
		}
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

		$date_and_time=date(MJ_hmgt_date_formate(),strtotime($appointment->appointment_date))."(".MJ_hmgt_appoinment_time_language_translation($appointment->appointment_time_with_a).")";
		if(!empty($meeting_data->agenda))
		{
			$agenda=$meeting_data->agenda;
		}
		else
		{
			$agenda='';
		}
		if(!empty($meeting_data->zoom_meeting_id))
		{
			$zoom_meeting_id=$meeting_data->zoom_meeting_id;
		}
		else
		{
			$zoom_meeting_id='';
		}
		if(!empty($meeting_data->password))
		{
			$password123=$meeting_data->password;
		}
		else
		{
			$password123='';
		}
		$i=1;
		$cal_array [] = array (
			'type' =>  'appointment',
			'title' =>  esc_html__( 'Appointment', 'hospital_mgt' ) ,
			'start' => $appointment_start_date_new,
			'end' =>$appointment_enddate,
			'patient_name' =>$patient_name,
			'doctor_name' =>$doctor_name,
			'meeting_join_link' =>$meeting_join_link,
			'topic' =>$agenda,
			'zoom_meeting_id' =>$zoom_meeting_id,
			'meeting_password' =>$password123,
			'date_and_time' =>$date_and_time,
			'appointment_date' =>$appointment->appointment_date,
			'appointment_time'=> $appointment->appointment_time_with_a);
	}
}
//get event data
$args['post_type'] = array('hmgt_event');
$args['posts_per_page'] = -1;
$args['post_status'] = 'public';
$q = new WP_Query();
$retrieve_class = $q->query( $args );

if(!empty($retrieve_class))
{
	foreach ($retrieve_class as $retrieved_data)
	{
		$event_for_array=explode(",",get_post_meta( $retrieved_data->ID, 'notice_for',true));
		$role=MJ_hmgt_get_current_user_role();	
		
		if(in_array($role,$event_for_array))
		{
			$i=1;
			$cal_array [] = array (	
					'type' =>  'event',
					'title' => $retrieved_data->post_title,
					'start' => get_post_meta($retrieved_data->ID,'start_date',true),
					'end' => date('Y-m-d',strtotime(get_post_meta($retrieved_data->ID,'end_date',true).' +'.$i.' days')) ,
					'event_for' =>MJ_hmgt_get_role_name_in_event($event_for_array),
					'event_comment' =>$retrieved_data->post_content,
					'backgroundColor' => 'green'						
				);	
		}
	} 
}
//get notice data
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
		$role=MJ_hmgt_get_current_user_role();	
		
		if(in_array($role,$notice_for_array))
		{
			$i=1;
			$cal_array [] = array (	
						'type' =>  'notice',
						'title' => $retrieved_data->post_title,
						'start' => get_post_meta($retrieved_data->ID,'start_date',true),
						'end' =>date('Y-m-d',strtotime(get_post_meta($retrieved_data->ID,'end_date',true).' +'.$i.' days')),
						'event_for' =>MJ_hmgt_get_role_name_in_event($notice_for_array),
						'event_comment' =>$retrieved_data->post_content,				
						'backgroundColor' => '#F25656'								
					);	
		}
	}
}
?>
<div id="zoom_booked_popup" class="modal-body " style="display:none;"><!--MODAL BODY DIV START-->
	<style>
	    .ui-dialog 
		{
		    z-index: 10000;
	    }
	    #zoom_booked_popup p
	    {
	    	margin-bottom: 0;
	    }
	    #zoom_booked_popup
	    {
	    	padding-top: 18px;
	    }
	    #join_link_href
	    {
	    	color: white;
	    }
	    .ui-dialog-titlebar-close
	    {
	    	height: 22px !important;
    		font-size: 11px !important;
	    }
	</style>
	<p><b><?php esc_html_e('Doctor Name:','hospital_mgt');?></b> <span id="doctor_name"></span></p><br>
	<p><b><?php esc_html_e('Patient Name:','hospital_mgt');?></b> <span id="patient_name"></span></p><br>
	<p><b><?php esc_html_e('Date & Time:','hospital_mgt');?> </b> <span id="dateTime"></span></p><br>
	<p><b><?php esc_html_e('Topic:','hospital_mgt');?></b> <span id="topic"></span></p><br>
	<p><b><?php esc_html_e('Meeting ID:','hospital_mgt');?></b> <span id="zoom_meeting_id"></span></p><br>
	<p><b><?php esc_html_e('Password:','hospital_mgt');?></b> <span id="meeting_password"></span></p><br>
	<form method="post" accept-charset="utf-8">
		<a id="join_link_href" href="javascript:void(0);" class="btn btn-success" target="_blank"><i class="fa fa-video-camera" aria-hidden="true"></i> <?php esc_html_e('Join','hospital_mgt');?> </a>
	</form>		
</div><!--MODAL BODY DIV END-->
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<script type="text/javascript"	src="<?php echo HMS_PLUGIN_URL.'/assets/js/popper.min.js'; ?>"></script>
<link rel="stylesheet"	type = "text/css" href="<?php echo HMS_PLUGIN_URL.'/assets/css/sweetalert.css'; ?>"/>
<link rel="stylesheet"	type = "text/css" href="<?php echo HMS_PLUGIN_URL.'/assets/css/example.css'; ?>"/>
<link rel="stylesheet"	type = "text/css" href="<?php echo HMS_PLUGIN_URL.'/assets/css/bootstrap-select.min.css'; ?>"/>
<link rel="stylesheet"	type = "text/css" href="<?php echo HMS_PLUGIN_URL.'/assets/css/dataTables.css'; ?>"/>
<link rel="stylesheet"	type = "text/css" href="<?php echo HMS_PLUGIN_URL.'/assets/css/dataTables-editor.css'; ?>"/>
<link rel="stylesheet"	type = "text/css" href="<?php echo HMS_PLUGIN_URL.'/assets/css/dataTables-tableTools.css'; ?>"/>
<link rel="stylesheet"	type = "text/css" href="<?php echo HMS_PLUGIN_URL.'/assets/css/dataTables-responsive.css'; ?>"/>
<link rel="stylesheet"	type = "text/css" href="<?php echo HMS_PLUGIN_URL.'/assets/css/jquery-ui.css'; ?>"/>
<link rel="stylesheet"	type = "text/css" href="<?php echo HMS_PLUGIN_URL.'/assets/css/font-awesome.css'; ?>"/>
<link rel="stylesheet"	type = "text/css" href="<?php echo HMS_PLUGIN_URL.'/assets/css/popup.css'; ?>"/>
<link rel="stylesheet"	type = "text/css" href="<?php echo HMS_PLUGIN_URL.'/assets/css/style.css'; ?>"/>
<link rel="stylesheet"	type = "text/css" href="<?php echo HMS_PLUGIN_URL.'/assets/css/custom.css'; ?>"/>
<link rel="stylesheet"	type = "text/css" href="<?php echo HMS_PLUGIN_URL.'/assets/css/fullcalendar.css'; ?>"/>
<link rel="stylesheet"	type = "text/css" href="<?php echo HMS_PLUGIN_URL.'/lib/select2-3.5.3/select2.css'; ?>"/>
<link rel="stylesheet"	type = "text/css" href="<?php echo HMS_PLUGIN_URL.'/assets/css/bootstrap.min.css'; ?>"/>
<link rel="stylesheet"	type = "text/css" href="<?php echo HMS_PLUGIN_URL.'/assets/css/datepicker-defualt.css'; ?>"/>  
<link rel="stylesheet"	type = "text/css" href="<?php echo HMS_PLUGIN_URL.'/assets/css/time.css'; ?>"/>  
<link rel="stylesheet"	type = "text/css" href="<?php echo HMS_PLUGIN_URL.'/assets/css/bootstrap-multiselect.min.css'; ?>"/>	
<link rel="stylesheet"	type = "text/css" href="<?php echo HMS_PLUGIN_URL.'/assets/css/white.css'; ?>"/>
<link rel="stylesheet"	type = "text/css" href="<?php echo HMS_PLUGIN_URL.'/assets/css/hospitalmgt.css'; ?>"/>
<?php  if (is_rtl())
		 {?>
			<link rel="stylesheet" type = "text/css" href="<?php echo HMS_PLUGIN_URL.'/assets/css/bootstrap-rtl.css'; ?>"/>
			<link rel="stylesheet" type="text/css"	href="<?php echo HMS_PLUGIN_URL.'/assets/css/custom-rtl.css'; ?>"/>		
		<?php  } ?>

<link rel="stylesheet"	type = "text/css" href="<?php echo HMS_PLUGIN_URL.'/lib/validationEngine/css/validationEngine-jquery.css'; ?>"/>
<link rel="stylesheet"	type = "text/css" href="<?php echo HMS_PLUGIN_URL.'/assets/css/hospital-responsive.css'; ?>"/>
<link rel="stylesheet"	type = "text/css" href="<?php echo HMS_PLUGIN_URL.'/lib/bootstrap-fileinput-master/css/fileinput-default.css'; ?>"/>
<script type="text/javascript"	src="<?php echo HMS_PLUGIN_URL.'/assets/js/jquery-3-6-0.js'; ?>"></script>
<script type="text/javascript" src="<?php echo HMS_PLUGIN_URL.'/lib/select2-3.5.3/select2.js'; ?>"></script>
<script type="text/javascript"	src="<?php echo HMS_PLUGIN_URL.'/assets/js/jquery-ui.js'; ?>"></script>
<script type="text/javascript"	src="<?php echo HMS_PLUGIN_URL.'/assets/js/jquery-timeago.js'; ?>"></script>
<script type="text/javascript"	src="<?php echo HMS_PLUGIN_URL.'/assets/js/moment.js'; ?>"></script>
<script type="text/javascript"	src="<?php echo HMS_PLUGIN_URL.'/assets/js/bootstrap-select.min.js'; ?>"></script>
<script type="text/javascript"	src="<?php echo HMS_PLUGIN_URL.'/assets/js/fullcalendar.js'; ?>"></script>
<script type="text/javascript"	src="<?php echo HMS_PLUGIN_URL.'/assets/js/jquery.dataTables.min.js'; ?>"></script>
<script type="text/javascript"	src="<?php echo HMS_PLUGIN_URL.'/assets/js/dataTables-tableTools.js'; ?>"></script>
<script type="text/javascript"	src="<?php echo HMS_PLUGIN_URL.'/assets/js/dataTables-editor.js'; ?>"></script>
<script type="text/javascript"	src="<?php echo HMS_PLUGIN_URL.'/assets/js/dataTables-responsive.js'; ?>"></script>
<script type="text/javascript"	src="<?php echo HMS_PLUGIN_URL.'/assets/js/bootstrap.min.js'; ?>"></script>
<script type="text/javascript"	src="<?php echo HMS_PLUGIN_URL.'/assets/js/bootstrap-datepicker.js'; ?>"></script>
<script type="text/javascript"	src="<?php echo HMS_PLUGIN_URL.'/assets/js/time.js'; ?>"></script>
<script type="text/javascript"	src="<?php echo HMS_PLUGIN_URL.'/assets/js/bootstrap-multiselect.js'; ?>"></script>
<script type="text/javascript"	src="<?php echo HMS_PLUGIN_URL.'/assets/js/responsive-tabs.js'; ?>"></script>
<script type="text/javascript"	src="<?php echo HMS_PLUGIN_URL.'/assets/js/sweetalert-dev.js'; ?>"></script>

<?php		
$lancode=get_locale();
$code=substr($lancode,0,2);
?>
<script type="text/javascript"	src="<?php echo HMS_PLUGIN_URL.'/assets/js/calendar-lang/'.$code.'.js'; ?>"></script>
<?php $lancode=get_locale();
$code=substr($lancode,0,2);	
?>
<script type="text/javascript"	src="<?php echo HMS_PLUGIN_URL.'/lib/validationEngine/js/languages/jquery.validationEngine-'.$code.'.js'; ?>"></script>
<script type="text/javascript"	src="<?php echo HMS_PLUGIN_URL.'/lib/validationEngine/js/jquery-validationEngine.js'; ?>"></script>
<script type="text/javascript"	src="<?php echo HMS_PLUGIN_URL.'/lib/bootstrap-fileinput-master/js/plugins/canvas-to-blob.min.js'; ?>"></script>
<script type="text/javascript"	src="<?php echo HMS_PLUGIN_URL.'/lib/bootstrap-fileinput-master/js/fileinput-default.js'; ?>"></script>
<script type="text/javascript"	src="<?php echo HMS_PLUGIN_URL.'/lib/bootstrap-fileinput-master/js/fileinput.js'; ?>"></script>
<script type="text/javascript"	src="<?php echo HMS_PLUGIN_URL.'/lib/bootstrap-fileinput-master/js/fileinput_locale_es.js'; ?>"></script>

<script>
    var $ = jQuery.noConflict();
    var calendar_laungage ="<?php echo MJ_hmgt_calander_laungage();?>";
    document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
     var calendar = new FullCalendar.Calendar(calendarEl, {
			 height:600,
			  locale: calendar_laungage,
	           eventLimit: true, // allow "more" link when too many events		
	           headerToolbar: {
			left: 'prev,today,next',
	        center: 'title',
	        right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
	      },
			editable: false,
			eventTimeFormat: { // like '14:30:00'
		   		hour: 'numeric',
			   	minute: '2-digit',
			  	meridiem: 'short'
	  		},
			//timeFormat: 'h(:mm)A',
			dayMaxEventRows: 1,	
			slotDuration:'00:15:00',			
			events:<?php echo json_encode($cal_array);?>,
			eventClick:  function(event, jsEvent, view) 
	        {
	        	//----------FOR ZOOM ----------//
				if(event.event._def.extendedProps.type=='appointment')
				{
					$("#zoom_booked_popup #doctor_name").html(event.event._def.extendedProps.doctor_name);
					$("#zoom_booked_popup #patient_name").html(event.event._def.extendedProps.patient_name);
					$("#zoom_booked_popup #dateTime").html(event.event._def.extendedProps.date_and_time);					
					$("#zoom_booked_popup #topic ").html(event.event._def.extendedProps.topic);
					$("#zoom_booked_popup #zoom_meeting_id ").html(event.event._def.extendedProps.zoom_meeting_id);
					$("#zoom_booked_popup #meeting_password ").html(event.event._def.extendedProps.meeting_password);

					var meeting_data_join_link= event.event._def.extendedProps.meeting_join_link;
					var virtual_meeting = '<?php echo get_option("hmgt_enable_virtual_appointment");?>';
					var today = new Date();
					var dd = today.getDate();
					var mm = today.getMonth()+1; 
					var yyyy = today.getFullYear();
					if(dd<10) 
					{
						dd='0'+dd;
					} 

					if(mm<10) 
					{
						mm='0'+mm;
					} 
					var new_today = yyyy+'-'+mm+'-'+dd;
					var appointment_date = event.event._def.extendedProps.appointment_date;
					if(virtual_meeting == "yes")
					{
						if(meeting_data_join_link != "")
						{
							if(new_today <= appointment_date )
							{
							
								$("#join_link_href").attr('href', meeting_data_join_link);
								$( "#zoom_booked_popup" ).removeClass( "display_none" );
								$("#zoom_booked_popup").dialog({ modal: true, title: 'Virtual Appointment Meeting',width:340, height:410 });
								$(".ui-dialog-titlebar-close").text('x');
							}
						}
					} 
				}	
				//----------FOR ZOOM ----------//
	       	},
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
					var tooltip = '<div class="tooltipevent dashboard_appointment"><?php  esc_html_e( 'Doctor Name', 'hospital_mgt' ) ?>  : ' + event.doctor_name + '<br> <?php  esc_html_e( 'Patient Name', 'hospital_mgt' ) ?> :' + event.patient_name +' <br>  <?php  esc_html_e( 'Date', 'hospital_mgt' ) ?>  :' + full_date +'<br> <?php  esc_html_e( 'Time', 'hospital_mgt' ) ?>  :'+ time +' </div>'; 
					var $tool = $(tooltip).appendTo('body');
					
					$(this).mouseover(function(e) 
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
						var tooltip = '<div class="tooltipevent dashboard_appointment"><?php  esc_html_e( 'Event Name', 'hospital_mgt' ) ?>  : ' + event.title + '<br> <?php  esc_html_e( 'Start Date', 'hospital_mgt' ) ?> :' + full_start_date +' <br>  <?php  esc_html_e( 'End Date', 'hospital_mgt' ) ?>  :' + full_end_date +'<br> <?php  esc_html_e( 'Event For', 'hospital_mgt' ) ?>  :'+ event.event_for +' <br> <?php  esc_html_e( 'Comment', 'hospital_mgt' ) ?>  :'+ event.event_comment +'</div>';
					}
					else
					{
						var tooltip = '<div class="tooltipevent dashboard_appointment"><?php  esc_html_e( 'Notice Name', 'hospital_mgt' ) ?>  : ' + event.title + '<br> <?php  esc_html_e( 'Start Date', 'hospital_mgt' ) ?> :' + full_start_date +' <br>  <?php  esc_html_e( 'End Date', 'hospital_mgt' ) ?>  :' + full_end_date +'<br> <?php  esc_html_e( 'Notice For', 'hospital_mgt' ) ?>  :'+ event.event_for +' <br> <?php  esc_html_e( 'Comment', 'hospital_mgt' ) ?>  :'+ event.event_comment +'</div>';
					}
					var $tool = $(tooltip).appendTo('body');	
					
					$(this).mouseover(function(e)
					{
						$(this).css('z-index', 10000);
							$tool.fadeIn('500');
							$tool.fadeTo('10', 1.9);
					}).mousemove(function(e) {
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
      calendar.render();	
	});
</script>
<style>
@media only screen and (max-width : 768px) 
{
	input[type=radio] 
	{
		margin: 2px 0 0;
		
	}	
	.radio-inline input[type=radio]
	{
		margin-left: -20px;
	}
	.radio input[type=radio]
	{
		margin-left: -20px;
	}
}
@media only screen and (max-width : 480px) 
{
	input[type=checkbox], input[type=radio]
	 {
		margin: 2px 0px 0px;
	 } 
}
</style>
</head>
<body class="hospital-management-content"><!-- start body div-->
<?php
	$user = wp_get_current_user ();
?>
<!--task-event POP up code -->
	<div class="popup-bg popup_dashboard">
	    <div class="overlay-content content_width">
			<div class="modal-content dashboad_1">
				<div class="task_event_list">
				</div>  
				<div class="view_meeting_detail_popup">
		    	</div>   
			</div>
	    </div>     
  	</div>
  	<!-- <div class="popup-bg popup_dashboard1">
	    <div class="overlay-content">
			<div class="modal-content ">
				
			</div>
	    </div>     
  	</div> -->
 <!-- End task-event POP-UP Code -->
<div class="container-fluid mainpage frontend_side"><!-- start container fluid div-->
    <div class="navbar float-start w-100 h-100 padding_top_front_end_header"><!-- start navbar div-->
     <!-- HOSPTAL LOGO AND NAME -->	
		<div class="col-md-8 col-sm-8 col-xs-6">
				<h3><img src="<?php echo esc_url(get_option( 'hmgt_hospital_logo', 'hospital_mgt' )) ?>" class="img-circle head_logo" width="40" height="40" /> <span class="hmgt_hospital_name_span"><?php echo esc_html(get_option('hmgt_hospital_name','hospital_mgt'));?></span></h3>
	    
	</div><!-- end navbar div-->
	<ul class="nav navbar-right col-md-4 col-sm-4 col-xs-6">
			<!-- BEGIN USER LOGIN DROPDOWN -->
			<li class="dropdown ms-auto">
				<a id="dropdownMenufront" data-toggle="dropdown" class="btn btn-default dropdown-toggle border-0" data-bs-toggle="dropdown" aria-expanded="false">
						<?php
							$userimage = get_user_meta( $user->ID,'hmgt_user_avatar',true );
							if (empty ( $userimage )){
								echo '<img src='.MJ_hmgt_get_default_userprofile($obj_hospital->role).' height="40px" width="40px" class="img-circle" />';
							}
							else	
								echo '<img src=' . $userimage . ' height="40px" width="40px" class="img-circle"/>';
							?>
								<span>	<?php echo $user->display_name;?> </span> <b class="caret"></b>
				</a>
				<ul class="dropdown-menu extended logout" aria-labelledby="dropdownMenufront">
					<li>
						 <a class="dropdown-item" href="?dashboard=user&page=account"><i class="fa fa-user"></i>
							<?php esc_html_e('My Profile','hospital_mgt');?></a>
					</li>
					<li>
						<a class="dropdown-item" href="<?php echo wp_logout_url(home_url()); ?>"><i class="fas fa-sign-out-alt"></i><?php esc_html_e('Log Out','hospital_mgt');?> </a>
				   </li>
				</ul>
			</li>
		</ul>
</div><!-- end container fluid div-->
<div class="container-fluid"><!-- start container fluid div-->
	<div class="row responsive_add_main_front_end"><!-- start row div-->
		<div class="col-sm-2 col-md-2 col-12 nopadding hospital_left nav-side-menu"><!-- start menu div-->
			<!--  Left Side -->
			<?php
			$role = $obj_hospital->role;
			if($role=='doctor')
			{
				$menu = get_option( 'hmgt_access_right_doctor');
			}
			elseif($role=='patient')
			{
				$menu = get_option( 'hmgt_access_right_patient');
			}
			elseif($role=='nurse')
			{
				$menu = get_option( 'hmgt_access_right_nurse');
			}
			elseif($role=='receptionist')
			{
				$menu = get_option( 'hmgt_access_right_supportstaff');
			}
			elseif($role=='accountant')
			{
				$menu = get_option( 'hmgt_access_right_accountant');
			}
			elseif($role=='pharmacist')
			{
				$menu = get_option( 'hmgt_access_right_pharmacist');
			}
			elseif($role=='laboratorist')
			{
				$menu = get_option( 'hmgt_access_right_laboratories');
			}	
			$class = "";
			if (! isset ( $_REQUEST ['page'] ))	
				$class = 'class = "active"';		
			$patient_type='';
			if($role=='patient')
				$patient_type=get_user_meta(get_current_user_id(),'patient_type',true);	 
			?>
			<ul class="menu-sec navbar-nav nav nav-pills nav-stacked out navbar-collapse responsive_nav_bar_frontend collapse">
				<li>
				<a href="<?php echo site_url();?>"><span class="icone"><img src="<?php echo plugins_url( 'hospital-management/assets/images/icon/home.png' )?>"/></span><span class="title"><?php esc_html_e('Home','hospital_mgt');?></span></a></li>
				<li <?php echo $class;?>><a href="?dashboard=user"><span class="icone"><img src="<?php echo plugins_url( 'hospital-management/assets/images/icon/dashboard.png' )?>"/></span><span
						class="title"><?php esc_html_e('Dashboard','hospital_mgt');?></span></a></li>
						<?php
						$role = $obj_hospital->role;												
						$access_page_view_array=array();	
						if(!empty($menu))	
						{											
							foreach ( $menu as $key1=>$value1 ) 
							{									
								foreach ( $value1 as $key=>$value ) 
								{													
									if($value['view']=='1')
									{
										$access_page_view_array[]=$value ['page_link'];
										
										if (isset ( $_REQUEST ['page'] ) && $_REQUEST ['page'] == $value ['page_link'])
											$class = 'class = "active"';
										else
											$class = "";	
										
										echo '<li ' . $class . '><a href="?dashboard=user&page=' . $value ['page_link'] . '" class="left-tooltip" data-tooltip="'. $value ['menu_title'] . '" title="'. $value ['menu_title'] . '">
										<span class="icone"> <img src="' .$value ['menu_icone'].'" /></span><span class="title">'.MJ_hmgt_change_menutitle($key). '</span></a></li>';
									}	
								}									
							}
						}	
					?>								
			</ul>
		</div><!-- end menu div-->
		<div class="page-inner min_height_1050 col-sm-10 col-md-10 col-12 min_height_1050_responsive "><!-- start page inner div-->
			<div class="row row_responsive right_side <?php if(isset($_REQUEST['page'])){echo $_REQUEST['page'];}else{ echo 'front_dashboard';}
		?>"><!-- start dashboard content div-->
				<?php
				if (isset ( $_REQUEST ['page'] ))
				{			
					if(in_array($_REQUEST ['page'],$access_page_view_array))
					{
						require_once HMS_PLUGIN_DIR . '/template/' . $_REQUEST['page'] . '.php';
						return false;
					}
					else
					{	
						wp_redirect ('?dashboard=user');
						return false;
					}			
				}
				?>
				<!---start new dashboard------>
				<?php
				$page='patient';
				$patient_access=MJ_hmgt_page_access_rolewise_and_accessright_dashboard($page);
				if($patient_access)
				{
				?>					
				<div class="row">	
					<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">			
						<a href="<?php echo home_url().'?dashboard=user&page=patient';?>">			
							<div class="panel info-box panel-white">
								<div class="panel-body patient">
									<div class="info-box-stats">
									<?php
                                    $user_accessq=MJ_hmgt_get_userrole_wise_access_right_array();
									$current_user_id=get_current_user_id();
									if($obj_hospital->role == 'doctor') 
									{
										$out_patient=count(MJ_hmgt_get_outpatient_list_by_doctor_dashboard_outpatient($current_user_id));
										$inpatient=count(MJ_hmgt_get_outpatient_list_by_doctor_dashboard_inpatient($current_user_id));
									    $patient_count=$out_patient + $inpatient;
									}
									else
									{
										$get_patient = array('role' => 'patient');
										$patientdata=get_users($get_patient);
										if(!empty($patientdata))
										{
											$patient_count=count($patientdata);
										}
										else
										{
											$patient_count=0;
										}
									}
									
									
									//var_dump($patient_count);
									
									?>
										<p class="counter"><?php echo $patient_count;?></p>
										<span class="info-box-title"><?php echo esc_html( esc_html__( 'Patient', 'hospital_mgt' ) );?></span>
									</div>
									<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/patient.png"?>" class="dashboard_background">
								</div>
							</div>
						</a>
					</div>	
				<?php
				}
				$page='doctor';
				$doctor_access=MJ_hmgt_page_access_rolewise_and_accessright_dashboard($page);
				if($doctor_access)
				{
				?>	
					<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">			
					<a href="<?php echo home_url().'?dashboard=user&page=doctor';?>">			
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
				<?php
				}
				$page='nurse';
				$nurse_access=MJ_hmgt_page_access_rolewise_and_accessright_dashboard($page);
				if($nurse_access)
				{
				?>		
					<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
					<a href="<?php echo home_url().'?dashboard=user&page=nurse';?>">
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
				<?php
				}	
				$page='supportstaff';
				$supportstaff_access=MJ_hmgt_page_access_rolewise_and_accessright_dashboard($page);
				if($supportstaff_access)
				{
				?>	
					<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
					<a href="<?php echo home_url().'?dashboard=user&page=supportstaff';?>">
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
				<?php
				}	
				$page='message';
				$message_access=MJ_hmgt_page_access_rolewise_and_accessright_dashboard($page);
				if($message_access)
				{
				?>	
					
					<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
					<a href="<?php echo home_url().'?dashboard=user&page=message&tab=inbox';?>">	
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
				<?php
				}	
				$page='account';
				$account_access=MJ_hmgt_page_access_rolewise_and_accessright_dashboard($page);
				if($account_access)
				{
				?>	
					<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
					<a href="<?php echo home_url().'?dashboard=user&page=account';?>">	
						<div class="panel info-box panel-white">
							<div class="panel-body setting">
								<div class="info-box-stats">
									<p class="counter"> &nbsp;</p>
									<span class="info-box-title"><?php echo esc_html( esc_html__( 'Setting', 'hospital_mgt' ) );?></span>
								</div>
								<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/setting-image.png"?>" class="dashboard_background">
							</div>
						</div>
						</a>
					</div>
				<?php
				}	
				$page='appointment';
				$appointment_access=MJ_hmgt_page_access_rolewise_and_accessright_dashboard($page);
				if($appointment_access)
				{
				?>		
					<?php
					if($obj_hospital->role == 'nurse' || $obj_hospital->role == 'doctor')
					{
					?>
					<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
					<a href="<?php echo home_url().'?dashboard=user&page=appointment';?>">
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
					<?php 
					}			
				}	
				$page='prescription';
				$prescription_access=MJ_hmgt_page_access_rolewise_and_accessright_dashboard($page);
				if($prescription_access)
				{							
					if($obj_hospital->role == 'doctor')
					{
					?>
					<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
					<a href="<?php echo home_url().'?dashboard=user&page=prescription';?>">
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
					<?php 
					} 
				}	
				$page='bedallotment';
				$bedallotment_access=MJ_hmgt_page_access_rolewise_and_accessright_dashboard($page);
				if($bedallotment_access)
				{				
					if($obj_hospital->role == 'nurse' || $obj_hospital->role == 'doctor') 
					{
						?>
					<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
					<a href="<?php echo home_url().'?dashboard=user&page=bedallotment&tab=bedassign';?>">
						<div class="panel info-box panel-white">
							<div class="panel-body assignbed">
								<div class="info-box-stats">
									<p class="counter"><?php MJ_hmgt_tables_rows('hmgt_bed_allotment');?></p>
									
									<span class="info-box-title width_45px"><?php echo esc_html__('Assign Bed/Nurse', 'hospital_mgt');?></span>
								</div>
								 <img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/assign-bed-image.png"?>" class="dashboard_background">   
								
							</div>
						</div>
					</a>
					</div>
					<?php 
					}
				}	
				$page='treatment';
				$treatment_access=MJ_hmgt_page_access_rolewise_and_accessright_dashboard($page);
				if($treatment_access)
				{							
					if($obj_hospital->role == 'doctor') 
					{
					?>
					<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
					<a href="<?php echo home_url().'?dashboard=user&page=treatment';?>">
						<div class="panel info-box panel-white">
							<div class="panel-body treatment">
								<div class="info-box-stats">
									<p class="counter"><?php MJ_hmgt_tables_rows('hmgt_treatment');?></p>
									
									<span class="info-box-title"><?php echo esc_html( esc_html__( 'Treatment', 'hospital_mgt' ) );?></span>
								</div>
								  <img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/tretment-image.png"?>" class="dashboard_background">
								
							</div>
						</div>
						</a>
					</div>
					<?php
					}
				}	
				$page='event';
				$event_access=MJ_hmgt_page_access_rolewise_and_accessright_dashboard($page);
				if($event_access)
				{						
				 ?>
					<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
						<a href="<?php echo home_url().'?dashboard=user&page=event';?>">
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
										$noticedata=$obj_hospital->all_events_notice;
										echo count($noticedata);
										?></p>
										
										<span class="info-box-title width_10px"><?php echo esc_html__('Events/ Notice', 'hospital_mgt' );?></span>
									</div>
									<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/notice-event-image.png"?>" class="dashboard_background">									
								</div>
							</div>
						</a>
					</div>
				<?php 					
				}	
				$page='report';
				$report_access=MJ_hmgt_page_access_rolewise_and_accessright_dashboard($page);
				if($report_access)
				{
					 if($obj_hospital->role == 'doctor') 
					 {
						?>
						<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
						<a href="<?php echo home_url().'?dashboard=user&page=report';?>">
							<div class="panel info-box panel-white">
								<div class="panel-body operation_report">
									<div class="info-box-stats">
										<p class="counter">&nbsp;</p>
										
										<span class="info-box-title"><?php echo esc_html( esc_html__( 'Report', 'hospital_mgt' ) );?></span>
									</div>
									<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/report.png"?>" class="dashboard_background">
								</div>
							</div>
							</a>
						</div>
					<?php
					}
				}	
				$page='pharmacist';
				$pharmacist_access=MJ_hmgt_page_access_rolewise_and_accessright_dashboard($page);
				if($pharmacist_access)
				{					
					?>
					<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
						<a href="<?php echo home_url().'?dashboard=user&page=pharmacist';?>">
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
					<?php 
				}	
				$page='medicine';
				$medicine_access=MJ_hmgt_page_access_rolewise_and_accessright_dashboard($page);
				if($medicine_access)
				{
					if($obj_hospital->role == 'pharmacist') 
					{
					?>
						<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
						<a href="<?php echo home_url().'?dashboard=user&page=medicine';?>">
							<div class="panel info-box panel-white">
								<div class="panel-body medicine">
									<div class="info-box-stats">
										<p class="counter"><?php MJ_hmgt_tables_rows('hmgt_medicine');?></p>
										
										<span class="info-box-title"><?php echo esc_html( esc_html__( 'Medicine', 'hospital_mgt' ) );?></span>
									</div>
									<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/medicine.png"?>" class="dashboard_background"> 
								</div>
							</div>
							</a>
						</div>
					<?php
					} 
				}	
				$page='laboratorystaff';
				$laboratorystaff_access=MJ_hmgt_page_access_rolewise_and_accessright_dashboard($page);
				if($laboratorystaff_access)
				{	
					?>
					<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
					<a href="<?php echo home_url().'?dashboard=user&page=laboratorystaff';?>">
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
					<?php 
				}	
				$page='diagnosis';
				$diagnosis_access=MJ_hmgt_page_access_rolewise_and_accessright_dashboard($page);
				if($diagnosis_access)
				{	
					if($obj_hospital->role == 'doctor' || $obj_hospital->role == 'laboratorist')
					{
					?>
						<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
						<a href="<?php echo home_url().'?dashboard=user&page=diagnosis';?>">
							<div class="panel info-box panel-white">
								<div class="panel-body diagnosis">
									<div class="info-box-stats">
										<p class="counter"><?php MJ_hmgt_tables_rows('hmgt_diagnosis');?></p>
										
										<span class="info-box-title width_10px"><?php echo esc_html__('Diagnosis Report', 'hospital_mgt' );?></span>
									</div>
									<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/diagnosis-image.png"?>" class="dashboard_background">
									
								</div>
							</div>
						</a>
						</div>
					<?php
					}
				}	
				$page='accountant';
				$accountant_access=MJ_hmgt_page_access_rolewise_and_accessright_dashboard($page);
				if($accountant_access)
				{
				?>
					<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
					<a href="<?php echo home_url().'?dashboard=user&page=accountant';?>">
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
					<?php 
				}	
				$page='invoice';
				$invoice_access=MJ_hmgt_page_access_rolewise_and_accessright_dashboard($page);
				if($invoice_access)
				{	
					if($obj_hospital->role == 'accountant')
					{
					?>
						<div class="col-lg-2 col-md-2 col-xs-6 col-sm-3">
						<a href="<?php echo home_url().'?dashboard=user&page=invoice';?>">
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
					<?php
					} 
				}					
				?>
				</div>
				<div class="row dashboard_panel_heading_border">
					<div class="col-md-6 no-paddingR">
						<!--  Start Prescription Box -->
						<?php
						if($prescription_access)
						{
						?>
							<div class="panel panel-white event priscription">
								<div class="panel-heading ">					
								<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/Prescription.png"?>" >
									<h3 class="panel-title"><?php esc_html_e('Prescription','hospital_mgt');?></h3>						
								</div>
								<div class="panel-body">
									<div class="events">
										 <?php 
										 $obj_var=new MJ_hmgt_prescription();
										$prescriptiondata=$obj_var->MJ_hmgt_get_prescription_on_fronted_dashboard();
										
										if(!empty($prescriptiondata))
										{
											foreach ($prescriptiondata as $retrieved_data)
											{ 
											?>								
												<div class="calendar-event"> 
													<p class="remainder_title_pr Bold viewpriscription show_task_event" id="<?php echo $retrieved_data->priscription_id; ?>" model="Prescription Details" >  <?php esc_html_e('Patient Name','hospital_mgt');?> : 
													<?php 	$patient = MJ_hmgt_get_user_detail_byid( $retrieved_data->patient_id);
															echo  $patient['first_name']." ".$patient['last_name'];
														?>
													</p>
													<p class="remainder_date_pr"> <?php  echo date(MJ_hmgt_date_formate(),strtotime($retrieved_data->pris_create_date));?> </p>
													<p class="remainder_title_pr viewpriscription" > <?php esc_html_e('Description','hospital_mgt');?>	 : <?php
														if($retrieved_data->prescription_type=='report')
														{		
															echo $retrieved_data->report_description; 
														}
														else
														{
															echo $retrieved_data->case_history; 
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
						<?php
						}
						?>
						<!-- End Prescription Box -->
								
						<!--  Start Operation Box -->
						<?php
						$page='operation';
						$operation_access=MJ_hmgt_page_access_rolewise_and_accessright_dashboard($page);
						if($operation_access)
						{
						?>
							<div class="panel panel-white event operation">
								<div class="panel-heading ">
								<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/Operation-List.png"?>" >
								<h3 class="panel-title"><?php esc_html_e('Operation','hospital_mgt');?></h3>						
								</div>
								<div class="panel-body">
									<div class="events">
										<?php
										$obj_ot = new MJ_hmgt_operation();
										$ot_data=$obj_ot->MJ_hmgt_get_operation_on_fronted_dashboard();
										if(!empty($ot_data))
										{
											foreach ($ot_data as $retrieved_data)
											{		
												$patient_data =	MJ_hmgt_get_user_detail_byid($retrieved_data->patient_id);		
											?>
												<div class="calendar-event"> 
													<p class="remainder_title_pr Bold viewoperation show_task_event" id="<?php echo $retrieved_data->operation_id; ?>" model="Operation Details">	<?php esc_html_e('Patient Name','hospital_mgt');?> : <?php echo $patient_data['first_name']." ".$patient_data['last_name']; ?></p>
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
						<?php
						}
						?>
						<!-- End Operation Box -->
						
						<!-- start calendar Box -->
						<div class="panel panel-white">
						   <div class="panel-heading margin_bottom_15 cal_font">
								<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/calender.png"?>" >
								<h3 class="panel-title"><?php esc_html_e('Calendar','hospital_mgt');?></h3>			
							</div>
							<div class="panel-body">
								<div id="calendar" class="x_content full_calender"></div>
								<br>
								<mark class="mark_appointment_fronend">&nbsp;&nbsp;&nbsp;</mark>
								<span><?php esc_html_e('Appointment', 'hospital_mgt');?> <span>
							</div>
						</div>	
						<!-- end calendar Box -->							
					 </div>	
					<div class="col-md-6">	
						<!-- Start Appointment Box -->	
						<?php
						if($appointment_access)
						{
						?>
							<div class="panel panel-white Appoinment">
								<div class="panel-heading">
									<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/Appointment.png"?>" >
									<h3 class="panel-title"><?php esc_html_e('Appointment','hospital_mgt');?></h3>	
									<?php
									if($obj_hospital->role == 'patient')
									{
									?>		
										<a href="?dashboard=user&page=appointment&tab=addappoint&&action=insert" class="btn btn-default float_r_mar_r_10"><i class="fa fa-plus-circle"></i> <?php esc_html_e('Add Appointment', 'hospital_mgt'); ?></a>
									<?php
									}
									?>	
								</div>
								<div class="panel-body">
									<div class="events">
										<?php								
										$appointment_data=$obj_appointment->MJ_hmgt_get_appointment_on_fronted_dashboard();
										
										if(!empty($appointment_data))
										{
											foreach ($appointment_data as $retrieved_data)
											{		
												$patient_data =	MJ_hmgt_get_user_detail_byid($retrieved_data->patient_id);
											?>									
											<div class="calendar-event"> 
												<p class="remainder_title Bold save1 show_task_event" id="<?php echo $retrieved_data->appointment_id; ?>" model="Appointment Details">
												<?php esc_html_e('Patient Name','hospital_mgt');?> : <?php  echo $patient_data['first_name']." ".$patient_data['last_name']; ?>  </p>
												<p class="remainder_date width_160_pos">
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
						<?php
						}
						?>	
						<!--  End Appoinment box -->
						 
						<!--  Start assigned bed Box -->
						<?php
						if($bedallotment_access)
						{
						?>
							<div class="panel panel-white event assignbed">
								<div class="panel-heading">
								<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/Assign--Bed-nurse.png"?>" >
								<h3 class="panel-title"><?php esc_html_e('Assigned Bed','hospital_mgt');?></h3>						
								</div>
								<div class="panel-body">
									<div class="events">
										<?php
										$obj_bed = new MJ_hmgt_bedmanage();
										$bedallotment_data=$obj_bed->MJ_hmgt_get_bedallotment_on_fronted_dashboard();
										if(!empty($bedallotment_data))
										{
											foreach ($bedallotment_data as $retrieved_data)
											{
											?>									
												<div class="calendar-event"> 
													<p class="remainder_title Bold viewbedlist show_task_event" id="<?php echo $retrieved_data->bed_allotment_id; ?>" model="Assigned Bed Details" > <?php esc_html_e('Patient Name','hospital_mgt'); ?> : 	  
														<?php
														$patient_data =	MJ_hmgt_get_user_detail_byid($retrieved_data->patient_id);	
														echo $patient_data['first_name']." ".$patient_data['last_name'];
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
						<?php
						}
						?>
						<!-- End assigned bed Box -->
						<!--  Start Event Box -->
						<?php
						if($event_access)
						{						
						?>
							<div class="panel panel-white event">
								<div class="panel-heading ">
								<img src="<?php echo HMS_PLUGIN_URL."/assets/images/dashboard/event.png"?>" >
								<h3 class="panel-title"><?php esc_html_e('Events','hospital_mgt');?></h3>						
								</div>					
								<div class="panel-body">
									<div class="events">	
									<?php         
									$args = array(								 
									  'post_type'   => 'hmgt_event',
									  'order'     => 'DESC',
									  'orderby'   => 'ID'
									);
									
									$retrieve_class = get_posts($args);
									
									if(!empty($retrieve_class))
									{ 
										$i=1;
										foreach ($retrieve_class as $retrieved_data)
										{ 
											$event_for_array=explode(",",get_post_meta( $retrieved_data->ID, 'notice_for',true));
											$role=MJ_hmgt_get_current_user_role();	
											
											if(in_array($role,$event_for_array))
											{
												if($i<=3)
												{	
												?>
													<div class="calendar-event">
														<p class="remainder_title Bold viewdetail show_task_event" id="<?php echo $retrieved_data->ID; ?>" model="Event Details">
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
												$i++;
											}
										}
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
										  'post_type'   => 'hmgt_notice',
										  'order'     => 'DESC',
										  'orderby'   => 'ID'
										);
										
										$retrieve_class = get_posts($args);
										
										if(!empty($retrieve_class))
										{ 
											$i=1;										
											foreach ($retrieve_class as $retrieved_data)
											{ 
												$notice_for_array=explode(",",get_post_meta( $retrieved_data->ID, 'notice_for',true));
												$role=MJ_hmgt_get_current_user_role();	
													
												if(in_array($role,$notice_for_array))
												{
													if($i<=3)
													{
													?>						
														<div class="calendar-event"> 
															<p class="remainder_title Bold viewdetail show_task_event" id="<?php echo $retrieved_data->ID; ?>" model="Notice Details">	
																<?php echo esc_html($retrieved_data->post_title); ?>	
															</p>
															<p class="remainder_date">	<?php echo date(MJ_hmgt_date_formate(),strtotime(get_post_meta($retrieved_data->ID,'start_date',true))); ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <?php echo date(MJ_hmgt_date_formate(),strtotime(get_post_meta($retrieved_data->ID,'end_date',true))); ?></p>
															<p class="remainder_title viewdetail"><?php echo esc_html($retrieved_data->post_content); ?></p>
														</div>	
													<?php
													}
													$i++;
												}
											}
										}
										?>					
									</div>
								</div>
							</div>
						<?php
						}?>
						
						<!--  End Notice box -->
					</div>
				</div>
				<!---End new dashboard------>
			</div><!-- end dashboard content div-->
	    </div><!-- end page inner div-->
	</div><!-- end row div-->
 </div><!-- end container fluid div-->
</body>
</html>
<?php ?>