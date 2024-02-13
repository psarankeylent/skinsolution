<?php

namespace TrackingActivityLog\TrackAdminUser\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveCartPriceRuleDataObserver implements ObserverInterface
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
        
        $ruleId = $observer->getRule()->getId();  // you will get cart price rule object        
        $rule = $this->rulesFactory->create()->load($ruleId);
    
        $origData = $rule->getOrigData();
        $data    = $post;
        
        /*echo "<pre>";
        print_r($origData);
        print_r($data);
        exit;*/
       
        
        // Converted Data into Json Encoded
        $jsonEncodedOrigData = $this->json->serialize($origData);
        $jsonEncodedData = $this->json->serialize($data);

        $adminUser = $this->adminSession->getUser()->getUsername();
        $adminEmail = $this->adminSession->getUser()->getEmail();

        $actionLogsModel = $this->adminUserActionsLogsFactory->create();

        $action = '';
        if(isset($post['rule_id']) && array_key_exists('rule_id', $post))
        {
            $action = 'Edit';
            $identifier = $post['rule_id'];

        }
        else
        {
            $action = 'New';
            $identifier = $post['name'];        // If new rule created then rule_id not generated.
        }
        
        try{

            $actionLogsModel->setData('entity', 'Shopping Cart Rule')
                        ->setData('identifier', $identifier)
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
            //echo $e->getMessage();
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/TrackAdminUser.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("Error ".$e->getMessage());

        }
    }  


}