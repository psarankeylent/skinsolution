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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Department;

use Magento\Store\Model\Store;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\StorefrontLabel\AbstractCollection;
use Aheadworks\Helpdesk2\Model\Department as DepartmentModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department as DepartmentResourceModel;
use Aheadworks\Helpdesk2\Model\Data\Processor\Model\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\StorefrontLabel\Resolver;

/**
 * Class Collection
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Department
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = DepartmentInterface::ID;

    /**
     * @var ProcessorInterface
     */
    private $objectDataProcessor;

    /**
     * @var LoadHandler
     */
    private $loadHandler;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param Resolver $storefrontLabelResolver
     * @param ProcessorInterface $objectDataProcessor
     * @param LoadHandler $loadHandler
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        Resolver $storefrontLabelResolver,
        ProcessorInterface $objectDataProcessor,
        LoadHandler $loadHandler,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->objectDataProcessor = $objectDataProcessor;
        $this->loadHandler = $loadHandler;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $storefrontLabelResolver,
            $connection,
            $resource
        );
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(DepartmentModel::class, DepartmentResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function addFieldToFilter($field, $condition = null)
    {
        $fieldsToProcess = $this->processAddFieldToFilter($field, $condition);

        if (!empty($fieldsToProcess)) {
            return parent::addFieldToFilter($fieldsToProcess, $condition);
        }

        return $this;
    }

    /**
     * Process adding fields to filter
     *
     * @param string|array $field
     * @param null|string|array $condition
     * @return array|string
     */
    private function processAddFieldToFilter($field, $condition = null)
    {
        $fieldsToProcess = null;
        if (is_array($field)) {
            $fieldsToProcess = [];
            foreach ($field as $fieldName) {
                if ($fieldName === DepartmentInterface::STORE_IDS) {
                    $this->addStoreIdsFilter($condition);
                } else {
                    $fieldsToProcess[] = $fieldName;
                }
            }
        } else {
            if ($field === DepartmentInterface::STORE_IDS) {
                $this->addStoreIdsFilter($condition);
            } else {
                $fieldsToProcess = $field;
            }
        }

        return $fieldsToProcess;
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    protected function _afterLoad()
    {
        $this->attachRelationTable(
            DepartmentResourceModel::DEPARTMENT_STORE_TABLE_NAME,
            DepartmentInterface::ID,
            'department_id',
            'store_id',
            DepartmentInterface::STORE_IDS,
            [],
            [],
            true
        );
        $this->attachRelationTable(
            DepartmentResourceModel::DEPARTMENT_AGENT_TABLE_NAME,
            DepartmentInterface::ID,
            'department_id',
            'agent_id',
            DepartmentInterface::AGENT_IDS,
            [],
            [],
            true
        );
        $this->attachRelationTable(
            DepartmentResourceModel::DEPARTMENT_PERMISSION_TABLE_NAME,
            DepartmentInterface::ID,
            'department_id',
            ['role_id', 'type'],
            DepartmentInterface::PERMISSIONS,
            [],
            [],
            true
        );
        $this->attachRelationTable(
            DepartmentResourceModel::DEPARTMENT_GATEWAY_TABLE_NAME,
            DepartmentInterface::ID,
            'department_id',
            'gateway_id',
            DepartmentInterface::GATEWAY_IDS,
            [],
            [],
            true
        );

        /** @var DepartmentModel $model */
        foreach ($this as $model) {
            $this->loadHandler->load($model, $this->getStoreId());
            $this->objectDataProcessor->prepareModelAfterLoad($model);
        }

        return parent::_afterLoad();
    }

    /**
     * @inheritdoc
     */
    protected function _renderFiltersBefore()
    {
        $this->joinLinkageTable(
            DepartmentResourceModel::DEPARTMENT_STORE_TABLE_NAME,
            DepartmentInterface::ID,
            'department_id',
            DepartmentInterface::STORE_IDS,
            'store_id'
        );
        parent::_renderFiltersBefore();
    }

    /**
     * @inheritdoc
     */
    protected function getStorefrontLabelEntityType()
    {
        return DepartmentInterface::STOREFRONT_LABEL_ENTITY_TYPE;
    }

    /**
     * Add filter by store IDs
     *
     * @param int|array $store
     */
    private function addStoreIdsFilter($store)
    {
        if (!is_array($store)) {
            $store = [$store];
        }

        $store[] = [
            'eq' => Store::DEFAULT_STORE_ID
        ];

        $this->addFilter(DepartmentInterface::STORE_IDS, ['in' => $store], 'public');
    }
}
