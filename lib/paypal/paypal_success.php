<?php
$obj_payment= new MJ_hmgt_invoice();
$p 	= new Hmgt_paypal_class(); // paypal class
	
if(isset($_REQUEST["invoice_id"]))
{
	
	$inv_amount=0;
	$invoiceid=$_REQUEST['invoice_id'];
	$invoice_res =$obj_payment->MJ_hmgt_get_invoice_data($_REQUEST['invoice_id']);	
	$item_name='Invoice';
	$inv_amount=$invoice_res->invoice_amount;
	$user_id  = $invoice_res->patient_id;
}

$user_info = get_userdata($user_id);
$this_script = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
$p->add_field('business', get_option('hospital_paypal_email')); // Call the facilitator eaccount

$p->add_field('cmd', '_cart'); // cmd should be _cart for cart checkout
$p->add_field('upload', '1');
$p->add_field('return', home_url().'/?dashboard=user&page=invoice&action=success'); // return URL after the transaction got over
$p->add_field('cancel_return', home_url().'/?dashboard=user&page=invoice&action=cancel'); // cancel URL if the trasaction was cancelled during half of the transaction
$p->add_field('notify_url', home_url().'/?dashboard=user&page=invoice&action=ipn'); // Notify URL which received IPN (Instant Payment Notification)
$p->add_field('currency_code', get_option( 'hmgt_currency_code' ));
$p->add_field('invoice', date("His").rand(1234, 9632));
$p->add_field('item_name_1','invoice');
$p->add_field('item_number_1', 4);
$p->add_field('quantity_1', 1);
//$p->add_field('amount_1', get_membership_price(get_user_meta($user_id,'membership_id',true)));
$p->add_field('amount_1', $_POST['income_amount'][0]);
//$p->add_field('amount_1', 1);//Test purpose
$p->add_field('first_name',$user_info->first_name);
$p->add_field('last_name', $user_info->last_name);
$p->add_field('address1',$user_info->address);
$p->add_field('city', $user_info->city_name);
$p->add_field('custom', $user_id."_".$invoiceid."_".$inv_amount);
$p->add_field('rm',2);
		
$p->add_field('state', get_user_meta($user_id,'state_name',true));
$p->add_field('country', get_option( 'hmgt_contry' ));
//$p->add_field('zip', get_user_meta($user_id,'zip_code',true));
$p->add_field('email',$user_info->user_email);
/* var_dump($p);
die; */
$p->submit_paypal_post(); // POST it to paypal
//$p->dump_fields(); // Show the posted values for a reference, comment this line before app goes live
//exit;
?>