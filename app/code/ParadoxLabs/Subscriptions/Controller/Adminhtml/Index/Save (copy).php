<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\Subscriptions\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;

// Email notification
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;

/**
 * Save Class
 */
class Save extends View
{
    /**
     * @var \ParadoxLabs\Subscriptions\Model\Service\Subscription
     */
    protected $subscriptionService;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $dateProcessor;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Service\ItemManager
     */
    protected $itemManager;

    /**
     * Save constructor.
     *
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     * @param \ParadoxLabs\Subscriptions\Api\SubscriptionRepositoryInterface $subscriptionRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \ParadoxLabs\Subscriptions\Helper\Data $helper
     * @param \ParadoxLabs\Subscriptions\Model\Service\Subscription $subscriptionService
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $dateProcessor
     * @param \ParadoxLabs\Subscriptions\Model\Service\ItemManager|null $itemManager
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \ParadoxLabs\Subscriptions\Api\SubscriptionRepositoryInterface $subscriptionRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \ParadoxLabs\Subscriptions\Helper\Data $helper,
        \ParadoxLabs\Subscriptions\Model\Service\Subscription $subscriptionService,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $dateProcessor,
        \ParadoxLabs\Subscriptions\Model\Service\ItemManager $itemManager = null,
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        StateInterface $state,
        StoreManagerInterface $storeManager,
        \CreditCard\Expiring\Model\CreditCardExpiringModelFactory $creditCardExpiringModelFactory,
        \ParadoxLabs\TokenBase\Model\CardFactory $cardFactory,
        \Magento\Email\Model\TemplateFactory $templateFactory
    ) {
        parent::__construct(
            $context,
            $resultPageFactory,
            $registry,
            $subscriptionRepository,
            $customerRepository,
            $resultLayoutFactory,
            $helper
        );

        $this->subscriptionService = $subscriptionService;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->dateProcessor = $dateProcessor;
        // BC preservation -- arguments added in 3.4.0
        $this->itemManager = $itemManager ?: \Magento\Framework\App\ObjectManager::getInstance()->get(
            \ParadoxLabs\Subscriptions\Model\Service\ItemManager::class
        );
        $this->scopeConfig = $scopeConfig;
        $this->inlineTranslation = $state;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->creditCardExpiringModelFactory = $creditCardExpiringModelFactory;
        $this->cardFactory = $cardFactory;
        $this->templateFactory = $templateFactory;
    }

    /**
     * Subscription save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $initialized    = $this->initModels();
        $resultRedirect = $this->resultRedirectFactory->create();

        /**
         * If we were not able to load the model, short-circuit.
         */
        if ($initialized !== true) {
            $resultRedirect->setRefererOrBaseUrl();
            return $resultRedirect;
        }

        /** @var \ParadoxLabs\Subscriptions\Model\Subscription $subscription */
        $subscription = $this->registry->registry('current_subscription');
       
        // Checking old frequency
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $subscriptionCollection = $objectManager->get('ParadoxLabs\Subscriptions\Model\SubscriptionFactory')->create()->getCollection();
        $subscriptionCollection->addFieldToFilter('entity_id', $subscription['entity_id']);

        foreach ($subscriptionCollection as $subs) {
            $oldFrequencyCount = $subs->getData('frequency_count');
            $oldFrequencyUnit = $subs->getData('frequency_unit');

            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/subscriptions_custom_email.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);

            $logger->info('Old Frequency Count '.$oldFrequencyCount);
            $logger->info('Old Frequency Unit '.$oldFrequencyUnit);
        }

        try {
            $data         = $this->getRequest()->getParams();
            //echo "<pre>"; print_r($data); exit;
            $data['next_run'] = $this->dateProcessor->convertConfigTimeToUtc(
                $this->dateProcessor->date($data['next_run'])
            );

            /**
             * Update subscription details
             */
            $this->updateSubscriptionDetails($subscription, $data);

            /**
             * Update payment
             */
            $this->subscriptionService->changePaymentId(
                $subscription,
                $data['tokenbase_id'],
                isset($data['payment']) && is_array($data['payment']) ? $data['payment'] : []
            );

            if (isset($data['billing'])) {
                $this->subscriptionService->changeBillingAddress($subscription, $data['billing']);
            }

            /**
             * Update shipping address
             */
            /** @var \Magento\Quote\Model\Quote $quote */
            $quote        = $subscription->getQuote();

            if ((bool)$quote->getIsVirtual() === false) {
                $this->subscriptionService->changeShippingAddress($subscription, $data['shipping']);
                $this->subscriptionService->changeShippingMethod($subscription, $data['shipping']['method']);
            }

            $subscription->addRelatedObject($quote, true);
            $this->subscriptionRepository->save($subscription);


            // code added for sending email start
            // ===================== Subscription Notification Email Start ======================================//
            if($oldFrequencyCount != $subscription->getData('frequency_count'))
            {
                $subscriptionEmail = $this->sendChangeFrequencyEmail($subscription, $oldFrequencyCount, $oldFrequencyUnit);
            }        
            // ===================== Subscription Notification Email End ======================================= //

            $this->messageManager->addSuccessMessage(__('Subscription # %1 saved.', $subscription->getIncrementId()));

            if ($this->getRequest()->getParam('back', false)) {
                $resultRedirect->setPath('*/*/view', ['entity_id' => $subscription->getId(), '_current' => true]);
            } else {
                $resultRedirect->setPath('*/*/index');
            }
        } catch (\Exception $e) {
            $this->helper->log('subscriptions', (string)$e);
            $this->messageManager->addErrorMessage(__('ERROR: %1', $e->getMessage()));

            $resultRedirect->setPath('*/*/view', ['entity_id' => $subscription->getId(), '_current' => true]);
        }

        return $resultRedirect;
    }

    public function sendChangeFrequencyEmail($subscription, $oldFrequency, $oldFrequencyUnit)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/subscriptions_custom_email.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);


        try{

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $customer = $objectManager->get('Magento\Customer\Model\CustomerFactory')->create()->load($subscription->getData('customer_id'));
            $quote = $objectManager->get('Magento\Quote\Model\QuoteFactory')->create()->load($subscription->getData('quote_id'));

            $productName = "";
            foreach ($quote->getAllItems() as $item) {
                $productName = $item->getName();
            }

            $templateId = 48;

            if($oldFrequency == 1)
            {
                $oldFrequency = $oldFrequency.' '.$oldFrequencyUnit;
            }
            else
            {
                $oldFrequency = $oldFrequency.' '.$oldFrequencyUnit.'s';
            }

            // New Frequency
            $frequency = $subscription->getData('frequency_count');
            if($frequency == 1)
            {
                $frequency = $frequency.' '.$subscription->getData('frequency_unit');
            }
            else
            {
                $frequency = $frequency.' '.$subscription->getData('frequency_unit').'s';
            }

            $name = $customer->getFirstname().' '.$customer->getLastname();
            $templateVars = [
                'customer_name'   => $name,
                'subscription_id' => $subscription->getData('increment_id'),
                'last_run'        => date('F d Y',strtotime($subscription->getData('last_run'))),
                'next_run'        => date('F d Y',strtotime($subscription->getData('next_run'))),
                'product_name'    => $productName,
                'old_frequency'       => $oldFrequency,
                'new_frequency'       => $frequency
            ];

            $fromEmail = $this->scopeConfig->getValue("trans_email/ident_sales/email",\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $fromName = $this->scopeConfig->getValue("trans_email/ident_sales/name",\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $toEmail = $customer->getEmail();
            $storeId = $this->storeManager->getStore()->getStoreId();
            $from = ['email' => $fromEmail, 'name' => $fromName];

            $this->inlineTranslation->suspend();
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                'store' => $storeId
            ];

            $transport = $this->transportBuilder->setTemplateIdentifier($templateId, $storeScope)
                ->setTemplateOptions($templateOptions)
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo($toEmail)
                ->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
            $logger->info('Frequency change email sent successfully.');

            // ============================== Logged email data to table start =======================================================

            // Getting Telephone
            $cardCollection = $this->cardFactory->create()->getCollection();
            $cardCollection->addFieldToFilter('customer_id', $subscription->getData('customer_id'));
            $telephone="";
            foreach ($cardCollection as $card) {
                if(isset($card->getAddress()['telephone']))
                {
                    $telephone = $card->getAddress()['telephone'];
                }
            }

            // Text Message getting
            $templateObject    = $this->templateFactory->create()->load($templateId);
            $emailTextMessage  = $templateObject->getTemplateText();

            // Object to Store data in 'customer_notification_log'
            $logDataModel = $this->creditCardExpiringModelFactory->create();

            $logDataModel->setCustomerName($name)
                ->setCustomerEmail($toEmail)
                ->setPhone($telephone)
                ->setEmailSent('yes')
                ->setNotificationType('change frequency')
                ->setEmailMessage($emailTextMessage)
                ->save();

            // ================================= Logged email data to table end ====================================================

        }catch(\Exception $e){
            $logger->info('Error while sending email '.$e->getMessage());

            // Object to Store data in 'customer_notification_log'
            $logDataModel = $this->creditCardExpiringModelFactory->create();

            $logDataModel->setCustomerName('')
                ->setCustomerEmail('')
                ->setPhone('')
                ->setEmailSent('no')
                ->setNotificationType('change frequency')
                ->setEmailMessage($e->getMessage())
                ->save();
        }

    }


    /**
     * Update subscription details based on the given $data form input
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription
     * @param array $data
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateSubscriptionDetails(
        \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription,
        array $data
    ) {
        $subscription->setLength($data['length']);
        $subscription->setDescription($data['description']);
        $subscription->setNextRun($data['next_run']);

        if (!empty($data['interval']) && $data['interval'] !== '0') {
            $this->updateInterval($subscription, (int)$data['interval']);
        } else {
            $subscription->setFrequencyCount($data['frequency_count']);
            $subscription->setFrequencyUnit($data['frequency_unit']);
        }

        if (isset($data['notes'])) {
            $subscription->setAdditionalInformation('notes', $data['notes']);
        } elseif ($subscription->getAdditionalInformation('notes') !== null) {
            $subscription->setAdditionalInformation('notes', '');
        }
    }

    /**
     * Update subscription frequency/pricing based on the given interval, if valid and changed
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription
     * @param int $optionValueId
     * @return void
     */
    protected function updateInterval(
        \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription,
        int $optionValueId
    ) {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $subscription->getQuote();
        $item  = current($quote->getAllVisibleItems());

        $this->itemManager->updateInterval($subscription, $item, $optionValueId);
    }
}

