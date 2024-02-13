<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ssmd\Quote\Model\Cart;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;

/**
 * Update cart item
 */
class UpdateCartItemCustomPrice
{
    /**
     * Update cart item
     *
     * @param Quote $cart
     * @param int $cartItemId
     * @param float $price
     * @return void
     * @throws GraphQlInputException
     * @throws GraphQlNoSuchEntityException
     * @throws NoSuchEntityException
     */
    public function execute(Quote $cart, int $cartItemId, ?float $price): void
    {
        $this->updateItemCustomPrice($cartItemId, $cart, $price);
    }

    /**
     * Updates item price for the specified cart
     *
     * @param int $itemId
     * @param Quote $cart
     * @param ?float $customPrice
     * @throws GraphQlNoSuchEntityException
     * @throws NoSuchEntityException
     * @throws GraphQlInputException
     */
    private function updateItemCustomPrice(int $itemId, Quote $cart, ?float $customPrice)
    {
        $cartItem = $cart->getItemById($itemId);
        if ($cartItem === false) {
            throw new GraphQlNoSuchEntityException(
                __('Could not find cart item with id: %1.', $itemId)
            );
        }
        $cartItem->setCustomPrice($customPrice);
        $cartItem->setOriginalCustomPrice($customPrice);
        $cartItem->setAdditionalData('impersonation_item');
	//$cartItem->getProduct()->setIsSuperMode(true);
	//$cartItem->setIsSuperMode(true);
        $this->validateCartItem($cartItem);
    }

    /**
     * Validate cart item
     *
     * @param Item $cartItem
     * @return void
     * @throws GraphQlInputException
     */
    private function validateCartItem(Item $cartItem): void
    {
        if ($cartItem->getHasError()) {
            $errors = [];
            foreach ($cartItem->getMessage(false) as $message) {
                $errors[] = $message;
            }
            if (!empty($errors)) {
                throw new GraphQlInputException(
                    __(
                        'Could not update the product with SKU %sku: %message',
                        ['sku' => $cartItem->getSku(), 'message' => __(implode("\n", $errors))]
                    )
                );
            }
        }
    }
}
