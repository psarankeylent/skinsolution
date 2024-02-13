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
        \ParadoxLabs\Subscriptions\Model\Service\ItemManager $itemManager = null
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
        $this->dateProcessor = $dateProcessor;
        // BC preservation -- arguments added in 3.4.0
        $this->itemManager = $itemManager ?: \Magento\Framework\App\ObjectManager::getInstance()->get(
            \ParadoxLabs\Subscriptions\Model\Service\ItemManager::class
        );
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

        try {
            $data         = $this->getRequest()->getParams();
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
