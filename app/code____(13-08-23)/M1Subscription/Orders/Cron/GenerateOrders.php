<?php

namespace M1Subscription\Orders\Cron;
 
use Magento\Framework\App\Config\ScopeConfigInterface;

class GenerateOrders
{
    
    protected $subscriptionProfileFactory;
    protected $resultPageFactory;
    protected $dataHelper;
    protected $customerSubscriptionFactory;

    /**
     * Constructor
     *
     */
    public function __construct(
        \M1Subscription\Orders\Model\SubscriptionProfileFactory  $subscriptionProfileFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \M1Subscription\Orders\Helper\Data $dataHelper,
        \LegacySubscription\Subscriptions\Model\CustomerSubscriptionFactory $customerSubscriptionFactory
    ) {
        $this->subscriptionProfileFactory  = $subscriptionProfileFactory;
        $this->resultPageFactory    = $resultPageFactory;
        $this->dataHelper  = $dataHelper;
        $this->customerSubscriptionFactory  = $customerSubscriptionFactory;
    }

    public function execute()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/create_order_cron.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("============= Execution job ============");

        $date = new \DateTime();
        $toDate     = $date->format('Y-m-d H:i:s');
        $fromDate   = $date->modify("-10 days")->format('Y-m-d H:i:s');


        /*$collection = $this->customerSubscriptionFactory->create()->getCollection();

        $collection->addFieldToFilter('status', 0)
                   ->addFieldToFilter("reference_id", array('neq' => 'NULL'))
                   ->addFieldToFilter('created_at', array('from'=>$fromDate, 'to'=>$toDate))
                   ->setOrder('created_at DESC');*/


        $data = [];
        $newData=[];
        $dataArray = [];

        $collection = $this->subscriptionProfileFactory->create()->getCollection();
       
        $collection->addFieldToFilter("reference_id", array('in' => array('6570429','6570428','6651731')));
                    //->addFieldToFilter("reference_id", array('eq' => '6570428'));
                   //->addFieldToFilter('created_at', array('from' => $fromDate, 'to' => $toDate))
                   //->setOrder('created_at','DESC');

        //echo $collection->getSelect(); exit;
        //echo "<pre>"; print_r($collection->getData());

       // $data = $collection->getData('details');
        //$data = unserialize($data);

        $orderCreated = [];
        foreach ($collection as $value) {
            $details = $value->getData('details');
            $data = unserialize($details);
            
            //echo "<pre>"; print_r($data);exit;
       
                try{

                    if(!empty($data))
                    {
                        $newData['profile_id']      = $value->getData('reference_id');
                        $newData['method_code']     = $data['method_code'];
                        $newData['order_info']      = $data['order_info'];
                        $newData['customer_id']     = $data['order_info']['customer_id'];
                        $newData['customer_email']  = $data['order_info']['customer_email'];

                    
                        // Shipping Address
                        $newData['shipping_address']['firstname']  = $data['shipping_address']['firstname'];
                        $newData['shipping_address']['lastname']   = $data['shipping_address']['lastname'];
                        $newData['shipping_address']['street']     = $data['shipping_address']['street'];
                        $newData['shipping_address']['city']       = $data['shipping_address']['city'];
                        $newData['shipping_address']['region']     = $data['shipping_address']['region'];
                        $newData['shipping_address']['region_id']  = $data['shipping_address']['region_id'];
                        $newData['shipping_address']['country_id'] = $data['shipping_address']['country_id'];
                        $newData['shipping_address']['postcode']   = $data['shipping_address']['postcode'];
                        $newData['shipping_address']['telephone']  = $data['shipping_address']['telephone'];

                        // Billing Address
                        $newData['billing_address']['firstname']  = $data['billing_address']['firstname'];
                        $newData['billing_address']['lastname']   = $data['billing_address']['lastname'];
                        $newData['billing_address']['street']     = $data['billing_address']['street'];
                        $newData['billing_address']['city']       = $data['billing_address']['city'];
                        $newData['billing_address']['region']     = $data['billing_address']['region'];
                        $newData['billing_address']['region_id']  = $data['billing_address']['region_id'];
                        $newData['billing_address']['country_id'] = $data['billing_address']['country_id'];
                        $newData['billing_address']['postcode']   = $data['billing_address']['postcode'];
                        $newData['billing_address']['telephone']  = $data['billing_address']['telephone'];


                        // Items Details
                        $newData['items']['sku'] = $data['order_item_info']['sku'];
                        //$newData['items']['option_id'] = 2;
                        //$newData['items']['option_type_id'] = 5;
                        //$newData['items']['regular_price'] = $data['order_item_info']['sku'];
                        $newData['items']['subtotal'] = $data['order_info']['subtotal'];
                        if(array_key_exists('discount_amount', $data['order_item_info']))
                        {
                            $newData['items']['discount_amount'] = $data['order_item_info']['discount_amount'];
                        }
                        

                        //$dataArray[] = $newData;
                        
                    }
                    else
                    {
                        $newData = [];
                    }  
                    //echo "<pre>"; print_r($newData); exit;

                    // create order from above details.
                    if(empty($newData))
                    {
                        $logger->info("Reference Id not found ". $value->getData('reference_id'));
                        continue;
                    }

                    
                    $result = $this->dataHelper->createCustomOrder($newData);
                    $orderCreated[] = $result;
                    
                }
                catch(\Exception $e)
                {
                    $logger->info('Error-'.$e->getMessage());
                }
        }
        echo "<pre>"; print_r("These orders are created ".json_encode($orderCreated)); exit;

    }

    
}

