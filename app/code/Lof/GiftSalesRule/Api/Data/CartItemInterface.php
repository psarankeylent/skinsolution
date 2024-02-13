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
interface CartItemInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const PRODUCT_ID                = 'product_id';
    const QTY           = 'qty';
    /**
     * Get the cart_id.
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
     * Set the cart_id.
     *
     * @param float $value Value
     *
     * @return $this
     */
    public function setQty($value);

}