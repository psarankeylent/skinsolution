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
namespace Aheadworks\Helpdesk2\Observer\Ticket;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Aheadworks\Helpdesk2\Model\Automation\Event\AbstractHandler as EventHandler;
use Aheadworks\Helpdesk2\Model\Automation\EventDataInterface;
use Aheadworks\Helpdesk2\Model\Automation\EventDataInterfaceFactory;
use Aheadworks\Helpdesk2\Model\Source\Automation\Event;

/**
 * Class AutomationEventObserver
 *
 * @package Aheadworks\Helpdesk2\Observer\Ticket
 */
class AutomationEventObserver implements ObserverInterface
{
    /**
     * @var EventHandler
     */
    private $eventHandler;

    /**
     * @var EventDataInterfaceFactory
     */
    private $eventDataFactory;

    /**
     * @param EventHandler $eventHandler
     * @param EventDataInterfaceFactory $eventDataFactory
     */
    public function __construct(
        EventHandler $eventHandler,
        EventDataInterfaceFactory $eventDataFactory
    ) {
        $this->eventHandler = $eventHandler;
        $this->eventDataFactory = $eventDataFactory;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function execute(Observer $observer)
    {
        /** @var EventDataInterface $eventData */
        $eventData = $this->eventDataFactory->create();
        $eventData
            ->setEventName(str_replace(Event::EVENT_NAME_PREFIX, '', $observer->getEvent()->getName()))
            ->setTicket($observer->getData('ticket'))
            ->setMessage($observer->getData('message'));

        $this->eventHandler->trigger($eventData);
    }
}
