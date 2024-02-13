<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CreditCard\Expiring\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class CreditCardExpiring
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
        \Sms\Custom\Helper\SmsHelper $smsHelper,
        \CreditCard\Expiring\Model\CreditCardExpiringModelFactory $creditCardExpiringModelFactory,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->logger = $logger;
        $this->smsHelper            = $smsHelper;
        $this->scopeConfig          = $scopeConfig;
        $this->inlineTranslation    = $inlineTranslation;
        $this->transportBuilder     = $transportBuilder;
        $this->creditCardExpiringModelFactory = $creditCardExpiringModelFactory;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        //$this->logger->addInfo("Cronjob CreditCardExpiring is executed.");

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/credit_card_expiring.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Line 52 = ');

        $now                = new \DateTime();
        $toDate             = $now->format('Y-m-d 00:00:00');
        $experationDays     = $now->modify("+30days")->format('Y-m-d 00:00:00');

        $objectManager  = \Magento\Framework\App\ObjectManager::getInstance();
        $resource       = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection     = $resource->getConnection();

        $selectQuery = $connection->select()
            ->from(['sbs' => 'paradoxlabs_subscription'])
            ->join(['card' => 'paradoxlabs_stored_card'], 'card.customer_id = sbs.customer_id')
            ->join(['item' => 'quote_item'], 'item.quote_id = sbs.quote_id')
            ->where('sbs.status = ?', 'active')
            ->where('card.expires >= ?', $toDate)
            ->where('card.expires <= ?', $experationDays)
            ->group('card.id');

        $result = $connection->fetchAll($selectQuery);

        if(count($result)>0){
            foreach ($result as $key => $value) {
		$subscription_id=$value['entity_id'];
                $customer_email = $value['customer_email'];
                $product_name   = $value['name'];
                $next_run       = $value['next_run'];
                $next_run       = date("m/d/Y", strtotime($next_run));

                $additional     = json_decode($value['additional']);
                $cc_type        = $additional->cc_type;
                $cc_last4       = $additional->cc_last4;

                $address        = json_decode($value['address']);
                $firstname      = $address->firstname;
                $lastname       = $address->lastname;
                $telephone      = $address->telephone;
                
                $customer_name  = $firstname.' '.$lastname;

                if($customer_email){
                    $logger->info('Subscription: '.$subscription_id.', Email: '.$customer_email.', Name: '.$customer_name.', cc_type: '.$cc_type.' Product: '.$product_name.', Next Run: '.$next_run);

                    //$this->sendEmail($customer_email, $customer_name, $cc_type, $cc_last4, $product_name, $next_run);
                }
                
            } 

        }


    }

    public function sendEmail($customer_email, $customer_name, $cc_type, $cc_last4, $product_name, $next_run)
    {
        $this->inlineTranslation->suspend();
        $sender = [
            'name'  => $this->scopeConfig->getValue('trans_email/ident_support/name',ScopeInterface::SCOPE_STORE),
            'email' => $this->scopeConfig->getValue('trans_email/ident_support/email',ScopeInterface::SCOPE_STORE),
        ];

        $transport = $this->transportBuilder
            //->setTemplateIdentifier('alle_customer_email_template')
            ->setTemplateIdentifier(1000043)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )
            ->setTemplateVars([
                'customer_name'  => $customer_name,
                'cc_type'   => $cc_type,
                'cc_last4'    => $cc_last4,
                'next_run'    => $next_run,
                'product_name'    => $product_name
            ])
            ->setFrom($sender)
            ->addTo($customer_email)
            ->getTransport();

        try {
            $transport->sendMessage();
            $creditCardExpire = $this->creditCardExpiringModelFactory->create();
            $dataToSave = array('customer_email' => $customer_email, 'email_sent' => "yes", 'notification_type'=>'Credit Card Expire');
            $creditCardExpire->setData($dataToSave);
            $creditCardExpire->save();
        } catch (\Exception $exception) {
            $creditCardExpire = $this->creditCardExpiringModelFactory->create();
            $dataToSave = array('customer_email' => $customer_email, 'email_sent' => "no", 'notification_type'=>'Credit Card Expire');
            $creditCardExpire->setData($dataToSave);
            $creditCardExpire->save();
        }
        $this->inlineTranslation->resume();
        return $this;
    }

}


