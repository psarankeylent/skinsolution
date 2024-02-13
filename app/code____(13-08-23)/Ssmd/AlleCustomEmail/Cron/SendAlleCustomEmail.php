<?php

namespace Ssmd\AlleCustomEmail\Cron;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class SendAlleCustomEmail
{
    protected $alleCustomFactory;
    protected $orderCollectionFactory;
    protected $storeManager;
    protected $inlineTranslation;
    protected $escaper;
    protected $transportBuilder;
    protected $scopeConfig;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Ssmd\AlleCustom\Model\AlleCustomFactory $alleCustomFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Translate\Inline\StateInterface $inlineTranslation,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->alleCustomFactory = $alleCustomFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->storeManager = $storeManager;
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        /*$now        = new \DateTime();
        $toDate     = $now->format('Y-m-d H:i:s');
        $fromDate   = $now->modify("-50 days")->format('Y-m-d H:i:s');*/
    
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/alle_custom_email.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        
        try {
        
            $alleMembersCollection = $this->alleCustomFactory->create()
                                ->getCollection()
                                ->addFieldToFilter('sent_email', array('sent_email' => 0))
                                ->setOrder('id', 'DESC');
            $alleMembersCollection->getSelect()->limit(50);

            //echo "<pre>"; print_r($alleMembersCollection->getData()); exit;
            if(!empty($alleMembersCollection->getData()))
            {
                foreach ($alleMembersCollection as $value) {
                    $id = $value->getId();
                    $incrementId = $value->getIncrementId();
                    $brilliantcoupon1 = $value->getData('brilliantcoupon1');
                    $brilliantcoupon2 = $value->getData('brilliantcoupon2');
                    $orderCollection  = $this->orderCollectionFactory->create()
                                ->addFieldToFilter('increment_id', array('increment_id' => $incrementId))
                                ->addFieldToFilter('status',       array('status' => 'pending'));

                    if(!empty($orderCollection->getData()))
                    {
                        foreach ($orderCollection as $order) {
                            $email = $order->getCustomerEmail();
                            if(isset($email) && $email != "")
                            {
                                $send = $this->sendEmail($email, $id, $incrementId, $brilliantcoupon1, $brilliantcoupon2);
                            }
                        }
                    }
                }
            }
            else
            {
                $logger->info("No records found to send email.");
            }

        } catch (\Exception $e) {
            //$logger->info("AlleCustomEmail - Exception Error occurred: " . $e->getMessage());
        }
	
    } 

    public function sendEmail($toEmail, $id, $incrementId, $brilliantcoupon1, $brilliantcoupon2)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/alle_custom_email.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("Cronjob running to send alle email..");

        $toEmail = 'hitesh@skinsolutions.md';
        
        try {
            $this->inlineTranslation->suspend();
            $sender = [
                'name'  => $this->scopeConfig->getValue('trans_email/ident_support/name',ScopeInterface::SCOPE_STORE),
                'email' => $this->scopeConfig->getValue('trans_email/ident_support/email',ScopeInterface::SCOPE_STORE),
            ];
            $transport = $this->transportBuilder
                ->setTemplateIdentifier(41)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                    'OrderID'  => $incrementId,
                    'coupon1'   => $brilliantcoupon1,
                    'coupon2'   => $brilliantcoupon2
                ])
                ->setFrom($sender)
                ->addTo($toEmail)
                ->getTransport();
            
            $logger->info("Data ".$incrementId.'-'.$brilliantcoupon1.'-'.$brilliantcoupon2);

            $transport->sendMessage();

            // Update 'alle_member_customers' table with 'sent_email' field to 1.
            $alleModel = $this->alleCustomFactory->create()->load($id);
            $alleModel->setData('sent_email', 1)
                      ->save();
            
            $this->inlineTranslation->resume();


        } catch (\Exception $e) {
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/alle_custom_email.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("Cronjob Alle Email Send Error : ".$e->getMessage());
        }

    }

}


