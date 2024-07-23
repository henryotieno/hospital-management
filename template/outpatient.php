<?php
MJ_hmgt_browser_javascript_check();
$user_object=new MJ_hmgt_user();
$Hospital_object=new MJ_hmgt_Hospital_Management();
$role='patient';
$id=0;
$patient_type='outpatient';
$obj_bloodbank=new MJ_hmgt_bloodbank();
$active_tab = isset($_REQUEST['tab'])?$_REQUEST['tab']:'outpatientlist';
$current_user_id=get_current_user_id();
$valtemp=0;
$user_object=new MJ_hmgt_user();
//access right function
$user_access=MJ_hmgt_get_userrole_wise_access_right_array();
if (isset ( $_REQUEST ['page'] ))
{	
	if($user_access['view']=='0')
	{	
		MJ_hmgt_access_right_page_not_access_message();
		die;
	}
	if(!empty($_REQUEST['action']))
	{
		if (isset ( $_REQUEST ['page'] ) && $_REQUEST ['page'] == $user_access['page_link'] && ($_REQUEST['action']=='edit'))
		{
			if($user_access['edit']=='0')
			{	
				MJ_hmgt_access_right_page_not_access_message();
				die;
			}			
		}
		if (isset ( $_REQUEST ['page'] ) && $_REQUEST ['page'] == $user_access['page_link'] && ($_REQUEST['action']=='delete'))
		{
			if($user_access['delete']=='0')
			{	
				MJ_hmgt_access_right_page_not_access_message();
				die;
			}	
		}
		if (isset ( $_REQUEST ['page'] ) && $_REQUEST ['page'] == $user_access['page_link'] && ($_REQUEST['action']=='insert'))
		{
			if($user_access['add']=='0')
			{	
				MJ_hmgt_access_right_page_not_access_message();
				die;
			}	
		}
	}	
}
?>

<script type="text/javascript">
jQuery(document).ready(function() {
	"use strict";
	<?php
	if (is_rtl())
		{
		?>	
			$('#out_patient_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#out_patient_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>

	$('#symptoms').multiselect(
	{
		nonSelectedText :'<?php esc_html_e('Select Symptoms','hospital_mgt');?>',
		includeSelectAllOption: true,
	    selectAllText : '<?php esc_html_e('Select all','hospital_mgt'); ?>',
		templates: {
	            button: '<button class="multiselect btn btn-default dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="multiselect-selected-text"></span><b class="caret"></b></button>',
	        },
			buttonContainer: '<div class="dropdown" />'
	});
	 
	$('#outpatient_list').DataTable({
		"responsive": true,
		"order": [[ 1, "asc" ]],
		"aoColumns":[
	                  {"bSortable": false},
	                  {"bSortable": true},
	                  {"bSortable": true},
	                  {"bSortable": true},
	                  {"bVisible": true},
	                  {"bVisible": true},
	                  {"bVisible": true},
	                  {"bSortable": false}
	               ],
			language:<?php echo MJ_hmgt_datatable_multi_language();?>	   

		});
		
	$.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
	$('#birth_date').datepicker({
		endDate: '+0d',
        autoclose: true
   }); 
	
	//user name not  allow space validation
	$('#username').keypress(function( e ) {
       if(e.which === 32) 
         return false;
    });
	
} );
</script>
<?php 
//SAVE Outpatient DATA
if(isset($_POST['save_outpatient']))
{
	$nonce = $_POST['_wpnonce'];
	if (wp_verify_nonce( $nonce, 'save_outpatient_nonce' ) )
	{
		if(isset($_FILES['upload_user_avatar_image']) && !empty($_FILES['upload_user_avatar_image']) && $_FILES['upload_user_avatar_image']['size'] !=0)
		{
			if($_FILES['upload_user_avatar_image']['size'] > 0)
			{
				$patient_image=MJ_hmgt_load_documets($_FILES['upload_user_avatar_image'],'upload_user_avatar_image','pimg');
				$patient_image_url=content_url().'/uploads/hospital_assets/'.$patient_image;
			}
			else 
			{
				$patient_image=$_REQUEST['hidden_upload_user_avatar_image'];
				$patient_image_url=$patient_image;
			}	
		}
		else{
			if(isset($_REQUEST['hidden_upload_user_avatar_image']))
				$patient_image=$_REQUEST['hidden_upload_user_avatar_image'];
				$patient_image_url=$patient_image;
		}
		
		if(isset($_REQUEST['action'])&& $_REQUEST['action']=='insert')
		{	
			//multiple dignosis insert
			$upload_dignosis_array=array();
		
			if(!empty($_FILES['diagnosis']['name']))
			{
				$count_array=count($_FILES['diagnosis']['name']);

				for($a=0;$a<$count_array;$a++)
				{			
					foreach($_FILES['diagnosis'] as $image_key=>$image_val)
					{	
						if($_FILES['diagnosis']['name'][$a]!='')
						{	
							$diagnosis_array[$a]=array(
							'name'=>$_FILES['diagnosis']['name'][$a],
							'type'=>$_FILES['diagnosis']['type'][$a],
							'tmp_name'=>$_FILES['diagnosis']['tmp_name'][$a],
							'error'=>$_FILES['diagnosis']['error'][$a],
							'size'=>$_FILES['diagnosis']['size'][$a]
							);							
						}	
					}
				}
				if(!empty($diagnosis_array))
				{
					foreach($diagnosis_array as $key=>$value)		
					{	
						$get_file_name=$diagnosis_array[$key]['name'];	
						
						$upload_dignosis_array[]=MJ_hmgt_load_multiple_documets($value,$value,$get_file_name);				
					} 
				}				
			}
			if( !email_exists( $_POST['email'] ) && !username_exists( $_POST['username'] ))
			{
				$ext=MJ_hmgt_check_valid_extension($patient_image_url);
				if(!$ext == 0)
				{
					$diagnosis_report_url=$upload_dignosis_array;
					$ext1=MJ_hmgt_check_valid_file_extension_for_diagnosis($diagnosis_report_url);
					if($ext1 == 0 )
					{
						$result=$user_object->MJ_hmgt_add_user($_POST);
						$user_object->MJ_hmgt_upload_multiple_diagnosis_report($result,$upload_dignosis_array);					
						$returnans=update_user_meta( $result,'hmgt_user_avatar',$patient_image_url);
						if($result)
						{
							$guardian_data=array('patient_id'=>$result,
							'doctor_id'=>$_POST['doctor'],
							'symptoms'=>implode(",",$_POST['symptoms']),
							'inpatient_create_date'=>date("Y-m-d H:i:s"),
							'inpatient_create_by'=>get_current_user_id()
							);
							$inserted=MJ_hmgt_add_guardian($guardian_data,$id);
							
							if($inserted)
							{
								wp_redirect ( home_url() . '?dashboard=user&page=outpatient&tab=outpatientlist&message=1');
							}
						}
					}
					else
					{  
					?>
						<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
						</button>
							<p><p><?php esc_html_e('Sorry, only JPG,JPEG,PNG,GIF,DOC,PDF and ZIP files are allowed.','hospital_mgt'); ?></p></p>
						</div><?php 
					}
				}
				else
				{ 
					 ?>
					<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
					</button>
					<p><p><?php esc_html_e('Sorry, only JPG, JPEG, PNG & GIF files are allowed!.','hospital_mgt');?></p></p>
				</div>
			<?php 
			   }
			}
			else 
			{?>
				<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
				</button>
				<p><p><?php esc_html_e('Username Or Emailid Already Exist.','hospital_mgt');?></p></p>
				</div>
			<?php 
			}
		}
		if(isset($_REQUEST['action'])&& $_REQUEST['action']=='edit')
		{
			//multiple dignosis upload
			$upload_dignosis_array=array();
			$not_upload_dignosis_array=array();
		
			if(!empty($_FILES['diagnosis']['name']))
			{
				$count_array=count($_FILES['diagnosis']['name']);

				for($a=0;$a<$count_array;$a++)
				{			
					foreach($_FILES['diagnosis'] as $image_key=>$image_val)
					{	
						if($_FILES['diagnosis']['name'][$a]!='')
						{	
							$diagnosis_array[$a]=array(
							'name'=>$_FILES['diagnosis']['name'][$a],
							'type'=>$_FILES['diagnosis']['type'][$a],
							'tmp_name'=>$_FILES['diagnosis']['tmp_name'][$a],
							'error'=>$_FILES['diagnosis']['error'][$a],
							'size'=>$_FILES['diagnosis']['size'][$a]
							);							
						}
						else
						{
							if(!empty($_POST['hidden_attach_report'][$a]))
							{
								$not_upload_dignosis_array[$a]=$_POST['hidden_attach_report'][$a];
							}	
						}
					}
				}
				if(!empty($diagnosis_array))
				{
					foreach($diagnosis_array as $key=>$value)		
					{	
						$get_file_name=$diagnosis_array[$key]['name'];	
						
						$upload_dignosis_array[]=MJ_hmgt_load_multiple_documets($value,$value,$get_file_name);				
					} 	
				}				
			}
			$upload_array_merge=array_merge($upload_dignosis_array,$not_upload_dignosis_array);

			$ext=MJ_hmgt_check_valid_extension($patient_image_url);
			if(!$ext == 0)
			{
				$ext1=MJ_hmgt_check_valid_file_extension_for_diagnosis($upload_array_merge);
				if($ext1 == 0 )
				{	
					$patientid=MJ_hmgt_id_decrypt($_REQUEST['outpatient_id']);
					$result=$user_object->MJ_hmgt_add_user($_POST);
					$guardian_data=array('patient_id'=>MJ_hmgt_id_decrypt($_REQUEST['outpatient_id']),
									'doctor_id'=>$_POST['doctor'],
									'symptoms'=>implode(",",$_POST['symptoms']),
									'inpatient_create_date'=>date("Y-m-d H:i:s"),'inpatient_create_by'=>get_current_user_id()
									);	
						$result1=MJ_hmgt_update_guardian($guardian_data,MJ_hmgt_id_decrypt($_REQUEST['outpatient_id']));
					
					global $wpdb;
					$table_diagnosis = $wpdb->prefix. 'hmgt_diagnosis';
					$result_delete_dignosis = $wpdb->query("DELETE FROM $table_diagnosis where report_cost IS NULL AND patient_id = ".MJ_hmgt_id_decrypt($_REQUEST['outpatient_id']));
					
					$returnans=$user_object->MJ_hmgt_upload_multiple_diagnosis_report(MJ_hmgt_id_decrypt($_REQUEST['outpatient_id']),$upload_array_merge);	
					
					$returnans=update_user_meta( $result,'hmgt_user_avatar',$patient_image_url);
				}
						
				else
				{  ?>
					<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
					</button>
					<p><p><?php esc_html_e('Sorry, only JPG,JPEG,PNG,GIF,DOC,PDF and ZIP files are allowed.','hospital_mgt'); ?></p></p>
					</div><?php 
				 }
			} 
			else
			{
			?>
				<div id="message" class="updated below-h2 notice is-dismissible">
				<p><p><?php esc_html_e('Sorry, only JPG, JPEG, PNG & GIF files are allowed!.','hospital_mgt');?></p></p>
				</div><?php 
			}

			$outpatient_id=0;
			if(isset($_POST['patient_convert']))
			{
				$outpatient_id=MJ_hmgt_id_decrypt($_REQUEST['outpatient_id']);
			}
			if(isset($result) ||isset($result1) || isset($returnans))
			{
				wp_redirect ( home_url() . '?dashboard=user&page=outpatient&tab=outpatientlist&message=2&outpatient_id='.$outpatient_id);
			}
		}
	}
}
	
if(isset($_REQUEST['action'])&& $_REQUEST['action']=='delete')
{
	$result=$user_object->MJ_hmgt_delete_usedata(MJ_hmgt_id_decrypt($_REQUEST['outpatient_id']));
	$result=MJ_hmgt_delete_guardian(MJ_hmgt_id_decrypt($_REQUEST['outpatient_id']));
	if($result)
	{
		wp_redirect ( home_url() . '?dashboard=user&page=outpatient&tab=outpatientlist&message=3');
	}
}
	
if(isset($_REQUEST['message']))
{
	$message =$_REQUEST['message'];
	if($message == 1)
	{?>
			<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
			</button>
			<p>
			<?php 
				esc_html_e('Record inserted successfully','hospital_mgt');
			?></p></div>
			<?php 
		
	}
	elseif($message == 2)
	{  
		if(isset($_REQUEST['outpatient_id']))
				$valtemp=MJ_hmgt_id_encrypt($_REQUEST['outpatient_id']);
			?><div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">X</span>
				</button>
				<p><?php
				esc_html_e("Record updated successfully",'hospital_mgt');
				?></p>
				</div>
			<?php 
		
	}
	elseif($message == 3) 
	{?>
	<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
		</button><p>
	<?php 
		esc_html_e('Record deleted successfully','hospital_mgt');
	?></div></p><?php
			
	}
}	?>

<!-- POP up code -->
<div class="popup-bg">
    <div class="overlay-content">
    <div class="modal-content">
    <div class="patient_data">
     </div>
     
    </div>
    </div> 
    
</div>
<!-- End POP-UP Code -->

<script type="text/javascript">
jQuery(document).ready(function($) {
	"use strict";
	var tempval=<?php echo $valtemp;?>;
		if(tempval!=0){
		swal({
						title: "Outpatient successfully converted to inpatient!",
						text: "Do you Want to Admit this patient?",
						type: "warning",
						showCancelButton: true,
						confirmButtonColor: '#22baa0',
						confirmButtonText: 'Yes',
						cancelButtonText: "No",
						closeOnConfirm: false,
						closeOnCancel: true
					},
						function(isConfirm){
						if (isConfirm){
							window.location.href = "<?php echo home_url().'?dashboard=user&page=patient&tab=addpatient_step2&action=edit&patient_id='.$valtemp; ?>";
						} else {
							tempval=0;
						 window.location.href = "<?php echo home_url() . '?dashboard=user&page=outpatient&tab=outpatientlist&message=2';?>";
						}
					});
		}		
	$("body").on("click", ".remove_cirtificate", function()
	{
	    alert("<?php esc_html_e('Do you really want to delete this record ?','hospital_mgt');?>");
		$(this).parent().parent().remove();
	});	
	
		$("body").on("click",".symptoms_alert",function()
	 {
		var checked = $(".dropdown-menu input:checked").length;

		if(!checked)
		{
			alert("<?php esc_html_e('Please select atleast one Symtoms','hospital_mgt');?>");
			return false;
		}		
	});
	
	
});
</script>
<div class="panel-body panel-white"><!-- START PANEL BODY DIV-->
	 <ul class="nav nav-tabs panel_tabs" role="tablist">
	 
		  <li class="<?php if($active_tab=='outpatientlist'){?>active<?php }?>">
		
				<a href="?dashboard=user&page=outpatient&tab=outpatientlist" class="tab <?php echo $active_tab == 'outpatientlist' ? 'active' : ''; ?>">
				 <i class="fa fa-align-justify"></i> <?php esc_html_e('Outpatient List', 'hospital_mgt'); ?></a>

		  </li>
		 
		  <li class="<?php if($active_tab=='addoutpatient'){?>active<?php }?>">
		  <?php  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
				{?>
				<a href="?dashboard=user&page=outpatient&tab=addoutpatient&action=edit&outpatient_id=<?php echo $_REQUEST['outpatient_id'];?>" class="tab <?php echo $active_tab == 'addoutpatient' ? 'active' : ''; ?>">
				 <?php esc_html_e('Edit Outpatient', 'hospital_mgt'); ?></a> 
				<?php }
				else
				{
					if($user_access['add']=='1')
					{
					?>
						<a href="?dashboard=user&page=outpatient&tab=addoutpatient&&action=insert" class="tab <?php echo $active_tab == 'addoutpatient' ? 'active' : ''; ?>">
						<i class="fa fa-plus-circle"></i> <?php esc_html_e('Add Outpatient', 'hospital_mgt'); ?></a> 
					<?php
					}
					?>	
			 </a>  
			
		  </li>
		  <?php }?>
		 
		  
	</ul>
	<div class="tab-content"><!-- START TAB CONTENT DIV-->
	<?php
	if($active_tab=='outpatientlist')
	{ 	
		?>
		<div class="panel-body"><!-- START PANEL BODY DIV-->
			<div class="table-responsive"><!-- START TABLE RESPONSIVE DIV-->
			   <table id="outpatient_list" class="display dataTable " cellspacing="0"><!-- START Outpatient LIST TABLE-->
					 <thead>
					<tr>
					<th class="height_width_50"><?php  esc_html_e( 'Photo', 'hospital_mgt' ) ;?></th>
					 <th><?php esc_html_e( 'Patient Name', 'hospital_mgt' ) ;?></th>
					   <th><?php esc_html_e( 'Patient Id', 'hospital_mgt' ) ;?></th>
					  <th> <?php esc_html_e( 'Phone', 'hospital_mgt' ) ;?></th>
					  <th> <?php esc_html_e( 'Blood Group', 'hospital_mgt' ) ;?></th>
						<th> <?php esc_html_e( 'Email', 'hospital_mgt' ) ;?></th>
						<th> <?php esc_html_e( 'Assigned Doctor Name', 'hospital_mgt' ) ;?></th>
						<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
					</tr>
				</thead>
				<tfoot>
					<tr>
				<th><?php  esc_html_e( 'Photo', 'hospital_mgt' ) ;?></th>
					 <th><?php esc_html_e( 'Patient Name', 'hospital_mgt' ) ;?></th>
					   <th><?php esc_html_e( 'Patient Id', 'hospital_mgt' ) ;?></th>
					  <th> <?php esc_html_e( 'Phone', 'hospital_mgt' ) ;?></th>
					  <th> <?php esc_html_e( 'Blood Group', 'hospital_mgt' ) ;?></th>
						<th> <?php esc_html_e( 'Email', 'hospital_mgt' ) ;?></th>
						<th> <?php esc_html_e( 'Assigned Doctor Name', 'hospital_mgt' ) ;?></th>
						<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
					</tr>
				</tfoot>
		 
				<tbody>
				<?php 
			
				if($obj_hospital->role == 'doctor') 
				{

					$own_data=$user_access['own_data'];

					if($own_data == '1')
					{
						$patientdata=MJ_hmgt_get_outpatient_list_by_doctor($current_user_id);
					}
					else
					{
						$get_patient = array('role' => 'patient','meta_key'=>'patient_type','meta_value'=>'outpatient');
						$patientdata=get_users($get_patient);
					}
				}
				elseif($obj_hospital->role == 'nurse') 
				{
					$own_data=$user_access['own_data'];
					if($own_data == '1')
					{				
						$get_patient = array(
							'role' => 'patient',
							'meta_query' => array(
							'relation'    => 'AND',
							array(
									'key' => 'patient_type',
									'value' =>'outpatient',
									'compare' => '='
								),
							array(
									'key' => 'created_by',
									'value' =>$current_user_id,
									'compare' => '='
								),
							)
						);
						$patientdata=get_users($get_patient);
						
					}
					else
					{
						 $get_patient = array('role' => 'patient','meta_key'=>'patient_type','meta_value'=>'outpatient');
						 $patientdata=get_users($get_patient);
					}
				}
				elseif($obj_hospital->role == 'patient') 
				{
					$own_data=$user_access['own_data'];
					if($own_data == '1')
					{			
						$usertype=get_user_meta($current_user_id,'patient_type',true);
										
						if($usertype=='outpatient')
						{
							$patientdata=array();
							$patientdata[]=get_userdata($current_user_id);		
						}		
					}
					else
					{
						 $get_patient = array('role' => 'patient','meta_key'=>'patient_type','meta_value'=>'outpatient');
						 $patientdata=get_users($get_patient);
					}
				}
				else
				{
					 $get_patient = array('role' => 'patient','meta_key'=>'patient_type','meta_value'=>'outpatient');
					 $patientdata=get_users($get_patient);
				}
				
				if(!empty($patientdata))
				{
					foreach ($patientdata as $retrieved_data)
					{
						$doctordetail=MJ_hmgt_get_guardianby_patient($retrieved_data->ID);
						if(!empty($doctordetail['doctor_id']))
						{
							$doctor = get_userdata($doctordetail['doctor_id']);		
						}
					?>
					<tr>
						<td class="user_image"><?php $uid=$retrieved_data->ID;
							 $userimage=get_user_meta($uid, 'hmgt_user_avatar', true);
								if(empty($userimage))
								{
									echo '<img src='.esc_url(get_option( 'hmgt_patient_thumb' )).' height="50px" width="50px" class="img-circle" />';
								}
								else
								echo '<img src='.esc_url($userimage).' height="50px" width="50px" class="img-circle"/>';
						?></td>
						<td class="name">
						<?php
						if($user_access['edit']=='1')
						{
						?>
						<a href="?dashboard=user&page=outpatient&tab=addoutpatient&action=edit&outpatient_id=<?php echo MJ_hmgt_id_encrypt(esc_attr($retrieved_data->ID));?>">
						<?php
						}
						?>
						<?php echo esc_html($retrieved_data->display_name);?></a></td>
						<td class="patient_id">
						<?php 
								echo get_user_meta($uid, 'patient_id', true);
						?></td>
						<td class="phone"><?php echo get_user_meta($uid, 'mobile', true);?></td>
						<td class="email"><?php echo get_user_meta($uid, 'blood_group', true);?></td>
						<td class="email"><?php echo esc_html($retrieved_data->user_email);?></td>
						<td class=""><?php if(!empty($doctor)) { echo esc_html($doctor->display_name); } ?></td>

						<td class="action"> 
						<a href="javascript:void(0);" class="show-view-popup btn btn-default" idtest="<?php echo esc_attr($retrieved_data->ID); ?>" type="<?php echo 'view_outpatient';?>">
						<i class="fa fa-eye"></i> <?php esc_html_e('Patient Detail', 'hospital_mgt');?></a>
						<a  href="?dashboard=user&page=outpatient&action=view_status&outpatient_id=<?php echo esc_attr($retrieved_data->ID);?>" class="show-popup btn btn-default" idtest="<?php echo esc_attr($retrieved_data->ID); ?>"><i class="fa fa-eye"></i> <?php esc_html_e('View Detail', 'hospital_mgt');?></a>
						<a  href="?page=patient&action=view_status&patient_id=<?php echo esc_attr($retrieved_data->ID);?>" class="show-charges-popup btn btn-default" idtest="<?php echo esc_attr($retrieved_data->ID); ?>">
						<i class="fa fa-money"></i> <?php esc_html_e('Charges', 'hospital_mgt');?></a>
						<?php
						if($user_access['edit']=='1')
						{
						?>
							<a href="?dashboard=user&page=outpatient&tab=addoutpatient&action=edit&outpatient_id=<?php echo MJ_hmgt_id_encrypt(esc_attr($retrieved_data->ID));?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
						<?php
						} 
						if($user_access['delete']=='1')
						{
						?>
							<a href="?dashboard=user&page=outpatient&tab=outpatientlist&action=delete&outpatient_id=<?php echo MJ_hmgt_id_encrypt(esc_attr($retrieved_data->ID));?>" class="btn btn-danger" 
							onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this record?','hospital_mgt');?>');">
							<?php esc_html_e( 'Delete', 'hospital_mgt' ) ;?> </a>
						<?php 
						} 
						?>
						</td>
					   
					</tr>
						<?php 
					} 			
				}?>
			 
				</tbody>

				</table><!-- END Outpatient LIST TABLE-->
			</div><!-- END TABLE RESPONSIVE DIV-->
		</div><!-- END PANEL BODY DIV-->
	
<?php
	}
	if($active_tab == 'addoutpatient')
	{
			$diagnosis_obj=new MJ_hmgt_dignosis();
        	$edit=0;
				if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
				{					
					$edit=1;
					$user_info = get_userdata(MJ_hmgt_id_decrypt($_REQUEST['outpatient_id']));
					$doctordetail=MJ_hmgt_get_guardianby_patient(MJ_hmgt_id_decrypt($_REQUEST['outpatient_id']));
					$diagnosis=$diagnosis_obj->MJ_hmgt_get_last_diagnosis_by_patient(MJ_hmgt_id_decrypt($_REQUEST['outpatient_id']));
					$doctor = get_userdata($doctordetail['doctor_id']);
				
				}
				else
				{
				  	$newpatient=MJ_hmgt_get_lastpatient_id($role);
				}
				
				?>
				<!-- POP up code -->
			<div class="popup-bg zindex_100000">
				<div class="overlay-content">
				<div class="modal-content">
				<div class="category_list">
				 </div>
				 
				</div>
				</div> 
				
			</div>
			<!-- End POP-UP Code -->
			<script type="text/javascript">
			function fileCheck(obj)
			{   //FILE VALIDATION
				"use strict";
				jQuery("#upload_user_avatar_preview").hide();
				var fileExtension = ['jpg','jpeg','png'];
				if (jQuery.inArray(jQuery(obj).val().split('.').pop().toLowerCase(), fileExtension) == -1)
				{
					alert("<?php esc_html_e('Sorry, only JPG, JPEG, PNG files are allowed.','hospital_mgt');?>");
					jQuery(obj).val('');
				}	
			}
			</script>
       <div class="panel-body">	<!-- START PANEL BODY DIV-->
			<form name="out_patient_form" action="" method="post" class="form-horizontal" id="out_patient_form" enctype="multipart/form-data"><!-- START Outpatient FORM-->
				 <?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
				<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
				<input type="hidden" name="role" value="<?php echo esc_attr($role);?>"  />
				<input type="hidden" name="hmgt_user_avatar" value="<?php echo get_option( 'hmgt_guardian_thumb' );?>"  />
				<input type="hidden" name="patient_type" value="<?php echo esc_attr($patient_type);?>"  />
				<input type="hidden" name="diagnosis_id" value="<?php if(!empty($diagnosis)) echo esc_attr($diagnosis->diagnosis_id);?>"  />
				<input type="hidden" name="user_id" value="<?php if(isset($_REQUEST['outpatient_id'])) echo MJ_hmgt_id_decrypt(esc_attr($_REQUEST['outpatient_id']));?>"  />
				<div class="header">	
							<h3 class="first_hed"><?php esc_html_e('Personal Information','hospital_mgt');?></h3>
							<hr>
				</div>		
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="roll_id"><?php esc_html_e('Patient Id','hospital_mgt');?><span class="require-field">*</span></label>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
							<input id="patient_id" class="form-control validate[required]" type="text" 
							value="<?php if($edit){ echo esc_attr($user_info->patient_id);}else echo esc_attr($newpatient);?>" readonly name="patient_id">
						</div>
							<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="first_name"><?php esc_html_e('First Name','hospital_mgt');?><span class="require-field">*</span></label>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
							<input id="first_name" class="form-control validate[required,custom[onlyLetter_specialcharacter]] text-input" maxlength="50" type="text" value="<?php if($edit){ echo esc_attr($user_info->first_name);}elseif(isset($_POST['first_name'])) echo esc_attr($_POST['first_name']);?>" name="first_name">
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="middle_name"><?php esc_html_e('Middle Name','hospital_mgt');?></label>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
							<input id="middle_name" class="form-control validate[custom[onlyLetter_specialcharacter]]" type="text" maxlength="50" value="<?php if($edit){ echo esc_attr($user_info->middle_name);}elseif(isset($_POST['middle_name'])) echo esc_attr($_POST['middle_name']);?>" name="middle_name">
						</div>
						<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="last_name"><?php esc_html_e('Last Name','hospital_mgt');?><span class="require-field">*</span></label>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
							<input id="last_name" class="form-control validate[required,custom[onlyLetter_specialcharacter]] text-input" maxlength="50" type="text"  value="<?php if($edit){ echo esc_attr($user_info->last_name);}elseif(isset($_POST['last_name'])) echo esc_attr($_POST['last_name']);?>" name="last_name">
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="birth_date"><?php esc_html_e('Date of birth','hospital_mgt');?><span class="require-field">*</span></label>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
							<input id="birth_date" class="form-control validate[required]" type="text"  name="birth_date"  
							value="<?php if($edit){ echo date(MJ_hmgt_date_formate(),strtotime($user_info->birth_date));}elseif(isset($_POST['birth_date'])) echo esc_attr($_POST['birth_date']);?>" readonly>
						</div>
						<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="blood_group"><?php esc_html_e('Blood Group','hospital_mgt');?></label>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
							<?php if($edit){ $userblood=$user_info->blood_group; }elseif(isset($_POST['blood_group'])){$userblood=$_POST['blood_group'];}else{$userblood='';}?>
							<select id="blood_group" class="form-control" name="blood_group">
							<option value=""><?php esc_html_e('Select Blood Group','hospital_mgt');?></option>
							<?php foreach(MJ_hmgt_blood_group() as $blood){ ?>
									<option value="<?php echo esc_attr($blood);?>" <?php selected($userblood,$blood);  ?>><?php echo esc_html($blood); ?> </option>
							<?php } ?>
						</select>
						</div>
					</div>
				</div>	
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="gender"><?php esc_html_e('Gender','hospital_mgt');?><span class="require-field">*</span></label>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
						<?php $genderval = "male"; if($edit){ $genderval=$user_info->gender; }elseif(isset($_POST['gender'])) {$genderval=$_POST['gender'];}?>
							<label class="radio-inline">
							 <input type="radio" value="male" class="tog" name="gender"  <?php  checked( 'male', $genderval);  ?>/><?php esc_html_e('Male','hospital_mgt');?>
							</label>
							<label class="radio-inline">
							  <input type="radio" value="female" class="tog" name="gender"  <?php  checked( 'female', $genderval);  ?>/><?php esc_html_e('Female','hospital_mgt');?> 
							</label>
						</div>
							<?php if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit'){?>
					
								<label class="col-lg-2 col-md-2 col-sm-2 col-xs-10 control-label form-label" for="patient_convert"><?php  esc_html_e(' Convert into Inpatient','hospital_mgt');?></label>
								<div class="col-lg-3 col-md-3 col-sm-3 col-xs-2 has-feedback">
									<input type="checkbox"  name="patient_convert" value="inpatient">
								
								</div>

							<?php }?>
						</div>
				</div>
				<div class="header">
							<h3><?php esc_html_e('HomeTown Address Information','hospital_mgt');?></h3>
							<hr>
				</div>
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-lg-2 col-md-2 col-sm-2 col-xs-10 control-label form-label" for="address"><?php esc_html_e('Home Town Address','hospital_mgt');?><span class="require-field">*</span></label>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
							<input id="address" class="form-control validate[required,custom[address_description_validation]]" type="text" maxlength="150" name="address" 
							value="<?php if($edit){ echo esc_attr($user_info->address);}elseif(isset($_POST['address'])) echo esc_attr($_POST['address']);?>">
						</div>
						<label class="col-lg-2 col-md-2 col-sm-2 col-xs-10 control-label form-label" for="city_name"><?php esc_html_e('City','hospital_mgt');?><span class="require-field">*</span></label>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
							<input id="city_name" class="form-control validate[required,custom[city_state_country_validation]]" type="text"  maxlength="50" name="city_name" 
							value="<?php if($edit){ echo esc_attr($user_info->city_name);}elseif(isset($_POST['city_name'])) echo esc_attr($_POST['city_name']);?>">
						</div>
					</div>
				</div>
				
				<div class="form-group">
					<div class="mb-3 row">	 
						<label class="col-lg-2 col-md-2 col-sm-2 col-xs-10 control-label form-label" for="state_name"><?php esc_html_e('State','hospital_mgt');?></label>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
							<input id="state_name" class="form-control validate[custom[city_state_country_validation]]" type="text"  maxlength="50" name="state_name" 
							value="<?php if($edit){ echo esc_attr($user_info->state_name);}elseif(isset($_POST['state_name'])) echo esc_attr($_POST['state_name']);?>">
						</div>
						<label class="col-lg-2 col-md-2 col-sm-2 col-xs-10 control-label form-label" for="state_name"><?php esc_html_e('Country','hospital_mgt');?></label>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
							<input id="country_name" class="form-control validate[custom[city_state_country_validation]]" type="text"  maxlength="50" name="country_name" 
							value="<?php if($edit){ echo esc_attr($user_info->country_name);}elseif(isset($_POST['country_name'])) echo esc_attr($_POST['country_name']);?>">
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-lg-2 col-md-2 col-sm-2 col-xs-10 control-label form-label" for="zip_code"><?php esc_html_e('Zip Code','hospital_mgt');?><span class="require-field">*</span></label>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
							<input id="zip_code" class="form-control  validate[required,custom[onlyLetterNumber]]" type="text" maxlength="15"  name="zip_code" 
							value="<?php if($edit){ echo esc_attr($user_info->zip_code);}elseif(isset($_POST['zip_code'])) echo esc_attr($_POST['zip_code']);?>">
						</div>
					</div>
				</div>
				<div class="header">
							<h3><?php esc_html_e('Contact Information','hospital_mgt');?></h3>
							<hr>
				</div>
					<div class="form-group">
						<div class="mb-3 row">	
						<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="mobile"><?php esc_html_e('Mobile Number','hospital_mgt');?><span class="require-field">*</span></label>
						<div class="col-lg-1 col-md-1 col-sm-1 col-xs-12 margin_bottom_5px">
						<input type="text" value="<?php if($edit) { if(!empty($user_info->phonecode)){ echo esc_attr($user_info->phonecode); }else{ ?>+<?php echo MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }  }elseif(isset($_POST['phonecode'])){ echo esc_attr($_POST['phonecode']); }else{ ?>+<?php echo MJ_hmgt_get_countery_phonecode(get_option( 'hmgt_contry' )); }?>"  class="form-control  validate[required] onlynumber_and_plussign" name="phonecode" maxlength="5">
						</div>
						<div class="col-lg-2 col-md-2 col-sm-2 col-xs-12 has-feedback">
							<input id="mobile" class="form-control validate[required,custom[phone_number]] text-input" minlength="6" maxlength="15" type="text" value="<?php if($edit){ echo esc_attr($user_info->mobile);}elseif(isset($_POST['mobile'])) echo esc_attr($_POST['mobile']);?>" name="mobile">				
						</div>
						<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="phone"><?php esc_html_e('Phone','hospital_mgt');?></label>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
							<input id="phone" class="form-control validate[custom[phone_number]] text-input" minlength="6" maxlength="15" type="text" value="<?php if($edit){ echo esc_attr($user_info->phone);}elseif(isset($_POST['phone'])) echo esc_attr($_POST['phone']);?>" name="phone">
						</div>
					</div>
				</div>
				<div class="header">
							<h3><?php esc_html_e('Login Information','hospital_mgt');?></h3>
							<hr>
				</div>
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="email"><?php esc_html_e('Email','hospital_mgt');?><span class="require-field">*</span></label>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
							<input id="email" class="form-control validate[required,custom[email]] text-input" type="text" maxlength="100" name="email" 
							value="<?php if($edit){ echo esc_attr($user_info->user_email);}elseif(isset($_POST['email'])) echo esc_attr($_POST['email']);?>">
						</div>
						<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="username"><?php esc_html_e('User Name','hospital_mgt');?><span class="require-field">*</span></label>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
							<input id="username" class="form-control validate[required,custom[username_validation]]" type="text" maxlength="30" name="username" 
							value="<?php if($edit){ echo esc_attr($user_info->user_login);}elseif(isset($_POST['username'])) echo esc_attr($_POST['username']);?>" <?php if($edit) echo "readonly";?>>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-lg-2 col-md-2 col-sm-2 col-xs-12 control-label form-label" for="password"><?php esc_html_e('Password','hospital_mgt');?><?php if(!$edit) {?><span class="require-field">*</span><?php }?></label>
						<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12 has-feedback">
							<input id="password" class="form-control <?php if(!$edit){ echo 'validate[required,minSize[8]]'; }else{ echo 'validate[minSize[8]]'; }?>" type="password"  maxlength="12" name="password" value="">
						</div>
					</div>
				</div>
				<div class="header">
							<h3><?php esc_html_e('Other Information','hospital_mgt');?></h3>
							<hr>
				</div>
				
					<div class="form-group">
						<div class="mb-3 row">	
						<label class="col-sm-2 control-label form-label" for="doctor"><?php esc_html_e('Assign Doctor','hospital_mgt');?></label>
						<div class="col-sm-3 margin_bottom_5px">
							<?php if($edit){ if(!empty($doctor->ID)){ $doctorid=$doctor->ID; }else{  $doctorid=''; } }elseif(isset($_POST['doctor'])){$doctorid=$_POST['doctor'];}else{$doctorid='';}?>
							<select name="doctor" class="form-control">
							
							<option ><?php esc_html_e('Select Doctor','hospital_mgt');?></option>
							<?php
								if($obj_hospital->role == 'doctor') 
								{
									$get_doctor = get_current_user_id();
									$doctordata=array();
									$doctordata[]=get_userdata($get_doctor);
								}
								else
								{
									$get_doctor = array('role' => 'doctor');
									$doctordata=get_users($get_doctor);
								}	
								 if(!empty($doctordata))
								 {
									foreach($doctordata as $retrieved_data){?>
									<option value="<?php echo esc_attr($retrieved_data->ID); ?>" <?php selected($doctorid,$retrieved_data->ID);?>><?php echo esc_html($retrieved_data->display_name);?> - <?php echo MJ_hmgt_doctor_specialization_title($retrieved_data->ID); ?></option>
									<?php }
								 }?>
								 
							</select>
						</div>
					</div>
				</div>		
				<div class="form-group">
					<div class="mb-3 row">	
							<label class="col-sm-2 control-label form-label" for="symptoms"><?php esc_html_e('Symptoms','hospital_mgt');?><span class="require-field">*</span></label>
							<div class="col-sm-3 multiselect_validation_symtoms margin_bottom_5px">
								<select class="form-control symptoms_list" multiple="multiple" name="symptoms[]" id="symptoms">
								<!--<option value=""><?php esc_html_e('Select Symptoms','hospital_mgt');?></option>-->
								<?php 
								$symptoms_category = $user_object->MJ_hmgt_getPatientSymptoms();
								
								if(!empty($symptoms_category))
								{
									foreach ($symptoms_category as $retrive_data)
									{
										$symptoms_array=explode(",",$user_info->symptoms);
										?>
										<option value="<?php echo esc_attr($retrive_data->ID); ?>" <?php if(in_array($retrive_data->ID,$symptoms_array)){ echo 'selected'; } ?>><?php echo esc_html($retrive_data->post_title); ?></option>
										<?php
									}
								}
								?>					
								</select>
								<br>
							</div>
								<div class="col-sm-2"><button id="addremove" model="symptoms"><?php esc_html_e('Add Or Remove','hospital_mgt');?></button></div>
							</div>
					</div>
				<?php 
					if(!$edit)
					{ 			
					?>
				
					<div class="diagnosissnosis_div">
						<div class="form-group">
							<div class="mb-3 row">	
								<label class="col-sm-2 col-md-2 control-label form-label" for="diagnosis"><?php esc_html_e('Diagnosis Report','hospital_mgt');?></label>
								<div class="col-sm-6 col-md-4">
									<input type="file" class="form-control file dignosisreport" name="diagnosis[]">
								</div>
							</div>	
						</div>
					</div>
					<?php
					}			 
					if($edit)
					{ 			
						$diagnosis_obj=new MJ_hmgt_dignosis(); 
						$diagnosisdata=$diagnosis_obj->MJ_hmgt_get_diagnosis_outpatient(MJ_hmgt_id_decrypt($_REQUEST['outpatient_id']));
										
						if(!empty($diagnosisdata))
						{	
							?>
							<div class="diagnosissnosis_div">
							<?php
							foreach($diagnosisdata as $diagnosis)
							{
							?>
								<div class="form-group">
									<div class="mb-3 row">	
										<label class="col-sm-2 col-md-2 control-label form-label" for="diagnosis"><?php esc_html_e('Diagnosis Report','hospital_mgt');?></label>
										<div class="col-sm-3 col-md-4">
											<input type="file" class="form-control file dignosisreport" name="diagnosis[]" value=''>
										</div>
										<div class="col-sm-2 col-md-2">
											<?php 
											if(!empty($diagnosis) && $diagnosis->attach_report!="")
											{
											?>
												<a href="<?php echo content_url().'/uploads/hospital_assets/'.$diagnosis->attach_report;?>" target="_blank" class="btn btn-default"><i class="fa fa-download"></i><?php esc_html_e('View Report','hospital_mgt');?></a>
												<input type="hidden" name="hidden_attach_report[]" value="<?php print  $diagnosis->attach_report ?>" >
											<?php
											}
											else
											{
												?>
												<a href="javascript:void(0);" class="btn btn-default"><i class="fa fa-download"></i><?php esc_html_e('No Report','hospital_mgt');?></a>
												<?php 
											}
											?>
										</div>
									</div>
								</div>
							<?php
							}
							?>
							</div>		
						<?php
						}
						else
						{
							?>
							<div class="diagnosissnosis_div">
									<div class="form-group">
										<div class="mb-3 row">	
										<label class="col-sm-2 col-md-2 control-label form-label" for="diagnosis"><?php esc_html_e('Diagnosis Report','hospital_mgt');?></label>
										<div class="col-sm-3 col-md-4">
											<input type="file" class="form-control file dignosisreport" name="diagnosis[]">
										</div>
									</div>	
								</div>
							</div>
							<?php
						}			
					}
					?>	
				<div class="form-group">	
					<div class="mb-3 row">			
						<div class="col-sm-2">
						</div>
						<div class="col-sm-2">
							<input type="button" value="<?php esc_html_e('Add More Report','hospital_mgt') ?>" name="add_more_report" class="add_more_report_fronted btn btn-default">
						</div>
					</div>
				</div>
				<?php wp_nonce_field( 'save_outpatient_nonce' ); ?>
				<div class="form-group">
					<div class="mb-3 row">	
						<label class="col-sm-2 col-md-2 control-label form-label" for="photo"><?php esc_html_e('Image','hospital_mgt');?></label>
							<div class="col-sm-3 col-md-4">
								<input type="hidden" id="hmgt_user_avatar_url" class="form-control" name="hmgt_user_avatar_url"  
								value="<?php if($edit)echo esc_url( $user_info->hmgt_user_avatar );elseif(isset($_POST['upload_user_avatar_image'])) echo $_POST['upload_user_avatar_image']; ?>" readonly />
								<input type="hidden" name="hidden_upload_user_avatar_image" 
								value="<?php if($edit){ echo $user_info->hmgt_user_avatar;}elseif(isset($_POST['upload_user_avatar_image'])) echo $_POST['upload_user_avatar_image'];
								else echo get_option('hmgt_patient_thumb');?>">
								 <input id="upload_user_avatar_image" name="upload_user_avatar_image" type="file" onchange="fileCheck(this);" class="form-control file" value="<?php esc_html_e( 'Upload image', 'hospital_mgt' ); ?>" />
							 
						
					</div>
					<div class="clearfix"></div>
					
					<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
							 <div id="upload_user_avatar_preview" >
								 <?php if($edit) 
									{
									if($user_info->hmgt_user_avatar == "")
									{?>
									<img class="image_preview_css" alt="" src="<?php echo esc_url(get_option( 'hmgt_patient_thumb' )); ?>">
									<?php }
									else {
										
										?>
									<img class="image_preview_css" src="<?php if($edit)echo esc_url( $user_info->hmgt_user_avatar ); ?>" />
									<?php 
									}
									}
									else {
										?>
										<img class="image_preview_css" alt="" src="<?php echo esc_url(get_option( 'hmgt_patient_thumb' )); ?>">
										<?php 
									}?>
							</div>
				 </div>
				 </div>
				</div>		
				<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
					<input type="submit" value="<?php if($edit){ esc_html_e('Save Patient','hospital_mgt'); }else{ esc_html_e('Add Patient','hospital_mgt');}?>" name="save_outpatient" class="btn btn-success symptoms_alert"/>
				</div>
			</form><!-- END Outpatient FORM-->
		</div><!-- END PANEL BODY DIV-->
	<?php } ?>
</div><!-- END TAB CONTENT DIV-->