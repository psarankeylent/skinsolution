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

namespace ParadoxLabs\Subscriptions\Observer;

use \ParadoxLabs\Subscriptions\Model\Source\Status;

// Email notification
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;

/**
 * GenerateSubscriptionsObserver Class
 */
class GenerateSubscriptionsObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \ParadoxLabs\Subscriptions\Helper\Data
     */
    protected $helper;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\SubscriptionFactory
     */
    protected $subscriptionFactory;

    /**
     * @var \Magento\Quote\Api\Data\CartInterfaceFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Quote\Api\Data\AddressInterfaceFactory
     */
    protected $quoteAddressFactory;

    /**
     * @var \Magento\Framework\DataObject\Copy
     */
    protected $objectCopyService;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

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
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $emulator;

    /**
     * @var \Magento\Framework\App\ProductMetadata
     */
    protected $productMetadata;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Service\QuoteManager
     */
    protected $quoteManager;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Service\ItemManager
     */
    protected $itemManager;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Config
     */
    protected $config;

    /**
     * @var \Magento\Sales\Api\OrderCustomerManagementInterface
     */
    protected $orderCustomerManager;

    /**
     * @var \ParadoxLabs\TokenBase\Helper\Data
     */
    protected $tokenbaseHelper;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Service\Payment
     */
    protected $paymentService;

    /**
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * GenerateSubscriptionsObserver constructor.
     *
     * @param \ParadoxLabs\Subscriptions\Observer\GenerateSubscriptionsObserver\Context $context
     */
    public function __construct(
        \ParadoxLabs\Subscriptions\Observer\GenerateSubscriptionsObserver\Context $context,
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        StateInterface $state,
        \CreditCard\Expiring\Model\CreditCardExpiringModelFactory $creditCardExpiringModelFactory,
        \ParadoxLabs\TokenBase\Model\CardFactory $cardFactory,
        \Magento\Email\Model\TemplateFactory $templateFactory
    ) {
        $this->helper = $context->getHelper();
        $this->subscriptionFactory = $context->getSubscriptionFactory();
        $this->quoteFactory = $context->getQuoteFactory();
        $this->quoteAddressFactory = $context->getQuoteAddressFactory();
        $this->customerRepository = $context->getCustomerRepository();
        $this->objectCopyService = $context->getObjectCopyService();
        $this->vaultHelper = $context->getVaultHelper();
        $this->subscriptionRepository = $context->getSubscriptionRepository();
        $this->dateProcessor = $context->getDateProcessor();
        $this->cartRepository = $context->getCartRepository();
        $this->orderRepository = $context->getOrderRepository();
        $this->storeManager = $context->getStoreManager();
        $this->emulator = $context->getEmulator();
        $this->productMetadata = $context->getProductMetadata();
        $this->quoteManager = $context->getQuoteManager();
        $this->itemManager = $context->getItemManager();
        $this->config = $context->getConfig();
        $this->orderCustomerManager = $context->getOrderCustomerManager();
        $this->tokenbaseHelper = $context->getTokenbaseHelper();
        $this->customerSession = $context->getCustomerSession();
        $this->paymentService = $context->getPaymentService();
        $this->eventManager = $context->getEventManager();
        $this->scopeConfig = $scopeConfig;
        $this->inlineTranslation = $state;
        $this->transportBuilder = $transportBuilder;
        $this->creditCardExpiringModelFactory = $creditCardExpiringModelFactory;
        $this->cardFactory = $cardFactory;
        $this->templateFactory = $templateFactory;
    }

    /**
     * Create subscriptions as needed on order place.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @throws \Exception
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->config->moduleIsActive() !== true) {
            return;
        }

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getData('quote');

        // Don't process any further unless a subscription was ordered.
        if ($this->quoteManager->quoteContainsSubscription($quote) === false) {
            return;
        }

        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getData('order');

        if ($order instanceof \Magento\Sales\Api\Data\OrderInterface) {
            // Standard checkouts
            $this->processOrder($order);
        } else {
            // Multishipping checkout ... multiple orders
            $orders = $observer->getEvent()->getData('orders');

            if (count($orders)) {
                foreach ($orders as $order) {
                    if ($order instanceof \Magento\Sales\Api\Data\OrderInterface) {
                        $this->processOrder($order);
                    }
                }
            }
        }
    }

    /**
     * Create subscriptions for any subscription items purchased in the given order.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return void
     * @throws \Exception
     */
    public function processOrder(
        \Magento\Sales\Api\Data\OrderInterface $order
    ) {
        /** @var \Magento\Sales\Model\Order $order */
        /** @var \Magento\Sales\Model\Order\Payment $payment */
        $payment = $order->getPayment();

        // Ensure we don't end up generating new subscriptions from existing ones.
        if ((int)$payment->getAdditionalInformation('is_subscription_generated') === 1) {
            return;
        }

        // If we are not in the correct scope, emulate it to ensure everything comes out correct.
        $emulate = ((int)$this->storeManager->getStore()->getStoreId() !== (int)$order->getStoreId());
        if ($emulate === true) {
            $this->emulator->startEnvironmentEmulation(
                $order->getStoreId(),
                \Magento\Framework\App\Area::AREA_FRONTEND,
                true
            );
        }

        // Ensure customer is registered
        $this->registerCustomer($order);

        /** @var \Magento\Sales\Model\Order\Item $item */
        foreach ($order->getAllItems() as $item) {
            if ($this->itemManager->isSubscription($item) === true) {
                /**
                 * For each active subscription item,
                 * Create a matching quote
                 * Initialize an associated subscription
                 */

                try {
                    $subscription = $this->generateSubscription($order, $item);

                    $message = __(
                        'Subscription created. Initial order total: %1',
                        $order->formatPriceTxt($order->getGrandTotal())
                    );

                    $subscription->recordBilling($order, $message);

                    $this->eventManager->dispatch(
                        'paradoxlabs_new_subscription_save_before',
                        [
                            'subscription' => $subscription,
                            'source_order' => $order,
                            'source_item'  => $item,
                        ]
                    );

                    $this->subscriptionRepository->save($subscription);

                    // ===================== Subscription Notification Email Start ======================================//
                    $subscriptionEmail = $this->sendSubscriptionEmail($subscription);
                    // ===================== Subscription Notification Email End ======================================= //



                } catch (\Throwable $e) {
                    $this->helper->log('subscriptions', (string)$e);

                    if ($emulate === true) {
                        $this->emulator->stopEnvironmentEmulation();
                    }

                    throw $e;
                }
            }
        }

        if ($emulate === true) {
            $this->emulator->stopEnvironmentEmulation();
        }
    }

    /**
     * Create a subscription for the given item.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Magento\Sales\Api\Data\OrderItemInterface $item
     * @return \ParadoxLabs\Subscriptions\Model\Subscription
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function generateSubscription(
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Sales\Api\Data\OrderItemInterface $item
    ) {
        /** @var \Magento\Sales\Model\Order\Item $item */

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->generateSubscriptionQuote($order, $item);

        /** @var \ParadoxLabs\Subscriptions\Model\Subscription $subscription */
        $subscription = $this->subscriptionFactory->create();

        $subscription->setStoreId($quote->getStoreId());
        $subscription->setCustomerId($quote->getCustomerId());
        $subscription->setQuote($quote);
        $subscription->setFrequencyCount($this->itemManager->getFrequencyCount($item));
        $subscription->setFrequencyUnit($this->itemManager->getFrequencyUnit($item));
        $subscription->setLength($this->itemManager->getLength($item));
        $subscription->setDescription($this->itemManager->getSubscriptionDescription($item));
        $subscription->setSubtotal($this->quoteManager->getSubscriptionSubtotal($quote));
        $subscription->calculateNextRun();
        $subscription->setStatus(Status::STATUS_ACTIVE);

        $subscription->addRelatedObject($quote, true);

        return $subscription;
    }

    /**
     * Create a subscription base quote for the given item.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Magento\Sales\Api\Data\OrderItemInterface $item
     * @return \Magento\Quote\Api\Data\CartInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function generateSubscriptionQuote(
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Sales\Api\Data\OrderItemInterface $item
    ) {
        /**
         * Initialize objects
         */

        /** @var \Magento\Sales\Model\Order\Item $item */
        /** @var \Magento\Sales\Model\Order $order */
        /** @var \Magento\Quote\Model\Quote $orderQuote */
        $orderQuote = $this->cartRepository->get($order->getQuoteId());

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteFactory->create();

        $this->objectCopyService->copyFieldsetToTarget(
            'sales_convert_order',
            'to_subscription_quote',
            $order,
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
            $orderQuote->getBillingAddress(),
            $billingAddress
        );

        $billingAddress->setCustomerId($orderQuote->getCustomerId());
        $billingAddress->setEmail($orderQuote->getCustomerEmail());

        /**
         * Duplicate shipping address
         */

        /** @var \Magento\Quote\Model\Quote\Address $shippingAddress */
        $shippingAddress = $this->quoteAddressFactory->create();

        $this->objectCopyService->copyFieldsetToTarget(
            'sales_copy_order_shipping_address',
            'to_order',
            $orderQuote->getShippingAddress(),
            $shippingAddress
        );

        $shippingAddress->setCustomerId($orderQuote->getCustomerId());
        $shippingAddress->setEmail($orderQuote->getCustomerEmail());

        /**
         * Duplicate payment object
         */

        $this->objectCopyService->copyFieldsetToTarget(
            'sales_convert_order_payment',
            'to_quote_payment',
            $order->getPayment(),
            $quote->getPayment()
        );

        $this->copyTokenbaseId($order, $quote);

        $quote->getPayment()->setId(null);
        $quote->getPayment()->setQuoteId(null);

        // Record the quote/order source to prevent a generation loop
        $this->quoteManager->setQuoteExistingSubscription($quote);

        $this->prepareVaultData($quote, $order);

        /**
         * Duplicate customer info
         */
        $this->objectCopyService->copyFieldsetToTarget(
            'sales_convert_order_customer',
            'to_quote',
            $order,
            $quote
        );

        // Try to load and set customer.
        $customerId = $order->getCustomerId();

        if ($customerId > 0) {
            try {
                $customer = $this->customerRepository->getById($customerId);

                $quote->assignCustomer($customer);
            } catch (\Exception $e) {
                // Ignore missing customer error
            }
        }

        /**
         * Pull quote together
         */

        // Set a far-off quote updated date to avoid pruning. This is the highest Magento allows (timestamp).
        $updatedAt = $this->dateProcessor->date('2038-01-01', null, false);

        $quote->setIsMultiShipping(false)
            ->setIsActive(false)
            ->setIsSuperMode(true)
            ->setUpdatedAt($updatedAt->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT))
            ->setBillingAddress($billingAddress)
            ->setShippingAddress($shippingAddress);

        /**
         * Set the product and price
         */
        $this->addItemToQuote($quote, $item);

        /**
         * Set shipping info
         */
        $this->setQuoteShippingMethod($quote, $order);

        $this->eventManager->dispatch(
            'paradoxlabs_new_subscription_collect_totals_before',
            [
                'quote'         => $quote,
                'source_order'  => $order,
                'source_item'   => $item,
            ]
        );

        $quote->setTotalsCollectedFlag(false)
            ->collectTotals()
            ->setTriggerRecollect(0);

        return $quote;
    }

    /**
     * Add our subscription item+product to the new quote, with appropriate pricing.
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @param \Magento\Sales\Api\Data\OrderItemInterface $item
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function addItemToQuote(
        \Magento\Quote\Api\Data\CartInterface $quote,
        \Magento\Sales\Api\Data\OrderItemInterface $item
    ) {
        /** @var \Magento\Quote\Model\Quote $quote */
        /** @var \Magento\Sales\Model\Order\Item $item */

        $product = $item->getProduct();

        if (!$product->getId()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Could not find product for item %1 (%2)', $item->getSku(), $item->getId())
            );
        }

        $info = $this->itemManager->getInfoBuyRequest($item);

        $quote->addProduct($product, $info, \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_LITE);

        /** @var \Magento\Quote\Model\Quote\Item $quoteItem */
        $quoteItem = $quote->getItemsCollection()->getFirstItem();

        $newPrice = $this->itemManager->calculatePrice(
            $quoteItem,
            2,
            $quote->getBaseCurrencyCode(),
            $quote->getQuoteCurrencyCode()
        );

        $quoteItem->setCustomPrice($newPrice);
        $quoteItem->setOriginalCustomPrice($newPrice);

        $quoteItem->setData('qty', $item->getQtyOrdered());
    }

    /**
     * Set shipping method on the new quote.
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return void
     */
    protected function setQuoteShippingMethod(
        \Magento\Quote\Api\Data\CartInterface $quote,
        \Magento\Sales\Api\Data\OrderInterface $order
    ) {
        /** @var \Magento\Sales\Model\Order $order */

        $this->quoteManager->setQuoteShippingMethod(
            $quote,
            $order->getShippingMethod(),
            $order->getShippingDescription()
        );
    }

    /**
     * If the payment method is not TokenBase, convert it to its proper vault form for later.
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function prepareVaultData(
        \Magento\Quote\Api\Data\CartInterface $quote,
        \Magento\Sales\Api\Data\OrderInterface $order
    ) {
        /** @var \Magento\Quote\Model\Quote $quote */
        /** @var \Magento\Sales\Model\Order $order */

        if ($this->vaultHelper->isQuoteVaultPayment($quote) === true) {
            $payment = $quote->getPayment();
            $method = $payment->getMethod();

            if (strpos($method, 'vault') === false) {
                $payment->setMethod($this->vaultHelper->getVaultMethodCode($method));
            }

            // token_metadata was used in 2.1.0-2.1.2. In 2.1.3 the values were moved to the top level.
            $metadata = $payment->getAdditionalInformation('token_metadata')
                ?: $payment->getAdditionalInformation();

            if ($metadata === null || !isset($metadata['public_hash']) || empty($metadata['public_hash'])) {
                // We're missing the vault info. Fetch and store it.

                /** @var \Magento\Sales\Model\Order\Payment $orderPayment */
                $orderPayment = $order->getPayment();

                if ($orderPayment->getId() === null) {
                    // The order must be saved to trigger vault hash generation.
                    // @see \Magento\Vault\Observer\AfterPaymentSaveObserver::execute()
                    $this->orderRepository->save($order);
                }

                $vault = $this->getVaultExtension($orderPayment->getExtensionAttributes());

                if ($vault !== null) {
                    // Fix Vault customer ID if guest checkout.
                    if ($vault->getCustomerId() === null) {
                        $this->vaultHelper->fixCardCustomerId($vault, $order->getCustomerId());
                    }

                    // Store the data at the appropriate spot.
                    if (version_compare($this->productMetadata->getVersion(), '2.1.3', '>=')) {
                        $payment->setAdditionalInformation('customer_id', $vault->getCustomerId());
                        $payment->setAdditionalInformation('public_hash', $vault->getPublicHash());
                    } else {
                        $payment->setAdditionalInformation(
                            'token_metadata',
                            [
                                'customer_id' => $vault->getCustomerId(),
                                'public_hash' => $vault->getPublicHash(),
                            ]
                        );
                    }
                } else {
                    throw new \Magento\Framework\Exception\LocalizedException(
                        __('Could not find payment card.')
                    );
                }
            }

            // Set Vault card payment info on the quote.
            $card = $this->paymentService->getQuoteCard($quote);
            $expires = strtotime((string)$this->vaultHelper->getCardExpires($card));
            $payment->setData('cc_type', $this->vaultHelper->getCardType($card));
            $payment->setData('cc_last_4', $this->vaultHelper->getCardLast4($card));
            $payment->setData('cc_exp_year', date('Y', $expires));
            $payment->setData('cc_exp_month', date('m', $expires));
        }

        return $this;
    }

    /**
     * Get the Vault order payment extension (Vault card), if any.
     *
     * @param \Magento\Sales\Api\Data\OrderPaymentExtensionInterface|null $extensionAttributes
     * @return \Magento\Vault\Api\Data\PaymentTokenInterface|null
     */
    protected function getVaultExtension(
        \Magento\Sales\Api\Data\OrderPaymentExtensionInterface $extensionAttributes = null
    ) {
        if ($extensionAttributes === null) {
            return null;
        }

        $card = $extensionAttributes->getVaultPaymentToken();
        if ($card === null || empty($card->getGatewayToken())) {
            return null;
        }

        return $card;
    }

    /**
     * Register customer account for the order if not already registered. Subscriptions must have an account.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @return $this
     */
    protected function registerCustomer(
        \Magento\Sales\Api\Data\OrderInterface $order
    ) {
        /** @var \Magento\Sales\Model\Order $order */

        if ($order->getCustomerId() === null) {
            // Load customer by email, create if not exists.
            try {
                $customerData = $this->customerRepository->get($order->getCustomerEmail());
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $customerData = $this->orderCustomerManager->create($order->getId());
            }

            $order->setCustomerId($customerData->getId());
        }

        return $this;
    }

    /**
     * Handle 2.3+ fieldset/extension attribute funkiness and ensure tokenbase_id gets set on the subscription.
     *
     * @param \Magento\Sales\Api\Data\OrderInterface $source
     * @param \Magento\Quote\Model\Quote $target
     * @return void
     */
    protected function copyTokenbaseId(
        \Magento\Sales\Api\Data\OrderInterface $source,
        \Magento\Quote\Model\Quote $target
    ) {
        /** @var \Magento\Sales\Api\Data\OrderPaymentExtensionInterface $sourcePaymentExtnAttr */
        $sourcePaymentExtnAttr = $source->getPayment()->getExtensionAttributes();
        $tokenbaseId           = $source->getPayment()->getData('tokenbase_id');
        if ($sourcePaymentExtnAttr instanceof \Magento\Sales\Api\Data\OrderPaymentExtensionInterface
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

    public function sendSubscriptionEmail($subscription)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/subscriptions_custom_email.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customer = $objectManager->get('Magento\Customer\Model\CustomerFactory')->create()->load($subscription->getData('customer_id'));
        $quote = $objectManager->get('Magento\Quote\Model\QuoteFactory')->create()->load($subscription->getData('quote_id'));
        
        $productName = "";
        foreach ($quote->getAllItems() as $item) {
            $productName = $item->getName();
        }

        $frequency='';
        if($subscription->getData('frequency_count') == 1)
        {
            $frequency = $subscription->getData('frequency_count').' '.$subscription->getData('frequency_unit');
        }
        else
        {
            $frequency = $subscription->getData('frequency_count').' '.$subscription->getData('frequency_unit').'s';
        }

        $templateId = 44;
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
                'last_run'        => date('F d Y',strtotime($subscription->getData('last_run'))),
                'next_run'        => date('F d Y',strtotime($subscription->getData('next_run'))),
                'product_name'    => $productName,
                'frequency'       => $frequency
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
            $dataToSave = array('customer_email' => $customer_email, 'email_sent' => "yes", 'notification_type'=>'New subscription created', 'email_message' => $emailTextMessage);
            $trackLog->setData($dataToSave);
            $trackLog->save();

            $logger->info('New subscription email sent successfully.');

        } catch (\Exception $e) {
            $trackLog = $this->creditCardExpiringModelFactory->create();
            $dataToSave = array('customer_email' => $customer_email, 'email_sent' => "no", 'notification_type'=>'New subscription created','email_message' => $e->getMessage());
            $trackLog->setData($dataToSave);
            $trackLog->save();

            $logger->info('Error while sending new subscription email '.$e->getMessage());
        }
        $this->inlineTranslation->resume();

    }
}

