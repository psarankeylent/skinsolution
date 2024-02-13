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
namespace Aheadworks\Helpdesk2\Model\Automation;

use Magento\Sales\Api\Data\OrderInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageInterface;

/**
 * Interface EventDataInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Automation
 */
interface EventDataInterface
{
    /**#@+
     * Event data
     */
    const EVENT_NAME = 'event_name';
    const MESSAGE = 'message';
    const TICKET = 'ticket';
    const ORDER = 'order';
    const ESCALATION_MESSAGE = 'escalation_message';
    /**#@-*/

    /**
     * Get event name
     *
     * @return string
     */
    public function getEventName();

    /**
     * Set event name
     *
     * @param string $eventName
     * @return $this
     */
    public function setEventName($eventName);

    /**
     * Get message
     *
     * @return MessageInterface|null
     */
    public function getMessage();

    /**
     * Set message
     *
     * @param MessageInterface $message
     * @return $this
     */
    public function setMessage($message);

    /**
     * Get ticket
     *
     * @return TicketInterface|null
     */
    public function getTicket();

    /**
     * Set ticket
     *
     * @param TicketInterface $ticket
     * @return $this
     */
    public function setTicket($ticket);

    /**
     * Get order
     *
     * @return OrderInterface|null
     */
    public function getOrder();

    /**
     * Set order
     *
     * @param OrderInterface $order
     * @return $this
     */
    public function setOrder($order);

    /**
     * Get escalation message
     *
     * @return string
     */
    public function getEscalationMessage();

    /**
     * Set escalation message
     *
     * @param string $escalationMessage
     * @return $this
     */
    public function setEscalationMessage($escalationMessage);
}
