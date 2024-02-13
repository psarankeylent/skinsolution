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
namespace Aheadworks\Helpdesk2\Model\Migration;

use Magento\Framework\FlagManager;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ResourceConnection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department as DepResource;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket as TicketResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message as MessageResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid as TicketGridResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\QuickResponse as QuickResponseResourceModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\StorefrontLabel\Repository as StorefrontLabelRepository;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway as GatewayResourceModel;
use Aheadworks\Helpdesk2\Model\Migration\Step\MigrationStepInterface;
use Aheadworks\Helpdesk2\Model\Migration\Step\Ticket as TicketStep;

/**
 * Class Processor
 *
 * @package Aheadworks\Helpdesk2\Model\Migration
 */
class Processor
{
    const MIGRATION_COMPLETED_FLAG = 'aw_helpdesk2_migration_completed';

    /**
     * @var MigrationStepInterface
     */
    private $migrationSteps;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var FlagManager
     */
    private $flagManager;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @param LoggerInterface $logger
     * @param FlagManager $flagManager
     * @param ResourceConnection $resource
     * @param MigrationStepInterface[] $migrationSteps
     */
    public function __construct(
        LoggerInterface $logger,
        FlagManager $flagManager,
        ResourceConnection $resource,
        array $migrationSteps
    ) {
        $this->logger = $logger;
        $this->flagManager = $flagManager;
        $this->resource = $resource;
        $this->migrationSteps = $migrationSteps;
    }

    /**
     * Process data migration
     *
     * @param bool $cleanBeforeRun
     * @param int $limit
     */
    public function process($cleanBeforeRun, $limit)
    {
        $this->writeToLog('Migration STARTED');
        $this->flagManager->deleteFlag(self::MIGRATION_COMPLETED_FLAG);
        if ($cleanBeforeRun) {
            $this->truncateData();
            $this->writeToLog('Old database entries cleared');
        }

        foreach ($this->migrationSteps as $step) {
            try {
                $result = $step->migrate($limit);
                $this->writeToLog($result);
            } catch (\Exception $exception) {
                $this->writeToLog('Critical error occurred');
                $this->writeToLog($exception->getMessage());
                $this->writeToLog('Try again');
                break;
            }
        }

        $this->flagManager->saveFlag(self::MIGRATION_COMPLETED_FLAG, 1);
        $this->writeToLog('Migration FINISHED');
    }

    /**
     * Truncate data
     */
    private function truncateData()
    {
        $conn = $this->resource->getConnection();
        $conn->query('SET FOREIGN_KEY_CHECKS = 0;');
        $conn->truncateTable($this->resource->getTableName(DepResource::DEPARTMENT_PERMISSION_TABLE_NAME));
        $conn->truncateTable($this->resource->getTableName(DepResource::DEPARTMENT_STORE_TABLE_NAME));
        $conn->truncateTable($this->resource->getTableName(DepResource::DEPARTMENT_AGENT_TABLE_NAME));
        $conn->truncateTable($this->resource->getTableName(DepResource::DEPARTMENT_OPTION_TYPE_VALUE_TABLE_NAME));
        $conn->truncateTable($this->resource->getTableName(DepResource::DEPARTMENT_OPTION_TABLE_NAME));
        $conn->truncateTable($this->resource->getTableName(DepResource::DEPARTMENT_GATEWAY_TABLE_NAME));
        $conn->truncateTable($this->resource->getTableName(DepResource::MAIN_TABLE_NAME));

        $conn->truncateTable($this->resource->getTableName(TicketGridResourceModel::MAIN_TABLE_NAME));
        $conn->truncateTable($this->resource->getTableName(MessageResourceModel::ATTACHMENT_TABLE_NAME));
        $conn->truncateTable($this->resource->getTableName(MessageResourceModel::MAIN_TABLE_NAME));
        $conn->truncateTable($this->resource->getTableName(TicketResourceModel::TICKET_ENTITY_TEXT_TABLE_NAME));
        $conn->truncateTable($this->resource->getTableName(TicketResourceModel::TICKET_ENTITY_INT_TABLE_NAME));
        $conn->truncateTable($this->resource->getTableName(TicketResourceModel::TICKET_ENTITY_VARCHAR_TABLE_NAME));
        $conn->truncateTable($this->resource->getTableName(TicketResourceModel::TICKET_OPTION_TABLE_NAME));
        $conn->truncateTable($this->resource->getTableName(TicketResourceModel::TICKET_ENTITY_TABLE_NAME));

        $conn->truncateTable($this->resource->getTableName(QuickResponseResourceModel::MAIN_TABLE_NAME));
        $conn->truncateTable($this->resource->getTableName(StorefrontLabelRepository::MAIN_TABLE_NAME));
        $conn->truncateTable($this->resource->getTableName(GatewayResourceModel::MAIN_TABLE_NAME));
        $conn->query('SET FOREIGN_KEY_CHECKS = 1;');

        $this->flagManager->deleteFlag(TicketStep::LAST_MIGRATED_TICKET_ID_FLAG_CODE);
    }

    /**
     * Write message to system.log
     *
     * @param string $message
     */
    private function writeToLog($message) {
        $message = 'HDU2 Migration: ' . $message;
        $this->logger->info($message);
    }
}
