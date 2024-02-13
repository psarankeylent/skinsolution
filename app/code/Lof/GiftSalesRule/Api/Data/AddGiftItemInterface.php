<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Lof
 * @package   Lof\GiftSalesRule
 * @author    landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 * @license   http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\GiftSalesRule\Api\Data;

/**
 * GiftRule interface.
 *
 * @api
 * @author    landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 */
interface AddGiftItemInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const CART_ID                = 'cart_id';
    const GIFT_RULE_ID           = 'gift_rule_id';
    const GIFT_RULE_CODE         = 'gift_rule_code';
    const PRODUCTS               = 'products';
    /**
     * Get the cart_id.
     *
     * @return string
     */
    public function getCartId();

    /**
     * Set the cart_id.
     *
     * @param string $value Value
     *
     * @return $this
     */
    public function setCartId($value);

    /**
     * Get the gift_rule_id.
     *
     * @return string
     */
    public function getGiftRuleId();

    /**
     * Set the gift_rule_id.
     *
     * @param string $value Value
     *
     * @return $this
     */
    public function setGiftRuleId($value);

    /**
     * Get the gift_rule_code.
     *
     * @return string
     */
    public function getGiftRuleCode();

    /**
     * Set the gift_rule_code.
     *
     * @param string $value Value
     *
     * @return $this
     */
    public function setGiftRuleCode($value);

    /**
     * Lists items in the cart.
     *
     * @return \Lof\GiftSalesRule\Api\Data\AddGiftProductItemInterface[]|null Array of items. Otherwise, null.
     */
    public function getProducts();

    /**
     * Sets items in the cart.
     *
     * @param \Lof\GiftSalesRule\Api\Data\AddGiftProductItemInterface[] $items
     * @return $this
     */
    public function setProducts(array $products = null);

    /**
     * Retrieve existing extension attributes object or create a new one.Interface
     *
     * @return \Lof\GiftSalesRule\Api\Data\AddGiftItemExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Lof\GiftSalesRule\Api\Data\AddGiftItemExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Lof\GiftSalesRule\Api\Data\AddGiftItemExtensionInterface $extensionAttributes);
}
