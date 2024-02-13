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
 * Class Action
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Automation
 */
class Action implements OptionSourceInterface
{
    /**#@+
     * Action values
     */
    const SEND_EMAIL_TO_CUSTOMER = 'send_email_to_customer';
    const SEND_EMAIL_TO_AGENT = 'send_email_to_agent';
    const CHANGE_STATUS = 'change_status';
    const CHANGE_PRIORITY = 'change_priority';
    const ASSIGN_TICKET = 'assign_ticket';
    const CHANGE_DEPARTMENT = 'change_department';
    const ADD_TICKET_TAG = 'add_ticket_tag';
    const CREATE_SYSTEM_MESSAGE = 'create_system_message';
    /**#@-*/

    /**#@+
     * Config values
     */
    const IS_EMAIL_DISABLED_FOR_SAME_AGENT = 'is_email_disabled_for_same_agent';
    const STORE_IDS_TO_SEND_EMAIL = 'store_ids_to_send_email';
    /**#@-*/

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::SEND_EMAIL_TO_CUSTOMER,
                'label' => __('Send email to Customer')
            ],
            [
                'value' => self::SEND_EMAIL_TO_AGENT,
                'label' => __('Send email to Agent')
            ],
            [
                'value' => self::CHANGE_STATUS,
                'label' => __('Change status to')
            ],
            [
                'value' => self::CHANGE_PRIORITY,
                'label' => __('Change priority to')
            ],
            [
                'value' => self::ASSIGN_TICKET,
                'label' => __('Assign ticket to')
            ],
            [
                'value' => self::CHANGE_DEPARTMENT,
                'label' => __('Change department to')
            ],
            [
                'value' => self::ADD_TICKET_TAG,
                'label' => __('Add tag')
            ],
            [
                'value' => self::CREATE_SYSTEM_MESSAGE,
                'label' => __('Display System Message in Admin Ticket Thread')
            ]
        ];
    }

    /**
     * Get options
     *
     * @return array
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
     */
    public function getAvailableOptionsByEventType()
    {
        $options = $this->getOptions();
        return [
            Event::NEW_TICKET_FROM_CUSTOMER => [
                [
                    'label' => $options[self::SEND_EMAIL_TO_CUSTOMER],
                    'value' => self::SEND_EMAIL_TO_CUSTOMER
                ],
                [
                    'label' => $options[self::SEND_EMAIL_TO_AGENT],
                    'value' => self::SEND_EMAIL_TO_AGENT
                ],
                [
                    'label' => $options[self::CHANGE_STATUS],
                    'value' => self::CHANGE_STATUS
                ],
                [
                    'label' => $options[self::CHANGE_PRIORITY],
                    'value' => self::CHANGE_PRIORITY
                ],
                [
                    'label' => $options[self::ASSIGN_TICKET],
                    'value' => self::ASSIGN_TICKET
                ],
                [
                    'label' => $options[self::CHANGE_DEPARTMENT],
                    'value' => self::CHANGE_DEPARTMENT
                ],
                [
                    'label' => $options[self::ADD_TICKET_TAG],
                    'value' => self::ADD_TICKET_TAG
                ]
            ],
            Event::NEW_TICKET_FROM_AGENT => [
                [
                    'label' => $options[self::SEND_EMAIL_TO_CUSTOMER],
                    'value' => self::SEND_EMAIL_TO_CUSTOMER
                ],
                [
                    'label' => $options[self::SEND_EMAIL_TO_AGENT],
                    'value' => self::SEND_EMAIL_TO_AGENT
                ],
                [
                    'label' => $options[self::ADD_TICKET_TAG],
                    'value' => self::ADD_TICKET_TAG
                ]
            ],
            Event::NEW_REPLY_FROM_CUSTOMER => [
                [
                    'label' => $options[self::SEND_EMAIL_TO_CUSTOMER],
                    'value' => self::SEND_EMAIL_TO_CUSTOMER
                ],
                [
                    'label' => $options[self::SEND_EMAIL_TO_AGENT],
                    'value' => self::SEND_EMAIL_TO_AGENT
                ],
                [
                    'label' => $options[self::CHANGE_STATUS],
                    'value' => self::CHANGE_STATUS
                ],
                [
                    'label' => $options[self::CHANGE_PRIORITY],
                    'value' => self::CHANGE_PRIORITY
                ],
                [
                    'label' => $options[self::ASSIGN_TICKET],
                    'value' => self::ASSIGN_TICKET
                ],
                [
                    'label' => $options[self::CHANGE_DEPARTMENT],
                    'value' => self::CHANGE_DEPARTMENT
                ],
                [
                    'label' => $options[self::ADD_TICKET_TAG],
                    'value' => self::ADD_TICKET_TAG
                ]
            ],
            Event::NEW_REPLY_FROM_AGENT => [
                [
                    'label' => $options[self::SEND_EMAIL_TO_CUSTOMER],
                    'value' => self::SEND_EMAIL_TO_CUSTOMER
                ],
                [
                    'label' => $options[self::SEND_EMAIL_TO_AGENT],
                    'value' => self::SEND_EMAIL_TO_AGENT
                ],
                [
                    'label' => $options[self::ADD_TICKET_TAG],
                    'value' => self::ADD_TICKET_TAG
                ]
            ],
            Event::RECURRING_TASK => [
                [
                    'label' => $options[self::SEND_EMAIL_TO_CUSTOMER],
                    'value' => self::SEND_EMAIL_TO_CUSTOMER
                ],
                [
                    'label' => $options[self::SEND_EMAIL_TO_AGENT],
                    'value' => self::SEND_EMAIL_TO_AGENT
                ],
                [
                    'label' => $options[self::CHANGE_STATUS],
                    'value' => self::CHANGE_STATUS
                ],
                [
                    'label' => $options[self::CHANGE_PRIORITY],
                    'value' => self::CHANGE_PRIORITY
                ],
                [
                    'label' => $options[self::ASSIGN_TICKET],
                    'value' => self::ASSIGN_TICKET
                ],
                [
                    'label' => $options[self::CHANGE_DEPARTMENT],
                    'value' => self::CHANGE_DEPARTMENT
                ],
                [
                    'label' => $options[self::ADD_TICKET_TAG],
                    'value' => self::ADD_TICKET_TAG
                ],
                [
                    'label' => $options[self::CREATE_SYSTEM_MESSAGE],
                    'value' => self::CREATE_SYSTEM_MESSAGE
                ]
            ],
            Event::TICKET_ASSIGNED => [
                [
                    'label' => $options[self::SEND_EMAIL_TO_CUSTOMER],
                    'value' => self::SEND_EMAIL_TO_CUSTOMER
                ],
                [
                    'label' => $options[self::SEND_EMAIL_TO_AGENT],
                    'value' => self::SEND_EMAIL_TO_AGENT
                ],
                [
                    'label' => $options[self::ADD_TICKET_TAG],
                    'value' => self::ADD_TICKET_TAG
                ]
            ],
            Event::ORDER_STATUS_CHANGED => [
                [
                    'label' => $options[self::SEND_EMAIL_TO_CUSTOMER],
                    'value' => self::SEND_EMAIL_TO_CUSTOMER
                ]
            ]
        ];
    }

    /**
     * Get available config types by event type
     *
     * @return array
     */
    public function getAvailableConfigTypesByEventType()
    {
        $storeConfig = [
            self::SEND_EMAIL_TO_CUSTOMER => self::STORE_IDS_TO_SEND_EMAIL
        ];

        return [
            Event::TICKET_ASSIGNED => [
                self::SEND_EMAIL_TO_AGENT => self::IS_EMAIL_DISABLED_FOR_SAME_AGENT,
                self::SEND_EMAIL_TO_CUSTOMER => self::STORE_IDS_TO_SEND_EMAIL
            ],
            Event::NEW_TICKET_FROM_CUSTOMER => $storeConfig,
            Event::NEW_TICKET_FROM_AGENT => $storeConfig,
            Event::NEW_REPLY_FROM_CUSTOMER => $storeConfig,
            Event::NEW_REPLY_FROM_AGENT => $storeConfig,
            Event::RECURRING_TASK => $storeConfig,
            Event::ORDER_STATUS_CHANGED => $storeConfig
        ];
    }
}
