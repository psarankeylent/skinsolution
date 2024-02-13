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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\RejectingPattern;

use Aheadworks\Helpdesk2\Model\ResourceModel\AbstractCollection;
use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface;
use Aheadworks\Helpdesk2\Model\Rejection\Pattern as RejectingPatternModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\RejectingPattern as RejectingPatternResourceModel;

/**
 * Class Collection
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\RejectingPattern
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = RejectingPatternInterface::ID;

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(RejectingPatternModel::class, RejectingPatternResourceModel::class);
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    protected function _afterLoad()
    {
        $this->attachRelationTable(
            RejectingPatternResourceModel::SCOPE_TABLE,
            RejectingPatternInterface::ID,
            'pattern_id',
            'scope',
            RejectingPatternInterface::SCOPE_TYPES,
            [],
            [],
            true
        );

        return parent::_afterLoad();
    }

    /**
     * @inheritdoc
     */
    protected function _renderFiltersBefore()
    {
        $this->joinLinkageTable(
            RejectingPatternResourceModel::SCOPE_TABLE,
            RejectingPatternInterface::ID,
            'pattern_id',
            RejectingPatternInterface::SCOPE_TYPES,
            'scope'
        );

        parent::_renderFiltersBefore();
    }
}
