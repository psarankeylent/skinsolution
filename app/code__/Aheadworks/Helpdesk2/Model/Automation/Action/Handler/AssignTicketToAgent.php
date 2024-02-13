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
namespace Aheadworks\Helpdesk2\Model\Automation\Action\Handler;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Automation\Action\Message\Management as MessageManagement;
use Aheadworks\Helpdesk2\Model\Source\Department\AgentList as Source;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Helpdesk2\Model\Automation\Action\ActionInterface;
use Aheadworks\Helpdesk2\Model\User\Repository as UserRepository;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Source\Department\AgentList;

/**
 * Class AssignTicketToAgent
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Action\Handler
 */
class AssignTicketToAgent implements ActionInterface
{
    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var DepartmentRepositoryInterface
     */
    private $departmentRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var MessageManagement
     */
    private $messageManagement;

    /**
     * @var Source
     */
    private $source;

    /**
     * @param TicketRepositoryInterface $ticketRepository
     * @param DepartmentRepositoryInterface $departmentRepository
     * @param UserRepository $userRepository
     * @param MessageManagement $messageManagement
     * @param Source $source
     */
    public function __construct(
        TicketRepositoryInterface $ticketRepository,
        DepartmentRepositoryInterface $departmentRepository,
        UserRepository $userRepository,
        MessageManagement $messageManagement,
        Source $source
    ) {
        $this->ticketRepository = $ticketRepository;
        $this->departmentRepository = $departmentRepository;
        $this->userRepository = $userRepository;
        $this->messageManagement = $messageManagement;
        $this->source = $source;
    }

    /**
     * @inheritdoc
     */
    public function run($actionData, $eventData)
    {
        $ticket = $eventData->getTicket();
        $prevValue = $ticket->getAgentId();
        $newValue = $actionData['value'];

        $result = $this->perform($ticket, $newValue);
        if ($result) {
            $this->messageManagement->createAutomationMessage(
                $ticket->getEntityId(),
                $this->getOptionLabel($prevValue),
                $this->getOptionLabel($newValue),
                $eventData->getEventName()
            );
        }

        return $result;
    }

    /**
     * Perform handler
     *
     * @param TicketInterface $ticket
     * @param int $newValue
     * @return bool
     * @throws NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    private function perform($ticket, $newValue)
    {
        if ($ticket->getAgentId() == $newValue) {
            return false;
        }
        if ($newValue == AgentList::NOT_ASSIGNED_VALUE) {
            $ticket->setAgentId($newValue);
            $this->ticketRepository->save($ticket);
            return true;
        }

        if (!$this->isAgentAvailable($newValue)) {
            return false;
        }

        $department = $this->departmentRepository->get($ticket->getDepartmentId());
        if (in_array($newValue, $department->getAgentIds())) {
            $ticket->setAgentId($newValue);
            $this->ticketRepository->save($ticket);
            return true;
        }

        return false;
    }

    /**
     * Check if agent is available
     *
     * @param int $agentId
     * @return bool
     */
    private function isAgentAvailable($agentId)
    {
        try {
            $agent = $this->userRepository->getById($agentId);
            $result = (bool)$agent->getIsActive();
        } catch (NoSuchEntityException $exception) {
            $result = false;
        }

        return $result;
    }

    /**
     * Retrieve option Label by id
     *
     * @param $id
     * @return string
     */
    private function getOptionLabel($id) {
        try {
            return $this->source->getOptionById($id)['label'];
        } catch (LocalizedException $exception) {
            return '';
        }
    }
}
