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
namespace Aheadworks\Helpdesk2\Model\Data\Validator\Ticket\InlineEditor;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentPermissionInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Permission\Manager;

/**
 * Class Validator
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Validator\Ticket\InlineEditor
 */
class Validator
{
    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var Manager
     */
    private $permissionManager;

    /**
     * @var array
     */
    private $map = [
        TicketInterface::STATUS_ID => DepartmentPermissionInterface::TYPE_UPDATE,
        TicketInterface::PRIORITY_ID => DepartmentPermissionInterface::TYPE_UPDATE,
        TicketInterface::DEPARTMENT_ID => DepartmentPermissionInterface::TYPE_UPDATE,
        TicketInterface::AGENT_ID => DepartmentPermissionInterface::TYPE_UPDATE,
    ];

    /**
     * @param TicketRepositoryInterface $ticketRepository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param Manager $permissionManager
     */
    public function __construct(
        TicketRepositoryInterface $ticketRepository,
        DataObjectProcessor $dataObjectProcessor,
        Manager $permissionManager
    ) {
        $this->ticketRepository = $ticketRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->permissionManager = $permissionManager;
    }

    /**
     * Validate item for inline editor
     *
     * @param array $item
     * @return bool
     * @throws LocalizedException
     */
    public function validateItem($item)
    {
        $ticketId = $item[TicketInterface::ENTITY_ID];
        $ticket = $this->ticketRepository->getById($ticketId);
        $ticketData = $this->dataObjectProcessor->buildOutputDataArray($ticket, TicketInterface::class);
        $changes = array_diff($item, array_intersect_key($ticketData, $item));
        $ticketActions = $this->resolveTicketActions($changes);
        foreach ($ticketActions as $action) {
            if (!$this->permissionManager->isAdminAbleToPerformTicketAction($ticketId, $action)) {
                throw new LocalizedException(__('Not enough permissions to update the ticket'));
            }
        }

        return true;
    }

    /**
     * Resolve ticket actions depending on changes
     *
     * @param array $changes
     * @return array
     */
    public function resolveTicketActions($changes)
    {
        $actions = [];
        foreach ($changes as $key => $change) {
            if (isset($this->map[$key]) && !in_array($this->map[$key], $actions)) {
                $actions[] = $this->map[$key];
            }
        }

        return $actions;
    }
}
