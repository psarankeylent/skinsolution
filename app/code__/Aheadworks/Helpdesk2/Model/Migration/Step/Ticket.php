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
namespace Aheadworks\Helpdesk2\Model\Migration\Step;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterfaceFactory;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Migration\Source\Helpdesk1TableNames;
use Aheadworks\Helpdesk2\Model\Migration\Step\Ticket\MessagesMigrator;
use Aheadworks\Helpdesk2\Model\Migration\Step\Ticket\MigrationHelper;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket as TicketResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid as TicketGridResourceModel;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\FlagManager;

/**
 * Class Ticket
 *
 * @package Aheadworks\Helpdesk2\Model\Migration\Step
 */
class Ticket implements MigrationStepInterface
{
    const DEFAULT_COUNT_OF_TICKETS_TO_MIGRATE = 100000;
    const PAGE_SIZE = 100;
    const LAST_MIGRATED_TICKET_ID_FLAG_CODE = 'aw_helpdesk2_last_migrated_ticket_id';

    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var FlagManager
     */
    private $flagManager;

    /**
     * @var MigrationHelper
     */
    private $migrationHelper;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var TicketGridResourceModel
     */
    private $ticketGridResource;

    /**
     * @var MessagesMigrator
     */
    private $messagesMigrator;

    /**
     * @param TicketRepositoryInterface $ticketRepository
     * @param FlagManager $flagManager
     * @param MigrationHelper $migrationHelper
     * @param MessagesMigrator $messagesMigrator
     * @param ResourceConnection $resource
     * @param TicketGridResourceModel $ticketGridResource
     */
    public function __construct(
        TicketRepositoryInterface $ticketRepository,
        FlagManager $flagManager,
        MigrationHelper $migrationHelper,
        MessagesMigrator $messagesMigrator,
        ResourceConnection $resource,
        TicketGridResourceModel $ticketGridResource
    ) {
        $this->ticketRepository = $ticketRepository;
        $this->flagManager = $flagManager;
        $this->migrationHelper = $migrationHelper;
        $this->messagesMigrator = $messagesMigrator;
        $this->resourceConnection = $resource;
        $this->ticketRepository = $ticketRepository;
        $this->ticketGridResource = $ticketGridResource;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function migrate($limit)
    {
        if ($limit) {
            $pageCount = $limit <= self::PAGE_SIZE ? 1 : ceil($limit / self::PAGE_SIZE);
        } else {
            $pageCount = ceil(self::DEFAULT_COUNT_OF_TICKETS_TO_MIGRATE / self::PAGE_SIZE);
            $limit = self::DEFAULT_COUNT_OF_TICKETS_TO_MIGRATE;
        }
        $currentPageNumber = 1;
        $migratedEntityCount = 0;
        $lastMigratedTicketIdBeforeExecution = $this->getLastMigratedTicketId();

        $hdu1TicketsChunk = $this->getTicketsToMerge(
            $limit <= self::PAGE_SIZE ? $limit : self::PAGE_SIZE,
            $currentPageNumber,
            $lastMigratedTicketIdBeforeExecution
        );

        while ($pageCount >= $currentPageNumber) {
            if (empty($hdu1TicketsChunk)) {
                break;
            }
            $lastMigratedTicketId = null;
            foreach ($hdu1TicketsChunk as $ticketData) {
                $hdu1TicketId = $ticketData[TicketInterface::ENTITY_ID];
                if ($this->ifExists($hdu1TicketId)) {
                    continue;
                }
                try {
                    // ticket
                    $ticketData = $this->migrationHelper->prepareTicketData($ticketData);
                    $ticketId = $this->createTicket($ticketData);

                    // messages
                    $this->messagesMigrator->migrateMessages($hdu1TicketId, $ticketId);

                    // update flat grid
                    $migratedTicket = $this->ticketRepository->getById($ticketId);
                    $this->ticketGridResource->refresh($migratedTicket);

                    $migratedEntityCount++;
                    $lastMigratedTicketId = $hdu1TicketId;
                } catch (\Exception $exception) {
                    throw new LocalizedException(
                        __("Ticket entity ID: %1", $hdu1TicketId . " - " . $exception->getMessage())
                    );
                }
            }
            $currentPageNumber++;
            $this->saveLastMigratedTicketId($lastMigratedTicketId);
            $hdu1TicketsChunk = $this->getTicketsToMerge(
                self::PAGE_SIZE,
                $currentPageNumber,
                $lastMigratedTicketIdBeforeExecution
            );
        }
        
        $result = $migratedEntityCount . ' tickets were migrated';
        if (($migratedEntityCount === $limit || $migratedEntityCount === self::DEFAULT_COUNT_OF_TICKETS_TO_MIGRATE)) {
            $result .= '. Run command again to migrate more tickets';
        }

        return $result;
    }

    /**
     * Get ticket items
     *
     * @param int $pageSize
     * @param int $pageNumber
     * @param int $minTicketId
     * @return array[]
     */
    private function getTicketsToMerge($pageSize, $pageNumber, $minTicketId)
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection
            ->select()
            ->from(
                ['main_table' => $connection->getTableName(Helpdesk1TableNames::TICKET)],
                [
                    TicketInterface::ENTITY_ID => 'main_table.id',
                    TicketInterface::UID => 'main_table.uid',
                    TicketInterface::SUBJECT => 'main_table.subject',
                    TicketInterface::DEPARTMENT_ID => 'main_table.department_id',
                    TicketInterface::AGENT_ID => 'main_table.agent_id',
                    TicketInterface::STORE_ID => 'main_table.store_id',
                    TicketInterface::ORDER_ID => 'main_table.order_id',
                    TicketInterface::CUSTOMER_ID => 'main_table.customer_id',
                    TicketInterface::CUSTOMER_EMAIL => 'main_table.customer_email',
                    TicketInterface::CUSTOMER_NAME => 'main_table.customer_name',
                    TicketInterface::CC_RECIPIENTS => 'main_table.cc_recipients',
                    TicketInterface::STATUS_ID => 'main_table.status',
                    TicketInterface::PRIORITY_ID => 'main_table.priority',
                    TicketInterface::CREATED_AT => 'main_table.created_at'
                ]
            )
            ->limitPage($pageNumber, $pageSize);

        if ($minTicketId > 0) {
            $select->where('main_table.id > ?', $minTicketId, 'gt');
        }

        return $connection->fetchAll($select);
    }

    /**
     * Get last migrated ticket ID
     *
     * @return int
     */
    private function getLastMigratedTicketId()
    {
        return (int)$this->flagManager->getFlagData(self::LAST_MIGRATED_TICKET_ID_FLAG_CODE);
    }

    /**
     * Save last migrated ticket ID
     *
     * @param int $lastMigratedTicketId
     * @return $this
     */
    private function saveLastMigratedTicketId($lastMigratedTicketId)
    {
        $this->flagManager->saveFlag(self::LAST_MIGRATED_TICKET_ID_FLAG_CODE, $lastMigratedTicketId);
        return $this;
    }

    /**
     * Create ticket
     *
     * @param array $ticketData
     * @return int
     */
    private function createTicket($ticketData)
    {
        $tableName = TicketResourceModel::TICKET_ENTITY_TABLE_NAME;
        $connection = $this->resourceConnection->getConnection();
        $connection->insertOnDuplicate($tableName, $ticketData);

        return $connection->lastInsertId($tableName);
    }

    /**
     * Check if ticket already exists
     *
     * @param int $ticketId
     * @return bool
     */
    public function ifExists($ticketId)
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection
            ->select()
            ->from($this->resourceConnection->getTableName(TicketResourceModel::TICKET_ENTITY_TABLE_NAME))
            ->where('entity_id = ?', $ticketId);

        return (bool)$connection->fetchRow($select);
    }
}
