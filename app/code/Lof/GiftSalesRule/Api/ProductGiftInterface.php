<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Lof
 * @package   Lof\GiftSalesRule
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 * @license   http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\GiftSalesRule\Api;

/**
 * GiftRule repository interface.
 *
 * @api
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 */
interface ProductGiftInterface
{
    /**
     * Get a giftrule by Quote ID for guest
     *
     * @param string $cartId Quote id
     * @return \Lof\GiftSalesRule\Api\Data\GiftRuleDataInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getGiftsByQuoteId($cartId);

    /**
     * Get a customer giftrule by Quote ID for customer
     *
     * @param string $cartId Quote id
     * @return \Lof\GiftSalesRule\Api\Data\GiftRuleDataInterface[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomerGiftsByQuoteId($cartId);

    /**
     * Removes the specified item from the specified cart for guest
     *
     * @param string $cartId The cart ID.
     * @param int $itemId The item ID of the item to be removed.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified item or cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The item could not be removed.
     */
    public function deleteGiftByQuoteItemId($cartId, $itemId);

    /**
     * Removes the specified item from the specified cart for customer
     * @param int $cartId The cart ID.
     * @param int $itemId The item ID of the item to be removed.
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException The specified item or cart does not exist.
     * @throws \Magento\Framework\Exception\CouldNotSaveException The item could not be removed.
     */
    public function deleteCustomerGiftByQuoteItemId($cartId, $itemId);

    /**
     * add gift item to cart for guest
     * @param \Lof\GiftSalesRule\Api\Data\AddGiftItemInterface $addGiftItem
     * @return \Lof\GiftSalesRule\Api\Data\AddGiftRuleDataInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function addGift($addGiftItem);

    /**
     * add gift item to cart for customer
     * @param int $cartId
     * @param \Lof\GiftSalesRule\Api\Data\AddGiftItemInterface $addGiftItem
     * @return \Lof\GiftSalesRule\Api\Data\AddGiftRuleDataInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function addCustomerGift($cartId, $addGiftItem);
    
}
