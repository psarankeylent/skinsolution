

<a href="export.csv">Open</a>

<?php
exit;

ini_set('display_errors', 1);
ini_set('memory_limit', '5G');
error_reporting(E_ALL);

use Magento\Framework\App\Bootstrap;
require 'app/bootstrap.php';
//echo "call"; exit;
$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$state = $objectManager->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');


// QA
$incrementIds = ['QA101360361','QA101360362','QA101360366','QA101360371','QA101360372'];      // array of increment ids.
// local
//$incrementIds = ['QA100359458','QA101360362','QA100359457','QA100359455','QA100359453'];      // array of increment ids.

$data = [
            ['increment_id' => 'QA101360261','ship_date' => '2023-12-18 02:44:01', 'shipment_cost' => '55.88', 'shipped_from' => 'Warehouse name'],
            ['increment_id' => 'QA101360186','ship_date' => '2023-12-11 12:54:01', 'shipment_cost' => '44.04', 'shipped_from' => 'Warehouse name'],
            ['increment_id' => 'QA100359254','ship_date' => '2023-12-11 11:54:04', 'shipment_cost' => '10.30', 'shipped_from' => 'Warehouse name'],
            ['increment_id' => 'QA100359073','ship_date' => '2023-11-01 10:04:01', 'shipment_cost' => '99.03', 'shipped_from' => 'Warehouse name'],
            ['increment_id' => 'QA100359069','ship_date' => '2023-11-08 11:54:00', 'shipment_cost' => '100.00', 'shipped_from' => 'Warehouse name']   
        ];


if(!empty($data))
{
    $resourceConnection = $objectManager->get('Magento\Framework\App\ResourceConnection');
    $connection = $resourceConnection->getConnection();
    $tableName = $connection->getTableName('order_shipment');

    $connection->insertMultiple($tableName, $data);

    //echo $collection->getSelect(); exit;
   
    echo "Data inserted successfully.";
}


$sql = "INSERT INTO `order_shipment`
            (`increment_id`, `ship_date`,`shipment_cost`,`shipped_from`) 
        VALUES
            ('QA101360261','2023-12-18 02:42:01','55.88','Warehouse name'),
            ('QA101360186','2023-11-28 02:54:01','11.05','Warehouse name'),
            ('QA100359254','2023-11-13 03:09:01','33.90','Warehouse name'),
            ('QA100359073','2023-10-12 02:11:01','99.03','Warehouse name'),
            ('QA100359069','2023-11-10 04:42:01','100.00','Warehouse name')
            ";


        
        




