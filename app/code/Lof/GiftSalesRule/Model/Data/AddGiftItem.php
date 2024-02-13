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

use Magento\Framework\Model\AbstractExtensibleModel;
use Magento\Framework\Api\AttributeValueFactory;
use Magento\Framework\DataObject;
use Lof\GiftSalesRule\Api\Data\AddGiftItemInterface;

/**
 * AddGiftItem model.
 *
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class AddGiftItem extends AbstractExtensibleModel implements AddGiftItemInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCartId()
    {
        return $this->getData(self::CART_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setCartId($value)
    {
        return $this->setData(self::CART_ID, $value);
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
    public function getGiftRuleCode()
    {
        return $this->getData(self::GIFT_RULE_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setGiftRuleCode($value)
    {
        return $this->setData(self::GIFT_RULE_CODE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getProducts()
    {
        return $this->getData(self::PRODUCTS);
    }

    /**
     * {@inheritdoc}
     */
    public function setProducts(array $products = null)
    {
        return $this->setData(self::PRODUCTS, $products);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        if (!$this->_getExtensionAttributes()) {
            $this->setExtensionAttributes(
                $this->extensionAttributesFactory->create(get_class($this))
            );
        }
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(\Lof\GiftSalesRule\Api\Data\AddGiftItemExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
