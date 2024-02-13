<?php
namespace TrackingActivityLog\TrackAdminUser\Plugin\Config;

class SaveStoreConfigPlugin
{
     /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;
    protected $json;
    protected $adminUserActionsLogsFactory;
    protected $adminSession;

    /**
     * AroundSaveConfig constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \TrackingActivityLog\TrackAdminUser\Model\AdminUserActionsLogsFactory $adminUserActionsLogsFactory,
        \Magento\Backend\Model\Auth\Session $adminSession
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->json = $json;
        $this->adminUserActionsLogsFactory = $adminUserActionsLogsFactory; 
        $this->adminSession = $adminSession;
    }


    public function aroundSave(
        \Magento\Config\Model\Config $subject,
        callable $proceed
    ) {

        $oldConfigs = [];
        $newConfigs  = [];

        $section = $subject->getSection();
        //$section = $subject->getStore();

        $oldConfigs = $this->scopeConfig->getValue($section);
        //Proceed
        $returnValue = $proceed();
        
        $newConfigs = $this->scopeConfig->getValue($section);

        $jsonEncodedOrigData = $this->json->serialize($oldConfigs);
        $jsonEncodedData = $this->json->serialize($newConfigs);
       
        $adminUser = $this->adminSession->getUser()->getUsername();       
        $actionLogsModel = $this->adminUserActionsLogsFactory->create();

        try{

            $actionLogsModel->setData('entity', 'Store Configuration')
                        ->setData('identifier', 'admin config')
                        ->setData('user_name',$adminUser)
                        ->setData('action', 'Save')
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