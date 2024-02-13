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
interface GiftItemInterface
{
    const ID                = 'id';
    const SKU                = 'sku';
    const NAME              = 'name';
    const GIFT_PRICE        = 'gift_price';
    const FREE_SHIP         = 'free_ship';
    const ADDED             = 'added';
    const CONFIGURABLE      = 'configurable';
    const HAS_OPTIONS       = 'has_options';
    const REQUIRED_OPTIONS  = 'required_options';
    const FINAL_PRICE       = 'final_price';
    const IMAGE             = 'image';
    /**
     * Get the id.
     *
     * @return int
     */
    public function getId();

    /**
     * Set the id.
     *
     * @param int $value Value
     *
     * @return $this
     */
    public function setId($value);

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName();

    /**
     * Set the name.
     *
     * @param string $value Value
     *
     * @return $this
     */
    public function setName($value);

    /**
     * Get the gift_price.
     *
     * @return string
     */
    public function getGiftPrice();

    /**
     * Set the gift_price.
     *
     * @param string $value Value
     *
     * @return $this
     */
    public function setGiftPrice($value);

    /**
     * Get the free_ship.
     *
     * @return bool
     */
    public function getFreeShip();

    /**
     * Set the free_ship.
     *
     * @param bool $value Value
     *
     * @return $this
     */
    public function setFreeShip($value);

    /**
     * Get the added.
     *
     * @return bool
     */
    public function getAdded();

    /**
     * Set the added.
     *
     * @param bool $value Value
     *
     * @return $this
     */
    public function setAdded($value);

    /**
     * Get the configurable.
     *
     * @return bool
     */
    public function getConfigurable();

    /**
     * Set the configurable.
     *
     * @param bool $value Value
     *
     * @return $this
     */
    public function setConfigurable($value);

    /**
     * Get the required_options.
     *
     * @return bool
     */
    public function getRequiredOptions();

    /**
     * Set the required_options.
     *
     * @param bool $value Value
     *
     * @return $this
     */
    public function setRequiredOptions($value);

    /**
     * Get the has_options.
     *
     * @return bool
     */
    public function getHasOptions();

    /**
     * Set the has_options.
     *
     * @param bool $value Value
     *
     * @return $this
     */
    public function setHasOptions($value);

    /**
     * Get the final_price.
     *
     * @return string
     */
    public function getFinalPrice();

    /**
     * Set the final_price.
     *
     * @param string $value Value
     *
     * @return $this
     */
    public function setFinalPrice($value);


    /**
     * Get the image.
     *
     * @return string
     */
    public function getImage();

    /**
     * Set the image.
     *
     * @param string $value Value
     *
     * @return $this
     */
    public function setImage($value);

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
     * Populate the object from array values. It is better to use setters instead of the generic setData method.
     *
     * @param array $values Value
     *
     * @return $this
     */
    public function populateFromArray(array $values);
}
