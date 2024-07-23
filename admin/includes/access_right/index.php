<?php
$active_tab = isset($_GET['tab'])?$_GET['tab']:'Doctor';
?>
<!-- View Popup Code start -->	
<div class="popup-bg">
    <div class="overlay-content">
    	<div class="notice_content"></div>    
    </div> 
</div>	
<!-- View Popup Code end -->
<!-- page inner div start-->
<div class="page-inner min_height_1631">
	<!-- Page Title div start -->
	<div class="page-title">
		<h3><img src="<?php echo esc_url(get_option( 'hmgt_hospital_logo', 'hospital_mgt')); ?>" class="img-circle head_logo" width="40" height="40" /><?php echo esc_html(get_option('hmgt_hospital_name','hospital_mgt'));?></h3>
	</div>	
	<!-- Page Title div end -->
	<!--  main-wrapper div start  -->
	<div  id="main-wrapper" class="notice_page">
		<!-- panel-white div start  -->
		<div class="panel panel-white">
			<!-- panel-body div start  -->
			<div class="panel-body">
				<h2 class="nav-tab-wrapper">
					<a href="?page=hmgt_access_right&tab=Doctor" class="nav-tab <?php echo $active_tab == 'Doctor' ? 'nav-tab-active' : ''; ?>">
					<?php echo '<span class="dashicons dashicons-menu"></span>'.esc_html__('Doctor', 'hospital_mgt'); ?></a>

					<a href="?page=hmgt_access_right&tab=Patient" class="nav-tab <?php echo $active_tab == 'Patient' ? 'nav-tab-active' : ''; ?>">
					<?php echo '<span class="dashicons dashicons-menu"></span>'.esc_html__('Patient', 'hospital_mgt'); ?></a> 
			 
				 <a href="?page=hmgt_access_right&tab=Nurse" class="nav-tab <?php echo $active_tab == 'Nurse' ? 'nav-tab-active' : ''; ?>">
					<?php echo '<span class="dashicons dashicons-menu"></span>'.esc_html__('Nurse', 'hospital_mgt'); ?></a> 
			  
				<a href="?page=hmgt_access_right&tab=Laboratorist" class="nav-tab <?php echo $active_tab == 'Laboratorist' ? 'nav-tab-active' : ''; ?>">
					<?php echo '<span class="dashicons dashicons-menu"></span>'.esc_html__('Laboratorist', 'hospital_mgt'); ?></a> 

				 <a href="?page=hmgt_access_right&tab=Pharmacist" class="nav-tab <?php echo $active_tab == 'Pharmacist' ? 'nav-tab-active' : ''; ?>">
					<?php echo '<span class="dashicons dashicons-menu"></span>'.esc_html__('Pharmacist', 'hospital_mgt'); ?></a>
			 
				<a href="?page=hmgt_access_right&tab=Accountant" class="nav-tab <?php echo $active_tab == 'Accountant' ? 'nav-tab-active' : ''; ?>">
					<?php echo '<span class="dashicons dashicons-menu"></span>'.esc_html__('Accountant', 'hospital_mgt'); ?></a> 

				 <a href="?page=hmgt_access_right&tab=SupportStaff" class="nav-tab <?php echo $active_tab == 'SupportStaff' ? 'nav-tab-active' : ''; ?>">
					<?php echo '<span class="dashicons dashicons-menu"></span>'.esc_html__('Support Staff', 'hospital_mgt'); ?></a> 
				
				<a href="?page=hmgt_access_right&tab=management" class="nav-tab <?php echo $active_tab == 'management' ? 'nav-tab-active' : ''; ?>">
					<?php echo '<span class="dashicons dashicons-menu"></span>'.esc_html__('Management', 'hospital_mgt'); ?></a> 
				
				</h2>
				<?php
				if($active_tab == 'Doctor')
				{
					require_once HMS_PLUGIN_DIR. '/admin/includes/access_right/doctor.php';
				}
				elseif($active_tab == 'Patient')
				{
					require_once HMS_PLUGIN_DIR. '/admin/includes/access_right/Patient.php';
				}
				elseif($active_tab == 'Nurse')
				{
					require_once HMS_PLUGIN_DIR. '/admin/includes/access_right/nurse.php';
				}
				elseif($active_tab == 'Laboratorist')
				{
					require_once HMS_PLUGIN_DIR. '/admin/includes/access_right/laboratorist.php';
				}
				elseif($active_tab == 'Pharmacist')
				{
					require_once HMS_PLUGIN_DIR. '/admin/includes/access_right/pharmacist.php';
				}
				elseif($active_tab == 'Accountant')
				{
					require_once HMS_PLUGIN_DIR. '/admin/includes/access_right/accountant.php';
				}
				elseif ($active_tab == 'SupportStaff')
				{
					require_once HMS_PLUGIN_DIR. '/admin/includes/access_right/supportstaff.php';
				}
				elseif ($active_tab == 'management')
				{
					require_once HMS_PLUGIN_DIR. '/admin/includes/access_right/management.php';
				}
				?>
			</div>
			<!-- panel-body div end -->
	 	</div>
		<!-- panel-white div end -->
	</div>
	<!--  main-wrapper div end -->
</div>
<!-- page inner div end -->