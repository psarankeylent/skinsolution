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
namespace Lof\GiftSalesRule\Model\Data;

use Magento\Framework\DataObject;
use Lof\GiftSalesRule\Api\Data\AddGiftRuleDataInterface;

/**
 * AddGiftRuleData model.
 *
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class AddGiftRuleData extends DataObject implements AddGiftRuleDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($value)
    {
        return $this->setData(self::STATUS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getMessage()
    {
        return $this->getData(self::MESSAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setMessage($value)
    {
        return $this->setData(self::MESSAGE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getGiftRuleId()
    {
        return $this->getData(self::GIFT_RULE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setGiftRuleId($value)
    {
        return $this->setData(self::GIFT_RULE_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuoteId()
    {
        return $this->getData(self::QUOTE_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setQuoteId($value)
    {
        return $this->setData(self::QUOTE_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getQuoteItemId()
    {
        return $this->getData(self::QUOTE_ITEM_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setQuoteItemId($value)
    {
        return $this->setData(self::QUOTE_ITEM_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getProductGiftId()
    {
        return $this->getData(self::PRODUCT_GIFT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductGiftId($value)
    {
        return $this->setData(self::PRODUCT_GIFT_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function populateFromArray(array $values)
    {
        $this->setStatus($values['status']);
        $this->setMessage($values['message']);
        $this->setGiftRuleId($values['gift_rule_id']);
        $this->setQuoteId($values['quote_id']);
        $this->setQuoteItemId($values['quote_item_id']);
        $this->setProductGiftId($values['product_gift_id']);

        return $this;
    }
}
