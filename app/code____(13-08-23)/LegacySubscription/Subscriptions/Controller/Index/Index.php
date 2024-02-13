<?php

namespace LegacySubscription\Subscriptions\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;


class Index extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;
    protected $_messageManager;
    protected $dataHelper;
    protected $customerSubscriptionFactory;
    protected $region;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \LegacySubscription\Subscriptions\Helper\Data $dataHelper,
        \LegacySubscription\Subscriptions\Model\CustomerSubscriptionFactory $customerSubscriptionFactory,
        \Magento\Directory\Model\Region $region
    )
    {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->_messageManager = $messageManager;
        $this->dataHelper = $dataHelper;
        $this->customerSubscriptionFactory = $customerSubscriptionFactory;
        $this->region = $region;    
    }

    public function execute()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/ssmd_create_order_report.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $collection = $this->customerSubscriptionFactory->create()->getCollection();
        $collection->addFieldToFilter('sku', ['neq' => 'NULL']);
        $collection->addFieldToFilter('customer_id', ['neq' => 'NULL']);

        //echo "<pre>"; print_r($collection->getData()); exit;

        try{
            $data = [];
            $dataArray = [];

            if(!empty($collection->getData()))
            {                
                foreach ($collection as $value) {

                    $data['legacy_id']  = $value->getData('legacy_id');
                    $data['profile_id']  = $value->getData('profile_id');
                    $data['customer_id'] = $value->getData('customer_id');

                    // Getting Region_id from name
                    $shippingRegionId = $this->getRegionId($value->getData('ship_state'), $value->getData('ship_country'));
                    $billingRegionId  = $this->getRegionId($value->getData('bill_state'), $value->getData('bill_country'));

                    // Shipping Address
                    $data['shipping_address']['firstname'] = $value->getData('ship_first_name');
                    $data['shipping_address']['lastname'] = $value->getData('ship_last_name');
                    $data['shipping_address']['street'] = $value->getData('ship_address');
                    $data['shipping_address']['city'] = $value->getData('ship_city');
                    $data['shipping_address']['region'] = $value->getData('ship_state');
                    $data['shipping_address']['region_id'] = $shippingRegionId;
                    $data['shipping_address']['country_id'] = $value->getData('ship_country');
                    $data['shipping_address']['postcode'] = $value->getData('ship_zip');
                    $data['shipping_address']['telephone'] = $value->getData('ship_telephone');

                    // Billing Address
                    $data['billing_address']['firstname'] = $value->getData('bill_first_name');
                    $data['billing_address']['lastname'] = $value->getData('bill_last_name');
                    $data['billing_address']['street'] = $value->getData('bill_address');
                    $data['billing_address']['city'] = $value->getData('bill_city');
                    $data['billing_address']['region'] = $value->getData('bill_state');
                    $data['billing_address']['region_id'] = $billingRegionId;
                    $data['billing_address']['country_id'] = $value->getData('bill_country');
                    $data['billing_address']['postcode'] = $value->getData('bill_zip');
                    $data['billing_address']['telephone'] = $value->getData('bill_telephone');


                    // Items Details
                    $data['items']['sku'] = $value->getData('sku');
                    $data['items']['option_id'] = 2;
                    $data['items']['option_type_id'] = 5;
                    $data['items']['regular_price'] = $value->getData('regular_price');
                    $data['items']['subtotal'] = $value->getData('amount');
                    $data['items']['discount_amount'] = $value->getData('discount_amount');

                    $dataArray[] = $data;
                }
            }
            else
            {
                $dataArray = [];
            }  
            //echo "<pre>"; print_r($dataArray); exit;

            // create order from above details.
            foreach ($dataArray as $order) {
                $result = $this->dataHelper->createCustomOrder($order);
            }
                    
        }
        catch(\Exception $e)
        {
            $logger->info('Error-'.$e->getMessage());
        }  

    }
    
    public function getRegionId($stateName, $countryId)
    {
        return $this->region->loadByName($stateName, $countryId)->getRegionId();
    }            
}

