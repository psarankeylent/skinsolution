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
namespace Aheadworks\Helpdesk2\Model\Ticket;

use Magento\Framework\Api\AbstractExtensibleObject;
use Aheadworks\Helpdesk2\Api\Data\TicketOptionInterface;

/**
 * Class Option
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket
 */
class Option extends AbstractExtensibleObject implements TicketOptionInterface
{
    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->_get(self::ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getTicketId()
    {
        return $this->_get(self::TICKET_ID);
    }

    /**
     * @inheritdoc
     */
    public function setTicketId($ticketId)
    {
        return $this->setData(self::TICKET_ID, $ticketId);
    }

    /**
     * @inheritdoc
     */
    public function getLabel()
    {
        return $this->_get(self::LABEL);
    }

    /**
     * @inheritdoc
     */
    public function setLabel($label)
    {
        return $this->setData(self::LABEL, $label);
    }

    /**
     * @inheritdoc
     */
    public function getValue()
    {
        return $this->_get(self::VALUE);
    }

    /**
     * @inheritdoc
     */
    public function setValue($value)
    {
        return $this->setData(self::VALUE, $value);
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(
        \Aheadworks\Helpdesk2\Api\Data\TicketOptionExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
