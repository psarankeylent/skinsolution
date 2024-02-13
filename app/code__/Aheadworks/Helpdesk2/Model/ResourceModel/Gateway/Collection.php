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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Gateway;

use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\AbstractCollection;
use Aheadworks\Helpdesk2\Model\Gateway as GatewayModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway as GatewayResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department as DepartmentResourceModel;
use Aheadworks\Helpdesk2\Model\Data\Processor\Model\ProcessorInterface;

/**
 * Class Collection
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Gateway
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = GatewayDataInterface::ID;

    /**
     * @var ProcessorInterface
     */
    private $objectDataProcessor;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param ProcessorInterface $objectDataProcessor
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        ProcessorInterface $objectDataProcessor,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->objectDataProcessor = $objectDataProcessor;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(GatewayModel::class, GatewayResourceModel::class);
    }

    /**
     * Apply not assigned filter
     *
     * @return $this
     */
    public function applyNotAssignedFilter()
    {
        $this
            ->getSelect()
            ->joinLeft(
                ['dep_gate_tbl' => $this->getTable(DepartmentResourceModel::DEPARTMENT_GATEWAY_TABLE_NAME)],
                'main_table.id = dep_gate_tbl.gateway_id',
                []
            )->where('dep_gate_tbl.department_id is null');

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function _afterLoad()
    {
        /** @var GatewayModel $model */
        foreach ($this as $model) {
            $this->objectDataProcessor->prepareModelAfterLoad($model);
        }

        return parent::_afterLoad();
    }
}
