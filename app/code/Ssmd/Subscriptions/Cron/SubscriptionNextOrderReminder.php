<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ssmd\Subscriptions\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class SubscriptionNextOrderReminder
{

    protected $logger;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Psr\Log\LoggerInterface $logger,
        \ParadoxLabs\Subscriptions\Model\SubscriptionFactory $subscriptionFactory,
        \CreditCard\Expiring\Model\CreditCardExpiringModelFactory $creditCardExpiringModelFactory,
        \ParadoxLabs\TokenBase\Model\CardFactory $cardFactory,
        \Magento\Email\Model\TemplateFactory $templateFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->scopeConfig          = $scopeConfig;
        $this->inlineTranslation    = $inlineTranslation;
        $this->transportBuilder     = $transportBuilder;
        $this->logger = $logger;
        $this->subscriptionFactory = $subscriptionFactory;
        $this->creditCardExpiringModelFactory = $creditCardExpiringModelFactory;
        $this->cardFactory = $cardFactory;
        $this->templateFactory = $templateFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    { 

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/upcoming_expire_subscriptions.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Start upcoming subscriptions...');
       
        $fromDate = date('Y-m-d 00:00:00', strtotime('5days'));
        $toDate = date('Y-m-d 23:59:59', strtotime('5days'));       

        $subscriptionCollection = $this->subscriptionFactory->create()->getCollection();
        $subscriptionCollection->getSelect()
                ->join(
                    'quote_item',
                    'main_table.quote_id = quote_item.quote_id',
                    ['name as product_name']
                )->join(
                    'customer_entity',
                    "main_table.customer_id = customer_entity.entity_id",
                    ["email","CONCAT(customer_entity.firstname, ' ', customer_entity.lastname) as customer_name"]
            );

        $subscriptionCollection->addFieldToFilter('main_table.status', 'active');
        $subscriptionCollection->addFieldToFilter('main_table.next_run', array('gteq' => $fromDate));
        $subscriptionCollection->addFieldToFilter('main_table.next_run', array('lteq' => $toDate));

        if($subscriptionCollection->count()>0){
            foreach ($subscriptionCollection as $value) {

                $subscriptionId = $value->getIncrementId();
                $customer_id    = $value->getCustomerId();
                $next_run       = $value->getNextRun();
                $frequencyCount = $value->getFrequencyCount();
                $frequencyUnit  = $value->getFrequencyUnit();
                $product_name   = $value->getProductName();
                $customer_email = $value->getEmail();
                $customer_name  = $value->getCustomerName();

                if($frequencyCount == 1)
                {
                    $frequency = $frequencyCount.' '.$frequencyUnit;
                }
                else
                {
                    $frequency = $frequencyCount.' '.$frequencyUnit.'s';
                }
               
                $loggerData = "subscription_id ".$subscriptionId."Email : ".$customer_email." Customer Name : ".$customer_name." Product Name : ".$product_name." Next Run : ".$next_run;
                

                $logger->info("===== START =====");
                $logger->info($loggerData);
                $logger->info("===== END =====");

                //$this->sendEmail($product_name, $customer_name, $customer_email, $frequency, $next_run);

            }
        }
        else
        {
            $logger->info(" There is no subscriptions for this date. ".date('Y-m-d 00:00:00', strtotime('5days')));
        }

    }

    public function sendEmail($product_name, $customer_name, $customer_email, $frequency, $next_run)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/upcoming_expire_subscriptions.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        // ================ Send email code start ===============
        $templateId = 50;
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
                'customer_name'   => $customer_name,
                'next_run'        => $next_run,
                'product_name'    => $product_name,
                'frequency'       => $frequency
            ])
            ->setFrom($sender)
            ->addTo($customer_email)
            ->getTransport();


        try {
            $transport->sendMessage();

            // Text Message getting
            //$templateObject    = $this->templateFactory->create()->load($templateId);
            //$emailTextMessage  = $templateObject->getTemplateText();

            $trackLog = $this->creditCardExpiringModelFactory->create();
            $dataToSave = array('customer_email' => $customer_email, 'email_sent' => "yes", 'notification_type'=>'Subscription Reminder', 'email_message' => "");
            $trackLog->setData($dataToSave);
            $trackLog->save();

            $logger->info('Subscription reminder email sent successfully.');


        } catch (\Exception $e) {
            $trackLog = $this->creditCardExpiringModelFactory->create();
            $dataToSave = array('customer_email' => $customer_email, 'email_sent' => "no", 'notification_type'=>'Subscription Reminder','email_message' => "");
            $trackLog->setData($dataToSave);
            $trackLog->save();
        }
        $this->inlineTranslation->resume();
    }
}
