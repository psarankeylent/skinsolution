<?php
declare(strict_types=1);

namespace Ssmd\StoreCredit\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Model\MaskedQuoteIdToQuoteIdInterface;


use Magento\QuoteGraphQl\Model\Cart\CreateEmptyCartForCustomer;
use Magento\Quote\Api\CartManagementInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Quote\Model\QuoteIdToMaskedQuoteIdInterface;
use Magento\Quote\Model\ResourceModel\Quote\QuoteIdMask as QuoteIdMaskResourceModel;


class RemoveStoreCredit implements ResolverInterface
{
    /**
     * @var CreateEmptyCartForCustomer
     */
    private $createEmptyCartForCustomer;

    /**
     * @var CartManagementInterface
     */
    private $cartManagement;

    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var QuoteIdMaskResourceModel
     */
    private $quoteIdMaskResourceModel;
    /**
     * @var QuoteIdToMaskedQuoteIdInterface
     */
    private $quoteIdToMaskedQuoteId;

    /**
     * @var \Ssmd\StoreCredit\Model\Storecredit
     */
    protected $storecreditModel;

    public function __construct(
        \Ssmd\StoreCredit\Helper\Data  $storecreditModel,
        MaskedQuoteIdToQuoteIdInterface $maskedQuoteIdToQuoteId,
        CreateEmptyCartForCustomer $createEmptyCartForCustomer,
        CartManagementInterface $cartManagement,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        QuoteIdMaskResourceModel $quoteIdMaskResourceModel,
        QuoteIdToMaskedQuoteIdInterface $quoteIdToMaskedQuoteId

    ) {
        $this->storecreditModel = $storecreditModel;
        $this->maskedQuoteIdToQuoteId = $maskedQuoteIdToQuoteId;
        $this->createEmptyCartForCustomer = $createEmptyCartForCustomer;
        $this->cartManagement = $cartManagement;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->quoteIdMaskResourceModel = $quoteIdMaskResourceModel;
        $this->quoteIdToMaskedQuoteId = $quoteIdToMaskedQuoteId;
    }


    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {

        /** @var ContextInterface $context */
        if (false === $context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }

        $customerId = $context->getUserId();

        $maskedCartId = $args['input']['cart_id'];

        $quoteId = $this->maskedQuoteIdToQuoteId->execute($maskedCartId);

        try {
            $this->storecreditModel->removeQuotesStoreCredit($quoteId);

            $cart = $this->cartManagement->getCartForCustomer($customerId);

        } catch (Exception $e) {
            throw new LocalizedException(__($e->getMessage()), $e);
        }

        return [
            'model' => $cart
        ];
    }


}

