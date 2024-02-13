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
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Model\Ticket;

use Aheadworks\Helpdesk2\Model\Source\Department\AgentList as AgentListSource;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Model\ProcessorInterface;
use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;

/**
 * Class Agent
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Model\Ticket
 */
class Agent implements ProcessorInterface
{
    /**
     * @var DepartmentRepositoryInterface
     */
    private $departmentRepository;

    /**
     * @param DepartmentRepositoryInterface $departmentRepository
     */
    public function __construct(DepartmentRepositoryInterface $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
    }

    /**
     * Prepare model before save
     *
     * @param TicketInterface $ticket
     * @return TicketInterface
     * @throws NoSuchEntityException
     */
    public function prepareModelBeforeSave($ticket)
    {
        if (!$ticket->getAgentId() && !$ticket->getEntityId()) {
            $department = $this->departmentRepository->get(
                $ticket->getDepartmentId(),
                $ticket->getStoreId()
            );
            if ($department->getPrimaryAgentId()) {
                $ticket->setAgentId($department->getPrimaryAgentId());
            } else {
                $ticket->setAgentId(AgentListSource::NOT_ASSIGNED_VALUE);
            }
        }

        return $ticket;
    }

    /**
     * Prepare model after load
     *
     * @param TicketInterface $ticket
     * @return TicketInterface
     */
    public function prepareModelAfterLoad($ticket)
    {
        return $ticket;
    }
}
