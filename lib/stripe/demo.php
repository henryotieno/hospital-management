<?php

require_once('Stripe/init.php');
require_once('Stripe/lib/Stripe.php');

\Stripe\Stripe::setApiKey("sk_test_Wvcf60TLd1h1ZfPgG7XgNoUH");

$sub = \Stripe\Subscription::retrieve("sub_9D5ccKri0ZUbSM");
$sub->cancel();
var_dump($sub);
/*
$demo='{"id":"cus_9D72RGyWtvEZaE","object":"customer","account_balance":0,"created":1474111038,"currency":"usd","default_source":"card_18ugomHvCe5aa2o1jRcn5eBg","delinquent":false,"description":null,"discount":null,"email":"jignesh4@example.com","livemode":false,"metadata":[],"shipping":null,"sources":{"object":"list","data":[{"id":"card_18ugomHvCe5aa2o1jRcn5eBg","object":"card","address_city":null,"address_country":null,"address_line1":null,"address_line1_check":null,"address_line2":null,"address_state":null,"address_zip":null,"address_zip_check":null,"brand":"Visa","country":"US","customer":"cus_9D72RGyWtvEZaE","cvc_check":"pass","dynamic_last4":null,"exp_month":5,"exp_year":2017,"fingerprint":"oQy5cvCeTNkfsqsO","funding":"unknown","last4":"1111","metadata":[],"name":"jignesh@dasinfomedia.com","tokenization_method":null}],"has_more":false,"total_count":1,"url":"\/v1\/customers\/cus_9D72RGyWtvEZaE\/sources"},"subscriptions":{"object":"list","data":[{"id":"sub_9D72KKuldmQtN3","object":"subscription","application_fee_percent":null,"cancel_at_period_end":false,"canceled_at":null,"created":1474111038,"current_period_end":1476703038,"current_period_start":1474111038,"customer":"cus_9D72RGyWtvEZaE","discount":null,"ended_at":null,"livemode":false,"metadata":[],"plan":{"id":"2","object":"plan","amount":1900,"created":1474097480,"currency":"usd","interval":"month","interval_count":1,"livemode":false,"metadata":[],"name":"best","statement_descriptor":null,"trial_period_days":null},"quantity":1,"start":1474111038,"status":"active","tax_percent":null,"trial_end":null,"trial_start":null}],"has_more":false,"total_count":1,"url":"\/v1\/customers\/cus_9D72RGyWtvEZaE\/subscriptions"}}';


$demo_array=json_decode($demo);

var_dump((array)$demo_array->subscriptions->data[0]);
*/

?>