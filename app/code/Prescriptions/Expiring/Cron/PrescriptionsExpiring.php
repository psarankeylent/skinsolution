<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Prescriptions\Expiring\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class PrescriptionsExpiring
{

    protected $logger;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        ScopeConfigInterface $scopeConfig,
        \Sms\Custom\Helper\SmsHelper $smsHelper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \ConsultOnly\ConsultOnlyCollection\Model\ConsultOnlyFactory  $consultOnlyFactory,
        \Prescriptions\Expiring\Model\PrescriptionExpireScheduleFactory $prescriptionExpireScheduleFactory
    )
    {
        $this->storeManager         = $storeManager;
        $this->consultOnlyFactory   = $consultOnlyFactory;
        $this->scopeConfig          = $scopeConfig;
        $this->inlineTranslation    = $inlineTranslation;
        $this->transportBuilder     = $transportBuilder;
        $this->smsHelper            = $smsHelper;
        $this->prescriptionExpireScheduleFactory    = $prescriptionExpireScheduleFactory;
        $this->logger = $logger;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        
        //$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/prescription_expire.log');
        //$logger = new \Zend\Log\Logger();
        //$logger->addWriter($writer);
        //$logger->info('Line 40 = ');

        $notificationScheduleCollection = $this->prescriptionExpireScheduleFactory->create()->getCollection()->getFirstItem();

        if(count($notificationScheduleCollection->getData())>0){
            $intervel_days      = $notificationScheduleCollection->getData('intervel_days');
            $experation_days    = $notificationScheduleCollection->getData('Experation_days');
            $next_run           = $notificationScheduleCollection->getData('next_run');
        }

        if($next_run == date('Y-m-d')){

            $now                = new \DateTime();
            $toDate             = $now->format('Y-m-d 00:00:00');
            $experation         = "+".$experation_days."days";
            $experationDays     = $now->modify($experation)->format('Y-m-d 00:00:00');

            $new2               = new \DateTime();
            $todayDate          = $new2->format('Y-m-d');
            $next_next_run      = $new2->modify("+".$intervel_days."days")->format('Y-m-d');

            $consultOnlyCollection = $this->consultOnlyFactory->create()->getCollection()
                                ->addFieldToFilter('expiration_date', array('gteq' => $toDate))
                                ->addFieldToFilter('expiration_date', array('lteq' => $experationDays));

            if($consultOnlyCollection->count()>0){
                foreach ($consultOnlyCollection->getData() AS $consultOnly) {
                    $customerEmail  = $consultOnly['customer_email'];
                    $mobile      = $consultOnly['telephone'];
                    $expiration_date    = $consultOnly['expiration_date'];

                    $currentDatetime    = date_create();
                    $new_expiration_date = date_create($expiration_date);
                    $interval       = date_diff($currentDatetime, $new_expiration_date);
                    $expire_days    = $interval->format('%a');

                    if(!empty($customerEmail)){
                        $this->sendEmail($customerEmail,$expire_days);
                    }

                    if(!empty($mobile)){
                        $smsText = "We are reminding you that your 12-month prescription is about to expire. Please log in to your Account Dashboard to view the impacted products: https://www.skinsolutions.md/ ";
                        $this->smsHelper->sendSms($smsText,$mobile);
                    }

                }

            }

            $data = array('last_run' => $next_run, 'next_run' => $next_next_run);
                $notificationScheduleCollection->addData($data);
                $notificationScheduleCollection->save();

        }

        $this->logger->addInfo("Cronjob expiring is executed.");
    
    }



    public function sendEmail($customerEmail,$expire_days)
    {
        $first_name = "Rahul";
        $product_name = "-";
        $order_number = "#";
        $base_url = "https://qafe.skinsolutions.md/";

        $this->inlineTranslation->suspend();
        $sender = [
            'name'  => $this->scopeConfig->getValue('trans_email/ident_support/name',ScopeInterface::SCOPE_STORE),
            'email' => $this->scopeConfig->getValue('trans_email/ident_support/email',ScopeInterface::SCOPE_STORE),
        ];

        $transport = $this->transportBuilder
            //->setTemplateIdentifier('alle_customer_email_template')
            ->setTemplateIdentifier(1000042)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )
            ->setTemplateVars([
                'first_name'  => $first_name,
                'expire_days' => $expire_days,
                'product_name'    => $product_name,
                'order_number'    => $order_number,
                'base_url'    => $base_url
            ])
            ->setFrom($sender)
            ->addTo($customerEmail)
            ->getTransport();

        try {
            $transport->sendMessage();
        } catch (\Exception $exception) {
            //$this->logger->critical($exception->getMessage());
        }
        $this->inlineTranslation->resume();
        return $this;
    }



}

