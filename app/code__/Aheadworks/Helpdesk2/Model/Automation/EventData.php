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

use Magento\Framework\Api\AbstractExtensibleObject;

/**
 * Class EventData
 *
 * @package Aheadworks\Helpdesk2\Model\Automation
 */
class EventData extends AbstractExtensibleObject implements EventDataInterface
{
    /**
     * @inheritdoc
     */
    public function getEventName()
    {
        return $this->_get(self::EVENT_NAME);
    }

    /**
     * @inheritdoc
     */
    public function setEventName($eventName)
    {
        return $this->setData(self::EVENT_NAME, $eventName);
    }

    /**
     * @inheritdoc
     */
    public function getMessage()
    {
        return $this->_get(self::MESSAGE);
    }

    /**
     * @inheritdoc
     */
    public function setMessage($message)
    {
        return $this->setData(self::MESSAGE, $message);
    }

    /**
     * @inheritdoc
     */
    public function getTicket()
    {
        return $this->_get(self::TICKET);
    }

    /**
     * @inheritdoc
     */
    public function setTicket($ticket)
    {
        return $this->setData(self::TICKET, $ticket);
    }

    /**
     * @inheritdoc
     */
    public function getOrder()
    {
        return $this->_get(self::ORDER);
    }

    /**
     * @inheritdoc
     */
    public function setOrder($order)
    {
        return $this->setData(self::ORDER, $order);
    }

    /**
     * @inheritdoc
     */
    public function getEscalationMessage()
    {
        return $this->_get(self::ESCALATION_MESSAGE);
    }

    /**
     * @inheritdoc
     */
    public function setEscalationMessage($escalationMessage)
    {
        return $this->setData(self::ESCALATION_MESSAGE, $escalationMessage);
    }
}
