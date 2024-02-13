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
namespace Lof\GiftSalesRule\Api\Data;

/**
 * GiftRule interface.
 *
 * @api
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 */
interface GiftRuleInterface
{
    const TABLE_NAME             = 'lof_gift_salesrule';
    const RULE_ID                = 'rule_id';
    const MAXIMUM_NUMBER_PRODUCT = 'maximum_number_product';
    const PRICE_RANGE            = 'price_range';

    /**
     * Rule type actions
     */
    const OFFER_PRODUCT                 = 'offer_product';
    const OFFER_PRODUCT_PER_PRICE_RANGE = 'offer_product_per_price_range';

    /**
     * Get the maximum number product.
     *
     * @return int
     */
    public function getMaximumNumberProduct();

    /**
     * Set the maximum number product.
     *
     * @param int $value Value
     * @return $this
     */
    public function setMaximumNumberProduct($value);

    /**
     * Get the price range.
     *
     * @return float
     */
    public function getPriceRange();

    /**
     * Set the price range.
     *
     * @param decimal $value Value
     * @return $this
     */
    public function setPriceRange($value);
}
