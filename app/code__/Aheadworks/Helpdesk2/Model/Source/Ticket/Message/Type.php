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
namespace Aheadworks\Helpdesk2\Model\Source\Ticket\Message;

/**
 * Class Type
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Ticket\Message
 */
class Type
{
    /**
     * Message types
     */
    const ADMIN = 'admin-message';
    const CUSTOMER = 'customer-message';
    const INTERNAL = 'admin-internal';
    const SYSTEM = 'system-message';
    const TICKET_LOCK = 'ticket-lock-message';
    const ESCALATION = 'escalation-message';
}
