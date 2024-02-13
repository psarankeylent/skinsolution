<?php

namespace TrackingActivityLog\TrackAdminUser\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveCustomerDataObserver implements ObserverInterface
{
  
    protected $customerFactory;
    protected $json;
    protected $adminUserActionsLogsFactory;
    protected $adminSession;
    protected $request;

    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \TrackingActivityLog\TrackAdminUser\Model\AdminUserActionsLogsFactory $adminUserActionsLogsFactory,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\Framework\App\RequestInterface $request
    )
    {
        $this->customerFactory = $customerFactory;
        $this->json = $json;
        $this->adminUserActionsLogsFactory = $adminUserActionsLogsFactory; 
        $this->adminSession = $adminSession;
        $this->request = $request; 
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $post = $this->request->getParams();        
        $customer = $observer->getCustomer();  // you will get customer object
        
        $customerObj = $this->customerFactory->create()->load($customer->getId());

        $origData = [];
        $newData  = [];


        // Old(Orig) Customer Data
        $origData = $customerObj->getData();
        
        // New Customer Data
        $newData  = $customer->getData();
        
        $jsonEncodedOrigData = $this->json->serialize($origData);
        $jsonEncodedData = $this->json->serialize($newData);
       

        $adminUser = $this->adminSession->getUser()->getUsername();
       
        $actionLogsModel = $this->adminUserActionsLogsFactory->create();

        $action = '';
        if( isset($post['back']) &&  $post['back'] == 'new' )
        {
            $action = 'New';
        }
        else if( isset($post['back']) &&  $post['back'] == 'edit' )
        {
            $action = 'Edit';
        }
        else
        {
            $action = 'Save & close';   
        }


        try{

            $actionLogsModel->setData('entity', 'Customer')
                        ->setData('identifier', $customer->getEmail())
                        ->setData('user_name',$adminUser)
                        ->setData('action', $action)
                        ->setData('created_at', date('Y-m-d h:i:s'))
                        ->setData('data_before_save', $jsonEncodedOrigData)
                        ->setData('data_after_save', $jsonEncodedData)
                        ->save();
        }
        catch(\Exception $e)
        {
            // Put Log here or something
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/TrackAdminUser.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("Error ".$e->getMessage());

        }
        


    }  


}