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

/**
 * Interface GridInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket
 */
interface GridInterface
{
    /**
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ENTITY_ID = 'entity_id';
    const UID = 'uid';
    const RATING = 'rating';
    const LAST_MESSAGE_DATE = 'last_message_date';
    const LAST_MESSAGE_BY = 'last_message_by';
    const LAST_MESSAGE_TYPE = 'last_message_type';
    const DEPARTMENT_ID = 'department_id';
    const AGENT_ID = 'agent_id';
    const SUBJECT = 'subject';
    const FIRST_MESSAGE_CONTENT = 'first_message_content';
    const LAST_MESSAGE_CONTENT = 'last_message_content';
    const CUSTOMER_NAME = 'customer_name';
    const CUSTOMER_ID = 'customer_id';
    const CUSTOMER_EMAIL = 'customer_email';
    const PRIORITY_ID = 'priority_id';
    const ORDER_ID = 'order_id';
    const ORDER_INCREMENT_ID = 'order_increment_id';
    const CUSTOMER_MESSAGE_COUNT = 'customer_message_count';
    const AGENT_MESSAGE_COUNT = 'agent_message_count';
    const MESSAGE_COUNT = 'message_count';
    const STATUS_ID = 'status_id';
    const CREATED_AT = 'created_at';
}
