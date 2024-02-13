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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Permission;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Backend\Model\Auth\Session as AdminSession;
use Magento\User\Model\User;
use Aheadworks\Helpdesk2\Api\Data\DepartmentPermissionInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid\Collection as TicketCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department as DepartmentResource;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid as ticketResource;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;

/**
 * Class Manager
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Permission
 */
class Manager
{
    const TICKET_ACTION = 'ticket_action';

    /**
     * @var AdminSession
     */
    private $adminSession;

    /**
     * @var ticketResource
     */
    private $ticketResource;

    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @param AdminSession $adminSession
     * @param ticketResource $ticketResource
     * @param TicketRepositoryInterface $ticketRepository
     */
    public function __construct(
        AdminSession $adminSession,
        ticketResource $ticketResource,
        TicketRepositoryInterface $ticketRepository
    ) {
        $this->adminSession = $adminSession;
        $this->ticketResource = $ticketResource;
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * Apply agent filter to ticket collection
     *
     * @param TicketCollection $ticketCollection
     * @throws LocalizedException
     */
    public function applyAgentFilterToTicketCollection($ticketCollection)
    {
        $adminUser = $this->adminSession->getUser();
        if ($adminUser->getId()) {
            $connection = $ticketCollection->getConnection();
            $ticketCollection->getSelect()->joinLeft(
                ['dep_table' => $ticketCollection->getTable(DepartmentResource::MAIN_TABLE_NAME)],
                'main_table.department_id = dep_table.id',
                []
            )->joinLeft(
                ['dep_agent_table' => $ticketCollection->getTable(DepartmentResource::DEPARTMENT_AGENT_TABLE_NAME)],
                'dep_table.id = dep_agent_table.department_id AND dep_agent_table.agent_id = ' . $adminUser->getId(),
                []
            )->joinLeft(
                ['dep_perm_table' => $ticketCollection->getTable(DepartmentResource::DEPARTMENT_PERMISSION_TABLE_NAME)],
                $connection->quoteInto(
                    'dep_table.id = dep_perm_table.department_id AND dep_perm_table.type = ?',
                    DepartmentPermissionInterface::TYPE_VIEW
                ),
                []
            )->where(
                implode(' OR ', $this->prepareWhereCondition($connection, $adminUser))
            )->group('main_table.entity_id');
        }
    }

    /**
     * Check if current admin is able to perform ticket action
     *
     * @param int $ticketId
     * @param string $action
     * @return boolean
     * @throws LocalizedException
     */
    public function isAdminAbleToPerformTicketAction($ticketId, $action)
    {
        $adminUser = $this->adminSession->getUser();
        if (!$adminUser->getId()) {
            return false;
        }

        if ($action == DepartmentPermissionInterface::TYPE_UPDATE) {
            $ticket = $this->ticketRepository->getById($ticketId);
            if ($ticket->getIsLocked() && $ticket->getAgentId() != $adminUser->getId()) {
                return false;
            }
        }

        $connection = $this->ticketResource->getConnection();
        $select = $connection->select()
            ->from(['main_table' => $this->ticketResource->getMainTable()])
            ->joinLeft(
                ['dep_table' => $this->ticketResource->getTable(DepartmentResource::MAIN_TABLE_NAME)],
                'main_table.department_id = dep_table.id',
                []
            )->joinLeft(
                ['dep_agent_table' => $this->ticketResource->getTable(DepartmentResource::DEPARTMENT_AGENT_TABLE_NAME)],
                'dep_table.id = dep_agent_table.department_id AND dep_agent_table.agent_id = ' .  $adminUser->getId(),
                []
            )->joinLeft(
                ['dep_perm_table' => $this->ticketResource->getTable(
                    DepartmentResource::DEPARTMENT_PERMISSION_TABLE_NAME
                )],
                $connection->quoteInto(
                    'dep_table.id = dep_perm_table.department_id AND dep_perm_table.type = ?',
                    $action
                ),
                []
            )->where(
                implode(' OR ', $this->prepareWhereCondition($connection, $adminUser))
            )->where('main_table.entity_id = ?', $ticketId)
            ->group('main_table.entity_id');

        return !empty($connection->fetchRow($select));
    }

    /**
     * Prepare where condition
     *
     * @param AdapterInterface $connection
     * @param User $adminUser
     * @return array
     * @throws LocalizedException
     */
    private function prepareWhereCondition($connection, $adminUser)
    {
        return [
            $connection->quoteInto('dep_table.primary_agent_id = ?', $adminUser->getId()),
            $connection->quoteInto('dep_agent_table.agent_id = ?', $adminUser->getId()),
            $connection->quoteInto('main_table.agent_id = ?', $adminUser->getId()),
            $connection->quoteInto(
                'dep_perm_table.role_id IN (?) or dep_perm_table.role_id = 0',
                $adminUser->getRoles()
            )
        ];
    }
}
