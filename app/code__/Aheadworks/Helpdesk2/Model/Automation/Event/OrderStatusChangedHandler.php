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

use Magento\Framework\DataObject;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;
use Aheadworks\Helpdesk2\Model\Automation\EventDataInterface;
use Aheadworks\Helpdesk2\Model\Automation\Action\Pool as ActionPool;
use Aheadworks\Helpdesk2\Model\Automation\Loader as AutomationLoader;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;

/**
 * Class OrderStatusChangedHandler
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Event
 */
class OrderStatusChangedHandler
{
    /**
     * @var AutomationLoader
     */
    private $automationLoader;

    /**
     * @var ActionPool
     */
    private $actionPool;

    /**
     * @var CommandInterface
     */
    private $createCommand;

    /**
     * @var ProcessorInterface
     */
    private $postDataProcessor;

    /**
     * @param AutomationLoader $automationLoader
     * @param ActionPool $actionPool
     * @param CommandInterface $saveCommand
     * @param ProcessorInterface $postDataProcessor
     */
    public function __construct(
        AutomationLoader $automationLoader,
        ActionPool $actionPool,
        CommandInterface $saveCommand,
        ProcessorInterface $postDataProcessor
    ) {
        $this->automationLoader = $automationLoader;
        $this->actionPool = $actionPool;
        $this->createCommand = $saveCommand;
        $this->postDataProcessor = $postDataProcessor;
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
        foreach ($automationList as $automation) {
            if (!$this->isAutomationValid($automation, $eventData)) {
                continue;
            }

            $ticket = $this->prepareTicket($eventData->getOrder());
            $eventData->setTicket($ticket);

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

        return $this;
    }

    /**
     * Check is automation is valid
     *
     * @param AutomationInterface $automation
     * @param EventDataInterface $eventData
     * @return bool
     */
    private function isAutomationValid($automation, $eventData)
    {
        /** @var array $conditions */
        $conditions = $automation->getConditions();
        $result = false;
        foreach ($conditions as $conditionData) {
            if (in_array($eventData->getOrder()->getStatus(), $conditionData['value'])) {
                $result = true;
                break;
            }
        }

        return $result;
    }

    /**
     * Prepare ticket
     *
     * @param OrderInterface $order
     * @return DataObject|TicketInterface
     * @throws LocalizedException
     */
    private function prepareTicket($order)
    {
        $ticketData = [
            TicketInterface::CUSTOMER_ID => $order->getCustomerId(),
            TicketInterface::STORE_ID => $order->getStoreId(),
            TicketInterface::CUSTOMER_NAME => $order->getCustomerFirstname() . ' ' .  $order->getCustomerLastname(),
            TicketInterface::CUSTOMER_EMAIL => $order->getCustomerEmail(),
            TicketInterface::SUBJECT => __('Order #%1 has been referred to review', $order->getIncrementId()),
            MessageInterface::CONTENT => __('You are kindly informed that the order #%1 has just been referred to review.', $order->getIncrementId())
        ];

        $tickedData = $this->postDataProcessor->prepareEntityData($ticketData);
        return $this->createCommand->execute($tickedData);
    }
}
