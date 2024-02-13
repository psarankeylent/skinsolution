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
interface GiftRuleDataInterface
{
    const RULE_ID                = 'rule_id';
    const CODE                   = 'code';
    const LABEL                  = 'label';
    const QUOTE_ITEMS            = 'quote_items';
    const GIFT_QUOTE_ITEMS       = 'gift_quote_items';
    const NUMBER_OFFERED_PRODUCT = 'number_offered_product';
    const PRODUCT_ITEMS          = 'product_items';
    const REST_NUMBER            = 'rest_number';
    const NOTICE                 = 'notice';
    const CURRENT_QUOTE_ITEMS    = 'current_quote_items';
    const GIFT_ITEMS             = 'gift_items';
    /**
     * Get the number offered product.
     *
     * @return int
     */
    public function getNumberOfferedProduct();

    /**
     * Set the number offered product.
     *
     * @param int $value Value
     *
     * @return $this
     */
    public function setNumberOfferedProduct($value);

    /**
     * Get the code.
     *
     * @return string
     */
    public function getCode();

    /**
     * Set the code.
     *
     * @param string $value Value
     *
     * @return $this
     */
    public function setCode($value);

    /**
     * Get the rule id.
     *
     * @return int
     */
    public function getRuleId();

    /**
     * Set the rule id.
     *
     * @param int $value Value
     *
     * @return $this
     */
    public function setRuleId($value);

    /**
     * Get the label.
     *
     * @return string
     */
    public function getLabel();

    /**
     * Set the label.
     *
     * @param string $value Value
     *
     * @return $this
     */
    public function setLabel($value);

    /**
     * Get the code.
     *
     * @return mixed[]
     * [
     *     {product_id} => {qty}
     *     ...
     * ]
     */
    public function getQuoteItems();

    /**
     * Set the code.
     *
     * @param array $items Items
     *
     * @return $this
     */
    public function setGiftQuoteItems($items);

    /**
     * Get the code.
     *
     * @return mixed[]
     * [
     *     {product_id} => {qty}
     *     ...
     * ]
     */
    public function getGiftQuoteItems();

    /**
     * Set the code.
     *
     * @param array $items Items
     *
     * @return $this
     */
    public function setQuoteItems($items);

    /**
     * Get the code.
     *
     * @return mixed[]
     * [
     *     {product_id} => [ {product_data} ]
     *     ...
     * ]
     */
    public function getProductItems();

    /**
     * Set the code.
     *
     * @param array $items Items
     *
     * @return $this
     */
    public function setProductItems($items);

    /**
     * Get the rest number.
     *
     * @return int
     */
    public function getRestNumber();

    /**
     * Set the rest number.
     *
     * @param int $value Value
     *
     * @return $this
     */
    public function setRestNumber($value);

    /**
     * Populate the object from array values. It is better to use setters instead of the generic setData method.
     *
     * @param array $values Value
     *
     * @return $this
     */
    public function populateFromArray(array $values);

    /**
     * Get the code.
     *
     * @return string
     */
    public function getNotice();

    /**
     * Set the code.
     *
     * @param array $value value
     *
     * @return $this
     */
    public function setNotice($value);

    /**
     * get gift_items.
     *
     * @return \Lof\GiftSalesRule\Api\Data\GiftItemInterface[]|null Array of items. Otherwise, null.
     */
    public function getGiftItems();

    /**
     * Sets gift_items.
     *
     * @param \Lof\GiftSalesRule\Api\Data\GiftItemInterface[] $items
     * @return $this
     */
    public function setGiftItems(array $items = null);
}
