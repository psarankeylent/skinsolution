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

namespace ParadoxLabs\Subscriptions\Controller\Subscriptions;

use Magento\Customer\Model\Session;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use ParadoxLabs\Subscriptions\Model\Source\Status;

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
     * @var Status
     */
    protected $statusSource;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Config
     */
    protected $config;

    /**
     * ChangeStatus constructor.
     *
     * @param Context $context
     * @param Session $customerSession *Proxy
     * @param PageFactory $resultPageFactory
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Framework\Registry $registry
     * @param \ParadoxLabs\TokenBase\Model\CardFactory $cardFactory
     * @param \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository
     * @param \ParadoxLabs\TokenBase\Helper\Data $helper
     * @param \ParadoxLabs\TokenBase\Helper\Address $addressHelper
     * @param \ParadoxLabs\Subscriptions\Api\SubscriptionRepositoryInterface $subscriptionRepository
     * @param \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer *Proxy
     * @param \ParadoxLabs\Subscriptions\Model\Config $config
     * @param Status $statusSource
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        PageFactory $resultPageFactory,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\Registry $registry,
        \ParadoxLabs\TokenBase\Model\CardFactory $cardFactory,
        \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository,
        \ParadoxLabs\TokenBase\Helper\Data $helper,
        \ParadoxLabs\TokenBase\Helper\Address $addressHelper,
        \ParadoxLabs\Subscriptions\Api\SubscriptionRepositoryInterface $subscriptionRepository,
        \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer,
        \ParadoxLabs\Subscriptions\Model\Config $config,
        Status $statusSource,
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        StateInterface $state,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $resultPageFactory,
            $formKeyValidator,
            $registry,
            $cardFactory,
            $cardRepository,
            $helper,
            $addressHelper,
            $subscriptionRepository,
            $currentCustomer
        );

        $this->statusSource = $statusSource;
        $this->config = $config;
        $this->scopeConfig = $scopeConfig;
        $this->inlineTranslation = $state;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * Subscription status-change action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/subscriptions_change_status.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $logger->info("Calling file.");

        $initialized    = $this->initModels();
        $resultRedirect = $this->resultRedirectFactory->create();

        /**
         * If we were not able to load the model, short-circuit.
         */
        if ($initialized !== true || $this->formKeyIsValid() !== true) {
            $resultRedirect->setPath('*/*/index');
            return $resultRedirect;
        }
        $logger->info("Calling file again.");
        /** @var \ParadoxLabs\Subscriptions\Model\Subscription $subscription */
        $subscription = $this->registry->registry('current_subscription');

        try {
            $newStatus = $this->getAndValidateStatus();

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

            $resultRedirect->setPath('*/*/view', ['id' => $subscription->getId(), '_current' => true]);
        } catch (\Throwable $e) {
            $this->helper->log('subscriptions', (string)$e);
            $this->messageManager->addErrorMessage(__('ERROR: %1', $e->getMessage()));

            $resultRedirect->setPath('*/*/view', ['id' => $subscription->getId(), '_current' => true]);
        }

        return $resultRedirect;
    }

    /**
     * Get the new status to be set, and make sure we actually have permission to do so.
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getAndValidateStatus()
    {
        /** @var \ParadoxLabs\Subscriptions\Model\Subscription $subscription */
        $subscription = $this->registry->registry('current_subscription');

        $newStatus = $this->getRequest()->getParam('status');

        /**
         * Check whether we are allowed to make this change.
         */
        if ($this->statusSource->canSetStatusAsCustomer($subscription, $newStatus) !== true) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Invalid status request.'));
        }

        return $newStatus;
    }

    public function sendSubscriptionStatusChangeEmail($subscription, $newStatus)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/subscriptions_custom_email.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        try{

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
                $templateId = 1000045;
            }
            elseif($newStatus == 'active')
            {
                $templateId = 1000046;
            }
            elseif($newStatus == 'canceled')
            {
                $templateId = 1000047;
            }
            $templateVars = [
                'customer_name'   => $customer->getFirstname().' '.$customer->getLastname(),
                'today_date'      => date('F d Y'),
                'status'          => $newStatus,
                'product_name'    => $productName
            ];


            $fromEmail = $this->scopeConfig->getValue("trans_email/ident_sales/email",\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $fromName = $this->scopeConfig->getValue("trans_email/ident_sales/name",\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $toEmail = $customer->getEmail();
            $storeId = $this->storeManager->getStore()->getStoreId();
            $from = ['email' => $fromEmail, 'name' => $fromName];

            $this->inlineTranslation->suspend();
            $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
            $templateOptions = [
                'area' => \Magento\Framework\App\Area::AREA_ADMINHTML,
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
            $logger->info('Subscription email sent successfully.');

        }catch(\Exception $e){
            $logger->info('Error while sending email '.$e->getMessage());
        }

    }
}

