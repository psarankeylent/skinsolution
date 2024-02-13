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
interface AddGiftProductItemInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const PRODUCT_ID                = 'product_id';
    const PRODUCT_SKU                = 'sku';
    const QTY                       = 'qty';
    const UENC                      = 'uenc';
    const PRODUCT_OPTION           = 'product_option';

    /**
     * Get the sku.
     *
     * @return string
     */
    public function getSku();

    /**
     * Set the sku.
     *
     * @param string $value Value
     *
     * @return $this
     */
    public function setSku($value);

    /**
     * Get the product_id.
     *
     * @return string
     */
    public function getProductId();

    /**
     * Set the cart_id.
     *
     * @param string $value Value
     *
     * @return $this
     */
    public function setProductId($value);

    /**
     * Get the qty.
     *
     * @return float
     */
    public function getQty();

    /**
     * Set the qty.
     *
     * @param float $value Value
     *
     * @return $this
     */
    public function setQty($value);

    /**
     * Get the uenc.
     *
     * @return string
     */
    public function getUenc();

    /**
     * Set the uenc.
     *
     * @param string $value Value
     *
     * @return $this
     */
    public function setUenc($value);

    /**
     * Returns product option
     *
     * @return \Magento\Quote\Api\Data\ProductOptionInterface[]|null
     */
    public function getProductOption();

    /**
     * Sets product option
     *
     * @param \Magento\Quote\Api\Data\ProductOptionInterface[] $productOption
     * @return $this
     */
    public function setProductOption(array $productOption = null);

    
    /**
     * Retrieve existing extension attributes object or create a new one.Interface
     *
     * @return \Lof\GiftSalesRule\Api\Data\AddGiftProductItemExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     *
     * @param \Lof\GiftSalesRule\Api\Data\AddGiftProductItemExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(\Lof\GiftSalesRule\Api\Data\AddGiftProductItemExtensionInterface $extensionAttributes);
}
