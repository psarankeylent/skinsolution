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
use Lof\GiftSalesRule\Api\Data\GiftItemInterface;

/**
 * GiftItem model.
 *
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class GiftItem extends DataObject implements GiftItemInterface
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * {@inheritdoc}
     */
    public function setId($value)
    {
        return $this->setData(self::ID, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * {@inheritdoc}
     */
    public function setName($value)
    {
        return $this->setData(self::NAME, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getGiftPrice()
    {
        return $this->getData(self::GIFT_PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setGiftPrice($value)
    {
        return $this->setData(self::GIFT_PRICE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getFreeShip()
    {
        return $this->getData(self::FREE_SHIP);
    }

    /**
     * {@inheritdoc}
     */
    public function setFreeShip($value)
    {
        return $this->setData(self::FREE_SHIP, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdded()
    {
        return $this->getData(self::ADDED);
    }

    /**
     * {@inheritdoc}
     */
    public function setAdded($value)
    {
        return $this->setData(self::ADDED, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getConfigurable()
    {
        return $this->getData(self::CONFIGURABLE);
    }

    /**
     * {@inheritdoc}
     */
    public function setConfigurable($value)
    {
        return $this->setData(self::CONFIGURABLE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return $this->getData(self::REQUIRED_OPTIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function setRequiredOptions($value)
    {
        return $this->setData(self::REQUIRED_OPTIONS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getHasOptions()
    {
        return $this->getData(self::HAS_OPTIONS);
    }

    /**
     * {@inheritdoc}
     */
    public function setHasOptions($value)
    {
        return $this->setData(self::HAS_OPTIONS, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getFinalPrice()
    {
        return $this->getData(self::FINAL_PRICE);
    }

    /**
     * {@inheritdoc}
     */
    public function setFinalPrice($value)
    {
        return $this->setData(self::FINAL_PRICE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getImage()
    {
        return $this->getData(self::IMAGE);
    }

    /**
     * {@inheritdoc}
     */
    public function setImage($value)
    {
        return $this->setData(self::IMAGE, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getSku()
    {
        return $this->getData(self::SKU);
    }

    /**
     * {@inheritdoc}
     */
    public function setSku($value)
    {
        return $this->setData(self::SKU, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function populateFromArray(array $values)
    {
        $this->setId($values['id']);
        $this->setName($values['name']);
        if(isset($values['sku'])){
            $this->setSku($values['sku']);
        }
        $this->setGiftPrice($values['gift_price']);
        $this->setFreeShip($values['free_ship']);
        $this->setAdded($values['added']);
        $this->setConfigurable($values['configurable']);
        $this->setRequiredOptions($values['required_options']);
        $this->setHasOptions($values['has_options']);
        $this->setFinalPrice($values['final_price']);
        $this->setImage($values['image']);

        return $this;
    }
}
