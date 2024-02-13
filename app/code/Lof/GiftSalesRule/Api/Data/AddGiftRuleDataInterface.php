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
interface AddGiftRuleDataInterface
{
    const STATUS                = 'status';
    const MESSAGE               = 'message';
    const GIFT_RULE_ID          = 'gift_rule_id';
    const QUOTE_ID              = 'quote_id';
    const QUOTE_ITEM_ID         = 'quote_item_id';
    const PRODUCT_GIFT_ID       = 'product_gift_id';
    /**
     * Get status.
     *
     * @return string
     */
    public function getStatus();

    /**
     * Set status.
     *
     * @param string $value Value
     *
     * @return $this
     */
    public function setStatus($value);

    /**
     * Get the message.
     *
     * @return string
     */
    public function getMessage();

    /**
     * Set the message.
     *
     * @param string $value Value
     *
     * @return $this
     */
    public function setMessage($value);

    /**
     * Get the gift_rule_id.
     *
     * @return string
     */
    public function getGiftRuleId();

    /**
     * Set the rule id.
     *
     * @param int $value Value
     *
     * @return $this
     */
    public function setGiftRuleId($value);

    /**
     * Get the quote_id.
     *
     * @return string
     */
    public function getQuoteId();

    /**
     * Set the quote_id.
     *
     * @param int $value Value
     *
     * @return $this
     */
    public function setQuoteId($value);

    /**
     * Get the quote_item_id.
     *
     * @return string
     */
    public function getQuoteItemId();

    /**
     * Set the quote_item_id.
     *
     * @param int $value Value
     *
     * @return $this
     */
    public function setQuoteItemId($value);

    /**
     * Get the product_gift_id.
     *
     * @return mixed
     */
    public function getProductGiftId();

    /**
     * Set the product_gift_id.
     *
     * @param int[] $value Value
     *
     * @return $this
     */
    public function setProductGiftId($value);

    /**
     * Populate the object from array values. It is better to use setters instead of the generic setData method.
     *
     * @param array $values Value
     *
     * @return $this
     */
    public function populateFromArray(array $values);
}
