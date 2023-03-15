<?php

namespace Ssmd\Impersonation\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
class Save extends \Magento\Backend\App\Action
{
    protected $impersonationFactory;
    protected $helperData;
    protected $authSession;
    protected $customerFactory;
    protected $tokenFactory;
    protected $sessionManager;
    protected $encryptor;
    
    public function __construct(
        Action\Context $context,
        \Ssmd\Impersonation\Model\ImpersonationFactory $impersonationFactory,
        \Ssmd\Impersonation\Helper\Data $helperData,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Integration\Model\Oauth\TokenFactory $tokenFactory,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor
       
    ) {
        $this->impersonationFactory = $impersonationFactory;
        $this->helperData = $helperData;
        $this->authSession = $authSession;
        $this->customerFactory = $customerFactory;
        $this->tokenFactory = $tokenFactory;
        $this->sessionManager = $sessionManager;
        $this->encryptor = $encryptor;
        parent::__construct($context);
    }

    public function execute()
    {
        $customerId = $this->getRequest()->getParam('id');

        // TImezone
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $timezone = $objectManager->create('Magento\Framework\Stdlib\DateTime\TimezoneInterface');

        $dateTime = date('Y-m-d H:i:s');

        //echo $timezone->date()->format('Y-m-d H:i:s');
        /*echo $configTimezone = $timezone->getConfigTimezone();      // Get current user config timezone
        echo "<br>";
        echo $timezone->getDefaultTimezone();                   // Get system timezone UTC
        exit;
*/


        $convertedDatetime = $this->converToTz(
                $dateTime,
                // get default timezone of system (UTC)
                $timezone->getDefaultTimezone(), 
                // get Config Timezone of current user 
                $timezone->getConfigTimezone()
            );

       // echo $convertedDatetime; exit;

        $customerToken = $this->tokenFactory->create();
        $tokenKey = $customerToken->createCustomerToken($customerId)->getToken();

        $isFromOrderPage = $this->sessionManager->getIsFromOrderPage();

        $impersonationModel = $this->impersonationFactory->create();

        $adminToken = $this->encryptor->encrypt($this->authSession->getUser()->getUsername());
        $adminPassord = $this->encryptor->encrypt($this->authSession->getUser()->getPassword());

       $impersonationModel->setData('customer_token', $tokenKey)
                           ->setData('username', $this->authSession->getUser()->getUsername())
                           ->setData('firstname', $this->authSession->getUser()->getFirstname())
                           ->setData('admin_token', $adminToken)
                           ->setData('admin_password', $adminPassord)
                           ->setData('created_date', date('Y-m-d h:i:s'))
                           ->setData('access_time', $convertedDatetime)   
                           ->save();

        
        $dd = $impersonationModel->load($impersonationModel->getId(),'id');
        
        echo "<pre>"; print_r($dd->getData()); exit;

        $redirectUrl = $this->helperData->getImpersonationWebsite();

        $redirectUrl = $redirectUrl.'token/'.$adminToken;

        //echo $redirectUrl; exit;
        $this->_redirect($redirectUrl);

        
	}

    /**
     * converToTz convert Datetime from one zone to another
     * @param string $dateTime which we want to convert
     * @param string $toTz timezone in which we want to convert
     * @param string $fromTz timezone from which we want to convert
    */
    public function converToTz($dateTime="", $toTz='', $fromTz='')
    {   
        // timezone by php friendly values
        $date = new \DateTime($dateTime, new \DateTimeZone($fromTz));
        $date->setTimezone(new \DateTimeZone($toTz));
        $dateTime = $date->format('m/d/Y H:i:s');
        return $dateTime;
    }
}

?>