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
namespace Aheadworks\Helpdesk2\Model\Ticket\Layout;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\RequestInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;

/**
 * Class TicketLocator
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Layout
 */
class TicketLocator
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
     * Get ticket from request
     *
     * @param RequestInterface $request
     * @return TicketInterface
     * @throws NoSuchEntityException
     */
    public function getTicket($request)
    {
        $ticketId = $request->getParam('id');
        if ($ticketId) {
            return $this->ticketRepository->getById($ticketId);
        }
        $link = $request->getParam('key');
        if ($link) {
            return $this->ticketRepository->getByExternalLink($link);
        }

        throw new NoSuchEntityException(__('This ticket doesn\'t exist'));
    }
}
