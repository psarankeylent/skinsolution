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

namespace ParadoxLabs\Subscriptions\Observer\Payment;

/**
 * UnlinkDeletedCardObserver Class
 */
class UnlinkDeletedCardObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\Payment\CollectionFactory
     */
    protected $quotePaymentCollectionFactory;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\ResourceModel\Subscription\CollectionFactory
     */
    protected $subscriptionCollectionFactory;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Config
     */
    protected $config;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Source\Status|null
     */
    protected $statusSource;

    /**
     * UpdateQuotePaymentObserver constructor.
     *
     * @param \Magento\Quote\Model\ResourceModel\Quote\Payment\CollectionFactory $quotePaymentCollectionFactory
     * @param \ParadoxLabs\Subscriptions\Model\ResourceModel\Subscription\CollectionFactory $subscriptionColnFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \ParadoxLabs\Subscriptions\Model\Config $config
     * @param \ParadoxLabs\Subscriptions\Model\Source\Status|null $statusSource
     */
    public function __construct(
        \Magento\Quote\Model\ResourceModel\Quote\Payment\CollectionFactory $quotePaymentCollectionFactory,
        \ParadoxLabs\Subscriptions\Model\ResourceModel\Subscription\CollectionFactory $subscriptionColnFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \ParadoxLabs\Subscriptions\Model\Config $config,
        \ParadoxLabs\Subscriptions\Model\Source\Status $statusSource = null
    ) {
        $this->quotePaymentCollectionFactory = $quotePaymentCollectionFactory;
        $this->subscriptionCollectionFactory = $subscriptionColnFactory;
        $this->quoteRepository = $quoteRepository;
        $this->config = $config;
        // BC preservation -- argument added in 3.3.2
        $this->statusSource = $statusSource ?: \Magento\Framework\App\ObjectManager::getInstance()->get(
            \ParadoxLabs\Subscriptions\Model\Source\Status::class
        );
    }

    /**
     * Update subscription payment info on TokenBasecard save.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->config->moduleIsActive() !== true) {
            return;
        }

        /** @var \ParadoxLabs\TokenBase\Model\Card $card */
        $card = $observer->getEvent()->getData('object');

        if ($card instanceof \ParadoxLabs\TokenBase\Api\Data\CardInterface) {
            $this->checkCardSubscriptions($card);
        }
    }

    /**
     * Update quote_payment with any updated credit card information.
     *
     * @param \ParadoxLabs\TokenBase\Api\Data\CardInterface $card
     * @return void
     */
    public function checkCardSubscriptions(\ParadoxLabs\TokenBase\Api\Data\CardInterface $card)
    {
        /**
         * Load only subscriptions associated to the card in question, via quote payment records.
         */
        $payments = $this->quotePaymentCollectionFactory->create();
        $payments->addFieldToSelect('quote_id');
        $payments->addFieldToFilter('tokenbase_id', $card->getId());

        $quoteIds = $payments->getConnection()->fetchCol($payments->getSelect());

        if (empty($quoteIds)) {
            return;
        }

        /** @var \ParadoxLabs\Subscriptions\Model\ResourceModel\Subscription\Collection $subscriptions */
        $subscriptions = $this->subscriptionCollectionFactory->create();
        $subscriptions->addFieldToSelect('increment_id');
        $subscriptions->addFieldToFilter('status', ['nin' => $this->statusSource->getFinalStatuses()]);
        $subscriptions->addFieldToFilter('quote_id', ['in' => $quoteIds]);

        $subscriptionIds = $subscriptions->getColumnValues('increment_id');

        if (!empty($subscriptionIds)) {
            if (count($subscriptionIds) === 1) {
                $subText = __('subscription %1', current($subscriptionIds));
            } elseif (count($subscriptionIds) === 2) {
                $subText = __('subscriptions %1 and %2', $subscriptionIds[0], $subscriptionIds[1]);
            } else {
                $subText = __('subscriptions %1', implode(', ', $subscriptionIds));
            }

            throw new \Magento\Framework\Exception\StateException(
                __(
                    'Unable to remove %1: This payment is being used for %2. '
                    . 'Please update the subscription payment details, then try again.',
                    $card->getLabel(),
                    $subText
                )
            );
        }
    }
}
