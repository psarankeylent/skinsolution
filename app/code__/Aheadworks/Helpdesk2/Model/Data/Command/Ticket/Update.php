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
namespace Aheadworks\Helpdesk2\Model\Data\Command\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\TicketManagementInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Update
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Ticket
 */
class Update implements CommandInterface
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var TicketManagementInterface
     */
    private $ticketManagement;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param TicketRepositoryInterface $ticketRepository
     * @param TicketManagementInterface $ticketManagement
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        TicketRepositoryInterface $ticketRepository,
        TicketManagementInterface $ticketManagement
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->ticketRepository = $ticketRepository;
        $this->ticketManagement = $ticketManagement;
    }

    /**
     * @inheritdoc
     */
    public function execute($ticketData)
    {
        $ticket = $this->getTicketObject($ticketData);
        $this->dataObjectHelper->populateWithArray(
            $ticket,
            $ticketData,
            TicketInterface::class
        );

        return $this->ticketManagement->updateTicket($ticket);
    }

    /**
     * Get gateway object
     *
     * @param array $ticketData
     * @return TicketInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function getTicketObject($ticketData)
    {
        return $this->ticketRepository->getById($ticketData[TicketInterface::ENTITY_ID] ?? null);
    }
}
