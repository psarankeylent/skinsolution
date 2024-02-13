<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ssmd\AlleCustom\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class SendEmailAllePoints
{
    protected $scopeConfig;
    protected $inlineTranslation;
    protected $transportBuilder;
    protected $subscriptionFactory;
    protected $creditCardExpiringModelFactory;
    protected $cardFactory;
    protected $templateFactory;
    protected $storeManager;
    protected $alleCustomFactory;
    protected $order;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \ParadoxLabs\Subscriptions\Model\SubscriptionFactory $subscriptionFactory,
        \CreditCard\Expiring\Model\CreditCardExpiringModelFactory $creditCardExpiringModelFactory,
        \ParadoxLabs\TokenBase\Model\CardFactory $cardFactory,
        \Magento\Email\Model\TemplateFactory $templateFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Ssmd\AlleCustom\Model\AlleCustomFactory $alleCustomFactory,
        \Magento\Sales\Api\Data\OrderInterface $order
    )
    {
        $this->scopeConfig          = $scopeConfig;
        $this->inlineTranslation    = $inlineTranslation;
        $this->transportBuilder     = $transportBuilder;
        $this->subscriptionFactory = $subscriptionFactory;
        $this->creditCardExpiringModelFactory = $creditCardExpiringModelFactory;
        $this->cardFactory = $cardFactory;
        $this->templateFactory = $templateFactory;
        $this->storeManager   = $storeManager;
        $this->alleCustomFactory  = $alleCustomFactory;
        $this->order  = $order;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    { 
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/alle_points_email.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Start inserting data..');

        $alleCollection = $this->alleCustomFactory->create()->getCollection();
        $alleCollection->addFieldToFilter('sent_email', 0);
        $alleCollection->addFieldToFilter('brilliantcoupon1', ['neq' => 0]);  // non zero
        $alleCollection->addFieldToFilter('brilliantcoupon1', ['neq' => ""]);  // non blank
        
        if(!empty($alleCollection->getData()))
        { 
            //$logger->info('Data are available');

            foreach ($alleCollection as $value) {
                $orderIncrementId = $value->getData('increment_id');
                $alleId           = $value->getData('id');
                $amount           = (float)$value->getData('brilliantcoupon1');
                $order            = $this->order->loadByIncrementId($orderIncrementId);
                $customer_name    = $order->getCustomerFirstname().' '.$order->getCustomerLastname();
                $customer_email   = $order->getCustomerEmail();

                if( ($amount != 0)&&($amount != "") )
                {
                    $loggerData = "OrderId ".$orderIncrementId." Alle Points ".$amount." Email : ".$customer_email." Customer Name : ".$customer_name;

                    $logger->info("===== START =====");
                    $logger->info($loggerData);
                    $logger->info("===== END =====");

                    // Send email
                    //$this->sendEmail($customer_email, $customer_name, $amount, $alleId);
                }
            }

        } 
        

    }

    public function sendEmail($customer_email, $customer_name, $amount, $alleId)
    {
        $templateId = 1000054;
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

            // Set to 1 if email sent successfull.
            $alleModel = $this->alleCustomFactory->create()->load($alleId);
            $alleModel->setSentEmail(1);
            $alleModel->save();


        } catch (\Exception $e) {
            $trackLog = $this->creditCardExpiringModelFactory->create();
            $dataToSave = array('customer_email' => $customer_email, 'email_sent' => "no", 'notification_type'=>'Alle Points','email_message' => "");
            $trackLog->setData($dataToSave);
            $trackLog->save();
        }
        $this->inlineTranslation->resume();
    }

}
