<?php

namespace TrackingActivityLog\TrackAdminUser\Observer;

use Magento\Framework\Event\ObserverInterface;

class DeleteCustomerObserver implements ObserverInterface
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
        //echo "<pre>"; print_r($post); exit;
        
        $customer = $observer->getCustomer();  // you will get customer object

        $data = $customer->getData();

        $jsonEncodedData = $this->json->serialize($data);

        $adminUser = $this->adminSession->getUser()->getUsername();
        $adminEmail = $this->adminSession->getUser()->getEmail();

        $actionLogsModel = $this->adminUserActionsLogsFactory->create();

       
        try{

            $actionLogsModel->setData('entity', 'Customer')
                        ->setData('identifier', $customer->getEmail())
                        ->setData('user_name',$adminUser)
                        ->setData('action', 'Delete')
                        ->setData('created_at', date('Y-m-d h:i:s'))
                        ->setData('data_before_save', '')
                        ->setData('data_after_save', $jsonEncodedData)
                        ->save();
        }
        catch(\Exception $e)
        {
            // Put Log here or something
            //echo $e->getMessage();
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/TrackAdminUser.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("Error ".$e->getMessage());

        }
        


    }  


}