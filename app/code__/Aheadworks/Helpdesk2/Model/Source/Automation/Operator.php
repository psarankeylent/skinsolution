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
use Aheadworks\Helpdesk2\Model\ThirdPartyModule\Aheadworks\CustomerAttributes\AttributeProvider;

/**
 * Class Operator
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Automation
 */
class Operator
{
    /**
     * @var AttributeProvider
     */
    private $custAttributeProvider;

    /**
     * @var OperatorSource
     */
    private $operatorSource;

    /**
     * @param AttributeProvider $custAttributeProvider
     * @param OperatorSource $operatorSource
     */
    public function __construct(
        AttributeProvider $custAttributeProvider,
        OperatorSource $operatorSource
    ) {
        $this->custAttributeProvider = $custAttributeProvider;
        $this->operatorSource = $operatorSource;
    }

    /**
     * Get options array for event type
     *
     * @return array
     * @throws LocalizedException
     */
    public function getAvailableOptionsByConditionType()
    {
        $options = [
            Condition::CUSTOMER_GROUP => $this->operatorSource->getInOperator(),
            Condition::TICKET_STATUS => $this->operatorSource->getInOperator(),
            Condition::TICKET_PRIORITY => $this->operatorSource->getInOperator(),
            Condition::TICKET_DEPARTMENT => $this->operatorSource->getInOperator(),
            Condition::SUBJECT_CONTAINS => $this->operatorSource->getLikeOperator(),
            Condition::FIRST_MESSAGE_CONTAINS => $this->operatorSource->getLikeOperator(),
            Condition::LAST_MESSAGE_CONTAINS => $this->operatorSource->getLikeOperator(),
            Condition::TOTAL_MESSAGES => $this->operatorSource->getComparisonOperators(),
            Condition::TOTAL_AGENT_MESSAGES => $this->operatorSource->getComparisonOperators(),
            Condition::TOTAL_CUSTOMER_MESSAGES => $this->operatorSource->getComparisonOperators(),
            Condition::RATING => $this->operatorSource->getComparisonOperators(),
            Condition::LAST_REPLIED_HOURS => $this->operatorSource->getComparisonOperators(),
            Condition::LAST_REPLIED_BY => $this->operatorSource->getEqualOperator(),
            Condition::ORDER_STATUS => $this->operatorSource->getInOperator(),
            Condition::TICKET_TAG => $this->operatorSource->getInExtendedOperator()
        ];

        return array_merge($options, $this->custAttributeProvider->prepareAutomationOperators());
    }
}
