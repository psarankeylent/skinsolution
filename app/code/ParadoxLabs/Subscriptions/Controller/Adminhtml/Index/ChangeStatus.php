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
 * ChangeStatus Class
 */
class ChangeStatus extends View
{
    /**
     * @var \ParadoxLabs\Subscriptions\Model\Source\Status
     */
    protected $statusSource;

    /**
     * ChangeStatus constructor.
     *
     * @param Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Registry $registry
     * @param \ParadoxLabs\Subscriptions\Api\SubscriptionRepositoryInterface $subscriptionRepository
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \ParadoxLabs\Subscriptions\Helper\Data $helper
     * @param \ParadoxLabs\Subscriptions\Model\Source\Status $statusSource
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \ParadoxLabs\Subscriptions\Api\SubscriptionRepositoryInterface $subscriptionRepository,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \ParadoxLabs\Subscriptions\Helper\Data $helper,
        \ParadoxLabs\Subscriptions\Model\Source\Status $statusSource,
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

        $this->statusSource = $statusSource;
        $this->scopeConfig = $scopeConfig;
        $this->inlineTranslation = $state;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->creditCardExpiringModelFactory = $creditCardExpiringModelFactory;
        $this->cardFactory = $cardFactory;
        $this->templateFactory = $templateFactory;
    }

    /**
     * Subscription status-change action
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

        try {
            $newStatus = $this->getRequest()->getParam('status');

            if ($this->statusSource->canSetStatus($subscription, $newStatus) === true) {
                $subscription->setStatus($newStatus);
                $this->subscriptionRepository->save($subscription);

                // ===================== Subscription Status Change Notification Email Start ======================================//
                $this->sendSubscriptionStatusChangeEmail($subscription, $newStatus);
                // ===================== Subscription Status Change Notification Email End ======================================= //

                $this->messageManager->addSuccessMessage(
                    __(
                        'Subscription status changed to "%1".',
                        $this->statusSource->getOptionText($subscription->getStatus())
                    )
                );
            }

            $resultRedirect->setPath('*/*/view', ['entity_id' => $subscription->getId(), '_current' => true]);
        } catch (\Throwable $e) {
            $this->helper->log('subscriptions', (string)$e);
            $this->messageManager->addErrorMessage(__('ERROR: %1', $e->getMessage()));

            $resultRedirect->setPath('*/*/view', ['entity_id' => $subscription->getId(), '_current' => true]);
        }

        return $resultRedirect;
    }
    public function sendSubscriptionStatusChangeEmail($subscription, $newStatus)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/subscriptions_custom_email.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customer = $objectManager->get('Magento\Customer\Model\CustomerFactory')->create()->load($subscription->getData('customer_id'));
        $quote    = $objectManager->get('Magento\Quote\Model\QuoteFactory')->create()->load($subscription->getData('quote_id'));
        $items    = $quote->getAllItems();

        $productName = "";
        foreach ($quote->getAllItems() as $item) {
            $productName = $item->getName();
        }

        if($newStatus == 'paused')
        {
            $templateId = 45;
        }
        elseif($newStatus == 'active')
        {
            $templateId = 46;
        }
        elseif($newStatus == 'canceled')
        {
            $templateId = 45;  // canceled=paused
        }

        if($newStatus == 'paused')
        {
            $newStatus = 'Paused';
        }

        $name = $customer->getFirstname().' '.$customer->getLastname();
        $customer_email = $customer->getEmail();

        // ================ Send email code start ===============
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
                'customer_name'   => $name,
                'today_date'      => date('F d Y'),
                'status'          => $newStatus,
                'product_name'    => $productName
            ])
            ->setFrom($sender)
            ->addTo($customer_email)
            ->getTransport();

        
        try {
            $transport->sendMessage();
            
            // Text Message getting
            $templateObject    = $this->templateFactory->create()->load($templateId);
            $emailTextMessage  = $templateObject->getTemplateText();

            $trackLog = $this->creditCardExpiringModelFactory->create();
            $dataToSave = array('customer_email' => $customer_email, 'email_sent' => "yes", 'notification_type'=>'Status Change', 'email_message' => $emailTextMessage);
            $trackLog->setData($dataToSave);
            $trackLog->save();

            $logger->info('Status change email sent successfully.');

        } catch (\Exception $e) {
            $trackLog = $this->creditCardExpiringModelFactory->create();
            $dataToSave = array('customer_email' => $customer_email, 'email_sent' => "no", 'notification_type'=>'Status Change','email_message' => $e->getMessage());
            $trackLog->setData($dataToSave);
            $trackLog->save();

            $logger->info('Error while sending status change email '.$e->getMessage());
        }
        $this->inlineTranslation->resume();



    }
}

