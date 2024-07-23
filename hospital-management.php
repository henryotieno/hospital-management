<?php
/*
Plugin Name: Hospital Management
Plugin URI: https://mojoomla.com/
Description: Hospital Management System for wordpress plugin is ideal way to manage complete hospital operation. It has different user roles like doctor, patient, nurse, pharmacist , accountant, laboratory staff and Support staff.
Version: 45.0(21-03-2023)
Author: Mojoomla
Author URI: https://mojoomla.com/
Text Domain: hospital_mgt
Domain Path: /languages/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Copyright 2015  Mojoomla  (email : sales@mojoomla.com)
*/
define( 'HMS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
define( 'HMS_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );
define( 'HMS_PLUGIN_URL', untrailingslashit( plugins_url( '', __FILE__ ) ) );
define( 'HMS_CONTENT_URL',  content_url( ));
define( 'HMS_LOG_DIR',  WP_CONTENT_DIR.'/uploads/hospital_logs/');
define( 'HMS_LOG_file', HMS_LOG_DIR.'/hmgt_log.txt' );
require_once HMS_PLUGIN_DIR . '/settings.php';
if (isset($_REQUEST['page']))
{
	if($_REQUEST['page'] == 'callback')
	{
	   require_once HMS_PLUGIN_DIR. '/callback.php';
	}
}
?>