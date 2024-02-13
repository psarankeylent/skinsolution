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
namespace Aheadworks\Helpdesk2\Model\Ticket\Agent;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\RequestInterface;
use Aheadworks\Bup\Api\Data\UserProfileInterface;
use Aheadworks\Bup\Api\UserProfileMetadataRepositoryInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;

/**
 * Class Locator
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Agent
 */
class Locator
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var UserProfileMetadataRepositoryInterface
     */
    private $userProfileMetadataRepository;

    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @param RequestInterface $request
     * @param UserProfileMetadataRepositoryInterface $userProfileMetadataRepository
     * @param TicketRepositoryInterface $ticketRepository
     */
    public function __construct(
        RequestInterface $request,
        UserProfileMetadataRepositoryInterface $userProfileMetadataRepository,
        TicketRepositoryInterface $ticketRepository
    ) {
        $this->request = $request;
        $this->userProfileMetadataRepository = $userProfileMetadataRepository;
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * Get agent user profile on ticket page
     *
     * @return UserProfileInterface|null
     * @throws LocalizedException
     */
    public function locateAgentByTicketIdRequest()
    {
        $ticketId = $this->request->getParam('id');
        if ($ticketId) {
            try {
                $ticket = $this->ticketRepository->getById($ticketId);
            } catch (NoSuchEntityException $exception) {
                return null;
            }
            return $this->locateAgentByTicket($ticket);
        }

        $ticketLink = $this->request->getParam('key');
        if ($ticketLink) {
            try {
                $ticket = $this->ticketRepository->getByExternalLink($ticketLink);
            } catch (NoSuchEntityException $exception) {
                return null;
            }
            return $this->locateAgentByTicket($ticket);
        }

        return null;
    }

    /**
     * Get user profile by ticket
     *
     * @param TicketInterface $ticket
     * @return UserProfileInterface|null
     * @throws LocalizedException
     */
    public function locateAgentByTicket($ticket)
    {
        try {
            $userProfile = $this->userProfileMetadataRepository->get($ticket->getAgentId());
        } catch (NoSuchEntityException $exception) {
            $userProfile = null;
        }

        return $userProfile;
    }
}
