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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\AbstractEavCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket as TicketResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid as TicketGridResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag\Relation\Loader as TagLoader;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag as TagResourceModel;
use Aheadworks\Helpdesk2\Model\Ticket as TicketModel;
use Magento\Eav\Model\Config;
use Magento\Eav\Model\ResourceModel\Helper;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactory;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Validator\UniversalFactory;
use Psr\Log\LoggerInterface;

/**
 * Class Collection
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Ticket
 */
class Collection extends AbstractEavCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = TicketInterface::ENTITY_ID;

    /**
     * @var TagLoader
     */
    private $tagLoader;

    /**
     * @param EntityFactory $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param Config $eavConfig
     * @param ResourceConnection $resource
     * @param \Magento\Eav\Model\EntityFactory $eavEntityFactory
     * @param Helper $resourceHelper
     * @param UniversalFactory $universalFactory
     * @param TagLoader $tagLoader
     * @param AdapterInterface|null $connection
     */
    public function __construct(
        EntityFactory $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        Config $eavConfig,
        ResourceConnection $resource,
        \Magento\Eav\Model\EntityFactory $eavEntityFactory,
        Helper $resourceHelper,
        UniversalFactory $universalFactory,
        TagLoader $tagLoader,
        AdapterInterface $connection = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $eavConfig,
            $resource,
            $eavEntityFactory,
            $resourceHelper,
            $universalFactory,
            $connection
        );
        $this->tagLoader = $tagLoader;
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(TicketModel::class, TicketResourceModel::class);
    }

    /**
     * Prepare collection for automation
     *
     * @return $this
     */
    public function prepareForAutomation()
    {
        $this->getSelect()->reset(Select::COLUMNS)->columns([TicketInterface::CUSTOMER_ID]);
        $this->joinTicketAggregatedTable();
        $this
            ->getSelect()
            ->joinLeft(
                ['customer' => $this->getTable('customer_entity')],
                'e.customer_id = customer.entity_id',
                ['group_id' => 'customer.group_id']
            );

        return $this;
    }

    /**
     * Join ticket aggregated table
     *
     * @return $this
     */
    public function joinTicketAggregatedTable()
    {
        $this
            ->getSelect()
            ->join(
                ['grid_tbl' => $this->getTable(TicketGridResourceModel::MAIN_TABLE_NAME)],
                'e.entity_id = grid_tbl.entity_id'
            );

        return $this;
    }

    /**
     * Join ticket tag table
     *
     * @return $this
     */
    public function joinTagTable()
    {
        $this
            ->getSelect()
            ->joinLeft(
                ['ticket_tag_table' => $this->getTable(TagResourceModel::RELATION_TABLE_NAME)],
                'e.entity_id = ticket_tag_table.ticket_id',
                []
            )->group('e.entity_id');

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
                    $item->setData(TicketInterface::TAG_NAMES, $tags[$ticketId]);
                }
            }
        }
    }
}
