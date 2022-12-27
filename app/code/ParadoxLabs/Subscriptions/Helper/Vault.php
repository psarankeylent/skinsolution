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

namespace ParadoxLabs\Subscriptions\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Vault\Api\PaymentTokenRepositoryInterface;

/**
 * Vault helper - operations to abstract Vault vs. TokenBase cards as much as practical.
 *
 * This should be in TokenBase, but doing so would break 2.0 compatibility of that module as well.
 */
class Vault extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \ParadoxLabs\TokenBase\Helper\Data
     */
    protected $tokenbaseHelper;

    /**
     * @var \ParadoxLabs\TokenBase\Api\CardRepositoryInterface
     * @deprecated
     */
    protected $cardRepository;

    /**
     * @var PaymentTokenRepositoryInterface
     */
    protected $tokenRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Service\Payment
     * @deprecated
     */
    protected $paymentService;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\OfflinePayment\CardFactory
     * @deprecated
     */
    protected $offlineCardFactory;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Config
     */
    protected $config;

    /**
     * Vault constructor
     *
     * @param Context $context
     * @param \ParadoxLabs\TokenBase\Helper\Data $tokenbaseHelper
     * @param \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository
     * @param PaymentTokenRepositoryInterface $tokenRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \ParadoxLabs\Subscriptions\Model\Service\Payment $paymentService *Proxy
     * @param \ParadoxLabs\Subscriptions\Model\OfflinePayment\CardFactory $offlineCardFactory
     * @param \ParadoxLabs\Subscriptions\Model\Config $config
     */
    public function __construct(
        Context $context,
        \ParadoxLabs\TokenBase\Helper\Data $tokenbaseHelper,
        \ParadoxLabs\TokenBase\Api\CardRepositoryInterface $cardRepository,
        \Magento\Vault\Api\PaymentTokenRepositoryInterface $tokenRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \ParadoxLabs\Subscriptions\Model\Service\Payment $paymentService,
        \ParadoxLabs\Subscriptions\Model\OfflinePayment\CardFactory $offlineCardFactory,
        \ParadoxLabs\Subscriptions\Model\Config $config = null
    ) {
        parent::__construct($context);

        $this->tokenbaseHelper = $tokenbaseHelper;
        $this->cardRepository = $cardRepository;
        $this->tokenRepository = $tokenRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->paymentService = $paymentService;
        $this->offlineCardFactory = $offlineCardFactory;
        // BC preservation -- argument added in 3.2.0
        $this->config = $config ?: \Magento\Framework\App\ObjectManager::getInstance()->get(
            \ParadoxLabs\Subscriptions\Model\Config::class
        );
    }

    /**
     * Get text label for the given card.
     *
     * @param \Magento\Vault\Api\Data\PaymentTokenInterface $card
     * @return string
     */
    public function getCardLabel(\Magento\Vault\Api\Data\PaymentTokenInterface $card)
    {
        // Our methods handle this implicitly.
        if ($card instanceof \ParadoxLabs\TokenBase\Api\Data\CardInterface) {
            return $card->getLabel();
        }

        // Braintree PayPal has to be different.
        if ($card->getType() === 'account' && strpos((string)$card->getPaymentMethodCode(), 'paypal') !== false) {
            return __('PayPal: %1', $this->getCardType($card));
        }

        // Vault cards do not.
        return __(
            '%1 XXXX-%2',
            $this->getCardType($card),
            $this->getCardLast4($card)
        );
    }

    /**
     * Get CC type for the given card.
     *
     * @param \Magento\Vault\Api\Data\PaymentTokenInterface $card
     * @return string
     */
    public function getCardType(\Magento\Vault\Api\Data\PaymentTokenInterface $card)
    {
        if ($card instanceof \ParadoxLabs\TokenBase\Api\Data\CardInterface) {
            return $card->getType();
        }

        // For Vault cards, grab the CC details. We can only assume they'll follow conventions.
        $details = $card->getTokenDetails();

        if (is_string($details)) {
            $details = json_decode((string)$details, true);
        }

        $type = $card->getType();
        if (isset($details['cc_type'])) {
            $type = $details['cc_type'];
        } elseif (isset($details['type'])) {
            $type = $details['type'];
        } elseif (isset($details['payerEmail'])) {
            $type = $details['payerEmail'];
        }

        return $type;
    }

    /**
     * Get CC last-4 digits for the given card.
     *
     * @param \Magento\Vault\Api\Data\PaymentTokenInterface $card
     * @return string
     */
    public function getCardLast4(\Magento\Vault\Api\Data\PaymentTokenInterface $card)
    {
        if ($card instanceof \ParadoxLabs\TokenBase\Api\Data\CardInterface) {
            return $card->getAdditional('cc_last4');
        }

        // For Vault cards, grab the CC details. We can only assume they'll follow conventions.
        $details = $card->getTokenDetails();

        if (is_string($details)) {
            $details = json_decode((string)$details, true);
        }

        $ccLast4 = '';
        if (isset($details['cc_last_4'])) {
            $ccLast4 = $details['cc_last_4'];
        } elseif (isset($details['maskedCC'])) {
            $ccLast4 = $details['maskedCC'];
        } elseif (isset($details['cc_last_4_digits'])) {
            $ccLast4 = $details['cc_last_4_digits'];
        }

        return $ccLast4;
    }

    /**
     * Get expires date for the given card.
     *
     * @param \Magento\Vault\Api\Data\PaymentTokenInterface $card
     * @return string
     */
    public function getCardExpires(\Magento\Vault\Api\Data\PaymentTokenInterface $card)
    {
        // Our methods handle this implicitly.
        if ($card instanceof \ParadoxLabs\TokenBase\Api\Data\CardInterface) {
            return $card->getExpires();
        }

        // Vault stores expires date as first of the next month. Roll that back for the customer.
        $expires = strtotime((string)$card->getExpiresAt()) - 1;

        return date('c', $expires);
    }

    /**
     * Get active vault customer cards.
     *
     * @param int $customerId
     * @return \Magento\Vault\Api\Data\PaymentTokenInterface[]
     */
    public function getVaultActiveCustomerCards($customerId = null)
    {
        if ($customerId === null || $customerId < 1) {
            return [];
        }

        $tokenCriteria = $this->searchCriteriaBuilder->addFilter('customer_id', $customerId)
                                                     ->addFilter('is_active', 1)
                                                     ->addFilter('is_visible', 1)
                                                     ->create();

        $tokens = $this->tokenRepository->getList($tokenCriteria)->getItems();
        $cards  = [];
        if (!empty($tokens)) {
            foreach ($tokens as $token) {
                $cards[] = $token;
            }
        }

        return $cards;
    }

    /**
     * Get the Vault card for the given quote, if any.
     *
     * Note: This assumes the quote has an already-stored card associated, and it's actually a vault card.
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return \Magento\Vault\Api\Data\PaymentTokenInterface|null
     */
    public function getVaultQuoteCard(\Magento\Quote\Api\Data\CartInterface $quote)
    {
        /** @var \Magento\Quote\Model\Quote $quote */

        // token_metadata was used in 2.1.0-2.1.2. In 2.1.3 the values were moved to the top level.
        $tokenMeta = $quote->getPayment()->getAdditionalInformation('token_metadata')
            ?: $quote->getPayment()->getAdditionalInformation();

        if (is_array($tokenMeta) && isset($tokenMeta['public_hash'])) {
            $tokenCriteria = $this->searchCriteriaBuilder->addFilter('public_hash', $tokenMeta['public_hash'])
                                                         ->setPageSize(1)
                                                         ->create();

            $tokens = $this->tokenRepository->getList($tokenCriteria)->getItems();

            if (!empty($tokens)) {
                return array_shift($tokens);
            }
        }

        return null;
    }

    /**
     * Determine whether the given quote is Vault or not.
     *
     * @param \Magento\Quote\Api\Data\CartInterface $quote
     * @return bool
     */
    public function isQuoteVaultPayment(\Magento\Quote\Api\Data\CartInterface $quote)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        return $this->isVaultMethod($quote->getPayment()->getMethod());
    }

    /**
     * Check whether the given method code is Vault-enabled.
     *
     * @param string $methodCode
     * @return bool
     */
    public function isVaultMethod($methodCode)
    {
        return (bool)$this->config->vaultMethodIsActive(
            $methodCode,
            $this->tokenbaseHelper->getCurrentStoreId()
        );
    }

    /**
     * Get the vault payment method code for the given non-vault method.
     *
     * @param string $methodCode
     * @return string
     */
    public function getVaultMethodCode($methodCode)
    {
        return $this->config->getVaultMethodCode($methodCode);
    }

    /**
     * Load an arbitrary card by hash.
     *
     * @param string $publicHash
     * @return \Magento\Vault\Api\Data\PaymentTokenInterface|null
     */
    public function getVaultCardByHash($publicHash)
    {
        $criteria = $this->searchCriteriaBuilder->addFilter('public_hash', $publicHash)
                                                ->setPageSize(1)
                                                ->create();

        $tokens = $this->tokenRepository->getList($criteria)->getItems();

        if (!empty($tokens)) {
            return array_shift($tokens);
        }

        return null;
    }

    /**
     * Fix card customer ID: If it's null, save the proper ID.
     *
     * @param \Magento\Vault\Api\Data\PaymentTokenInterface $card
     * @param int $customerId
     * @return \Magento\Vault\Api\Data\PaymentTokenInterface
     */
    public function fixCardCustomerId(\Magento\Vault\Api\Data\PaymentTokenInterface $card, $customerId)
    {
        if (($card instanceof \ParadoxLabs\TokenBase\Api\Data\CardInterface) === false
            && $customerId !== null
            && $card->getCustomerId() === null) {
            $card->setCustomerId($customerId);
            $this->tokenRepository->save($card);
        }

        return $card;
    }
}
