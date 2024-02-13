<?php
declare(strict_types=1);

namespace Ssmd\QUote\Model\Resolver;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\QuoteGraphQl\Model\Cart\GetCartForUser;
use Magento\QuoteGraphQl\Model\Cart\UpdateCartItem;

use Ssmd\Quote\Model\Cart\UpdateCartItemCustomPrice;

/**
 * @inheritdoc
 */
class SetCustomPriceOnCartItem implements ResolverInterface
{
    /**
     * SetCustomPriceOnCartItem
     */
    private $updateCartItemCustomPrice;

    /**
     * @var UpdateCartItem
     */
    private $updateCartItem;

    /**
     * @var GetCartForUser
     */
    private $getCartForUser;

    /**
     * @var CartItemRepositoryInterface
     */
    private $cartItemRepository;

    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    /**
     * @param GetCartForUser $getCartForUser
     * @param CartItemRepositoryInterface $cartItemRepository
     * @param UpdateCartItem $updateCartItem
     * @param CartRepositoryInterface $cartRepository
     */
    public function __construct(
        GetCartForUser $getCartForUser,
        CartItemRepositoryInterface $cartItemRepository,
        UpdateCartItem $updateCartItem,
        CartRepositoryInterface $cartRepository,
        UpdateCartItemCustomPrice $updateCartItemCustomPrice
    ) {
        $this->getCartForUser = $getCartForUser;
        $this->cartItemRepository = $cartItemRepository;
        $this->updateCartItem = $updateCartItem;
        $this->cartRepository = $cartRepository;
        $this->updateCartItemCustomPrice = $updateCartItemCustomPrice;
    }

    /**
     * @inheritdoc
     */
    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        if (empty($args['input']['cart_id'])) {
            throw new GraphQlInputException(__('Required parameter "cart_id" is missing.'));
        }
        $maskedCartId = $args['input']['cart_id'];

        if (empty($args['input']['cart_item_id'])) {
            throw new GraphQlInputException(__('Required parameter "cart_item_id" is missing.'));
        }

        /*if (empty($args['input']['custom_price'])) {
            throw new GraphQlInputException(__('Required parameter "custom_price" is missing.'));
        }*/

        if ($args['input']['custom_price'] < 0 ) {
            throw new GraphQlInputException(__('Items price cannot be less than zero'));
        }

        $cartItemId = $args['input']['cart_item_id'];
        $customPrice = $args['input']['custom_price'];

        $storeId = (int)$context->getExtensionAttributes()->getStore()->getId();
        $cart = $this->getCartForUser->execute($maskedCartId, $context->getUserId(), $storeId);

        try {
            $this->updateCartItemCustomPrice->execute($cart, $cartItemId, $customPrice);
            $this->cartRepository->save($cart);
        } catch (NoSuchEntityException $e) {
            throw new GraphQlNoSuchEntityException(__($e->getMessage()), $e);
        } catch (LocalizedException $e) {
            throw new GraphQlInputException(__($e->getMessage()), $e);
        }

        return [
            'cart' => [
                'model' => $cart,
            ],
        ];
    }
}
