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

use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Payment Class
 */
class Payment
{
    /**
     * @var \ParadoxLabs\Subscriptions\Model\Config
     */
    protected $config;

    /**
     * @var \ParadoxLabs\TokenBase\Helper\Data
     */
    protected $tokenbaseHelper;

    /**
     * @var \ParadoxLabs\Subscriptions\Helper\Vault
     */
    protected $vaultHelper;

    /**
     * @var \Magento\Framework\DataObject\Copy
     */
    protected $objectCopyService;

    /**
     * @var \Magento\Framework\App\ProductMetadata
     */
    protected $productMetadata;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\OfflinePayment\CardFactory
     */
    protected $offlineCardFactory;

    /**
     * @var \ParadoxLabs\TokenBase\Api\CardRepositoryInterface
     */
    protected $cardRepository;

    /**
     * Payment constructor.
     *
     * @param \ParadoxLabs\Subscriptions\Model\Config $config
     * @param \ParadoxLabs\TokenBase\Helper\Data $tokenbaseHelper
     * @param \ParadoxLabs\Subscriptions\Helper\Vault $vaultHelper
     * @param \Magento\Framework\DataObject\Copy $objectCopyService
     * @param \Magento\Framework\App\ProductMetadata $productMetadata
     * @param \ParadoxLabs\Subscriptions\Model\OfflinePayment\CardFactory $offlineCardFactory
     * @param \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository
     */
    public function __construct(
        \ParadoxLabs\Subscriptions\Model\Config $config,
        \ParadoxLabs\TokenBase\Helper\Data $tokenbaseHelper,
        \ParadoxLabs\Subscriptions\Helper\Vault $vaultHelper = null,
        \Magento\Framework\DataObject\Copy $objectCopyService = null,
        \Magento\Framework\App\ProductMetadata $productMetadata = null,
        \ParadoxLabs\Subscriptions\Model\OfflinePayment\CardFactory $offlineCardFactory = null,
        \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository = null
    ) {
        $this->config = $config;
        $this->tokenbaseHelper = $tokenbaseHelper;

        // BC preservation -- arguments added in 3.2.0
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $this->vaultHelper = $vaultHelper ?: $om->get(
            \ParadoxLabs\Subscriptions\Helper\Vault::class
        );
        $this->objectCopyService = $objectCopyService ?: $om->get(
            \Magento\Framework\DataObject\Copy::class
        );
        $this->productMetadata = $productMetadata ?: $om->get(
            \Magento\Framework\App\ProductMetadata::class
        );
        $this->offlineCardFactory = $offlineCardFactory ?: $om->get(
            \ParadoxLabs\Subscriptions\Model\OfflinePayment\CardFactory::class
        );
        $this->cardRepository = $cardRepository ?: $om->get(
            \ParadoxLabs\TokenBase\Api\CardRepositoryInterface::class
        );
    }

    /**
     * Check whether the given method code is TokenBase.
     *
     * @param string $methodCode
     * @return bool
     */
    public function isTokenBaseMethod($methodCode)
    {
        if (in_array($methodCode, $this->tokenbaseHelper->getActiveMethods(), true)) {
            return true;
        }

        return false;
    }

    /**
     * Check whether the given method code is offline.
     *
     * @param string $methodCode
     * @return bool
     */
    public function isOfflineMethod($methodCode)
    {
        try {
            $method = $this->tokenbaseHelper->getMethodInstance($methodCode);

            return $method->isOffline();
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * Check whether the given method code is Vault-enabled.
     *
     * @param string $methodCode
     * @return bool
     */
    public function isVaultMethod($methodCode)
    {
        return $this->vaultHelper->isVaultMethod($methodCode);
    }

    /**
     * Is the given payment method code allowed for subscriptions?
     *
     * @param string $methodCode
     * @return bool
     */
    public function isAllowedForSubscription($methodCode)
    {
        if ($this->isTokenBaseMethod($methodCode)
            || $this->isOfflineMethod($methodCode)
            || $this->isVaultMethod($methodCode)) {
            return true;
        }

        return false;
    }

    /**
     * Check whether the given method is available for the given quote. Assumes yes if insufficient data.
     *
     * @param string $methodCode
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return bool
     */
    public function isMethodAvailable($methodCode, $quote)
    {
        if (!($quote instanceof \Magento\Quote\Api\Data\CartInterface)) {
            return true;
        }

        try {
            $method = $this->tokenbaseHelper->getMethodInstance($methodCode);

            return $method->isAvailable($quote);
        } catch (\Exception $e) {
            // Noop
        }

        return true;
    }

    /**
     * Get an associative array of enabled offline payment methods (if any).
     *
     * @return \Magento\Payment\Model\MethodInterface[]
     */
    public function getOfflineMethods()
    {
        $methods = [];

        foreach ($this->tokenbaseHelper->getPaymentMethods() as $code => $data) {
            try {
                $method = $this->tokenbaseHelper->getMethodInstance($code);

                if ($method->isOffline() && (int)$method->getConfigData('active') === 1) {
                    $methods[$code] = $method;
                }
            } catch (\Exception $e) {
                // Noop
            }
        }

        return $methods;
    }

    /**
     * Get active customer cards.
     *
     * @param int $customerId
     * @return \Magento\Vault\Api\Data\PaymentTokenInterface[]
     */
    public function getActiveCustomerCards($customerId = null)
    {
        $cards = [];

        /** @var \ParadoxLabs\TokenBase\Model\Card $card */
        foreach ($this->tokenbaseHelper->getActiveCustomerCardsByMethod() as $card) {
            $cards[] = $card->getTypeInstance();
        }

        /**
         * Add any Vault cards
         */
        if ($customerId === null) {
            $customerId = $this->tokenbaseHelper->getCurrentCustomer()->getId();
        }
        $cards = array_merge(
            $cards,
            $this->vaultHelper->getVaultActiveCustomerCards($customerId)
        );

        /**
         * Add any offline methods
         */
        $offlineMethods = $this->getOfflineMethods();
        foreach ($offlineMethods as $method) {
            $cards[] = $this->offlineCardFactory->createMethodCard($method);
        }

        return $cards;
    }

    /**
     * Get active customer cards.
     *
     * @return \Magento\Vault\Api\Data\PaymentTokenInterface[]
     */
    public function getActiveCustomerCardsForQuote(\Magento\Quote\Api\Data\CartInterface $quote)
    {
        $cards = $this->getActiveCustomerCards($quote->getCustomerId());

        // Remove any unavailable methods.
        foreach ($cards as $k => $card) {
            $methodIsAvailable = $this->isMethodAvailable($card->getPaymentMethodCode(), $quote);

            if ($methodIsAvailable === false) {
                unset($cards[ $k ]);
            }
        }

        // Make sure quote card is included, even if inactive.
        try {
            $activeCard = $this->getQuoteCard($quote);
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $activeCard = null;
        }

        if ($activeCard instanceof \Magento\Vault\Api\Data\PaymentTokenInterface) {
            $found = false;
            foreach ($cards as $card) {
                if ($card->getPublicHash() === $activeCard->getPublicHash()) {
                    $found = true;
                    break;
                }
            }

            if ($found !== true) {
                array_unshift($cards, $activeCard);
            }
        }

        return $cards;
    }

    /**
     * Load an arbitrary card by hash.
     *
     * @param string $publicHash
     * @return \Magento\Vault\Api\Data\PaymentTokenInterface
     * @throws NoSuchEntityException
     */
    public function getCardByHash($publicHash)
    {
        $card = null;

        /**
         * Try to load TokenBase first (if it fits). Our hashes will always be 40 chars. Vault is typically 64.
         */
        if (strlen((string)$publicHash) === 40) {
            try {
                /** @var \ParadoxLabs\TokenBase\Model\Card $temp */
                $temp = $this->cardRepository->getById($publicHash);
                $card = $temp->getTypeInstance();
            } catch (NoSuchEntityException $e) {
                // Ignore card-not-found exception.
            }
        }

        /**
         * If we don't have a card yet, is it an offline method?
         */
        if (!($card instanceof \Magento\Vault\Api\Data\PaymentTokenInterface)
            && $this->isOfflineMethod($publicHash)) {
            $method = $this->tokenbaseHelper->getMethodInstance($publicHash);
            $card = $this->offlineCardFactory->createMethodCard($method);
        }

        /**
         * If we don't have a card yet, try the Vault.
         */
        if (!($card instanceof \Magento\Vault\Api\Data\PaymentTokenInterface)) {
            $card = $this->vaultHelper->getVaultCardByHash($publicHash);
        }

        /**
         * Verify we got something, and return.
         */
        if (!($card instanceof \Magento\Vault\Api\Data\PaymentTokenInterface)) {
            throw new NoSuchEntityException(__('Could not load card with hash "%1".', $publicHash));
        }

        return $card;
    }

    /**
     * Get the card for the given quote (TokenBase or Vault).
     *
     * Note: This assumes the quote has an already-stored card associated.
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return \Magento\Vault\Api\Data\PaymentTokenInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getQuoteCard(\Magento\Quote\Api\Data\CartInterface $quote)
    {
        /** @var \Magento\Quote\Model\Quote $quote */

        $card = null;

        if ($this->isTokenBaseMethod($quote->getPayment()->getMethod())) {
            /** @var \ParadoxLabs\TokenBase\Model\Card $card */
            $card = $this->cardRepository->getById($quote->getPayment()->getData('tokenbase_id'));
            $card = $card->getTypeInstance();
        } elseif ($this->isOfflineMethod($quote->getPayment()->getMethod())) {
            $method = $this->tokenbaseHelper->getMethodInstance($quote->getPayment()->getMethod());
            $card = $this->offlineCardFactory->createMethodCard($method);
        } else {
            $card = $this->vaultHelper->getVaultQuoteCard($quote);
        }

        if ($card instanceof \ParadoxLabs\TokenBase\Api\Data\CardInterface) {
            $card->setInfoInstance($quote->getPayment());
        }

        /**
         * Verify we got something, and return.
         */
        if (!($card instanceof \Magento\Vault\Api\Data\PaymentTokenInterface)) {
            throw new NoSuchEntityException(
                __('Could not load card for quote with id "%1".', $quote->getId())
            );
        }

        return $card;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Vault\Api\Data\PaymentTokenInterface $card
     * @param array $paymentData
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updatePayment(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Vault\Api\Data\PaymentTokenInterface $card,
        array $paymentData
    ) {
        $payment = $quote->getPayment();

        if ($card instanceof \ParadoxLabs\TokenBase\Api\Data\CardInterface) {
            /**
             * Update billing address from the given card. Only known for TokenBase.
             */
            $this->objectCopyService->copyFieldsetToTarget(
                'sales_copy_order_billing_address',
                'to_order',
                $card->getAddress(),
                $quote->getBillingAddress()
            );

            $payment->setMethod($card->getPaymentMethodCode());
            $payment->setData('tokenbase_id', $card->getId());
        } else {
            /**
             * Update payment data.
             */
            $payment->setAdditionalInformation('customer_id', $card->getCustomerId());
            $payment->setAdditionalInformation('public_hash', $card->getPublicHash());

            $payment->setMethod($this->vaultHelper->getVaultMethodCode($card->getPaymentMethodCode()));
            $payment->setData('tokenbase_id', null);
        }

        if ($card instanceof \ParadoxLabs\Subscriptions\Model\OfflinePayment\Card) {
            // Send payment form params to the method for sanitizing and assignment.
            // Note: We can't blindly set $paymentData on $payment, because that would be a security risk.
            $method = $this->tokenbaseHelper->getMethodInstance($card->getPaymentMethodCode());
            $method->setInfoInstance($payment);
            $method->assignData(new \Magento\Framework\DataObject($paymentData));
        }

        $expires = strtotime((string)$this->vaultHelper->getCardExpires($card));
        $payment->setData('cc_type', $this->vaultHelper->getCardType($card));
        $payment->setData('cc_last_4', $this->vaultHelper->getCardLast4($card));
        $payment->setData('cc_exp_year', date('Y', $expires));
        $payment->setData('cc_exp_month', date('m', $expires));
    }
}
