<?php
MJ_hmgt_browser_javascript_check();
$obj_bloodbank=new MJ_hmgt_bloodbank();
$active_tab =isset($_REQUEST['tab'])?$_REQUEST['tab']:'bloodmanage';
//access right
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
//Save Dispatch BLOOD DATA
if(isset($_POST['save_dispatch_blood']))
{
	$nonce = $_POST['_wpnonce'];
	if (wp_verify_nonce( $nonce, 'save_dispatch_blood_nonce' ) )
	{
		global $wpdb;		
		$table_bloodbank=$wpdb->prefix. 'hmgt_blood_bank';
		
		if($_REQUEST['action']=='edit')
		{	
			//check blood stock
			$blood_group=$_POST['blood_group'];
			$blood_status=$_POST['blood_status'];
			$old_blood_group=$_POST['old_blood_group'];
			$old_blood_status=$_POST['old_blood_status'];
			$dispatchblood_id=$_POST['dispatchblood_id'];
			
			$result_blood_group = $wpdb->get_row("SELECT * FROM $table_bloodbank where blood_group='$blood_group'");
			$oldblood_status=$result_blood_group->blood_status;
		
			if($blood_group == $old_blood_group)
			{
				$oldblood_status=$oldblood_status+$old_blood_status;
				if(empty($result_blood_group))
				{
					wp_redirect ( home_url() . '?dashboard=user&page=bloodbank&tab=adddispatchblood&action=edit&dispatchblood_id='.MJ_hmgt_id_encrypt($dispatchblood_id).'&message=5');
				}
				elseif($blood_status>$oldblood_status)
				{
					wp_redirect ( home_url() . '?dashboard=user&page=bloodbank&tab=adddispatchblood&action=edit&dispatchblood_id='.MJ_hmgt_id_encrypt($dispatchblood_id).'&message=5');
				}
				else
				{
					$result=$obj_bloodbank->MJ_hmgt_add_dispatch_blood($_POST);
					if($result)
					{	
						wp_redirect ( home_url() . '?dashboard=user&page=bloodbank&tab=dispatchbloodlist&message=2');
					}
				}
			}
			else
			{
				if(empty($result_blood_group))
				{
					wp_redirect ( home_url() . '?dashboard=user&page=bloodbank&tab=adddispatchblood&action=edit&dispatchblood_id='.MJ_hmgt_id_encrypt($dispatchblood_id).'&message=5');
				}
				elseif($blood_status>$oldblood_status)
				{
					wp_redirect ( home_url() . '?dashboard=user&page=bloodbank&tab=adddispatchblood&action=edit&dispatchblood_id='.MJ_hmgt_id_encrypt($dispatchblood_id).'&message=5');
				}
				else
				{
					$result=$obj_bloodbank->MJ_hmgt_add_dispatch_blood($_POST);
					if($result)
					{	
						wp_redirect ( home_url() . '?dashboard=user&page=bloodbank&tab=dispatchbloodlist&message=2');
					}
				}	
			}
		}
		else
		{
			//check blood stock
			$blood_group=$_POST['blood_group'];
			$blood_status=$_POST['blood_status'];
			$result_blood_group = $wpdb->get_row("SELECT * FROM $table_bloodbank where blood_group='$blood_group'");
			$oldblood_status=$result_blood_group->blood_status;
			
			if(empty($result_blood_group))
			{
				wp_redirect ( home_url() . '?dashboard=user&page=bloodbank&tab=adddispatchblood&message=5');
			}
			elseif($blood_status>$oldblood_status)
			{
				wp_redirect ( home_url() . '?dashboard=user&page=bloodbank&tab=adddispatchblood&message=5');
			}
			else
			{	
				$result=$obj_bloodbank->MJ_hmgt_add_dispatch_blood($_POST);
				if($result)
				{
					wp_redirect ( home_url() . '?dashboard=user&page=bloodbank&tab=dispatchbloodlist&message=1');
				}
			}
		}
	}
}
//Save Blood DONOR DATA
if(isset($_POST['save_blooddonor']))
{
	$nonce = $_POST['_wpnonce'];
	// var_dump($_POST);
	if (wp_verify_nonce( $nonce, 'save_blooddonor_nonce' ) )
	{	
		if($_REQUEST['action']=='edit')
		{
			$result=$obj_bloodbank->MJ_hmgt_add_blood_donor($_POST);
			// if($result)
			// {
				wp_redirect ( home_url() . '?dashboard=user&page=bloodbank&tab=blooddonorlist&message=2');
			// }	
		}
		else
		{
			$result=$obj_bloodbank->MJ_hmgt_add_blood_donor($_POST);
			if($result)
			{
				wp_redirect ( home_url() . '?dashboard=user&page=bloodbank&tab=blooddonorlist&message=1');
			}
		}
	}
}	?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	"use strict";
	<?php
	if (is_rtl())
		{
		?>	
			$('#blooddonor_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
			$('#bloodgroup_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
		<?php
		}
		else{
			?>
			$('#blooddonor_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			$('#bloodgroup_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
			<?php
		}
	?>
	$.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
      $('#last_donate_date').datepicker({
     endDate: '+0d',
        autoclose: true,
   }); 
} );
</script>
<?php
//SAVE BLOOD GROUP DATA		
if(isset($_POST['save_bloodgroup']))
{
	$nonce = $_POST['_wpnonce'];
	if (wp_verify_nonce( $nonce, 'save_bloodgroup_nonce' ) )
	{
		if($_REQUEST['action']=='edit')
		{
			global $wpdb;
			$table_bloodbank=$wpdb->prefix. 'hmgt_blood_bank';
			$blood_group=$_POST['blood_group'];
			$bloodgroup_id=$_POST['bloodgroup_id'];
			$allready_added_result = $wpdb->get_row("SELECT * FROM $table_bloodbank where blood_group = '$blood_group' AND blood_id !=".$bloodgroup_id);
			
			if(!empty($allready_added_result))
			{				
				wp_redirect ( home_url() . '?dashboard=user&page=bloodbank&tab=addbloodgoup&action=edit&bloodgroup_id='.MJ_hmgt_id_encrypt($bloodgroup_id).'&message=4');
			}
			else
			{
				$result=$obj_bloodbank->MJ_hmgt_add_blood_group($_POST);
				if($result)
				{
					wp_redirect ( home_url() . '?dashboard=user&page=bloodbank&tab=bloodmanage&message=2');
				}
			}
		}
		else
		{
			global $wpdb;
			$table_bloodbank=$wpdb->prefix. 'hmgt_blood_bank';
			$blood_group=$_POST['blood_group'];
			$allready_added_result = $wpdb->get_row("SELECT * FROM $table_bloodbank where blood_group = '$blood_group'");
			
			if(!empty($allready_added_result))
			{				
				wp_redirect ( home_url() . '?dashboard=user&page=bloodbank&tab=addbloodgoup&&action=insert&message=4');
			}
			else
			{
				$result=$obj_bloodbank->MJ_hmgt_add_blood_group($_POST);
				if($result)
				{
					wp_redirect ( home_url() . '?dashboard=user&page=bloodbank&tab=bloodmanage&message=1');
				}
			}
		}
	}
}

if(isset($_REQUEST['action'])&& $_REQUEST['action']=='delete')
{
	if(isset($_REQUEST['blooddonor_id']))
	{
		$result=$obj_bloodbank->MJ_hmgt_delete_blooddonor(MJ_hmgt_id_decrypt($_REQUEST['blooddonor_id']));
		wp_redirect( site_url () . '/?dashboard=user&page=bloodbank&tab=blooddonorlist&message=3'); 	
	}
	if(isset($_REQUEST['bloodgroup_id']))
	{
		$result=$obj_bloodbank->MJ_hmgt_delete_bloodgroup(MJ_hmgt_id_decrypt($_REQUEST['bloodgroup_id']));
		wp_redirect( site_url() . '/?dashboard=user&page=bloodbank&tab=bloodmanage&message=3');
	}	
	if(isset($_REQUEST['dispatchblood_id']))
	{
		$result=$obj_bloodbank->MJ_hmgt_delete_dispatchblood_data(MJ_hmgt_id_decrypt($_REQUEST['dispatchblood_id']));
		wp_redirect(  site_url() . '/?dashboard=user&page=bloodbank&tab=dispatchbloodlist&message=3');
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
			?></p>
		</div>
		<?php
	}
	elseif($message == 2)
	{?>
		<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
		</button><p><?php
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
	elseif($message == 4) 
	{?>
		<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
		</button><p>
		<?php 
			esc_html_e('This blood group allready added you want to update it','hospital_mgt');
		?></div></p><?php
	}
	elseif($message == 5) 
	{?>
		<div id="messages" class="alert_msg alert alert-success alert-dismissible fade in" role="alert"><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span>
		</button><p>
		<?php 
			esc_html_e('This Blood group is not available in the stock.','hospital_mgt');
		?></div></p><?php
	}
}
?>
<script type="text/javascript">
jQuery(document).ready(function() {
	"use strict";
	jQuery('#bloodgroup_list').DataTable({
			"responsive": true,
			"aoColumns":[
				  {"bSortable": true},
				  {"bSortable": true}                            
				  <?php  
					if($user_access['edit']=='1' || $user_access['delete']=='1')
					{
						?>
					  ,{"bSortable": false}
					 <?php  
					 } 
					 ?> 	
				],
			language:<?php echo MJ_hmgt_datatable_multi_language();?>});
} );
</script>

<div class="panel-body panel-white"><!-- START PANEL BODY DIV-->
	<ul class="nav nav-tabs panel_tabs" role="tablist">
		<li class="<?php if($active_tab=='bloodmanage'){?>active<?php }?>">
				<a href="?dashboard=user&page=bloodbank&tab=bloodmanage" class="tab <?php echo $active_tab == 'bloodmanage' ? 'active' : ''; ?>">
				 <i class="fa fa-align-justify"></i> <?php esc_html_e('Blood Manage', 'hospital_mgt'); ?></a>
		</li>
		<li class="<?php if($active_tab=='addbloodgoup'){?>active<?php }?>">
			  <?php  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && isset($_REQUEST['bloodgroup_id']))
				{?>
				<a href="?dashboard=user&page=bloodbank&tab=addbloodgoup&action=edit&bloodgroup_id=<?php if(isset($_REQUEST['bloodgroup_id'])) echo $_REQUEST['bloodgroup_id'];?>" class="tab <?php echo $active_tab == 'addbloodgoup' ? 'active' : ''; ?>">
				 <i class="fa fa"></i> <?php esc_html_e('Edit Blood Group', 'hospital_mgt'); ?></a>
				 <?php }
				else
				{
					if($user_access['add']=='1')
					{
					?>
						<a href="?dashboard=user&page=bloodbank&tab=addbloodgoup&&action=insert" class="tab <?php echo $active_tab == 'addbloodgoup' ? 'active' : ''; ?>">
						<i class="fa fa-plus-circle"></i> <?php esc_html_e('Add Blood Group', 'hospital_mgt'); ?></a>
				<?php 
					} 
				}
		    ?>
		</li>
		<li class="<?php if($active_tab=='blooddonorlist'){?>active<?php }?>">
			<a href="?dashboard=user&page=bloodbank&tab=blooddonorlist" class="tab <?php echo $active_tab == 'blooddonorlist' ? 'active' : ''; ?>">
			<i class="fa fa-align-justify"></i> <?php esc_html_e('Blood Donor List', 'hospital_mgt'); ?></a>
		</li>
		
		<li class="<?php if($active_tab=='addblooddonor'){?>active<?php }?>">
			  <?php  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && isset($_REQUEST['blooddonor_id']))
				{?>
				<a href="?dashboard=user&page=bloodbank&tab=addblooddonor&action=edit&blooddonor_id=<?php if(isset($_REQUEST['blooddonor_id'])) echo $_REQUEST['blooddonor_id'];?>" class="tab <?php echo $active_tab == 'addblooddonor' ? 'active' : ''; ?>">
				 <i class="fa fa"></i> <?php esc_html_e('Edit Blood Donor', 'hospital_mgt'); ?></a>
				 <?php }
				else
				{
					if($user_access['add']=='1')
					{
					?>
						<a href="?dashboard=user&page=bloodbank&tab=addblooddonor&&action=insert" class="tab <?php echo $active_tab == 'addblooddonor' ? 'active' : ''; ?>">
						<i class="fa fa-plus-circle"></i> <?php esc_html_e('Add Blood Donor', 'hospital_mgt'); ?></a>
				<?php 
					} 
				}
				?>
		</li>
		<li class="<?php if($active_tab=='dispatchbloodlist'){?>active<?php }?>">
			<a href="?dashboard=user&page=bloodbank&tab=dispatchbloodlist" class="tab <?php echo $active_tab == 'dispatchbloodlist' ? 'active' : ''; ?>">
			<i class="fa fa-align-justify"></i> <?php esc_html_e('Dispatch Blood List', 'hospital_mgt'); ?></a>
		</li>
		
		<li class="<?php if($active_tab=='adddispatchblood'){?>active<?php }?>">
			  <?php  if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit' && isset($_REQUEST['dispatchblood_id']))
				{?>
				<a href="?dashboard=user&page=bloodbank&tab=adddispatchblood&action=edit&dispatchblood_id=<?php if(isset($_REQUEST['dispatchblood_id'])) echo $_REQUEST['dispatchblood_id'];?>" class="tab <?php echo $active_tab == 'adddispatchblood' ? 'active' : ''; ?>">
				 <i class="fa fa"></i> <?php esc_html_e('Edit Dispatch Blood', 'hospital_mgt'); ?></a>
				 <?php }
				else
				{
					if($user_access['add']=='1')
					{
					?>
						<a href="?dashboard=user&page=bloodbank&tab=adddispatchblood&&action=insert" class="tab <?php echo $active_tab == 'adddispatchblood' ? 'active' : ''; ?>">
						<i class="fa fa-plus-circle"></i> <?php esc_html_e('Dispatch Blood', 'hospital_mgt'); ?></a>
				<?php 
					} 
				}
				?>
		</li>
	</ul>
	<div class="tab-content"><!--START TAB CONTENT DIV-->
    	 <?php if($active_tab=='bloodmanage'){?>
		<div class="panel-body"><!--START PANEL BODY DIV-->
            <div class="table-responsive"><!--START TABLE RESPONSIVE DIV-->
			    <table id="bloodgroup_list" class="display dataTable " cellspacing="0" width="100%"><!--START BLOOD GROUP LIST TABLE-->
					<thead>
						<tr>
						    <th><?php esc_html_e( 'Blood Group', 'hospital_mgt' ) ;?></th>
						    <th><?php esc_html_e( 'Number Of Bags', 'hospital_mgt' ) ;?></th> 			   
							<?php  
							if($user_access['edit']=='1' || $user_access['delete']=='1')
							{ ?>
								<th> <?php esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
							<?php
							}
							?>			
						</tr>
				    </thead>
					<tfoot>
						<tr>
						<th><?php esc_html_e( 'Blood Group', 'hospital_mgt' ) ;?></th>
						   <th><?php esc_html_e( 'Number Of Bags', 'hospital_mgt' ) ;?></th> 			  
							<?php  
							if($user_access['edit']=='1' || $user_access['delete']=='1')
							{ ?>
								<th> <?php esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
							<?php
							}
							?>			
						</tr>
					</tfoot>
					<tbody>
					 <?php 
						foreach($obj_bloodbank->MJ_hmgt_get_all_bloodgroups() as $retrieved_data){ ?>
						<tr>
							 <td class="blood_group">
							<?php 
									echo esc_html__("$retrieved_data->blood_group","hospital_mgt");
							?></td>
							<td class="subject_name"><?php  echo esc_html($retrieved_data->blood_status);?></td>
						 <?php  
							if($user_access['edit']=='1' || $user_access['delete']=='1')
							{ ?>
								<td class="action"> 
								<?php
								if($user_access['edit']=='1')
								{
								?>	
									<a href="?dashboard=user&page=bloodbank&tab=addbloodgoup&action=edit&bloodgroup_id=<?php echo MJ_hmgt_id_encrypt(esc_attr($retrieved_data->blood_id));?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
								<?php
								}
								if($user_access['delete']=='1')
								{
								?>	
									<a href="?dashboard=user&page=bloodbank&tab=bloodmanage&action=delete&bloodgroup_id=<?php echo MJ_hmgt_id_encrypt(esc_attr($retrieved_data->blood_id));?>" class="btn btn-danger" onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this record?','hospital_mgt');?>');">
								<?php esc_html_e( 'Delete', 'hospital_mgt' ) ;?> </a>
								<?php
								}
								?>	
								</td>
							<?php
							}
							?>
						</tr>
						<?php } ?>
					</tbody>
				</table><!--END BLOOD GROUP LIST TABLE-->
 		</div><!--END TABLE RESPONSIVE DIV-->
		</div><!--END PANEL BODY DIV-->
		<?php } 
			if($active_tab=='addbloodgoup')
			{
				$edit=0;
				if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
				{					
					$edit=1;
					$result = $obj_bloodbank->MJ_hmgt_get_single_bloodgroup(MJ_hmgt_id_decrypt($_REQUEST['bloodgroup_id']));
				}
				?>
    <div class="panel-body"><!--START PANEL BODY DIV-->
        <form name="bloodgroup_form" action="" method="post" class="form-horizontal" id="bloodgroup_form"><!--START BLOOD GROUP FORM-->
        <?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
		<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
		<input type="hidden" name="bloodgroup_id" value="<?php if(isset($_REQUEST['bloodgroup_id'])) echo MJ_hmgt_id_decrypt(esc_attr($_REQUEST['bloodgroup_id']));?>"  />
		<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="blood_group"><?php esc_html_e('Blood Group','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<?php if($edit){ $userblood=$result->blood_group; }elseif(isset($_POST['blood_group'])){$userblood=$_POST['blood_group'];}else{$userblood='';}?>
						<select id="blood_group" class="form-control validate[required] max_width_100" name="blood_group">
						<option value=""><?php esc_html_e('Select Blood Group','hospital_mgt');?></option>
						<?php foreach(MJ_hmgt_blood_group() as $blood){ ?>
								<option value="<?php echo esc_attr($blood);?>" <?php selected($userblood,$blood);  ?>><?php echo esc_html($blood); ?> </option>
						<?php } ?>
					</select>
					</div>
				</div>
			</div>
			<?php wp_nonce_field( 'save_bloodgroup_nonce' ); ?>
			<div class="form-group margin_bottom_5px">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="blood_status"><?php esc_html_e('Number Of Bags','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="blood_status" class="form-control validate[required] text-input" type="number" min="0" onKeyPress="if(this.value.length==4) return false;" value="<?php if($edit){ echo esc_attr($result->blood_status);}elseif(isset($_POST['blood_status'])) echo esc_attr($_POST['blood_status']);?>" name="blood_status">
					</div>
				</div>
			</div>
			<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
        	<input type="submit" value="<?php if($edit){ esc_html_e('Save','hospital_mgt'); }else{ esc_html_e('Save','hospital_mgt');}?>" name="save_bloodgroup" class="btn btn-success"/>
        </div>
        </form><!--END BLOOD GROUP FORM-->
    </div><!--END PANEL BODY DIV-->
		<?php 
		}
		if($active_tab=='addblooddonor')
		{
			$edit=0;
			if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
			{
				$edit=1;
				$result = $obj_bloodbank->MJ_hmgt_get_single_blooddonor(MJ_hmgt_id_decrypt($_REQUEST['blooddonor_id']));	
			}?>
		
    <div class="panel-body"><!--START PANEL BODY DIV-->
        <form name="blooddonor_form" action="" method="post" class="form-horizontal" id="blooddonor_form"><!--START BLOOD DONOR FORM-->
         <?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
		<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
		<input type="hidden" name="old_blood_group" value="<?php if($edit){ echo esc_attr($result->blood_group); } ?>">
		<input type="hidden" name="old_blood_status" value="<?php if($edit){ echo esc_attr($result->blood_status); } ?>">
		<input type="hidden" name="blooddonor_id" value="<?php if(isset($_REQUEST['blooddonor_id'])) echo MJ_hmgt_id_decrypt(esc_attr($_REQUEST['blooddonor_id']));?>"  />
		<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="first_name"><?php esc_html_e('Full Name','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="bool_dodnor_name" class="form-control validate[required,custom[onlyLetter_specialcharacter]] text-input"  maxlength="50" type="text" value="<?php if($edit){ echo esc_attr($result->donor_name);}elseif(isset($_POST['bool_dodnor_name'])) echo esc_attr($_POST['bool_dodnor_name']);?>" name="bool_dodnor_name">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="gender"><?php esc_html_e('Gender','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
					<?php $genderval = "male"; if($edit){ $genderval=$result->donor_gender; }elseif(isset($_POST['gender'])) {$genderval=$_POST['gender'];}?>
						<label class="radio-inline">
						 <input type="radio" value="male" class="tog" name="gender"  <?php  checked( 'male', $genderval);  ?>/><?php esc_html_e('Male','hospital_mgt');?>
						</label>
						<label class="radio-inline">
						  <input type="radio" value="female" class="tog" name="gender"  <?php  checked( 'female', $genderval);  ?>/><?php esc_html_e('Female','hospital_mgt');?> 
						</label>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="med_category_name"><?php esc_html_e('Age','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="dodnor_age" class="form-control validate[required,custom[integer],min[18]] text-input" min="0" max="99" type="number" onKeyPress="if(this.value.length==2) return false;"  value="<?php if($edit){ echo esc_attr($result->donor_age);}elseif(isset($_POST['dodnor_age'])) echo esc_attr($_POST['dodnor_age']);?>" name="dodnor_age">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">		
					<label class="col-sm-2 control-label form-label" for="phone"><?php esc_html_e('Phone','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="phone" class="form-control validate[required,custom[phone_number]] text-input" minlength="6" maxlength="15" type="text" value="<?php if($edit){ echo esc_attr($result->donor_phone);}elseif(isset($_POST['phone'])) echo esc_attr($_POST['phone']);?>" name="phone">					
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="email"><?php esc_html_e('Email','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="email" class="form-control validate[required,custom[email]] text-input" type="text" maxlength="100"  name="email" 
						value="<?php if($edit){ echo esc_attr($result->donor_email);}elseif(isset($_POST['email'])) echo esc_attr($_POST['email']);?>">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="bloodgruop"><?php esc_html_e('Blood Group','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<?php if($edit){ $userblood=$result->blood_group; }elseif(isset($_POST['blood_group'])){$userblood=$_POST['blood_group'];}else{$userblood='';}?>
						<select id="blood_group" class="form-control validate[required] max_width_100" name="blood_group">
							<option value=""><?php esc_html_e('Select Blood Group','hospital_mgt');?></option>
							<?php foreach(MJ_hmgt_blood_group() as $blood){ ?>
								<option value="<?php echo esc_attr($blood);?>" <?php selected($userblood,$blood);  ?>><?php echo esc_html($blood); ?> </option>
							<?php } ?>
						</select>
					</div>
				</div>
			</div>
			<?php wp_nonce_field( 'save_blooddonor_nonce' ); ?>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="blood_status"><?php esc_html_e('Number Of Bags','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="blood_status" class="form-control validate[required] text-input" type="number" min="1" onKeyPress="if(this.value.length==1) return false;" value="<?php if($edit){ echo esc_attr($result->blood_status);}elseif(isset($_POST['blood_status'])) echo esc_attr($_POST['blood_status']);?>" name="blood_status">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="last_donet_date"><?php esc_html_e('Last Donation Date','hospital_mgt');?></label>
					<div class="col-sm-8 margin_bottom_5px">
						<input id="last_donate_date" class="form-control " type="text"  value="<?php if($edit){ if(!empty($result->last_donet_date!='0000-00-00')) { echo date(MJ_hmgt_date_formate(),strtotime($result->last_donet_date)); }else{ echo ''; }}elseif(isset($_POST['last_donate_date'])) echo esc_attr($_POST['last_donate_date']);?>" name="last_donate_date">
					</div>
				</div>
			</div>
			
			<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
        	<input type="submit" value="<?php if($edit){ esc_html_e('Save','hospital_mgt'); }else{ esc_html_e('Save','hospital_mgt');}?>" name="save_blooddonor" class="btn btn-success"/>
        </div>
        </form><!--END BLOOD GROUP FORM !-->
    </div><!--END PANEL BODY DIV-->
				
    <?php 
	}
	if($active_tab=='blooddonorlist'){?>
	<script type="text/javascript">
		jQuery(document).ready(function() {
			"use strict";
			jQuery('#blooddonor_list').DataTable({
				"responsive": true,
				 "aoColumns":[
							  {"bSortable": true},
							  {"bSortable": true},
							  {"bSortable": true},
							  {"bSortable": true},	               	                             
							  {"bSortable": true},	               	                             
							  {"bSortable": true}, 
							  <?php  
							  if($user_access['edit']=='1' || $user_access['delete']=='1')
							  { ?>							  
									{"bSortable": false}
							  <?php
							  }
							   ?>
						   ],
						language:<?php echo MJ_hmgt_datatable_multi_language();?>		   	   
				});
		} );
		</script>
	<div class="panel-body"><!--START PANEL BODY DIV-->
		<form name="wcwm_report" action="" method="post">
        <div class="panel-body"><!--START PANEL BODY DIV-->
        	<div class="table-responsive"><!--START TABLE RESPONSIVE DIV-->
				<table id="blooddonor_list" class="display dataTable" cellspacing="0" width="100%"><!--START BLOOD DONOR LIST TABLE-->
					<thead>
						<tr>
							<th><?php esc_html_e('Name', 'hospital_mgt' ) ;?></th>
							<th><?php esc_html_e( 'Blood Group', 'hospital_mgt' ) ;?></th>
							<th><?php esc_html_e( 'Age', 'hospital_mgt' ) ;?></th>
							<th><?php esc_html_e( 'Gender', 'hospital_mgt' ) ;?></th>
							<th><?php esc_html_e( 'Number Of Bags', 'hospital_mgt' ) ;?></th> 
							<th><?php esc_html_e( 'Last Donation Date', 'hospital_mgt' ) ;?></th> 
							  <?php  
							  if($user_access['edit']=='1' || $user_access['delete']=='1')
							  { ?>		
							<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
							 <?php
							  }
							   ?>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<th><?php esc_html_e( 'Name', 'hospital_mgt' ) ;?></th>
							<th><?php esc_html_e( 'Blood Group', 'hospital_mgt' ) ;?></th>
							<th><?php esc_html_e( 'Age', 'hospital_mgt' ) ;?></th>
							<th><?php esc_html_e( 'Gender', 'hospital_mgt' ) ;?></th>
							<th><?php esc_html_e( 'Number Of Bags', 'hospital_mgt' ) ;?></th> 
							<th><?php esc_html_e( 'Last Donation Date', 'hospital_mgt' ) ;?></th> 
							  <?php  
							  if($user_access['edit']=='1' || $user_access['delete']=='1')
							  { ?>		
							<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
							 <?php
							  }
							   ?>
						</tr>
					</tfoot>
					<tbody>
					 <?php 
						if($obj_hospital->role == 'doctor' || $obj_hospital->role == 'nurse' || $obj_hospital->role == 'laboratorist' || $obj_hospital->role == 'pharmacist' || $obj_hospital->role == 'accountant' || $obj_hospital->role == 'receptionist') 
						{
							$own_data=$user_access['own_data'];
							if($own_data == '1')
							{
								$blooddonordata=$obj_bloodbank->MJ_hmgt_get_all_blooddonors_by_donor_create_by();
							}
							else
							{
								$blooddonordata=$obj_bloodbank->MJ_hmgt_get_all_blooddonors();
							}
						}
						else
						{
							$blooddonordata=$obj_bloodbank->MJ_hmgt_get_all_blooddonors();
						}
							
					 if(!empty($blooddonordata))
					 {
						foreach($blooddonordata as $retrieved_data)
						{ 
					?>
						<tr>
							<td class="name"><a href="javascript:void(0);"><?php echo esc_html($retrieved_data->donor_name);?></a></td>
							<td class="bloodgroup">
							<?php 
							  echo esc_html__("$retrieved_data->blood_group","hospital_mgt");
							?></td>
							<td class="age"><?php echo esc_html($retrieved_data->donor_age);?></td>
							<td class="age"><?php echo esc_html__("$retrieved_data->donor_gender","hospital_mgt");?></td>
							<td class="subject_name"><?php  
							if(!empty($retrieved_data->blood_status))
							{ 
								echo esc_html($retrieved_data->blood_status);
							}
							else
							{
								echo '-'; 
							}
							?></td>
							<td class="lastdonate_date"><?php if(!empty($retrieved_data-> last_donet_date!='0000-00-00')) { echo date(MJ_hmgt_date_formate(),strtotime($retrieved_data->last_donet_date)); }?></td>
						   <?php  
						  if($user_access['edit']=='1' || $user_access['delete']=='1')
						  { ?>	
							<td class="action"> 
							<?php
							if($user_access['edit']=='1')
							{
							?>
								<a href="?dashboard=user&page=bloodbank&tab=addblooddonor&action=edit&blooddonor_id=<?php echo MJ_hmgt_id_encrypt(esc_attr($retrieved_data->bld_donor_id));?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
							<?php
							}
							if($user_access['delete']=='1')
							{
							?>	
								<a href="?dashboard=user&page=bloodbank&tab=bloodbanklist&action=delete&blooddonor_id=<?php echo MJ_hmgt_id_encrypt(esc_attr($retrieved_data->bld_donor_id));?>" class="btn btn-danger" 
								onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this record?','hospital_mgt');?>');">
								<?php esc_html_e( 'Delete', 'hospital_mgt' ) ;?> </a>
						   <?php
							}
							?>	
							</td>
							<?php
						  }
						  ?>
						</tr>
						<?php 
						}						
					}?>
					</tbody>
				</table><!--END BLLOD DONOR LIST TABLE -->
            </div><!--END TABLE RESPONSIVE DIV-->
        </div><!--END PANEL BODY DIV-->
       
    </form>
    </div><!--END PANEL BODY DIV-->
		<?php
	}
	if($active_tab=='dispatchbloodlist')
	{
	?>
		<script type="text/javascript">
		jQuery(document).ready(function() {
			"use strict";
			jQuery('#dispatch_bood').DataTable({
				"responsive": true,
				 "aoColumns":[
							  {"bSortable": true},
							  {"bSortable": true},
							  {"bSortable": true},
							  {"bSortable": true},	               	                             
							  {"bSortable": true},	               	                             
							  {"bSortable": true},	               	                             
							  {"bSortable": true},
							 <?php  
							  if($user_access['edit']=='1' || $user_access['delete']=='1')
							  { ?>			
							  {"bSortable": false}
							  <?php 
							  }
							  ?>
						   ],
						language:<?php echo MJ_hmgt_datatable_multi_language();?>		   	   
				});
		} );
		</script>
	<div class="panel-body"><!--START PANEL BODY DIV-->
		<form name="wcwm_report" action="" method="post">
			<div class="panel-body"><!--START PANEL BODY DIV-->
				<div class="table-responsive"><!--START TABLE RESPONSIVE DIV-->
					<table id="dispatch_bood" class="display" cellspacing="0" width="100%"><!--START Dispatch BLLOD DIV-->
						<thead>
							<tr>
								<th><?php esc_html_e('Patient Name', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Blood Group', 'hospital_mgt' ) ;?></th>								
								<th><?php esc_html_e( 'Number Of Bags', 'hospital_mgt' ) ;?></th> 
								<th><?php esc_html_e( 'Date', 'hospital_mgt' ) ;?></th> 
								<th><?php esc_html_e( 'Charges', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
								<th><?php esc_html_e( 'Tax Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
								<th><?php esc_html_e( 'Total Charges', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
								 <?php  
							  if($user_access['edit']=='1' || $user_access['delete']=='1')
							  { ?>	
								<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
								<?php
							  }
							  ?>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<th><?php esc_html_e('Patient Name', 'hospital_mgt' ) ;?></th>
								<th><?php esc_html_e( 'Blood Group', 'hospital_mgt' ) ;?></th>								
								<th><?php esc_html_e( 'Number Of Bags', 'hospital_mgt' ) ;?></th> 
								<th><?php esc_html_e( 'Date', 'hospital_mgt' ) ;?></th> 
								<th><?php esc_html_e( 'Charges', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
								<th><?php esc_html_e( 'Tax Amount', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
								<th><?php esc_html_e( 'Total Charges', 'hospital_mgt' ) ;?> (<?php echo "<span>".MJ_hmgt_get_currency_symbol()."</span>";?>)</th>
								 <?php  
							  if($user_access['edit']=='1' || $user_access['delete']=='1')
							  { ?>	
								<th><?php  esc_html_e( 'Action', 'hospital_mgt' ) ;?></th>
								<?php
							  }
							  ?>
							</tr>
						</tfoot>
			 
						<tbody>
						 <?php 
						 if($obj_hospital->role == 'laboratorist' || $obj_hospital->role == 'pharmacist' || $obj_hospital->role == 'accountant' || $obj_hospital->role == 'receptionist') 
						{						
							$own_data=$user_access['own_data'];
							if($own_data == '1')
							{
								$dispatch_blooddata=$obj_bloodbank->MJ_hmgt_get_all_dispatch_blood_data_created_by();
							}
							else
							{
								$dispatch_blooddata=$obj_bloodbank->MJ_hmgt_get_all_dispatch_blood_data();
							}
						}
						elseif($obj_hospital->role == 'doctor') 
						{						
							$own_data=$user_access['own_data'];
							if($own_data == '1')
							{
								$dispatch_blooddata=$obj_bloodbank->MJ_hmgt_get_doctor_all_dispatch_blood_data_created_by();
							}
							else
							{
								$dispatch_blooddata=$obj_bloodbank->MJ_hmgt_get_all_dispatch_blood_data();
							}
						}
						elseif($obj_hospital->role == 'nurse') 
						{						
							$own_data=$user_access['own_data'];
							if($own_data == '1')
							{
								$dispatch_blooddata=$obj_bloodbank->MJ_hmgt_get_nurse_all_dispatch_blood_data_created_by();
							}
							else
							{
								$dispatch_blooddata=$obj_bloodbank->MJ_hmgt_get_all_dispatch_blood_data();
							}
						}
						elseif($obj_hospital->role == 'patient') 
						{
							$own_data=$user_access['own_data'];
							if($own_data == '1')
							{
								$dispatch_blooddata=$obj_bloodbank->MJ_hmgt_get_petient_dispatch_blood_data();
							}
							else
							{
								$dispatch_blooddata=$obj_bloodbank->MJ_hmgt_get_all_dispatch_blood_data();
							}
						}
						else
						{
							$dispatch_blooddata=$obj_bloodbank->MJ_hmgt_get_all_dispatch_blood_data();
						}
						
						 if(!empty($dispatch_blooddata))
						 {
							foreach($dispatch_blooddata as $retrieved_data)
							{ 
								$patient_data =	MJ_hmgt_get_user_detail_byid($retrieved_data->patient_id);
								?>
								<tr>							
									<td class="patient"><?php echo esc_html($patient_data['first_name']." ".$patient_data['last_name']."(".$patient_data['patient_id'].")");?></td>
									<td class="bloodgroup">
									<?php 							
										echo esc_html__("$retrieved_data->blood_group","hospital_mgt");
									?>
									</td>							
									<td class="subject_name"><?php  echo esc_html($retrieved_data->blood_status);?></td>
								   <td class=""><?php echo date(MJ_hmgt_date_formate(),strtotime($retrieved_data->date)); ?></td>
									<td class=""><?php  echo esc_html($retrieved_data->blood_charge);?></td>
									<td class=""><?php  echo esc_html($retrieved_data->total_tax);?></td>
									<td class=""><?php  echo esc_html($retrieved_data->total_blood_charge);?></td>
									 <?php  
									  if($user_access['edit']=='1' || $user_access['delete']=='1')
									  { ?>	
									<td class="action">
									<?php
									if($user_access['edit']=='1')
									{
									?>
									<a href="?dashboard=user&page=bloodbank&tab=adddispatchblood&action=edit&dispatchblood_id=<?php echo MJ_hmgt_id_encrypt(esc_attr($retrieved_data->dispatchblood_id));?>" class="btn btn-info"> <?php esc_html_e('Edit', 'hospital_mgt' ) ;?></a>
									<?php
									}
									if($user_access['delete']=='1')
									{
									?>
									
									<a href="?dashboard=user&page=bloodbank&tab=dispatchbloodlist&action=delete&dispatchblood_id=<?php echo MJ_hmgt_id_encrypt(esc_attr($retrieved_data->dispatchblood_id));?>" class="btn btn-danger" 
									onclick="return confirm('<?php esc_html_e('Are you sure you want to delete this record?','hospital_mgt');?>');">
									<?php esc_html_e( 'Delete', 'hospital_mgt' ) ;?> </a>
									<?php
									}
									?>
									</td>		
									<?php
									  }
									?>	
								</tr>
							<?php 
							}
						}?>
						</tbody>
					</table><!--END Dispatch BLLOD LIST TABLE-->
				</div><!--END TABLE RESPONSIVE DIV-->
			</div><!--END PANEL BODY DIV-->
	    </form>
    </div><!--END PANEL BODY DIV-->
	<?php
	}
	if($active_tab=='adddispatchblood')
	{
		$obj_bloodbank=new MJ_hmgt_bloodbank();
		?>
		<script type="text/javascript">
		$(document).ready(function()
		{
			"use strict";
			<?php
			if (is_rtl())
				{
				?>	
					$('#dispatch_blood_form').validationEngine({promptPosition : "bottomLeft",maxErrorsPerField: 1});
				<?php
				}
				else{
					?>
					$('#dispatch_blood_form').validationEngine({promptPosition : "bottomRight",maxErrorsPerField: 1});
					<?php
				}
			?>
			$.fn.datepicker.defaults.format =" <?php  echo MJ_hmgt_dateformat_PHP_to_jQueryUI(MJ_hmgt_date_formate()); ?>";
			$('#date').datepicker({
				endDate: '+0d',
				autoclose: true,
			}); 
			$('#tax_charge').multiselect(
			{
				nonSelectedText :'<?php esc_html_e('Select Tax','hospital_mgt');?>',
				includeSelectAllOption: true,
			    selectAllText : '<?php esc_html_e('Select all','hospital_mgt'); ?>',
				templates: {
			            button: '<button class="multiselect btn btn-default dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false"><span class="multiselect-selected-text"></span><b class="caret"></b></button>',
			        },
					buttonContainer: '<div class="dropdown" />'
			});
			$('#patient').select2();
		});
		</script>
		<?php
		$edit=0;
		if(isset($_REQUEST['action']) && $_REQUEST['action'] == 'edit')
		{
			$edit=1;
			$result = $obj_bloodbank->MJ_hmgt_get_single_dispatch_blood_data(MJ_hmgt_id_decrypt($_REQUEST['dispatchblood_id']));	
		}?>
    <div class="panel-body"><!--START PANEL BODY DIV-->
        <form name="dispatch_blood_form" action="" method="post" class="form-horizontal" id="dispatch_blood_form"><!--START Dispatch BLOOD FORM-->
			 <?php $action = isset($_REQUEST['action'])?$_REQUEST['action']:'insert';?>
			<input type="hidden" name="action" value="<?php echo esc_attr($action);?>">
			<input type="hidden" name="old_blood_group" value="<?php if($edit){ echo esc_attr($result->blood_group); } ?>">
			<input type="hidden" name="old_blood_status" value="<?php if($edit){ echo esc_attr($result->blood_status); } ?>">
			<input type="hidden" name="dispatchblood_id" value="<?php if(isset($_REQUEST['dispatchblood_id'])) echo MJ_hmgt_id_decrypt(esc_attr($_REQUEST['dispatchblood_id']));?>"  />		
			
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="patient"><?php esc_html_e('Patient','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<select name="patient_id" id="patient" class="form-control  max_width_100">
							<option value=""><?php esc_html_e('Select Patient','hospital_mgt');?></option>
							<?php 
							if($edit)
							{
								$patient_id1 = $result->patient_id;
							}							
							elseif(isset($_REQUEST['patient_id']))
							{
								$patient_id1 = $_REQUEST['patient_id'];
							}
							else 
							{
								$patient_id1 = "";
							}
							
							$patients = MJ_hmgt_inpatient_list();
							
							if(!empty($patients))
							{
								foreach($patients as $patient)
								{
									echo '<option value="'.$patient['id'].'" '.selected($patient_id1,$patient['id']).'>'.$patient['patient_id'].' - '.$patient['first_name'].' '.$patient['last_name'].'</option>';
								}
							}
							?>
						</select>
					</div>	
				</div>			
			</div>
			<div class="form-group">
				<div class="mb-3 row">	 
					<label class="col-sm-2 control-label form-label" for="bloodgruop"><?php esc_html_e('Blood Group','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<?php if($edit){ $userblood=$result->blood_group; }elseif(isset($_POST['blood_group'])){$userblood=$_POST['blood_group'];}else{$userblood='';}?>
						<select id="blood_group" class="form-control validate[required] max_width_100 selected_blood_group" name="blood_group">
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
					<label class="col-sm-2 control-label form-label" for="blood_status"><?php esc_html_e('Number Of Bags','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="blood_status" class="form-control validate[required] text-input dispatch_blood_status_check" type="number" min="1" onKeyPress="if(this.value.length==1) return false;" value="<?php if($edit){ echo esc_attr($result->blood_status);}elseif(isset($_POST['blood_status'])) echo esc_attr($_POST['blood_status']);?>" name="blood_status">
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for=""><?php esc_html_e('Charge','hospital_mgt');?> (<?php echo MJ_hmgt_get_currency_symbol(get_option( 'hmgt_currency_code' )); ?>)<span class="require-field">*</span></label>
					<div class="col-sm-8">				
						<input id="" class="form-control validate[required]" min="0" type="number" onKeyPress="if(this.value.length==8) return false;"  step="0.01" value="<?php if($edit){ echo esc_attr($result->blood_charge);}elseif(isset($_POST['blood_charge'])) echo esc_attr($_POST['blood_charge']);?>" name="blood_charge">				
					</div>
				</div>
			</div>
			<?php wp_nonce_field( 'save_dispatch_blood_nonce' ); ?>
			<div class="form-group">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="visiting_fees"><?php esc_html_e('Tax','hospital_mgt');?></label>
					<div class="col-sm-2">
						<select  class="form-control" id="tax_charge" name="tax[]" multiple="multiple">					
							<?php					
							if($edit)
							{
								$tax_id=explode(',',$result->tax);
							}
							else
							{	
								$tax_id[]='';
							}
							$obj_invoice= new MJ_hmgt_invoice();
							$hmgt_taxs=$obj_invoice->MJ_hmgt_get_all_tax_data();	
							
							if(!empty($hmgt_taxs))
							{
								foreach($hmgt_taxs as $entry)
								{
									$selected = "";
									if(in_array($entry->tax_id,$tax_id))
										$selected = "selected";
									?>
									<option value="<?php echo esc_attr($entry->tax_id); ?>" <?php echo esc_attr($selected); ?> ><?php echo esc_html($entry->tax_title);?>-<?php echo esc_html($entry->tax_value);?></option>
								<?php 
								}
							}
							?>
						</select>		
					</div>
				</div>
			</div>
			<div class="form-group margin_bottom_5px">
				<div class="mb-3 row">	
					<label class="col-sm-2 control-label form-label" for="last_donet_date"><?php esc_html_e('Date','hospital_mgt');?><span class="require-field">*</span></label>
					<div class="col-sm-8">
						<input id="date" class="form-control validate[required]" type="text"  value="<?php if($edit){ echo date(
				   MJ_hmgt_date_formate(),strtotime($result->date));}elseif(isset($_POST['date'])) echo esc_attr($_POST['date']);?>" name="date" autocomplete="off">
					</div>
				</div>
			</div>			
			<div class="offset-sm-2 col-lg-8 col-md-8 col-sm-8 col-xs-12">
				<input type="submit" value="<?php if($edit){ esc_html_e('Save','hospital_mgt'); }else{ esc_html_e('Save','hospital_mgt');}?>" name="save_dispatch_blood" class="btn btn-success"/>
			</div>
	    </form><!--END Dispatch BLOOD FORM-->
    </div><!--END PANEL BODY DIV-->
	<?php
	}
	?>
    </div><!--END PANEL BODY DIV-->
</div><!--END TAB CONTENT DIV-->