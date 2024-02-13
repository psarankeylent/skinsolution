<?php

namespace CustomOrders\GenerateOrders\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;


class Index extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $_messageManager;
    protected $dataHelper;
    protected $generateOrderFactory;
    protected $connectionResource;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \CustomOrders\GenerateOrders\Helper\Data $dataHelper,
        \CustomOrders\GenerateOrders\Model\GenerateOrderFactory $generateOrderFactory,
        \Magento\Framework\App\ResourceConnection $connectionResource
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_messageManager = $messageManager;
        $this->dataHelper = $dataHelper;
        $this->generateOrderFactory = $generateOrderFactory;
        $this->connectionResource = $connectionResource;     
    }

    public function execute()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/custom_orders_report.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $connection = $this->connectionResource->getConnection();

        try{

            $sqlQry = "SELECT count(id) as `no_of_skus`,
                        `reference_increment_id`,
                        group_concat(id) `id_list`,
                        `sku`,`sku_option_id`,`sku_option_type_id`
                    FROM `custom_orders`
                    WHERE `new_increment_id` is null
                    OR `new_increment_id` = ''
                    GROUP BY `reference_increment_id`";

            $orders = $connection->fetchAll($sqlQry);
           // echo strlen($orders[0]['id_list']);
            //echo "<pre>"; print_r($orders); exit;
            if(count($orders) == 0)
            {
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/custom_orders_report.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info('Response : There is no order found to create from!');
                echo "There is no order found to create from!";
                exit;
            } 

            //echo $orders->getSelect(); exit;            
            $orders_created = [];
            
            foreach ($orders as $customOrderArray) {
               
                if($customOrderArray['no_of_skus'] > 1)
                {
                    $idsArray = explode(',', $customOrderArray['id_list']);
                    foreach ($idsArray as $id) {

                        $customOrder = $this->generateOrderFactory->create();
                        $customOrder->load($id);
                        $orderDetailsArr = $customOrder->getData();
                       // echo "<pre>"; print_r($orderDetailsArr); exit;
                        $result = $this->dataHelper->createCustomOrder($orderDetailsArr);
                        $orderCreated[] = $result;
                    }
                    
                }
                else
                {
                    $orderDetailsArr = $customOrderArray;
                    $customOrder = $this->generateOrderFactory->create();
                    $customOrder->load($customOrderArray['id_list']);

                    $orderDetailsArr = $customOrder->getData();
                    //echo "<pre>"; print_r($orderDetailsArr); exit;

                    $result = $this->dataHelper->createCustomOrder($orderDetailsArr);
                    $orderCreated[] = $result;
                }
                
            }
            echo "<pre>"; print_r("These orders are created ".json_encode($orderCreated)); exit;
        }
        catch(\Exception $e)
        {
            $logger->info('Error-'.$e->getMessage());
        }  

    }
            
}

