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
namespace Aheadworks\Helpdesk2\Model\SampleData\Installer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Setup\SampleData\InstallerInterface as SampleDataInstallerInterface;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;
use Aheadworks\Helpdesk2\Api\Data\AutomationInterfaceFactory;
use Aheadworks\Helpdesk2\Api\AutomationRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Source\Automation\Condition as AutomationCondition;
use Aheadworks\Helpdesk2\Model\Source\Automation\OperatorSource as AutomationOperator;
use Aheadworks\Helpdesk2\Model\Source\Automation\Action as AutomationAction;
use Aheadworks\Helpdesk2\Model\Source\Automation\Event as AutomationEvent;
use Aheadworks\Helpdesk2\Model\Source\Department\AgentList;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Status as TicketStatus;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Priority as TicketPriority;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type as MessageType;

/**
 * Class Automation
 *
 * @package Aheadworks\Helpdesk2\Model\SampleData\Installer
 */
class Automation implements SampleDataInstallerInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var JsonSerializer
     */
    private $serializer;

    /**
     * @var AutomationInterfaceFactory
     */
    private $automationFactory;

    /**
     * @var AutomationRepositoryInterface
     */
    private $automationRepository;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DataObjectHelper $dataObjectHelper
     * @param AutomationInterfaceFactory $automationFactory
     * @param AutomationRepositoryInterface $automationRepository
     * @param JsonSerializer $serializer
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataObjectHelper $dataObjectHelper,
        AutomationInterfaceFactory $automationFactory,
        AutomationRepositoryInterface $automationRepository,
        JsonSerializer $serializer
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->automationFactory = $automationFactory;
        $this->automationRepository = $automationRepository;
        $this->serializer = $serializer;
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function install()
    {
        if (!$this->ifExists(1)) {
            $this->createAutomation($this->prepareAutomationData1());
        }
        if (!$this->ifExists(2)) {
            $this->createAutomation($this->prepareAutomationData2());
        }
        if (!$this->ifExists(3)) {
            $this->createAutomation($this->prepareAutomationData3());
        }
        if (!$this->ifExists(4)) {
            $this->createAutomation($this->prepareAutomationData4());
        }
        if (!$this->ifExists(5)) {
            $this->createAutomation($this->prepareAutomationData5());
        }
        if (!$this->ifExists(6)) {
            $this->createAutomation($this->prepareAutomationData6());
        }
        if (!$this->ifExists(7)) {
            $this->createAutomation($this->prepareAutomationData7());
        }
        if (!$this->ifExists(8)) {
            $this->createAutomation($this->prepareAutomationData8());
        }
    }

    /**
     * Prepare sample data of automation 1
     *
     * @return array
     */
    private function prepareAutomationData1()
    {
        $conditions = [];
        $actions = [
            [
                'action' => AutomationAction::SEND_EMAIL_TO_CUSTOMER,
                'value' => 'aw_helpdesk2_new_ticket_from_customer_send_email_to_customer'
            ],
            [
                'action' => AutomationAction::ASSIGN_TICKET,
                'value' => AgentList::NOT_ASSIGNED_VALUE
            ]
        ];

        return [
            AutomationInterface::ID => 1,
            AutomationInterface::NAME => 'New ticket from customer',
            AutomationInterface::IS_ACTIVE => 0,
            AutomationInterface::PRIORITY => 0,
            AutomationInterface::IS_REQUIRED_TO_BREAK => 1,
            AutomationInterface::EVENT => AutomationEvent::NEW_TICKET_FROM_CUSTOMER,
            AutomationInterface::CONDITIONS => $this->serializer->serialize($conditions),
            AutomationInterface::ACTIONS => $this->serializer->serialize($actions)
        ];
    }

    /**
     * Prepare sample data of automation 2
     *
     * @return array
     */
    private function prepareAutomationData2()
    {
        $conditions = [];
        $actions = [
            [
                'action' => AutomationAction::SEND_EMAIL_TO_CUSTOMER,
                'value' => 'aw_helpdesk2_new_ticket_from_agent_send_email_to_customer'
            ]
        ];

        return [
            AutomationInterface::ID => 2,
            AutomationInterface::NAME => 'New ticket from agent',
            AutomationInterface::IS_ACTIVE => 0,
            AutomationInterface::PRIORITY => 0,
            AutomationInterface::IS_REQUIRED_TO_BREAK => 1,
            AutomationInterface::EVENT => AutomationEvent::NEW_TICKET_FROM_AGENT,
            AutomationInterface::CONDITIONS => $this->serializer->serialize($conditions),
            AutomationInterface::ACTIONS => $this->serializer->serialize($actions)
        ];
    }

    /**
     * Prepare sample data of automation 3
     *
     * @return array
     */
    private function prepareAutomationData3()
    {
        $conditions = [];
        $actions = [
            [
                'action' => AutomationAction::SEND_EMAIL_TO_AGENT,
                'value' => 'aw_helpdesk2_new_reply_from_customer_send_email_to_agent'
            ],
            [
                'action' => AutomationAction::CHANGE_STATUS,
                'value' => TicketStatus::OPEN
            ]
        ];

        return [
            AutomationInterface::ID => 3,
            AutomationInterface::NAME => 'New reply from customer',
            AutomationInterface::IS_ACTIVE => 0,
            AutomationInterface::PRIORITY => 0,
            AutomationInterface::IS_REQUIRED_TO_BREAK => 1,
            AutomationInterface::EVENT => AutomationEvent::NEW_REPLY_FROM_CUSTOMER,
            AutomationInterface::CONDITIONS => $this->serializer->serialize($conditions),
            AutomationInterface::ACTIONS => $this->serializer->serialize($actions)
        ];
    }

    /**
     * Prepare sample data of automation 4
     *
     * @return array
     */
    private function prepareAutomationData4()
    {
        $conditions = [];
        $actions = [
            [
                'action' => AutomationAction::SEND_EMAIL_TO_CUSTOMER,
                'value' => 'aw_helpdesk2_new_reply_from_agent_send_email_to_customer'
            ]
        ];

        return [
            AutomationInterface::ID => 4,
            AutomationInterface::NAME => 'New reply from agent',
            AutomationInterface::IS_ACTIVE => 0,
            AutomationInterface::PRIORITY => 0,
            AutomationInterface::IS_REQUIRED_TO_BREAK => 1,
            AutomationInterface::EVENT => AutomationEvent::NEW_REPLY_FROM_AGENT,
            AutomationInterface::CONDITIONS => $this->serializer->serialize($conditions),
            AutomationInterface::ACTIONS => $this->serializer->serialize($actions)
        ];
    }

    /**
     * Prepare sample data of automation 5
     *
     * @return array
     */
    private function prepareAutomationData5()
    {
        $conditions = [];
        $actions = [
            [
                'action' => AutomationAction::SEND_EMAIL_TO_AGENT,
                'value' => 'aw_helpdesk2_ticket_reassign_send_email_to_agent'
            ]
        ];

        return [
            AutomationInterface::ID => 5,
            AutomationInterface::NAME => 'Ticket was assigned to another agent',
            AutomationInterface::IS_ACTIVE => 0,
            AutomationInterface::PRIORITY => 0,
            AutomationInterface::IS_REQUIRED_TO_BREAK => 1,
            AutomationInterface::EVENT => AutomationEvent::TICKET_ASSIGNED,
            AutomationInterface::CONDITIONS => $this->serializer->serialize($conditions),
            AutomationInterface::ACTIONS => $this->serializer->serialize($actions)
        ];
    }

    /**
     * Prepare sample data of automation 6
     *
     * @return array
     */
    private function prepareAutomationData6()
    {
        $conditions = [
            [
                'object' => AutomationCondition::TICKET_STATUS,
                'operator' => AutomationOperator::IN,
                'value' => [(string)TicketStatus::OPEN]
            ],
            [
                'object' => AutomationCondition::LAST_REPLIED_HOURS,
                'operator' => AutomationOperator::EQUALS_GREATER_THAN,
                'value' => '24'
            ],
            [
                'object' => AutomationCondition::LAST_REPLIED_BY,
                'operator' => AutomationOperator::EQUALS,
                'value' => MessageType::CUSTOMER
            ]
        ];
        $actions = [
            [
                'action' => AutomationAction::CHANGE_PRIORITY,
                'value' => TicketPriority::ASAP
            ]
        ];

        return [
            AutomationInterface::ID => 6,
            AutomationInterface::NAME => 'Change priority of an open ticket that is waiting for reply for 24+ hours',
            AutomationInterface::IS_ACTIVE => 0,
            AutomationInterface::PRIORITY => 0,
            AutomationInterface::IS_REQUIRED_TO_BREAK => 1,
            AutomationInterface::EVENT => AutomationEvent::RECURRING_TASK,
            AutomationInterface::CONDITIONS => $this->serializer->serialize($conditions),
            AutomationInterface::ACTIONS => $this->serializer->serialize($actions)
        ];
    }

    /**
     * Prepare sample data of automation 7
     *
     * @return array
     */
    private function prepareAutomationData7()
    {
        $conditions = [
            [
                'object' => AutomationCondition::TICKET_STATUS,
                'operator' => AutomationOperator::IN,
                'value' => [(string)TicketStatus::OPEN]
            ],
            [
                'object' => AutomationCondition::TOTAL_AGENT_MESSAGES,
                'operator' => AutomationOperator::EQUALS,
                'value' => '0'
            ],
            [
                'object' => AutomationCondition::TOTAL_CUSTOMER_MESSAGES,
                'operator' => AutomationOperator::EQUALS_GREATER_THAN,
                'value' => '3'
            ]
        ];
        $actions = [
            [
                'action' => AutomationAction::CHANGE_PRIORITY,
                'value' => TicketPriority::URGENT
            ]
        ];

        return [
            AutomationInterface::ID => 7,
            AutomationInterface::NAME => 'Change priority of a new ticket where customer left 3+ messages',
            AutomationInterface::IS_ACTIVE => 0,
            AutomationInterface::PRIORITY => 0,
            AutomationInterface::IS_REQUIRED_TO_BREAK => 1,
            AutomationInterface::EVENT => AutomationEvent::RECURRING_TASK,
            AutomationInterface::CONDITIONS => $this->serializer->serialize($conditions),
            AutomationInterface::ACTIONS => $this->serializer->serialize($actions)
        ];
    }

    /**
     * Prepare sample data of automation 8
     *
     * @return array
     */
    private function prepareAutomationData8()
    {
        $conditions = [
            [
                'object' => AutomationCondition::TICKET_STATUS,
                'operator' => AutomationOperator::IN,
                'value' => [(string)TicketStatus::WAITING]
            ],
            [
                'object' => AutomationCondition::LAST_REPLIED_HOURS,
                'operator' => AutomationOperator::EQUALS_GREATER_THAN,
                'value' => '48'
            ]
        ];
        $actions = [
            [
                'action' => AutomationAction::SEND_EMAIL_TO_CUSTOMER,
                'value' => 'aw_helpdesk2_automatic_followup_email_to_customer'
            ]
        ];

        return [
            AutomationInterface::ID => 8,
            AutomationInterface::NAME => 'Follow-up to customer to check if customer requires further assistance',
            AutomationInterface::IS_ACTIVE => 0,
            AutomationInterface::PRIORITY => 0,
            AutomationInterface::IS_REQUIRED_TO_BREAK => 1,
            AutomationInterface::EVENT => AutomationEvent::RECURRING_TASK,
            AutomationInterface::CONDITIONS => $this->serializer->serialize($conditions),
            AutomationInterface::ACTIONS => $this->serializer->serialize($actions)
        ];
    }

    /**
     * Check if automation already exists
     *
     * @param int $automationId
     * @return bool
     * @throws LocalizedException
     */
    private function ifExists($automationId)
    {
        $this->searchCriteriaBuilder
            ->addFilter(AutomationInterface::ID, $automationId)
            ->setCurrentPage(1)
            ->setPageSize(1);
        $automationList = $this->automationRepository->getList(
            $this->searchCriteriaBuilder->create()
        )->getItems();

        return count($automationList) > 0;
    }

    /**
     * Create automation
     *
     * @param array $automationData
     * @throws LocalizedException
     */
    private function createAutomation($automationData)
    {
        /** @var AutomationInterface $automation */
        $automation = $this->automationFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $automation,
            $automationData,
            AutomationInterface::class
        );

        $this->automationRepository->save($automation);
    }
}
