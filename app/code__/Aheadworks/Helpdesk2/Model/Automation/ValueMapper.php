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

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Model\Source\Automation\Condition;
use Aheadworks\Helpdesk2\Model\Source\Automation\Action;
use Aheadworks\Helpdesk2\Model\Source\Customer\Group as CustomerGroupSource;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Status as TicketStatusSource;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Priority as TicketPrioritySource;
use Aheadworks\Helpdesk2\Model\Source\Ticket\DepartmentList;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type as MessageType;
use Aheadworks\Helpdesk2\Model\Source\Department\AgentList;
use Aheadworks\Helpdesk2\Model\Source\Email\TemplateList;
use Aheadworks\Helpdesk2\Model\Source\Order\Status as OrderStatusSource;
use Aheadworks\Helpdesk2\Model\ThirdPartyModule\Aheadworks\CustomerAttributes\AttributeProvider;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Tags as TagsSource;
use Aheadworks\Helpdesk2\Ui\Component\Listing\Columns\Store\Options as StoreOptions;

/**
 * Class ValueMapper
 *
 * @package Aheadworks\Helpdesk2\Model\Automation
 */
class ValueMapper
{
    /**#@+
     * Value element types
     */
    const MULTISELECT = 'multiselect';
    const SELECT = 'select';
    const TEXT = 'text';
    const TEXTAREA = 'textarea';
    const BOOLEAN = 'boolean';
    const TAG_FIELD = 'tag_names';
    /**#@-*/

    /**
     * @var CustomerGroupSource
     */
    private $customerGroupSource;

    /**
     * @var TicketStatusSource
     */
    private $ticketStatusSource;

    /**
     * @var TicketPrioritySource
     */
    private $ticketPrioritySource;

    /**
     * @var DepartmentList
     */
    private $departmentList;

    /**
     * @var TemplateList
     */
    private $emailTemplateSource;

    /**
     * @var AgentList
     */
    private $agentList;

    /**
     * @var OrderStatusSource
     */
    private $orderStatusSource;

    /**
     * @var AttributeProvider
     */
    private $attributeProvider;

    /**
     * @var TagsSource
     */
    private $tagsSource;

    /**
     * @var StoreOptions
     */
    private $storeOptions;

    /**
     * @param CustomerGroupSource $customerGroupSource
     * @param TicketStatusSource $ticketStatusSource
     * @param TicketPrioritySource $ticketPrioritySource
     * @param DepartmentList $departmentList
     * @param TemplateList $emailTemplateSource
     * @param AgentList $agentList
     * @param OrderStatusSource $orderStatusSource
     * @param AttributeProvider $attributeProvider
     * @param TagsSource $tagsSource
     * @param StoreOptions $storeOptions
     */
    public function __construct(
        CustomerGroupSource $customerGroupSource,
        TicketStatusSource $ticketStatusSource,
        TicketPrioritySource $ticketPrioritySource,
        DepartmentList $departmentList,
        TemplateList $emailTemplateSource,
        AgentList $agentList,
        OrderStatusSource $orderStatusSource,
        AttributeProvider $attributeProvider,
        TagsSource $tagsSource,
        StoreOptions $storeOptions
    ) {
        $this->customerGroupSource = $customerGroupSource;
        $this->ticketStatusSource = $ticketStatusSource;
        $this->ticketPrioritySource = $ticketPrioritySource;
        $this->departmentList = $departmentList;
        $this->emailTemplateSource = $emailTemplateSource;
        $this->agentList = $agentList;
        $this->orderStatusSource = $orderStatusSource;
        $this->attributeProvider = $attributeProvider;
        $this->tagsSource = $tagsSource;
        $this->storeOptions = $storeOptions;
    }

    /**
     * Get available values by condition type
     *
     * @return array
     * @throws LocalizedException
     */
    public function getAvailableConditionValuesByConditionType()
    {
        $values = [
            Condition::CUSTOMER_GROUP => [
                'type' => self::MULTISELECT,
                'options' => $this->customerGroupSource->toOptionArray()
            ],
            Condition::TICKET_STATUS => [
                'type' => self::MULTISELECT,
                'options' => $this->ticketStatusSource->toOptionArray()
            ],
            Condition::TICKET_PRIORITY => [
                'type' => self::MULTISELECT,
                'options' => $this->ticketPrioritySource->toOptionArray()
            ],
            Condition::TICKET_DEPARTMENT => [
                'type' => self::MULTISELECT,
                'options' => $this->departmentList->toOptionArray()
            ],
            Condition::SUBJECT_CONTAINS => [
                'type' => self::TEXT
            ],
            Condition::FIRST_MESSAGE_CONTAINS => [
                'type' => self::TEXT
            ],
            Condition::LAST_MESSAGE_CONTAINS => [
                'type' => self::TEXT
            ],
            Condition::TOTAL_MESSAGES => [
                'type' => self::TEXT
            ],
            Condition::TOTAL_AGENT_MESSAGES => [
                'type' => self::TEXT
            ],
            Condition::TOTAL_CUSTOMER_MESSAGES => [
                'type' => self::TEXT
            ],
            Condition::RATING => [
                'type' => self::TEXT
            ],
            Condition::LAST_REPLIED_HOURS => [
                'type' => self::TEXT
            ],
            Condition::LAST_REPLIED_BY => [
                'type' => self::SELECT,
                'options' => [
                    [
                        'label' => __('Customer'),
                        'value' => MessageType::CUSTOMER
                    ],
                    [
                        'label' => __('Agent'),
                        'value' => MessageType::ADMIN
                    ]
                ]
            ],
            Condition::ORDER_STATUS => [
                'type' => self::MULTISELECT,
                'options' => $this->orderStatusSource->toOptionArray()
            ],
            Condition::TICKET_TAG => [
                'type' => self::MULTISELECT,
                'options' => $this->tagsSource->toOptionArray()
            ]
        ];

        return array_merge($values, $this->attributeProvider->prepareAutomationValues());
    }

    /**
     * Get available action values by action type
     *
     * @return array
     * @throws LocalizedException
     */
    public function getAvailableActionValuesByActionType()
    {
        return [
            Action::SEND_EMAIL_TO_CUSTOMER => [
                'type' => self::SELECT,
                'options' => $this->emailTemplateSource->toOptionArray()
            ],
            Action::SEND_EMAIL_TO_AGENT => [
                'type' => self::SELECT,
                'options' => $this->emailTemplateSource->toOptionArray()
            ],
            Action::CHANGE_STATUS => [
                'type' => self::SELECT,
                'options' => $this->ticketStatusSource->toOptionArray()
            ],
            Action::CHANGE_PRIORITY => [
                'type' => self::SELECT,
                'options' => $this->ticketPrioritySource->toOptionArray()
            ],
            Action::ASSIGN_TICKET => [
                'type' => self::SELECT,
                'options' => $this->agentList->toOptionArray()
            ],
            Action::CHANGE_DEPARTMENT => [
                'type' => self::SELECT,
                'options' => $this->departmentList->toOptionArray()
            ],
            Action::ADD_TICKET_TAG => [
                'type' => self::TAG_FIELD,
                'options' => $this->tagsSource->toOptionArray()
            ],
            Action::CREATE_SYSTEM_MESSAGE => [
                'type' => self::TEXTAREA
            ]
        ];
    }

    /**
     * Get available action config options by config type
     *
     * @return array
     */
    public function getAvailableActionConfigOptionsByConfigType()
    {
        return [
            Action::IS_EMAIL_DISABLED_FOR_SAME_AGENT => [
                'label' => __('Disable Sending to Oneself'),
                'value' => Action::IS_EMAIL_DISABLED_FOR_SAME_AGENT,
                'type' => self::BOOLEAN
            ],
            Action::STORE_IDS_TO_SEND_EMAIL => [
                'label' => __('Store'),
                'value' => Action::STORE_IDS_TO_SEND_EMAIL,
                'type' => self::MULTISELECT,
                'options' => $this->storeOptions->toOptionArray()
            ]
        ];
    }
}
