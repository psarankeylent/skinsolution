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
namespace Aheadworks\Helpdesk2\Model\Data\Validator\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;

/**
 * Class Agent
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Validator\Ticket
 */
class Agent extends AbstractValidator
{
    /**
     * @var DepartmentRepositoryInterface
     */
    private $departmentRepository;

    /**
     * @param DepartmentRepositoryInterface $departmentRepository
     */
    public function __construct(
        DepartmentRepositoryInterface $departmentRepository
    ) {
        $this->departmentRepository = $departmentRepository;
    }

    /**
     * Check if agent is correct
     *
     * @param TicketInterface $ticket
     * @return bool
     * @throws \Exception
     */
    public function isValid($ticket)
    {
        $this->_clearMessages();

        $department = $this->departmentRepository->get($ticket->getDepartmentId());
        $agentId = $ticket->getAgentId();
        $primaryAgentId = $department->getPrimaryAgentId();
        $departmentAgentIds = $department->getAgentIds();
        if ($agentId && $primaryAgentId != $agentId && !in_array($agentId, $departmentAgentIds)) {
            $this->_addMessages([__('Agent is not available for department: %1', $department->getName())]);
        }

        return empty($this->getMessages());
    }
}
