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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Automation;

use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Model\GroupManagement;
use Magento\Customer\Model\Attribute;
use Magento\Eav\Model\Config as EavConfig;
use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Collection as TicketCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\CollectionFactory as TicketCollectionFactory;
use Aheadworks\Helpdesk2\Model\Source\Automation\OperatorSource;
use Aheadworks\Helpdesk2\Model\Source\Automation\Condition;
use Aheadworks\Helpdesk2\Model\ThirdPartyModule\Aheadworks\CustomerAttributes\AttributeProvider;
use Aheadworks\Helpdesk2\Model\Source\Automation\Event;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;

/**
 * Class ConditionMatcher
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Automation
 */
class ConditionMatcher
{
    /**
     * @var TicketCollectionFactory
     */
    private $ticketCollectionFactory;

    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * @param TicketCollectionFactory $ticketCollectionFactory
     * @param EavConfig $eavConfig
     * @param EventManagerInterface $eventManager
     */
    public function __construct(
        TicketCollectionFactory $ticketCollectionFactory,
        EavConfig $eavConfig,
        EventManagerInterface $eventManager
    ) {
        $this->ticketCollectionFactory = $ticketCollectionFactory;
        $this->eavConfig = $eavConfig;
        $this->eventManager = $eventManager;
    }

    /**
     * Check if ticket matches automation conditions
     *
     * @param AutomationInterface $automation
     * @param int $ticketId
     * @return bool
     */
    public function isTicketMatched($automation, $ticketId)
    {
        $ticketCollection = $this->prepareCollection($automation);
        $ticketCollection->getSelect()->where('e.entity_id = ?', $ticketId);

        return count($ticketCollection->getAllIds(1)) > 0;
    }

    /**
     * Get all ticket IDS that meet automation conditions
     *
     * @param AutomationInterface $automation
     * @return array
     */
    public function getMatchedTicketIds($automation)
    {
        $ticketCollection = $this->prepareCollection($automation);
        return $ticketCollection->getAllIds();
    }

    /**
     * Prepare ticket collection
     *
     * @param AutomationInterface $automation
     * @return TicketCollection
     */
    private function prepareCollection($automation)
    {
        /** @var TicketCollection $ticketCollection */
        $ticketCollection = $this->ticketCollectionFactory->create();
        $ticketCollection->prepareForAutomation();

        /** @var array $conditions */
        $isTagTableJoined = false;
        $conditions = $automation->getConditions();
        foreach ($conditions as $condition) {
            if (strpos($condition['object'], AttributeProvider::ATTRIBUTE_CODE_PREFIX) !== false) {
                $this->applyCustomerAttrCondition($ticketCollection, $condition);
                continue;
            }
            if ($condition['object'] == Condition::TICKET_TAG) {
                if (!$isTagTableJoined) {
                    $ticketCollection->joinTagTable();
                    $isTagTableJoined = true;
                }
            }
            if ($condition['operator'] == OperatorSource::LIKE) {
                $this->applyLikeCondition($ticketCollection, $condition);
                continue;
            }
            if ($condition['object'] == Condition::CUSTOMER_GROUP
                && in_array(GroupManagement::NOT_LOGGED_IN_ID, $condition['value'])
            ) {
                $this->applyGuestCustomerGroupCondition($ticketCollection, $condition);
                continue;
            }
            if ($condition['object'] == Condition::LAST_REPLIED_HOURS) {
                $this->applyLastRepliedHoursCondition($ticketCollection, $condition);
                continue;
            }
            if ($condition['operator'] == OperatorSource::IS_EQUAL_TO) {
                $this->applyIsEqualToCondition($ticketCollection, $condition);
            }
            $query = $ticketCollection->getConnection()->prepareSqlCondition(
                $condition['object'],
                [$condition['operator'] => $condition['value']]
            );
            $this->eventManager->dispatch(
                Event::EVENT_NAME_PREFIX . 'prepare_condition_collection',
                [
                    'collection' => $ticketCollection,
                    'condition' => $condition
                ]
            );
            $ticketCollection->getSelect()->where($query);
        }

        return $ticketCollection;
    }

    /**
     * Apply in case like operator is used in conditions
     *
     * @param TicketCollection $collection
     * @param array $condition
     */
    private function applyLikeCondition($collection, $condition)
    {
        $values = explode(',', $condition['value']);
        $likeQuery = [];
        foreach ($values as $value) {
            $likeQuery[] = $collection->getConnection()->prepareSqlCondition(
                $condition['object'],
                [$condition['operator'] => '%'.trim($value).'%']
            );
        }

        $collection->getSelect()->where('(' . implode(' OR ', $likeQuery) . ')');
    }

    /**
     * Apply in case not logged in group is used in conditions
     *
     * @param TicketCollection $collection
     * @param array $condition
     */
    private function applyGuestCustomerGroupCondition($collection, $condition)
    {
        $connection = $collection->getConnection();
        $whereCondition = [
            $connection->prepareSqlCondition(
                'e.customer_id',
                ['null' => true]
            ),
            $connection->prepareSqlCondition(
                $condition['object'],
                [$condition['operator'] => $condition['value']]
            )
        ];

        $collection->getSelect()->where('(' . implode(' OR ', $whereCondition) . ')');
    }

    /**
     * Apply in case last replied hours condition is used
     *
     * @param TicketCollection $collection
     * @param array $condition
     */
    private function applyLastRepliedHoursCondition($collection, $condition)
    {
        $currentDate = new \DateTime();
        $query = $collection->getConnection()->prepareSqlCondition(
            "(UNIX_TIMESTAMP('{$currentDate->format('Y-m-d H:i:s')}') - "
            . "UNIX_TIMESTAMP({$condition['object']}))/60/60",
            [$condition['operator'] => $condition['value']]
        );

        $collection->getSelect()->where($query);
    }

    /**
     * Apply customer attribute condition
     *
     * @param TicketCollection $collection
     * @param array $condition
     * @return bool
     */
    private function applyCustomerAttrCondition($collection, $condition)
    {
        $attribute = explode(":", $condition['object']);
        try {
            /** @var Attribute $attributeModel */
            $attributeModel = $this->eavConfig->getAttribute('customer', $attribute[1]);
            $attrTable = $attributeModel->getBackendTable();
            $tableAlias = 'aw_cust_attr_' . $attribute[1] . '_tbl';
            $whereCondition = [
                'e.customer_id = ' . $tableAlias . '.entity_id',
                $tableAlias . '.attribute_id = ' . $attributeModel->getId()
            ];
            $collection
                ->getSelect()
                ->joinLeft(
                    [$tableAlias => $attrTable],
                    implode(' AND ', $whereCondition),
                    []
                );
            if ($condition['operator'] == OperatorSource::LIKE) {
                $condition['object'] = $tableAlias . '.value';
                $this->applyLikeCondition($collection, $condition);
            } else {
                $query = $collection->getConnection()->prepareSqlCondition(
                    $tableAlias . '.value',
                    [$condition['operator'] => $condition['value']]
                );
                $collection->getSelect()->where($query);
            }
        } catch (LocalizedException $exception) {
            return false;
        }

        return true;
    }

    /**
     * Apply is equal to condition
     *
     * @param TicketCollection $collection
     * @param array $condition
     */
    private function applyIsEqualToCondition($collection, $condition)
    {
        $connection = $collection->getConnection();
        if (is_array($condition['value'])) {
            $collection->getSelect()->having(
                $connection->quoteInto(
                    'count(distinct ' . $condition['object'] . ') >= ?',
                    count($condition['value'])
                )
            );
        }
    }
}
