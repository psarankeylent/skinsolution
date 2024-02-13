<?php
ini_set('display_errors', 1);
ini_set('memory_limit', '5G');
error_reporting(E_ALL);

use Magento\Framework\App\Bootstrap;
require 'app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');

$serializer = $objectManager->get('Magento\Framework\Serialize\Serializer\Json');
$orderObject = $objectManager->get('Magento\Sales\Model\OrderFactory');

?>

<?php

// Data Migration of ParadoxLabs Subscription Module.

// ============================= Databases ===========================================
$db1 = "ssmd_m1";
$db2 = "ssmd_m2";
// ============================ Database connection  check ===========================

// First DB Connection Check
$mysqli1 = new mysqli("localhost", "root", "hayan@123", $db1);
// Check connection
if ($mysqli1 -> connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli1 -> connect_error;
  exit();
}

// Second DB Connection Check  
$mysqli2 = new mysqli("localhost", "root", "hayan@123", $db2);

if ($mysqli2->connect_errno) {
  echo "Failed to connect to MySQL: " . $mysqli2->connect_error;
  exit();
}


// ================================= Tables ======================================

$aw_sarp2_profile_m1 					= 'mg_aw_sarp2_profile';
$sales_flat_order_m1   	 			= 'mg_sales_flat_order';
$sales_flat_order_payment_m1 	= 'mg_sales_flat_order_payment';		// sales_order_payment table.



// ================================ Ticket/Messages/Grid Data Migration ============================
// NOte We have already migrated profile table and stored data into 'subscription_profile'

$queryProfile = "SELECT * FROM $db1.$aw_sarp2_profile_m1";
$resultProfile = $mysqli1->query($queryProfile);

// Array
$data = [];
$profileData = [];

while ($rowProfile = $resultProfile->fetch_array(MYSQLI_ASSOC))
{
		$profileId = $rowProfile['reference_id'];
		$status 	 = $rowProfile['status'];
		$amount 	 = $rowProfile['amount'];
		$created_at 	 = $rowProfile['created_at'];
		$updated_at 	 = $rowProfile['updated_at'];
		$last_use 	 = $rowProfile['updated_at'];


	 	//$profileData[] = $rowProfile;
	 //	$details = $serializer->unserialize($rowProfile['details']);
	 	//$details = unserialize($rowProfile['details']);

	 	/*echo "<pre>"; 
	 //	print_r($rowProfile['details']);
	 	print_r($details);
	 	exit;

		$customerId    = $details['customer']['entity_id'];
		$customerEmail = $details['customer']['email'];
		$customerIp    = $details['order_info']['remote_ip'];
		$method 			 = $details['method_code'];*/


		//$orderId    = $details['order_info']['entity_id'];
		$orderId = 441200;
		$queryPayment = "SELECT * FROM $db1.$sales_flat_order_payment_m1 WHERE `parent_id` = ".$orderId."";
		$resultPayment = $mysqli1->query($queryPayment);

		while ($rowPayment = $resultPayment->fetch_array(MYSQLI_ASSOC))
		{
				$paymentId = $rowPayment['entity_id'];

				$active = 1;			// Card is active.
				$cc_number = $rowPayment['cc_last4'];
				$cc_type   = $rowPayment['cc_type'];
				$cc_month  = $rowPayment['cc_exp_month'];
				$cc_year   = $rowPayment['cc_ss_start_year'];
	  }
}

echo "<pre>"; print_r($paymentId); exit;
