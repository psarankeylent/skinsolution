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

namespace ParadoxLabs\Subscriptions\Model\Service;

/**
 * Subscription service model: Common actions to be performed on subscriptions.
 *
 * @api
 */
class Subscription
{
    /**
     * @var \ParadoxLabs\TokenBase\Api\CardRepositoryInterface
     */
    protected $cardRepository;

    /**
     * @var \Magento\Framework\DataObject\Copy
     */
    protected $objectCopyService;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Quote\Model\QuoteManagement
     */
    protected $quoteManagement;

    /**
     * @var \Magento\Quote\Api\Data\CartInterfaceFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Quote\Api\Data\AddressInterfaceFactory
     */
    protected $quoteAddressFactory;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $customerAddressRepository;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Magento\Sales\Model\Order\Email\Sender\OrderSender
     */
    protected $orderSender;
    
    /**
     * @var \ParadoxLabs\Subscriptions\Helper\Data
     */
    protected $helper;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Service\EmailSender
     */
    protected $emailSender;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $emulator;

    /**
     * @var \ParadoxLabs\Subscriptions\Helper\Vault
     */
    protected $vaultHelper;

    /**
     * @var \ParadoxLabs\Subscriptions\Api\SubscriptionRepositoryInterface
     */
    protected $subscriptionRepository;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $dateProcessor;

    /**
     * @var \Magento\Framework\App\ProductMetadata
     */
    protected $productMetadata;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Service\QuoteManager
     */
    protected $quoteManager;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Service\CurrencyManager
     */
    protected $currencyManager;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Service\Payment
     */
    protected $paymentService;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Source\Status
     */
    protected $statusSource;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Service\ItemManager
     */
    protected $itemManager;

    /**
     * Subscription service constructor.
     *
     * @param \ParadoxLabs\Subscriptions\Model\Service\Subscription\Context $context
     */
    public function __construct(
        \ParadoxLabs\Subscriptions\Model\Service\Subscription\Context $context
    ) {
        $this->registry = $context->getRegistry();
        $this->objectCopyService = $context->getObjectCopyService();
        $this->cardRepository = $context->getCartRepository();
        $this->quoteRepository = $context->getCartRepository();
        $this->quoteManagement = $context->getQuoteManagement();
        $this->quoteFactory = $context->getQuoteFactory();
        $this->quoteAddressFactory = $context->getQuoteAddressFactory();
        $this->customerRepository = $context->getCustomerRepository();
        $this->customerAddressRepository = $context->getCustomerAddressRepository();
        $this->eventManager = $context->getEventManager();
        $this->orderSender = $context->getOrderSender();
        $this->logger = $context->getLogger();
        $this->helper = $context->getHelper();
        $this->emailSender = $context->getEmailSender();
        $this->emulator = $context->getEmulator();
        $this->vaultHelper = $context->getVaultHelper();
        $this->subscriptionRepository = $context->getSubscriptionRepository();
        $this->dateProcessor = $context->getDateProcessor();
        $this->productMetadata = $context->getProductMetadata();
        $this->quoteManager = $context->getQuoteManager();
        $this->currencyManager = $context->getCurrencyManager();
        $this->paymentService = $context->getPaymentService();
        $this->statusSource = $context->getStatusSource();
        $this->itemManager = $context->getItemManager();
    }

    /**
     * Change subscription payment account to the given card.
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription
     * @param string $hash Card hash owned by the subscription customer
     * @param array $paymentData
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function changePaymentId(
        \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription,
        $hash,
        $paymentData = []
    ) {
        /** @var \ParadoxLabs\Subscriptions\Model\Subscription $subscription */

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $subscription->getQuote();
        $card = $this->paymentService->getCardByHash($hash);

        try {
            $quoteCard = $this->paymentService->getQuoteCard($quote);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            // An exception here means the existing card was deleted. That's enough to know to process the change.
        }

        if (!isset($quoteCard) || $card->getPublicHash() !== $quoteCard->getPublicHash()) {
            if ($card instanceof \Magento\Vault\Api\Data\PaymentTokenInterface
                && ($card->getCustomerId() == $subscription->getCustomerId()
                    || $card instanceof \ParadoxLabs\Subscriptions\Model\OfflinePayment\Card)) {
                $this->paymentService->updatePayment($quote, $card, $paymentData);

                $subscription->addRelatedObject($quote, true);

                $subscription->addLog(
                    __('Payment method changed to %1.', $this->vaultHelper->getCardLabel($card))
                );
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Invalid payment ID.')
                );
            }
        } elseif ($card instanceof \ParadoxLabs\Subscriptions\Model\OfflinePayment\Card) {
            // Handle offline payment changes when method itself has not changed
            $this->paymentService->updatePayment($quote, $card, $paymentData);
            $subscription->addRelatedObject($quote, true);
        }

        return $this;
    }

    /**
     * Change subscription billing address to the given data.
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription
     * @param array $data Array of address info
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function changeBillingAddress(
        \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription,
        $data
    ) {
        $this->changeAddress(
            $subscription,
            $subscription->getQuote()->getBillingAddress(),
            $data
        );

        return $this;
    }

    /**
     * Change subscription shipping address to the given data.
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription
     * @param array $data Array of address info
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function changeShippingAddress(
        \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription,
        $data
    ) {
        $this->changeAddress(
            $subscription,
            $subscription->getQuote()->getShippingAddress(),
            $data
        );

        return $this;
    }

    /**
     * Change the given subscription address to match the given data.
     *
     * Note targetAddress is expected to already be assigned as the subscription quote's billing or shipping address.
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription
     * @param \Magento\Quote\Api\Data\AddressInterface $targetAddress
     * @param array $newAddressData
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function changeAddress(
        \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription,
        \Magento\Quote\Api\Data\AddressInterface $targetAddress,
        array $newAddressData
    ) {
        /** @var \Magento\Quote\Model\Quote\Address $targetAddress */
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $subscription->getQuote();

        if (isset($newAddressData['address_id'])
            && $newAddressData['address_id'] > 0
            && $subscription->getCustomerId() > 0) {
            $customer = $this->customerRepository->getById($subscription->getCustomerId());

            if ($customer->getId() != $subscription->getCustomerId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(
                    __('Unable to load subscription customer.')
                );
            }

            $customerAddress = $this->customerAddressRepository->getById($newAddressData['address_id']);

            if ($customerAddress instanceof \Magento\Customer\Api\Data\AddressInterface
                && $customerAddress->getId() == $newAddressData['address_id']
                && $customerAddress->getCustomerId() == $customer->getId()) {
                $targetAddress->importCustomerAddressData($customerAddress);
            } else {
                throw new \Magento\Framework\Exception\NoSuchEntityException(
                    __('Please choose a valid address.')
                );
            }
        } else {
            $this->objectCopyService->copyFieldsetToTarget(
                'sales_convert_customer_address',
                'to_quote_address',
                $newAddressData,
                $targetAddress
            );
        }

        $modifiedAddressFields = $targetAddress->getData() ?: [];
        foreach ($modifiedAddressFields as $key => $value) {
            if ($this->shouldSkipAddressField($key) === false
                && !is_object($value)
                && $targetAddress->getOrigData($key) != $value) {
                $this->emulator->startEnvironmentEmulation(
                    $subscription->getStoreId(),
                    \Magento\Framework\App\Area::AREA_FRONTEND,
                    true
                );

                $quote->collectTotals();
                $quote->setTriggerRecollect(0);

                $targetAddress->validate();

                if ($targetAddress->getAddressType() === \Magento\Quote\Model\Quote\Address::ADDRESS_TYPE_SHIPPING) {
                    $targetAddress->setCollectShippingRates(true)
                                  ->collectShippingRates();
                }

                $this->emulator->stopEnvironmentEmulation();

                $subscription->setSubtotal($this->quoteManager->getSubscriptionSubtotal($quote));
                $subscription->addLog(
                    __(ucfirst($targetAddress->getAddressType()) . ' address changed.')
                );

                $subscription->addRelatedObject($quote, true);

                break;
            }
        }

        return $this;
    }

    /**
     * Change subscription shipping method to the given code. Must be an available method.
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription
     * @param string $methodCode
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function changeShippingMethod(
        \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription,
        $methodCode
    ) {
        /** @var \ParadoxLabs\Subscriptions\Model\Subscription $subscription */

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $subscription->getQuote();
        $shippingAddress = $quote->getShippingAddress();

        if ($methodCode !== $shippingAddress->getShippingMethod()) {
            $rate = $shippingAddress->getShippingRateByCode($methodCode);
            if ($rate === false) {
                $quote           = $this->recollectQuoteShippingRates($quote);
                $shippingAddress = $quote->getShippingAddress();
                $rate            = $shippingAddress->getShippingRateByCode($methodCode);
            }
            if ($rate instanceof \Magento\Quote\Model\Quote\Address\Rate) {
                $shippingAddress->setShippingMethod($rate->getCode());
                $shippingAddress->setShippingDescription($rate->getMethodDescription());

                $subscription->addLog(
                    __('Shipping method changed to %1 - %2.', $rate->getCarrierTitle(), $rate->getMethodTitle())
                );

                $subscription->addRelatedObject($quote, true);
            } else {
                throw new \Magento\Framework\Exception\NoSuchEntityException(
                    __('Please choose a valid shipping method.')
                );
            }
        }

        return $this;
    }

    /**
     * Generate a hash from fulfillment details (billing, shipping, payment) for the given subscription.
     *
     * Used for identifying subscriptions that can be merged and billed together.
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription
     * @return string
     */
    public function hashFulfillmentInfo(\ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription)
    {
        $keys  = [];

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $subscription->getQuote();

        // Customer
        $keys['customer_id'] = $subscription->getCustomerId();

        // Store
        $keys['store_id'] = $subscription->getStoreId();

        // Payment
        try {
            $keys['payment_account'] = $this->paymentService->getQuoteCard($quote)->getPublicHash();
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            // No-op: Let missing payment info pass through to fail on generation and trigger proper error handling.
        }

        // Shipping
        if ((bool)$quote->getIsVirtual() === false) {
            $shippingAddr = $quote->getShippingAddress();
            $shippingKeys = [
                'shipping_method',
                'street',
                'city',
                'region',
                'region_id',
                'postcode',
                'country_id',
            ];

            foreach ($shippingKeys as $key) {
                $keys[$key] = $shippingAddr->getData($key);
            }
        }

        // Fire an event off to allow modifying the hash info for grouping.
        $transport = new \Magento\Framework\DataObject($keys);

        $this->eventManager->dispatch(
            'paradoxlabs_subscription_billing_hash_fulfillment_info',
            [
                'subscription' => $subscription,
                'service'      => $this,
                'transport'    => $transport,
            ]
        );

        return hash('sha256', implode('-', $transport->getData()));
    }

    /**
     * Generate order for the given subscription(s). If multiple given, they should all share the same
     * payment and shipping info.
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface[] $subscriptions
     * @return bool Success
     */
    public function generateOrder($subscriptions)
    {
        /**
         * This wrapper function manages error handling and emulation.
         */

        /** @var \ParadoxLabs\Subscriptions\Model\Subscription $firstSubscription */
        $firstSubscription = current($subscriptions);

        $this->emulator->startEnvironmentEmulation(
            $firstSubscription->getStoreId(),
            \Magento\Framework\App\Area::AREA_FRONTEND,
            true
        );

        try {
            foreach ($subscriptions as $subscription) {
                $this->checkSubscriptionBillable($subscription);
            }

            $this->generateOrderInternal($subscriptions);

            return true;
        } catch (\Throwable $e) {
            $this->handleSubscriptionsError($subscriptions, $e);
        }

        $this->emulator->stopEnvironmentEmulation();

        return false;
    }

    /**
     * Generate order for the given subscription(s). If multiple given, they should all share the same
     * payment and shipping info.
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface[] $subscriptions
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function generateOrderInternal($subscriptions)
    {
        /**
         * Initialize quote from first subscription
         */
        $quote = $this->generateBillingQuote(
            current($subscriptions)
        );

        /**
         * Add item(s) from each subscription
         */
        /** @var \ParadoxLabs\Subscriptions\Model\Subscription $subscription */
        foreach ($subscriptions as $k => $subscription) {
            /** @var \Magento\Quote\Model\Quote $subscriptionQuote */
            $subscriptionQuote = $subscription->getQuote();

            try {
                // Attach subscription object to each quote item individually, for later reference (as needed).
                foreach ($subscriptionQuote->getAllItems() as $item) {
                    $item->setData('subscription', $subscription);

                    $this->itemManager->getIntervalModel($item);
                }

                // Check availability
                $this->checkStock($subscriptionQuote);

                // Merge item(s) onto the new quote.
                $quote->merge($subscriptionQuote);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                // Product is out of stock, disabled, or deleted. Mark it and move on.
                $this->handleSubscriptionsError([$subscription], $e);
                $this->subscriptionRepository->save($subscription);
                unset($subscriptions[$k]);
            }
        }

        /**
         * Calculate shipping and totals
         */
        $this->eventManager->dispatch(
            'paradoxlabs_subscription_collect_totals_before',
            [
                'quote'         => $quote,
                'subscriptions' => $subscriptions,
            ]
        );

        $this->quoteManager->setQuoteShippingMethod(
            $quote,
            $quote->getShippingAddress()->getShippingMethod(),
            $quote->getShippingAddress()->getShippingDescription()
        );

        // Pull the new shipping amount (if any) into totals.
        $quote->setTotalsCollectedFlag(false)
              ->collectTotals()
              ->setTriggerRecollect(0);

        if ($quote->hasItems() !== true) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Subscription does not contain any items to bill.')
            );
        }

        /**
         * Run the order
         */
        $this->eventManager->dispatch(
            'paradoxlabs_subscription_generate_before',
            [
                'quote'         => $quote,
                'subscriptions' => $subscriptions,
            ]
        );

        // This event allows for soft dependencies on payment methods (module won't be referenced unless used).
        $this->eventManager->dispatch(
            'paradoxlabs_subscription_prepare_payment_' . $quote->getPayment()->getMethod(),
            [
                'quote'         => $quote,
                'subscriptions' => $subscriptions,
            ]
        );

        $this->quoteRepository->save($quote);

        $ids = [];
        foreach ($subscriptions as $subscription) {
            $ids[] = $subscription->getIncrementId();
        }

        $this->helper->log(
            'subscriptions',
            __('Placing generateOrderInternal([%1])', implode(',', $ids))
        );

        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->quoteManagement->submit($quote);

        if (!($order instanceof \Magento\Sales\Api\Data\OrderInterface)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Failed to place order.')
            );
        }

        /**
         * Update post-order
         */
        $message = __(
            'Subscription billed. Order total: %1',
            $order->formatPriceTxt($order->getGrandTotal())
        );

        foreach ($subscriptions as $subscription) {
            $subscription->recordBilling($order, $message);
            $subscription->calculateNextRun();
        }

        $this->eventManager->dispatch(
            'paradoxlabs_subscription_generate_after',
            [
                'order'         => $order,
                'quote'         => $quote,
                'subscriptions' => $subscriptions,
            ]
        );

        foreach ($subscriptions as $subscription) {
            $this->subscriptionRepository->save($subscription);
        }

        $this->eventManager->dispatch(
            'paradoxlabs_subscription_generate_save_after',
            [
                'order'         => $order,
                'quote'         => $quote,
                'subscriptions' => $subscriptions,
            ]
        );

        /**
         * Send email
         */
        if ($order->getCanSendNewEmailFlag()) {
            try {
                $this->orderSender->send($order);
            } catch (\Throwable $e) {
                $this->logger->critical($e);
            }
        }

        return $this;
    }

    /**
     * Generate a new quote from the given subscription info.
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription
     * @return \Magento\Quote\Model\Quote
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function generateBillingQuote(\ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription)
    {
        /**
         * Initialize objects
         */

        /** @var \Magento\Quote\Model\Quote $sourceQuote */
        $sourceQuote = $subscription->getQuote();

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteFactory->create();

        $this->objectCopyService->copyFieldsetToTarget(
            'sales_convert_quote',
            'to_subscription_quote',
            $sourceQuote,
            $quote
        );

        /**
         * Duplicate billing address
         */

        /** @var \Magento\Quote\Model\Quote\Address $billingAddress */
        $billingAddress = $this->quoteAddressFactory->create();

        $this->objectCopyService->copyFieldsetToTarget(
            'sales_copy_order_billing_address',
            'to_order',
            $sourceQuote->getBillingAddress(),
            $billingAddress
        );

        $billingAddress->setCustomerId($sourceQuote->getBillingAddress()->getCustomerId());

        // Prevent 'no such address' errors if the address was deleted, but still keep the association if valid.
        if ($billingAddress->getCustomerAddressId()) {
            try {
                $billingAddress->importCustomerAddressData(
                    $this->customerAddressRepository->getById($billingAddress->getCustomerAddressId())
                );
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $billingAddress->setCustomerAddressId(null);
            }
        }

        /**
         * Duplicate shipping address
         */

        /** @var \Magento\Quote\Model\Quote\Address $shippingAddress */
        $shippingAddress = $this->quoteAddressFactory->create();

        $this->objectCopyService->copyFieldsetToTarget(
            'sales_copy_order_shipping_address',
            'to_order',
            $sourceQuote->getShippingAddress(),
            $shippingAddress
        );

        $shippingAddress->setShippingMethod($sourceQuote->getShippingAddress()->getShippingMethod())
                        ->setShippingDescription($sourceQuote->getShippingAddress()->getShippingDescription())
                        ->setCustomerId($sourceQuote->getShippingAddress()->getCustomerId());

        // Prevent 'no such address' errors if the address was deleted, but still keep the association if valid.
        if ($shippingAddress->getCustomerAddressId()) {
            try {
                $shippingAddress->importCustomerAddressData(
                    $this->customerAddressRepository->getById($shippingAddress->getCustomerAddressId())
                );
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $shippingAddress->setCustomerAddressId(null);
            }
        }

        /**
         * Duplicate payment object
         */

        $this->objectCopyService->copyFieldsetToTarget(
            'sales_convert_order_payment',
            'to_quote_payment',
            $sourceQuote->getPayment(),
            $quote->getPayment()
        );

        $this->copyTokenbaseId($sourceQuote, $quote);

        $quote->getPayment()->setId(null);
        $quote->getPayment()->setQuoteId(null);

        // Record the quote/order source to prevent a generation loop
        $this->quoteManager->setQuoteExistingSubscription($quote);

        // Force quote currency. Normally this gets pulled from the customer's session.
        // NB: Quote checks for 'forced_currency', but that's not implemented fully. Doesn't cover item collection.
        $quoteCurrency = $this->currencyManager->getCurrencyByCode($quote->getQuoteCurrencyCode());
        $quote->getStore()->setData('current_currency', $quoteCurrency);

        /**
         * Duplicate customer info
         */
        $customerId = $subscription->getCustomerId();

        if ($customerId > 0) {
            try {
                $customer = $this->customerRepository->getById($customerId);

                $quote->assignCustomer($customer);
            } catch (\Exception $e) {
                // Ignore missing customer error -- guest data was copied in sales_convert_quote
            }
        }

        /**
         * Pull quote together
         */

        $now = $this->dateProcessor->date(null, null, false);

        $quote->setIsMultiShipping(false)
              ->setIsActive(false)
              ->setUpdatedAt($now->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT))
              ->setBillingAddress($billingAddress)
              ->setShippingAddress($shippingAddress);

        return $quote;
    }

    /**
     * Run stock checks on our generated quote's items.
     *
     * We have to do this because Magento bypasses it on quote merge.
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function checkStock(\Magento\Quote\Model\Quote $quote)
    {
        $errors = [];
        $itemCount = 0;

        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($quote->getAllItems() as $item) {
            $item->setQty($item->getQty());

            if ($item->getProduct() instanceof \Magento\Catalog\Model\Product) {
                $item->getProduct()->setHasError(false);
            }
            $item->checkData();

            $itemCount++;

            if ($item->getHasError()
                || $item->getProduct() instanceof \Magento\Catalog\Model\Product === false
                || (bool)$item->getProduct()->isSalable() === false) {
                $message = $item->getMessage() ?: __('Item is not currently available for purchase.');
                if (!in_array($message, $errors) && !empty($message)) {
                    // filter duplicate messages
                    $errors[] = $message;
                }
            }
        }

        if (!empty($errors)) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __(implode("\n", $errors))
            );
        }
        if ($itemCount === 0) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Subscription does not contain any items to bill.')
            );
        }

        return $this;
    }

    /**
     * Handle exceptions from the subscription generation process.
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface[] $subscriptions
     * @param \Throwable $exception
     * @return $this
     */
    protected function handleSubscriptionsError($subscriptions, \Throwable $exception)
    {
        try {
            $ids = [];
            foreach ($subscriptions as $subscription) {
                if (in_array($subscription->getStatus(), $this->statusSource->getBillableStatuses(), true)) {
                    $ids[] = $subscription->getIncrementId();
                }
            }

            if (empty($ids)) {
                // If all subs in the group already failed separately (EG, out of stock), don't process further
                return $this;
            }

            $this->helper->log(
                'subscriptions',
                __('Error on generateOrder([%1]): %2', implode(',', $ids), (string)$exception)
            );

            $this->eventManager->dispatch(
                'paradoxlabs_subscription_billing_failed',
                [
                    'subscriptions' => $subscriptions,
                    'exception'     => $exception,
                ]
            );

            if ($this->isPaymentException($exception)) {
                $this->changeSubscriptionsStatus(
                    $subscriptions,
                    \ParadoxLabs\Subscriptions\Model\Source\Status::STATUS_PAYMENT_FAILED,
                    (string)__('ERROR: %1', $exception->getMessage())
                );

                $this->sendPaymentFailedEmail($subscriptions, $exception);
            } else {
                $this->changeSubscriptionsStatus(
                    $subscriptions,
                    \ParadoxLabs\Subscriptions\Model\Source\Status::STATUS_PAUSED,
                    (string)__('ERROR: %1', $exception->getMessage())
                );
            }

            $this->sendBillingFailedEmail($subscriptions, $exception);
        } catch (\Throwable $e) {
            $this->helper->log(
                'subscriptions',
                __('Error while handling "%1": %2', $exception->getMessage(), (string)$e)
            );
        }

        return $this;
    }

    /**
     * Send billing failure email to admin
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface[] $subscriptions
     * @param \Throwable $exception
     * @return $this
     */
    public function sendBillingFailedEmail($subscriptions, \Throwable $exception)
    {
        foreach ($subscriptions as $subscription) {
            $this->emailSender->sendBillingFailedEmail($subscription, $exception->getMessage());
        }

        return $this;
    }

    /**
     * Send payment failure email to customer
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface[] $subscriptions
     * @param \Throwable $exception
     * @return $this
     */
    public function sendPaymentFailedEmail($subscriptions, \Throwable $exception)
    {
        foreach ($subscriptions as $subscription) {
            $this->emailSender->sendPaymentFailedEmail($subscription, $exception->getMessage());
        }

        return $this;
    }

    /**
     * Set status for the given subscriptions, and log the change.
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface[] $subscriptions
     * @param string $status
     * @param null $message
     * @return $this
     */
    public function changeSubscriptionsStatus($subscriptions, $status, $message = null)
    {
        foreach ($subscriptions as $subscription) {
            $subscription->setStatus($status, $message);
            $this->subscriptionRepository->save($subscription);
        }

        return $this;
    }

    /**
     * Determine whether the given address field should be skipped when evaluating shipping address for changes.
     *
     * @param string $field
     * @return bool
     */
    protected function shouldSkipAddressField($field)
    {
        $includeFields = [
            'firstname',
            'lastname',
            'company',
            'telephone',
            'street',
            'city',
            'region_id',
            'postcode',
            'country_id',
            'fax',
            'vat_id',
        ];

        return in_array($field, $includeFields, true) === false;
    }

    /**
     * Determine whether the given exception is considered a payment exception (user-fixable, ideally).
     *
     * @param \Throwable $exception
     * @return bool
     */
    protected function isPaymentException(\Throwable $exception)
    {
        return $exception instanceof \Magento\Framework\Exception\PaymentException
            || $exception instanceof \Magento\Payment\Gateway\Http\ClientException
            || $exception instanceof \Magento\Payment\Gateway\Command\CommandException
            || $exception instanceof \Magento\Paypal\Model\Api\ProcessableException;
    }

    /**
     * Handle 2.3+ fieldset/extension attribute funkiness and ensure tokenbase_id gets set on the subscription.
     *
     * @param \Magento\Quote\Model\Quote $source
     * @param \Magento\Quote\Model\Quote $target
     * @return void
     */
    protected function copyTokenbaseId(
        \Magento\Quote\Model\Quote $source,
        \Magento\Quote\Model\Quote $target
    ) {
        /** @var \Magento\Quote\Api\Data\PaymentExtensionInterface $sourcePaymentExtnAttr */
        $sourcePaymentExtnAttr = $source->getPayment()->getExtensionAttributes();
        $tokenbaseId           = $source->getPayment()->getData('tokenbase_id');
        if ($sourcePaymentExtnAttr instanceof \Magento\Quote\Api\Data\PaymentExtensionInterface
            && !empty($sourcePaymentExtnAttr->getTokenbaseId())
            && empty($tokenbaseId)) {
            $tokenbaseId = $sourcePaymentExtnAttr->getTokenbaseId();
        }

        $target->getPayment()->setData('tokenbase_id', $tokenbaseId);

        /** @var \Magento\Quote\Api\Data\PaymentExtensionInterface $targetPaymentExtnAttr */
        $targetPaymentExtnAttr = $target->getPayment()->getExtensionAttributes();
        if ($targetPaymentExtnAttr instanceof \Magento\Quote\Api\Data\PaymentExtensionInterface) {
            $targetPaymentExtnAttr->setTokenbaseId($tokenbaseId);
        }
    }

    /**
     * Check whether the given subscription is eligible for billing.
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription
     * @return void
     * @throws \Magento\Framework\Exception\StateException
     */
    public function checkSubscriptionBillable(
        \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription
    ) {
        if (in_array($subscription->getStatus(), $this->statusSource->getBillableStatuses(), true) === false) {
            throw new \Magento\Framework\Exception\StateException(
                __(
                    "Subscriptions may only be billed in the '%1' status. #%2 has status '%3'.",
                    \ParadoxLabs\Subscriptions\Model\Source\Status::STATUS_ACTIVE,
                    $subscription->getIncrementId(),
                    $subscription->getStatus()
                )
            );
        }
    }

    /**
     * Recollect shipping rates
     *
     * @param $quote
     * @return mixed
     */
    protected function recollectQuoteShippingRates($quote)
    {
        $quote->collectTotals();
        $quote->getShippingAddress()->setCollectShippingRates(true)
              ->collectShippingRates();

        return $quote;
    }
}
