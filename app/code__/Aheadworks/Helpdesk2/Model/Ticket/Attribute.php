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
namespace Aheadworks\Helpdesk2\Model\Ticket;

use Magento\Eav\Model\Entity\Attribute as EavAttribute;
use Magento\Eav\Model\Entity\Attribute\Source\Table as EavSourceTable;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Magento\Eav\Model\ResourceModel\Entity\Attribute as AttributeResourceModel;
use Aheadworks\Helpdesk2\Api\Data\TicketAttributeInterface;

/**
 * Class Attribute
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket
 */
class Attribute extends EavAttribute implements TicketAttributeInterface, ScopedAttributeInterface
{
    const MODULE_NAME = 'Aheadworks_Helpdesk2';

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(AttributeResourceModel::class);
    }

    /**
     * Retrieve source model
     *
     * @return AbstractSource
     */
    public function getSourceModel()
    {
        //todo check if this method is needed
        $model = $this->_getData('source_model');
        if (empty($model)) {
            if ($this->getBackendType() == 'int' && $this->getFrontendInput() == 'select') {
                return $this->_getDefaultSourceModel();
            }
        }
        return $model;
    }

    /**
     * Get default attribute source model
     *
     * @return string
     */
    public function _getDefaultSourceModel()
    {
        return EavSourceTable::class;
    }

    /**
     * Retrieve attribute is global scope flag
     *
     * @return bool
     */
    public function isScopeGlobal()
    {
        return $this->getIsGlobal() == self::SCOPE_GLOBAL;
    }

    /**
     * @inheritdoc
     */
    public function getIsGlobal()
    {
        return (bool)$this->_getData(self::IS_GLOBAL);
    }

    /**
     * @inheritdoc
     */
    public function setIsGlobal($isGlobal)
    {
        $this->setData(self::IS_GLOBAL, $isGlobal);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsVisibleInGrid()
    {
        return (bool)$this->_getData(self::IS_VISIBLE_IN_GRID);
    }

    /**
     * @inheritdoc
     */
    public function setIsVisibleInGrid($isVisibleInGrid)
    {
        $this->setData(self::IS_VISIBLE_IN_GRID, $isVisibleInGrid);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getIsFilterableInGrid()
    {
        return (bool)$this->_getData(self::IS_FILTERABLE_IN_GRID);
    }

    /**
     * @inheritdoc
     */
    public function setIsFilterableInGrid($isFilterableInGrid)
    {
        $this->setData(self::IS_FILTERABLE_IN_GRID, $isFilterableInGrid);
        return $this;
    }
}
