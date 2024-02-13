<?php

namespace CustomOrders\GenerateOrders\Controller\Test;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;



class Index extends \Magento\Framework\App\Action\Action
{
    protected $resultPageFactory;

    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Psr\Log\LoggerInterface $logger,
        \ParadoxLabs\Subscriptions\Model\SubscriptionFactory $subscriptionFactory,
        \CreditCard\Expiring\Model\CreditCardExpiringModelFactory $creditCardExpiringModelFactory,
        \ParadoxLabs\TokenBase\Model\CardFactory $cardFactory,
        \Magento\Email\Model\TemplateFactory $templateFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Customer $customer,
        \Ssmd\AlleCustom\Model\AlleCustomFactory $alleCustomFactory
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->scopeConfig          = $scopeConfig;
        $this->inlineTranslation    = $inlineTranslation;
        $this->transportBuilder     = $transportBuilder;
        $this->logger = $logger;
        $this->subscriptionFactory = $subscriptionFactory;
        $this->creditCardExpiringModelFactory = $creditCardExpiringModelFactory;
        $this->cardFactory = $cardFactory;
        $this->templateFactory = $templateFactory;
        $this->storeManager = $storeManager;
        $this->customer         = $customer;
        $this->alleCustomFactory = $alleCustomFactory;
    }

    public function execute()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/alle_points_email.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $orderIncrementId = 'QA100359103';
        $customerId = 241248;

        $alleCollection = $this->alleCustomFactory->create()->getCollection();
        $alleCollection->addFieldToFilter('increment_id', $orderIncrementId);

        //echo "<pre>"; print_r($alleCollection->getData()); exit;
        if(!empty($alleCollection->getData()))
        {
            
            $customer = $this->customer->load($customerId);
            $customer_name = $customer->getFirstname().' '.$customer->getLastname();
            $customer_email = $customer->getEmail();
            $amount=0;
            $amount = (float)$alleCollection->getFirstItem()->getData()['brilliantcoupon1'];

            $loggerData = "Alle Points ".$amount."Email : ".$customer_email." Customer Name : ".$customer_name;

            $logger->info("===== START =====");
            $logger->info($loggerData);
            $logger->info("===== END =====");

            // Send email
            //$this->sendAlleEmail($customer_email, $customer_name, $amount);
        }
        else
        {
            $logger->info("===== No Data Found! =====");
        }
        
    }

    public function sendAlleEmail($customer_email, $customer_name, $amount)
    {
        $templateId = 54;
        $this->inlineTranslation->suspend();
        $sender = [
            'name'  => $this->scopeConfig->getValue('trans_email/ident_support/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'email' => $this->scopeConfig->getValue('trans_email/ident_support/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
        ];
        $transport = $this->transportBuilder
            ->setTemplateIdentifier($templateId)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )
            ->setTemplateVars([
                'customer_name'    => $customer_name,
                'amount_used'      => $amount
            ])
            ->setFrom($sender)
            ->addTo($customer_email)
            ->getTransport();

        
        try {
            $transport->sendMessage();
           
            $trackLog = $this->creditCardExpiringModelFactory->create();
            $dataToSave = array('customer_email' => $customer_email, 'email_sent' => "yes", 'notification_type'=>'Alle Points', 'email_message' => "");
            $trackLog->setData($dataToSave);
            $trackLog->save();

            //$logger->info('Alle Points email sent successfully.');

        } catch (\Exception $e) {
            $trackLog = $this->creditCardExpiringModelFactory->create();
            $dataToSave = array('customer_email' => $customer_email, 'email_sent' => "no", 'notification_type'=>'Alle Points','email_message' => "");
            $trackLog->setData($dataToSave);
            $trackLog->save();
        }
        $this->inlineTranslation->resume();
    }


    

   
}

