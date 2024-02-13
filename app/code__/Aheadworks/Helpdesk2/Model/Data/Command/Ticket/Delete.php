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

use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;

/**
 * Class Delete
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Ticket
 */
class Delete implements CommandInterface
{
    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @param TicketRepositoryInterface $ticketRepository
     */
    public function __construct(
        TicketRepositoryInterface $ticketRepository
    ) {
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        if (!isset($data[TicketInterface::ENTITY_ID])) {
            throw new \InvalidArgumentException(
                'Ticket entity ID param is required to delete'
            );
        }

        return $this->ticketRepository->deleteById($data[TicketInterface::ENTITY_ID]);
    }
}
