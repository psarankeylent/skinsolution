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
namespace Aheadworks\Helpdesk2\Model\Source\Automation;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Event
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Automation
 */
class Event implements OptionSourceInterface
{
    const EVENT_NAME_PREFIX = 'aw_helpdesk2_';

    /**#@+
     * Event types
     */
    const NEW_TICKET_FROM_CUSTOMER = 'new_ticket_from_customer';
    const NEW_TICKET_FROM_AGENT = 'new_ticket_from_agent';
    const NEW_REPLY_FROM_CUSTOMER = 'new_reply_from_customer';
    const NEW_REPLY_FROM_AGENT = 'new_reply_from_agent';
    const TICKET_ASSIGNED = 'ticket_assigned_to_another_agent';
    const TICKET_ESCALATED = 'ticket_escalated';
    const RECURRING_TASK = 'recurring_task';
    const ORDER_STATUS_CHANGED = 'order_status_changed';
    /**#@-*/

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::NEW_TICKET_FROM_CUSTOMER,
                'label' => __('New ticket from customer')
            ],
            [
                'value' => self::NEW_TICKET_FROM_AGENT,
                'label' => __('New ticket from agent')
            ],
            [
                'value' => self::NEW_REPLY_FROM_CUSTOMER,
                'label' => __('New reply from customer')
            ],
            [
                'value' => self::NEW_REPLY_FROM_AGENT,
                'label' => __('New reply from agent')
            ],
            [
                'value' => self::TICKET_ASSIGNED,
                'label' => __('Ticket was assigned to another agent')
            ],
            [
                'value' => self::RECURRING_TASK,
                'label' => __('Recurring Task')
            ],
            [
                'value' => self::ORDER_STATUS_CHANGED,
                'label' => __('Order status changed')
            ]
        ];
    }

    /**
     * Get option by option ID
     *
     * @param int $optionId
     * @return array|null
     */
    public function getOptionById($optionId)
    {
        foreach ($this->toOptionArray() as $option) {
            if ($option['value'] == $optionId) {
                return $option;
            }
        }

        return null;
    }
}
