<?php

namespace TrackingActivityLog\TrackAdminUser\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveCustomerAddressDataObserver implements ObserverInterface
{
  
    protected $customerFactory;
    protected $customerSession;
    protected $addressFactory;
    protected $json;
    protected $adminUserActionsLogsFactory;
    protected $adminSession;
    protected $request;

    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \TrackingActivityLog\TrackAdminUser\Model\AdminUserActionsLogsFactory $adminUserActionsLogsFactory,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\Framework\App\RequestInterface $request
    )
    {
        $this->customerFactory = $customerFactory;
        $this->customerSession = $customerSession;
        $this->addressFactory = $addressFactory;
        $this->json = $json;
        $this->adminUserActionsLogsFactory = $adminUserActionsLogsFactory; 
        $this->adminSession = $adminSession;
        $this->request = $request; 
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/TrackAdminUser.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $post = $this->request->getParams();
        
        // For Inline Edit of Customer if user has address then it will come here(Address is not editable Inline).
        if(isset($post['items']))
        {
            $logger->info("Customer Inline Edited Data ".json_encode($post['items']));
            return;
        }
        
        $customerAddress = $observer->getCustomerAddress();  // you will get customer address object
        //echo "<pre>"; print_r($customerAddress->getData()); exit;
        $address = $this->addressFactory->create()->load($customerAddress->getId());

        $customerId = $customerAddress->getData('parent_id');

        $customer = $this->customerFactory->create()->load($customerId);

        $origData = [];
        $newData  = [];

        //$origData = $address->getOrigData();
        $origData  = $address->getData();
        $newData   = $post;

        $defaultBilling = "1";       
        $defaultShipping = "1";
        if($customer->getDefaultBilling() == null)
        {
            $defaultBilling = "0";
        }
        if($customer->getDefaultShipping() == null)
        {
            $defaultShipping = "0";
        }
      

        $origData['default_billing'] = $defaultBilling;
        $origData['default_shipping'] = $defaultShipping;
      
        $jsonEncodedOrigData = $this->json->serialize($origData);
        $jsonEncodedData = $this->json->serialize($newData);
       

        $adminUser = $this->adminSession->getUser()->getUsername();
       
        $actionLogsModel = $this->adminUserActionsLogsFactory->create();

        $action = '';
        if( isset($post['entity_id']) &&  $post['entity_id'] == '' )
        {
            $action = 'new';
        }
        else if( isset($post['entity_id']) &&  $post['entity_id'] != '' )
        {
            $action = 'edit';
        }

        try{

            $setDefaultBillAddress = $this->customerSession->getCustomerCustomDefaultBillAddress();
            $setDefaultShipAddress = $this->customerSession->getCustomerCustomDefaultShipAddress();

            if($setDefaultBillAddress)
            {
                $origData['default_billing'] = $defaultBilling;
                $newData['default_billing'] = 1;

                $jsonEncodedOrigData = $this->json->serialize($origData);
                $jsonEncodedData = $this->json->serialize($newData);
                
                $actionLogsModel->setData('entity', 'Default Billing Address')
                        ->setData('identifier', $customerAddress->getId())
                        ->setData('user_name',$adminUser)
                        ->setData('action', 'Default Billing Set')
                        ->setData('created_at', date('Y-m-d h:i:s'))
                        ->setData('data_before_save', $jsonEncodedOrigData)
                        ->setData('data_after_save', $jsonEncodedData)
                        ->save();
                $logger->info("Set Default Billing");
            }
            else if($setDefaultShipAddress)
            {

                $origData['default_shipping'] = $defaultShipping;
                $newData['default_shipping'] = 1;

                $jsonEncodedOrigData = $this->json->serialize($origData);
                $jsonEncodedData = $this->json->serialize($newData);

                $actionLogsModel->setData('entity', 'Default Shipping Address')
                        ->setData('identifier', $customerAddress->getId())
                        ->setData('user_name',$adminUser)
                        ->setData('action', 'Default Shipping Set')
                        ->setData('created_at', date('Y-m-d h:i:s'))
                        ->setData('data_before_save', $jsonEncodedOrigData)
                        ->setData('data_after_save', $jsonEncodedData)
                        ->save();

                $logger->info("Set Default Shipping");
            }
            else
            {
                $actionLogsModel->setData('entity', 'Customer Address')
                        ->setData('identifier', $customer->getEmail())
                        ->setData('user_name',$adminUser)
                        ->setData('action', $action)
                        ->setData('created_at', date('Y-m-d h:i:s'))
                        ->setData('data_before_save', $jsonEncodedOrigData)
                        ->setData('data_after_save', $jsonEncodedData)
                        ->save();
            }


            // Unset Sessions
            $this->customerSession->unsCustomerCustomDefaultBillAddress();
            $this->customerSession->unsCustomerCustomDefaultShipAddress();

        }
        catch(\Exception $e)
        {
            // Put Log here or something
            //echo $e->getMessage();
            $logger->info("Error ".$e->getMessage());

        }


    }  


}