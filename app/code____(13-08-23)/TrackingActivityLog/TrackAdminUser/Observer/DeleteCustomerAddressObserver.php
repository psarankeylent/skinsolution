<?php

namespace TrackingActivityLog\TrackAdminUser\Observer;

use Magento\Framework\Event\ObserverInterface;

class DeleteCustomerAddressObserver implements ObserverInterface
{

    protected $customerFactory;
    protected $addressFactory;
    protected $json;
    protected $adminUserActionsLogsFactory;
    protected $adminSession;
    protected $request;

    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \TrackingActivityLog\TrackAdminUser\Model\AdminUserActionsLogsFactory $adminUserActionsLogsFactory,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\Framework\App\RequestInterface $request
    )
    {
        $this->customerFactory = $customerFactory;
        $this->addressFactory = $addressFactory;
        $this->json = $json;
        $this->adminUserActionsLogsFactory = $adminUserActionsLogsFactory; 
        $this->adminSession = $adminSession;
        $this->request = $request; 
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {

        $post = $this->request->getParams();        
        //echo "<pre>"; print_r($post); exit;
        
        $customerAddress = $observer->getCustomerAddress();  // you will get customer address object
        $address = $this->addressFactory->create()->load($customerAddress->getId());

        $customer = $this->customerFactory->create()->load($post['parent_id']);

        $data = $address->getData();
       // echo "<pre>"; print_r($data); exit;

        $jsonEncodedData = $this->json->serialize($data);

        $adminUser = $this->adminSession->getUser()->getUsername();
        $adminEmail = $this->adminSession->getUser()->getEmail();

        $actionLogsModel = $this->adminUserActionsLogsFactory->create();

       
        try{

            $actionLogsModel->setData('entity', 'Customer Address')
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