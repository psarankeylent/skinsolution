<?php

namespace TrackingActivityLog\TrackAdminUser\Observer;

use Magento\Framework\Event\ObserverInterface;

class DeleteCartPriceRuleDataObserver implements ObserverInterface
{
    protected $rulesFactory;
    protected $json;
    protected $adminUserActionsLogsFactory;
    protected $adminSession;
    protected $request;

    public function __construct(        
        \Magento\SalesRule\Model\RuleFactory $rulesFactory,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \TrackingActivityLog\TrackAdminUser\Model\AdminUserActionsLogsFactory $adminUserActionsLogsFactory,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\Framework\App\RequestInterface $request
    )
    {
        $this->rulesFactory = $rulesFactory;  
        $this->json = $json;
        $this->adminUserActionsLogsFactory = $adminUserActionsLogsFactory; 
        $this->adminSession = $adminSession;
        $this->request = $request; 
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $post = $this->request->getParams();
        //echo "<pre>"; print_r($post); exit;
    
        $ruleId = $observer->getRule()->getId();
        $rule = $this->rulesFactory->create()->load($ruleId);
    
        $adminUser = $this->adminSession->getUser()->getUsername();
        $actionLogsModel = $this->adminUserActionsLogsFactory->create();

        try{

            $actionLogsModel->setData('entity', 'Shopping Cart Rule')
                        ->setData('identifier', $post['id'])
                        ->setData('user_name',$adminUser)
                        ->setData('action', 'Delete')
                        ->setData('created_at', date('Y-m-d h:i:s'))
                        ->setData('data_before_save', '')
                        ->setData('data_after_save', '')
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