<?php 

require 'gocardless/vendor/autoload.php';
$token = "Pass Token Here";      //webhook secret key
$webhook = file_get_contents('php://input');
$webhook_array = json_decode($webhook, true);

$client = new \GoCardlessPro\Client(array(
		  'access_token' => '',     //sandbox secret key
		  'environment'  => \GoCardlessPro\Environment::SANDBOX
		));

/* $headers = getallheaders();
$provided_signature = $headers["Webhook-Signature"];
$calculated_signature = hash_hmac("sha256", $webhook, $token);
 */
//if ($provided_signature == $calculated_signature) { 
	foreach ($webhook_array['events'] as $event) {
			//$organisation_id = $event["links"]["organisation"];
       if($event['resource_type']=='payments' && $event['action']=='confirmed'){
		    $payment_id = $event['links']['payment'];         //payment id
			$payments = $client->payments()->get($payment_id);
			$mandate_id  = $payments->links->mandate;             //mendate ID
			$mandate = $client->mandates()->get($mandate_id);
			$customer_id = $mandate->links->customer;           //customer ID
			
			$log = fopen('webhooks.txt', 'a');
			$current_date = 'Payment confirmed: '.date("Y-m-d h:i:s");
			fwrite($log, print_r($current_date , TRUE) . "\n\n");
			fwrite($log, print_r($webhook, TRUE) . "\n\n");
			fclose($log);

				   
	   }
	     if($event['resource_type']=='payments' && $event['action']=='failed'){
		    $payment_id = $event['links']['payment'];         //payment id
			$payments = $client->payments()->get($payment_id);
			$mandate_id  = $payments->links->mandate;             //mendate ID
			$mandate = $client->mandates()->get($mandate_id);
			$customer_id = $mandate->links->customer;           //customer ID
			
			$log = fopen('webhooks.txt', 'a');
			$current_date = 'Payment failed: '.date("Y-m-d h:i:s");
			fwrite($log, print_r($current_date , TRUE) . "\n\n");
			fwrite($log, print_r($webhook, TRUE) . "\n\n");
			fclose($log);
   
	   }
	   if($event['resource_type']=='payments' && $event['action']=='cancelled'){
		    $payment_id = $event['links']['payment'];         //payment id
			$payments = $client->payments()->get($payment_id);
			$mandate_id  = $payments->links->mandate;             //mendate ID
			$mandate = $client->mandates()->get($mandate_id);
			$customer_id = $mandate->links->customer;           //customer ID
			
			$log = fopen('webhooks.txt', 'a');
			$current_date = 'Payment cancelled: '.date("Y-m-d h:i:s");
			fwrite($log, print_r($current_date , TRUE) . "\n\n");
			fwrite($log, print_r($webhook, TRUE) . "\n\n");
			fclose($log);
	   }
	}
//}


