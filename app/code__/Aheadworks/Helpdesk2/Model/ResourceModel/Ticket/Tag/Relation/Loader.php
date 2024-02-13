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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag\Relation;

use Aheadworks\Helpdesk2\Api\Data\TagInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketTagInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag as TagResource;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\MetadataPool;

class Loader
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(MetadataPool $metadataPool, ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
    }

    /**
     * Load tags for one ticket
     *
     * @param int $ticketId
     * @return array
     * @throws \Exception
     */
    public function loadForOneTicket($ticketId)
    {
        $connection = $this->getConnection();
        $select = $this->getSelect();
        $select->where('tag_ticket.ticket_id = :id', $ticketId);

        return $connection->fetchCol($select, ['id' => $ticketId]);
    }

    /**
     * Load tags for many tickets
     *
     * @param int[] $ticketIds
     * @return array
     * @throws \Exception
     */
    public function loadForManyTicket($ticketIds)
    {
        $connection = $this->getConnection();
        $select = $this->getSelect();
        $select->where('tag_ticket.ticket_id IN (?)', $ticketIds);

        $tags = $connection->fetchAll($select);

        $return = [];
        foreach ($tags as $tag) {
            $ticketId = $tag[TicketTagInterface::TICKET_ID];
            $return[$ticketId][] = $tag[TagInterface::NAME];
        }
        return $return;
    }

    /**
     * Retrieve select object
     *
     * @return \Magento\Framework\DB\Select
     * @throws \Exception
     */
    private function getSelect()
    {
        $connection = $this->getConnection();
        return $connection->select()
            ->from(['tag' => $this->resourceConnection->getTableName(TagResource::MAIN_TABLE_NAME)], 'name')
            ->joinLeft(
                ['tag_ticket' => $this->resourceConnection->getTableName(TagResource::RELATION_TABLE_NAME)],
                'tag.id = tag_ticket.tag_id',
                [TicketTagInterface::TICKET_ID => 'tag_ticket.ticket_id']
            );
    }

    /**
     * Get connection
     *
     * @return AdapterInterface
     * @throws \Exception
     */
    private function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->resourceConnection->getConnectionByName(
                $this->metadataPool->getMetadata(TicketInterface::class)->getEntityConnectionName()
            );
        }
        return $this->connection;
    }
}
