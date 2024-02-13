<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Helpdesk2
 * @version    2.0.6
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Helpdesk2\Model\Department\Option;

use Magento\Framework\Api\AbstractExtensibleObject;
use Aheadworks\Helpdesk2\Api\Data\DepartmentOptionValueInterface;

/**
 * Class Value
 *
 * @package Aheadworks\Helpdesk2\Model\Department\Option
 */
class Value extends AbstractExtensibleObject implements DepartmentOptionValueInterface
{
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getOptionId()
    {
        return $this->_get(self::OPTION_ID);
    }

    /**
     * @inheritdoc
     */
    public function setOptionId($optionId)
    {
        return $this->setData(self::OPTION_ID, $optionId);
    }

    /**
     * Get sort order
     *
     * @return int
     */
    public function getSortOrder()
    {
        return $this->_get(self::SORT_ORDER);
    }

    /**
     * @inheritdoc
     */
    public function setSortOrder($sortOrder)
    {
        return $this->setData(self::SORT_ORDER, $sortOrder);
    }

    /**
     * @inheritdoc
     */
    public function getStorefrontLabels()
    {
        return $this->_get(self::STOREFRONT_LABELS);
    }

    /**
     * @inheritdoc
     */
    public function setStorefrontLabels($storefrontLabels)
    {
        return $this->setData(self::STOREFRONT_LABELS, $storefrontLabels);
    }

    /**
     * @inheritdoc
     */
    public function getCurrentStorefrontLabel()
    {
        return $this->_get(self::CURRENT_STOREFRONT_LABEL);
    }

    /**
     * @inheritdoc
     */
    public function setCurrentStorefrontLabel($label)
    {
        return $this->setData(self::CURRENT_STOREFRONT_LABEL, $label);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(
        \Aheadworks\Helpdesk2\Api\Data\DepartmentOptionValueExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * @inheritdoc
     */
    public function getStorefrontLabelEntityType()
    {
        return self::STOREFRONT_LABEL_ENTITY_TYPE;
    }
}
