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
namespace Aheadworks\Helpdesk2\Api;

/**
 * Interface TicketManagementInterface
 * @api
 */
interface TicketManagementInterface
{
    /**
     * Create new ticket
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\TicketInterface $ticket
     * @param \Aheadworks\Helpdesk2\Api\Data\MessageInterface $message
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createNewTicket($ticket, $message);

    /**
     * Update ticket
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\TicketInterface $ticket
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function updateTicket($ticket);

    /**
     * Create new message
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\MessageInterface $message
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createNewMessage($message);

    /**
     * Escalate ticket
     *
     * @param int $ticketId
     * @param string $escalationMessage
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function escalateTicket($ticketId, $escalationMessage);
}
