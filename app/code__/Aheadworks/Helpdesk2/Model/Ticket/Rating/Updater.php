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
namespace Aheadworks\Helpdesk2\Model\Ticket\Rating;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\CouldNotSaveException;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Search\Builder as SearchBuilder;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Status as TicketStatus;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;

/**
 * Class Updater
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Rating
 */
class Updater
{
    /**
     * @var SearchBuilder
     */
    private $searchBuilder;

    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @param SearchBuilder $searchBuilder
     * @param TicketRepositoryInterface $ticketRepository
     */
    public function __construct(
        SearchBuilder $searchBuilder,
        TicketRepositoryInterface $ticketRepository
    ) {
        $this->searchBuilder = $searchBuilder;
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * Update all tickets rating
     *
     * Ticket re-saving will trigger rating update
     *
     * @throws LocalizedException
     * @throws CouldNotSaveException
     */
    public function update()
    {
        $tickets = $this->getTicketToProcess();
        foreach ($tickets as $ticket) {
            $this->ticketRepository->save($ticket);
        }
    }

    /**
     * Get tickets ready to be updated
     *
     * @return TicketInterface[]
     * @throws LocalizedException
     */
    private function getTicketToProcess()
    {
        $searchCriteriaBuilder = $this->searchBuilder->getSearchCriteriaBuilder();
        $searchCriteriaBuilder->addFilter(
            TicketInterface::STATUS_ID,
            [TicketStatus::NEW, TicketStatus::OPEN],
            'in'
        );
        return $this->searchBuilder->searchTickets();
    }
}
