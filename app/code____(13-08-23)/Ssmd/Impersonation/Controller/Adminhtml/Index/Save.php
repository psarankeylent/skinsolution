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
    protected $timezoneInterface;
    
    public function __construct(
        Action\Context $context,
        \Ssmd\Impersonation\Model\ImpersonationFactory $impersonationFactory,
        \Ssmd\Impersonation\Helper\Data $helperData,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Integration\Model\Oauth\TokenFactory $tokenFactory,
        \Magento\Framework\Session\SessionManagerInterface $sessionManager,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface
       
    ) {
        $this->impersonationFactory = $impersonationFactory;
        $this->helperData = $helperData;
        $this->authSession = $authSession;
        $this->customerFactory = $customerFactory;
        $this->tokenFactory = $tokenFactory;
        $this->sessionManager = $sessionManager;
        $this->encryptor = $encryptor;
        $this->timezoneInterface = $timezoneInterface;
        parent::__construct($context);
    }

    public function execute()
    {
        
        if($randomNumber = $this->getRequest()->getPost('random_number'))
        {                 
            $customerId = $this->getRequest()->getParam('id');
            $customer = $this->customerFactory->create()->load($customerId);
            
            // Customer token
            $customerToken = $this->tokenFactory->create();
            $tokenKey = $customerToken->createCustomerToken($customerId)->getToken();

            // Encrypt admin user & password
            $adminToken   = $this->encryptor->encrypt($this->authSession->getUser()->getUsername());
            $adminPassord = $this->encryptor->encrypt($randomNumber);
            
            // Convert timestamp according to config in magento
            $convertedDateTime = $this->converToTz(
                                        date('Y-m-d H:i:s'),                                        
                                        $this->timezoneInterface->getConfigTimezone(),
                                        $this->timezoneInterface->getDefaultTimezone(),
                                );

            $impersonationModel = $this->impersonationFactory->create();
            $impersonationModel->setData('customer_token', $tokenKey)
                               ->setData('username', $this->authSession->getUser()->getUsername())
                               ->setData('firstname', $this->authSession->getUser()->getFirstname())
                               ->setData('lastname', $this->authSession->getUser()->getLastname())
                               ->setData('customer_email', $customer->getEmail())
                               ->setData('admin_token', $adminToken)
                               ->setData('admin_password', $adminPassord)
                               ->setData('created_date', $convertedDateTime)
                               //->setData('access_time', date('h:i:s'))
                               ->save();

            
            /*$dt = $impersonationModel->load($impersonationModel->getId(),'id');        
            echo "<pre>"; print_r($dt->getData()); exit;*/
		$postUrl = $this->helperData->getImpersonationWebsite();
		$html = '<form id="impersonate-form" action="' . $postUrl."/impersonate" . '" method="get">';

		$html .= '<input type="hidden" name="_tx" value="'.$adminToken.'">';
		$html .= '</form>';
		$html .= '<script>document.getElementById("impersonate-form").submit();</script>';
		$this->setImpersonateCookie($adminToken);
		$this->getResponse()->setBody($html);

/**
            $redirectUrl = $this->helperData->getImpersonationWebsite();
            $redirectUrl = $redirectUrl.'?_tx='.$adminToken;

            $params = ['_tx' => $adminToken];

            $this->_redirect($redirectUrl, $params);
   **/
	     }

        
    }

	/** set cookie **/
	public function setImpersonateCookie($value) {
		$cookiePath = '/';
		$cookieDomain = '.skinsolutions.md'; 
		$cookieSecure = true;
		$cookieHttpOnly = true;
		$cookieLifetime = 60;
    		setcookie('_smdi', $value, $cookieLifetime, $cookiePath, $cookieDomain, $cookieSecure, $cookieHttpOnly);
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
        $dateTime = $date->format('Y-m-d H:i:s');
        return $dateTime;
    }


    public function getTimeAccordingToTimeZone($dateTime)
    {
        // for convert date time according to magento's config time zone
        $dateTimeAsTimeZone = $this->timezoneInterface
                                        ->date(new \DateTime($dateTime))
                                        ->format('Y-m-d H:i:s');    
        return $dateTimeAsTimeZone;
    }

   
}

?>
