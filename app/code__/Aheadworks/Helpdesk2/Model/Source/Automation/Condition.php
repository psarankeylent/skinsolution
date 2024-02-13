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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Data\OptionSourceInterface;
use Aheadworks\Helpdesk2\Model\ThirdPartyModule\Aheadworks\CustomerAttributes\AttributeProvider;

/**
 * Class Condition
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Automation
 */
class Condition implements OptionSourceInterface
{
    /**#@+
     * Condition values
     */
    const CUSTOMER_GROUP = 'customer.group_id';
    const TICKET_DEPARTMENT = 'grid_tbl.department_id';
    const SUBJECT_CONTAINS = 'grid_tbl.subject';
    const FIRST_MESSAGE_CONTAINS = 'grid_tbl.first_message_content';
    const LAST_MESSAGE_CONTAINS = 'grid_tbl.last_message_content';
    const TOTAL_MESSAGES = 'grid_tbl.message_count';
    const TOTAL_AGENT_MESSAGES = 'grid_tbl.agent_message_count';
    const TOTAL_CUSTOMER_MESSAGES = 'grid_tbl.customer_message_count';
    const LAST_REPLIED_HOURS = 'grid_tbl.last_message_date';
    const LAST_REPLIED_BY = 'grid_tbl.last_message_type';
    const RATING = 'grid_tbl.rating';
    const TICKET_STATUS = 'grid_tbl.status_id';
    const TICKET_PRIORITY = 'grid_tbl.priority_id';
    const ORDER_STATUS = 'order_status';
    const TICKET_TAG = 'ticket_tag_table.tag_id';
    /**#@-*/

    /**
     * @var AttributeProvider
     */
    private $custAttributeProvider;

    /**
     * @param AttributeProvider $custAttributeProvider
     */
    public function __construct(
        AttributeProvider $custAttributeProvider
    ) {
        $this->custAttributeProvider = $custAttributeProvider;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function toOptionArray()
    {
        $conditions = [
            [
                'value' => self::CUSTOMER_GROUP,
                'label' => __('Customer group')
            ],
            [
                'value' => self::TICKET_DEPARTMENT,
                'label' => __('Ticket department')
            ],
            [
                'value' => self::SUBJECT_CONTAINS,
                'label' => __('Subject')
            ],
            [
                'value' => self::FIRST_MESSAGE_CONTAINS,
                'label' => __('First message')
            ],
            [
                'value' => self::LAST_MESSAGE_CONTAINS,
                'label' => __('Last message')
            ],
            [
                'value' => self::TOTAL_MESSAGES,
                'label' => __('Total messages')
            ],
            [
                'value' => self::TOTAL_AGENT_MESSAGES,
                'label' => __("Total agents' messages")
            ],
            [
                'value' => self::TOTAL_CUSTOMER_MESSAGES,
                'label' => __("Total customer's messages")
            ],
            [
                'value' => self::LAST_REPLIED_HOURS,
                'label' => __('Last replied X hours ago')
            ],
            [
                'value' => self::LAST_REPLIED_BY,
                'label' => __('Last replied by')
            ],
            [
                'value' => self::RATING,
                'label' => __('Rating')
            ],
            [
                'value' => self::TICKET_STATUS,
                'label' => __('Ticket status')
            ],
            [
                'value' => self::TICKET_PRIORITY,
                'label' => __('Ticket priority')
            ],
            [
                'value' => self::ORDER_STATUS,
                'label' => __('Order status')
            ],
            [
                'value' => self::TICKET_TAG,
                'label' => __('Ticket Tag')
            ]
        ];

        return array_merge($conditions, $this->custAttributeProvider->prepareAutomationConditions());
    }

    /**
     * Get options
     *
     * @return array
     * @throws LocalizedException
     */
    public function getOptions()
    {
        $options = $this->toOptionArray();
        $result = [];
        foreach ($options as $option) {
            $result[$option['value']] = $option['label'];
        }

        return $result;
    }

    /**
     * Get options array for event type
     *
     * @return array
     * @throws LocalizedException
     */
    public function getAvailableOptionsByEventType()
    {
        $options = $this->getOptions();
        $eventTypeOptionSet = [
            Event::NEW_TICKET_FROM_CUSTOMER => [
                [
                    'label' => $options[self::CUSTOMER_GROUP],
                    'value' => self::CUSTOMER_GROUP
                ],
                [
                    'label' => $options[self::TICKET_DEPARTMENT],
                    'value' => self::TICKET_DEPARTMENT
                ],
                [
                    'label' => $options[self::SUBJECT_CONTAINS],
                    'value' => self::SUBJECT_CONTAINS
                ],
                [
                    'label' => $options[self::FIRST_MESSAGE_CONTAINS],
                    'value' => self::FIRST_MESSAGE_CONTAINS
                ]
            ],
            Event::NEW_TICKET_FROM_AGENT => [
                [
                    'label' => $options[self::CUSTOMER_GROUP],
                    'value' => self::CUSTOMER_GROUP
                ],
                [
                    'label' => $options[self::TICKET_DEPARTMENT],
                    'value' => self::TICKET_DEPARTMENT
                ],
                [
                    'label' => $options[self::SUBJECT_CONTAINS],
                    'value' => self::SUBJECT_CONTAINS
                ],
                [
                    'label' => $options[self::FIRST_MESSAGE_CONTAINS],
                    'value' => self::FIRST_MESSAGE_CONTAINS
                ]
            ],
            Event::NEW_REPLY_FROM_CUSTOMER => [
                [
                    'label' => $options[self::CUSTOMER_GROUP],
                    'value' => self::CUSTOMER_GROUP
                ],
                [
                    'label' => $options[self::TICKET_STATUS],
                    'value' => self::TICKET_STATUS
                ],
                [
                    'label' => $options[self::TICKET_PRIORITY],
                    'value' => self::TICKET_PRIORITY
                ],
                [
                    'label' => $options[self::TICKET_DEPARTMENT],
                    'value' => self::TICKET_DEPARTMENT
                ],
                [
                    'label' => $options[self::RATING],
                    'value' => self::RATING
                ],
                [
                    'label' => $options[self::TOTAL_MESSAGES],
                    'value' => self::TOTAL_MESSAGES
                ],
                [
                    'label' => $options[self::TOTAL_AGENT_MESSAGES],
                    'value' => self::TOTAL_AGENT_MESSAGES
                ],
                [
                    'label' => $options[self::TOTAL_CUSTOMER_MESSAGES],
                    'value' => self::TOTAL_CUSTOMER_MESSAGES
                ],
                [
                    'label' => $options[self::LAST_MESSAGE_CONTAINS],
                    'value' => self::LAST_MESSAGE_CONTAINS
                ],
                [
                    'label' => $options[self::LAST_REPLIED_HOURS],
                    'value' => self::LAST_REPLIED_HOURS
                ],
                [
                    'label' => $options[self::LAST_REPLIED_BY],
                    'value' => self::LAST_REPLIED_BY
                ],
                [
                    'label' => $options[self::TICKET_TAG],
                    'value' => self::TICKET_TAG
                ]
            ],
            Event::NEW_REPLY_FROM_AGENT => [
                [
                    'label' => $options[self::CUSTOMER_GROUP],
                    'value' => self::CUSTOMER_GROUP
                ],
                [
                    'label' => $options[self::TICKET_STATUS],
                    'value' => self::TICKET_STATUS
                ],
                [
                    'label' => $options[self::TICKET_PRIORITY],
                    'value' => self::TICKET_PRIORITY
                ],
                [
                    'label' => $options[self::TICKET_DEPARTMENT],
                    'value' => self::TICKET_DEPARTMENT
                ],
                [
                    'label' => $options[self::RATING],
                    'value' => self::RATING
                ],
                [
                    'label' => $options[self::TOTAL_MESSAGES],
                    'value' => self::TOTAL_MESSAGES
                ],
                [
                    'label' => $options[self::TOTAL_AGENT_MESSAGES],
                    'value' => self::TOTAL_AGENT_MESSAGES
                ],
                [
                    'label' => $options[self::TOTAL_CUSTOMER_MESSAGES],
                    'value' => self::TOTAL_CUSTOMER_MESSAGES
                ],
                [
                    'label' => $options[self::LAST_MESSAGE_CONTAINS],
                    'value' => self::LAST_MESSAGE_CONTAINS
                ],
                [
                    'label' => $options[self::LAST_REPLIED_HOURS],
                    'value' => self::LAST_REPLIED_HOURS
                ],
                [
                    'label' => $options[self::LAST_REPLIED_BY],
                    'value' => self::LAST_REPLIED_BY
                ],
                [
                    'label' => $options[self::TICKET_TAG],
                    'value' => self::TICKET_TAG
                ]
            ],
            Event::RECURRING_TASK => [
                [
                    'label' => $options[self::CUSTOMER_GROUP],
                    'value' => self::CUSTOMER_GROUP
                ],
                [
                    'label' => $options[self::TICKET_STATUS],
                    'value' => self::TICKET_STATUS
                ],
                [
                    'label' => $options[self::TICKET_PRIORITY],
                    'value' => self::TICKET_PRIORITY
                ],
                [
                    'label' => $options[self::TICKET_DEPARTMENT],
                    'value' => self::TICKET_DEPARTMENT
                ],
                [
                    'label' => $options[self::RATING],
                    'value' => self::RATING
                ],
                [
                    'label' => $options[self::TOTAL_MESSAGES],
                    'value' => self::TOTAL_MESSAGES
                ],
                [
                    'label' => $options[self::TOTAL_AGENT_MESSAGES],
                    'value' => self::TOTAL_AGENT_MESSAGES
                ],
                [
                    'label' => $options[self::TOTAL_CUSTOMER_MESSAGES],
                    'value' => self::TOTAL_CUSTOMER_MESSAGES
                ],
                [
                    'label' => $options[self::LAST_REPLIED_HOURS],
                    'value' => self::LAST_REPLIED_HOURS
                ],
                [
                    'label' => $options[self::LAST_REPLIED_BY],
                    'value' => self::LAST_REPLIED_BY
                ],
                [
                    'label' => $options[self::TICKET_TAG],
                    'value' => self::TICKET_TAG
                ]
            ],
            Event::TICKET_ASSIGNED => [
                [
                    'label' => $options[self::CUSTOMER_GROUP],
                    'value' => self::CUSTOMER_GROUP
                ],
                [
                    'label' => $options[self::TICKET_STATUS],
                    'value' => self::TICKET_STATUS
                ],
                [
                    'label' => $options[self::TICKET_PRIORITY],
                    'value' => self::TICKET_PRIORITY
                ],
                [
                    'label' => $options[self::TICKET_DEPARTMENT],
                    'value' => self::TICKET_DEPARTMENT
                ],
                [
                    'label' => $options[self::RATING],
                    'value' => self::RATING
                ],
                [
                    'label' => $options[self::TOTAL_MESSAGES],
                    'value' => self::TOTAL_MESSAGES
                ],
                [
                    'label' => $options[self::TOTAL_AGENT_MESSAGES],
                    'value' => self::TOTAL_AGENT_MESSAGES
                ],
                [
                    'label' => $options[self::TOTAL_CUSTOMER_MESSAGES],
                    'value' => self::TOTAL_CUSTOMER_MESSAGES
                ],
                [
                    'label' => $options[self::LAST_REPLIED_HOURS],
                    'value' => self::LAST_REPLIED_HOURS
                ],
                [
                    'label' => $options[self::LAST_REPLIED_BY],
                    'value' => self::LAST_REPLIED_BY
                ],
                [
                    'label' => $options[self::TICKET_TAG],
                    'value' => self::TICKET_TAG
                ]
            ],
            Event::ORDER_STATUS_CHANGED => [
                [
                    'label' => $options[self::ORDER_STATUS],
                    'value' => self::ORDER_STATUS
                ]
            ]
        ];

        foreach ($eventTypeOptionSet as $eventType => &$optionSet) {
            if ($eventType == Event::ORDER_STATUS_CHANGED) {
                continue;
            }
            $optionSet = array_merge($optionSet, $this->custAttributeProvider->prepareAutomationConditions());
        }

        return $eventTypeOptionSet;
    }
}
