<?php
//session_start();
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if(get_option('smgt_paymaster_pack')=="yes" && is_plugin_active('school-management/school-management.php'))
{
	$amount = $_SESSION['amount']=$_REQUEST['amount'];
	$pay_id = $_SESSION['fees_pay_id'] = $_REQUEST['fees_pay_id'];
	$system_name =  get_option('smgt_school_name');
	$system_logo =  get_option('smgt_school_logo');
	$data_user =get_current_user_id();
	$description = "Fee Payment";
	$currency =  get_option('smgt_currency_code');
}
if(get_option('gmgt_paymaster_pack')=="yes" && is_plugin_active('gym-management/gym-management.php'))
{
	$currency =  get_option('gmgt_currency_code');
	$fornt_end = "";
	if(isset($_POST['where_payment']))
	{
		$fornt_end = $_POST['where_payment'];
	}
	$amount = $_SESSION['amount']=$_REQUEST['amount'];	
	$pay_id = $_SESSION['mp_id'] = $_REQUEST['mp_id'];	
	$system_name =  get_option('gmgt_system_name');	
	$system_logo =  get_option('gmgt_system_logo');
	$data_user =$_REQUEST['member_id']; 
	if(isset($_POST['view_type']))
	{
		if($_POST['view_type'] == 'sale_payment')
		{
			$description = "Sales Payment";
			$payment_type = "Sales_Payment";
		}
		elseif($_POST['view_type'] == 'income')
		{
			$description = "Income Payment";
			$payment_type = "Income_Payment";
		}
	}
	else
	{
		$description = "Membership Payment";
		$payment_type = "Membership_Payment";
	}
	
	if(isset($_REQUEST['action'])&& $_REQUEST['action']=='frontend_book')
	{
		$frontend_class_action=$_REQUEST['action'];
		$class_id1=$_REQUEST['class_id1'];
		$day_id1=$_REQUEST['day_id1'];
		$startTime_1=$_REQUEST['startTime_1'];
		$class_date=$_REQUEST['class_date'];
		$Remaining_Member_limit_1=$_REQUEST['Remaining_Member_limit_1'];
		$bookedclass_membershipid=$_REQUEST['bookedclass_membershipid'];
	}
	
	
}
if(get_option('cmgt_paymaster_pack')=="yes" && is_plugin_active('church-management/church-management.php'))
{
	$fornt_end = "";
	if(isset($_POST['where_payment']))
	{
		$fornt_end = $_POST['where_payment'];
	}
	$amount = $_SESSION['amount']=$_REQUEST['amount'];	
	$pay_id = $_SESSION['mp_id'] = $_REQUEST['member_id'];	
	$donetion_type = $_SESSION['donetion_type'] = $_REQUEST['donetion_type'];	
	$system_name =  get_option('cmgt_system_name');	
	$system_logo =  get_option('cmgt_system_logo');
	$data_user =get_current_user_id(); 
	$description = " Donation Payment"; 
	$currency =  get_option('cmgt_currency_code');
}
if(get_option('amgt_paymaster_pack')=="yes" && is_plugin_active('apartment-management/apartment-management.php'))
{
	$fornt_end = "";
	if(isset($_POST['where_payment']))
	{
		$fornt_end = $_POST['where_payment'];
	}
	$amount = $_SESSION['amount']=$_REQUEST['amount'];	
	$pay_id =$_REQUEST['invoice_id'];
	$system_name =  get_option('amgt_system_name');	
	$system_logo =  get_option('amgt_system_logo');
	$data_user =get_current_user_id(); 
	$description = "Invoice Payment"; 
	$currency =  get_option('apartment_currency_code');
}
if(get_option('hmgt_paymaster_pack')=="yes" && is_plugin_active('hospital-management/hospital-management.php'))
{
	
	$fornt_end = "";
	if(isset($_POST['where_payment']))
	{
		$fornt_end = $_POST['where_payment'];
	}
	$amount = $_SESSION['amount']=$_POST['income_amount'][0];	
	$pay_id =$_REQUEST['invoice_id'];
	$payment_type =$_POST['income_entry'][0];
	$system_name =  get_option('hmgt_hospital_name');	
	$system_logo =  get_option('hmgt_hospital_logo');
	$data_user =get_current_user_id(); 
	$description = "Invoice Payment"; 
	$currency =  get_option('hmgt_currency_code');
	
}
$secret_key =get_option('secret_key');

$publisable_key =get_option('publishable_key');
?>
<form action="<?php print PM_PLUGIN_URL.'/lib/stripe/charge.php' ?>" method="POST">
<input type="hidden" name="amount" value="<?php print $amount ?>">
<input type="hidden" name="donetion_type" value="<?php print $donetion_type ?>">
<input type="hidden" name="pay_id" value="<?php print $pay_id ?>">
<input type="hidden" name="where_payment" value="<?php print isset($fornt_end)?$fornt_end:'' ?>">
<input type="hidden" name="secret_key" value="<?php print $secret_key ?>">
<input type="hidden" name="payment_type" value="<?php print $payment_type ?>">
<input type="hidden" name="data_user" value="<?php print $data_user ?>">
<?php
if(isset($_REQUEST['action'])&& $_REQUEST['action']=='frontend_book')
{
?>
	<input type="hidden" name="frontend_class_action" value="<?php print $frontend_class_action ?>">
	<input type="hidden" name="class_id1" value="<?php print $class_id1 ?>">
	<input type="hidden" name="day_id1" value="<?php print $day_id1 ?>">
	<input type="hidden" name="startTime_1" value="<?php print $startTime_1 ?>">
	<input type="hidden" name="class_date" value="<?php print $class_date ?>">
	<input type="hidden" name="Remaining_Member_limit_1" value="<?php print $Remaining_Member_limit_1 ?>">
	<input type="hidden" name="bookedclass_membershipid" value="<?php print $bookedclass_membershipid ?>">
<?php
}
?>
<script src="//code.jquery.com/jquery-2.0.2.min.js"></script>
  <script
    src="https://checkout.stripe.com/checkout.js" class="stripe-button"
    data-key="<?php print $publisable_key; ?>" // your publishable keys
    data-image="<?php print $system_logo ?>" // your company Logo
    data-name="<?php print $system_name ?>"
    data-description="<?php print $description ?>"
	data-currency="<?php print $currency ?>"
	data-locale="auto"
    data-amount="<?php print $amount*100; ?>" 
	data-user="<?php print $data_user; ?>">

  </script>
<script>
  $(function(){
$(".stripe-button-el").css("display","none");

 $(".stripe-button-el").get(0).click();
 });
</script>
</form>