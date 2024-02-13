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
namespace Aheadworks\Helpdesk2\Model\Automation\Event;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Model\Automation\EventDataInterface;
use Aheadworks\Helpdesk2\Model\Automation\Action\Pool as ActionPool;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation\ConditionMatcher;
use Aheadworks\Helpdesk2\Model\Automation\Loader as AutomationLoader;

/**
 * Class AbstractHandler
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Event
 */
class AbstractHandler
{
    /**
     * @var AutomationLoader
     */
    private $automationLoader;

    /**
     * @var ConditionMatcher
     */
    private $conditionMatcher;

    /**
     * @var ActionPool
     */
    private $actionPool;

    /**
     * @param AutomationLoader $automationLoader
     * @param ConditionMatcher $conditionMatcher
     * @param ActionPool $actionPool
     */
    public function __construct(
        AutomationLoader $automationLoader,
        ConditionMatcher $conditionMatcher,
        ActionPool $actionPool
    ) {
        $this->automationLoader = $automationLoader;
        $this->conditionMatcher = $conditionMatcher;
        $this->actionPool = $actionPool;
    }

    /**
     * Trigger automation event handler
     *
     * @param EventDataInterface $eventData
     * @return $this
     * @throws LocalizedException
     */
    public function trigger(EventDataInterface $eventData)
    {
        $automationList = $this->automationLoader->loadByEventName($eventData->getEventName());
        $ticketId = $this->resolveTicketId($eventData);
        foreach ($automationList as $automation) {
            if ($ticketId) {
                if (!$this->conditionMatcher->isTicketMatched($automation, $ticketId)) {
                    continue;
                }

                /** @var array $actions */
                $actions = $automation->getActions();
                foreach ($actions as $actionData) {
                    if (!isset($actionData['action']) || !isset($actionData['value'])) {
                        throw new LocalizedException(__('Automation action data is not correct'));
                    }

                    $actionHandler = $this->actionPool->getActionHandler($actionData['action']);
                    $actionHandler->run($actionData, $eventData);
                }
                if ($automation->getIsRequiredToBreak()) {
                    break;
                }
            }
        }

        return $this;
    }

    /**
     * Resolved ticket ID from provided event data
     *
     * @param EventDataInterface $eventData
     * @return int|null
     */
    private function resolveTicketId($eventData)
    {
        if ($eventData->getTicket()) {
            return $eventData->getTicket()->getEntityId();
        }

        if ($eventData->getMessage()) {
            return $eventData->getMessage()->getTicketId();
        }

        return null;
    }
}
