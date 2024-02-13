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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid\Backend;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid as TicketGridResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid\Collection as TicketCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Permission\Manager as PermissionManager;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag as TagResource;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag\Relation\Loader as TagLoader;
use Magento\Framework\Api\Search\AggregationInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Psr\Log\LoggerInterface;

/**
 * Class Collection
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid\Backend
 */
class Collection extends TicketCollection implements SearchResultInterface
{
    /**
     * @var AggregationInterface
     */
    private $aggregations;

    /**
     * @var PermissionManager
     */
    private $permissionManager;

    /**
     * @var TagLoader
     */
    private $tagLoader;

    /**
     * @var array
     */
    private $joinColumns = [
        'department_id' => 'main_table.department_id',
        'agent_id' => 'main_table.agent_id',
    ];

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param PermissionManager $permissionManager
     * @param TagLoader $tagLoader
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        PermissionManager $permissionManager,
        TagLoader $tagLoader,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->permissionManager = $permissionManager;
        $this->tagLoader = $tagLoader;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(Document::class, TicketGridResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        parent::_initSelect();
        $this->permissionManager->applyAgentFilterToTicketCollection($this);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addFieldToFilter($field, $condition = null)
    {
        $process = function ($field) {
            if (isset($this->joinColumns[$field])) {
                $this->addFilterToMap($field, $this->joinColumns[$field]);
            } elseif ($field === TicketInterface::TAG_NAMES) {
                $this->addTagNamesFilter($field);
            } else {
                $this->addFilterToMap($field, 'main_table.' . $field);
            }
        };

        if (is_array($field)) {
            array_walk($field, $process);
        } else {
            $process($field);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add tag_names filter
     *
     * @param string $field
     */
    private function addTagNamesFilter($field)
    {
        $this
            ->getSelect()
            ->joinLeft(
                ['ticket_tag_table' => $this->getTable(TagResource::RELATION_TABLE_NAME)],
                'main_table.entity_id = ticket_tag_table.ticket_id',
                []
            )
            ->group('main_table.entity_id');
        $this->addFilterToMap($field, 'ticket_tag_table.tag_id');
    }

    /**
     * @inheritdoc
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * @inheritdoc
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * @inheritdoc
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    protected function _afterLoad()
    {
        $this->attachTagNames();
        return parent::_afterLoad();
    }

    /**
     * Attach tag names to collection items
     *
     * @return void
     * @throws \Exception
     */
    private function attachTagNames()
    {
        $ids = $this->getColumnValues(TicketInterface::ENTITY_ID);
        if (count($ids)) {
            $tags = $this->tagLoader->loadForManyTicket($ids);
            foreach ($this as $item) {
                $ticketId = $item->getData(TicketInterface::ENTITY_ID);
                if (isset($tags[$ticketId])) {
                    $item->setData(TicketInterface::TAG_NAMES, implode(', ', $tags[$ticketId]));
                }
            }
        }
    }
}
