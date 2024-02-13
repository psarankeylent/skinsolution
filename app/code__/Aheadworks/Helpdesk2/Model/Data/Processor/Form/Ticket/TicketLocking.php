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
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Backend\Model\Auth\Session as AdminSession;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;
use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;

/**
 * Class TicketLocking
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket
 */
class TicketLocking implements ProcessorInterface
{
    /**
     * @var AdminSession
     */
    private $adminSession;

    /**
     * @var DepartmentRepositoryInterface
     */
    private $departmentRepository;

    /**
     * @param AdminSession $adminSession
     * @param DepartmentRepositoryInterface $departmentRepository
     */
    public function __construct(
        AdminSession $adminSession,
        DepartmentRepositoryInterface $departmentRepository
    ) {
        $this->adminSession = $adminSession;
        $this->departmentRepository = $departmentRepository;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function prepareEntityData($data)
    {
        $agentId = $data[TicketInterface::AGENT_ID] ?? null;
        $data['is_allowed_to_lock_ticket'] = $this->isAllowedToLockTicket($agentId);
        $data['is_allowed_to_unlock_ticket'] = $this->isAllowedToUnlockTicket(
            $agentId,
            $data[TicketInterface::DEPARTMENT_ID]
        );

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function prepareMetaData($meta)
    {
        return $meta;
    }

    /**
     * Check if ticket can be locked
     *
     * @param int $agentId
     * @return bool
     */
    private function isAllowedToLockTicket($agentId)
    {
        return $this->adminSession->getUser()->getId() == $agentId;
    }

    /**
     * Check if ticket can be unlocked
     *
     * @param int $agentId
     * @param int $departmentId
     * @return bool
     * @throws NoSuchEntityException
     */
    private function isAllowedToUnlockTicket($agentId, $departmentId)
    {
        $adminUser = $this->adminSession->getUser();
        $department = $this->departmentRepository->get($departmentId);
        return $agentId == $adminUser->getId()
            || $adminUser->getId() == $department->getPrimaryAgentId();
    }
}
