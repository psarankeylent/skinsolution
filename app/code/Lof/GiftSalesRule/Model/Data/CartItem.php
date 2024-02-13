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
use Lof\GiftSalesRule\Api\Data\CartItemInterface;

/**
 * CartItem model.
 *
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class CartItem extends AbstractExtensibleModel implements CartItemInterface
{
    /**
     * {@inheritdoc}
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setProductId($value)
    {
        return $this->setData(self::PRODUCT_ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getQty()
    {
        return $this->getData(self::QTY);
    }

    /**
     * {@inheritdoc}
     */
    public function setQty($value)
    {
        return $this->setData(self::QTY, $value);
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
    public function setExtensionAttributes(\Lof\GiftSalesRule\Api\Data\CartItemExtensionInterface $extensionAttributes)
    {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
