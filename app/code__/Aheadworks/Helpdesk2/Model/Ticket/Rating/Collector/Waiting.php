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
namespace Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector;

use Magento\Framework\DataObject;
use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\Collection as MessageCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\CollectionFactory as MessageCollectionFactory;
use Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Waiting\Factory as WaitingCollectorFactory;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type as MessageType;

/**
 * Class Waiting
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector
 */
class Waiting extends AbstractCollector
{
    /**
     * @var AbstractCollector[]
     */
    private $waitingCollectors = [];

    /**
     * @var MessageCollectionFactory
     */
    private $messageCollectionFactory;

    /**
     * @var WaitingCollectorFactory
     */
    private $waitingCollectorFactory;

    /**
     * @param MessageCollectionFactory $messageCollectionFactory
     * @param WaitingCollectorFactory $waitingCollectorFactory
     */
    public function __construct(
        MessageCollectionFactory $messageCollectionFactory,
        WaitingCollectorFactory $waitingCollectorFactory
    ) {
        $this->messageCollectionFactory = $messageCollectionFactory;
        $this->waitingCollectorFactory = $waitingCollectorFactory;
    }

    /**
     * @inheritdoc
     */
    public function getPoints()
    {
        $this->waitingCollectors = [];
        $messages = $this->getDiscussionMessages();
        foreach ($messages as $message) {
            $waitingCollector = $this->waitingCollectorFactory->createByMessageType($message->getType());
            $waitingCollector->setStartingTime($message->getCreatedAt());
            $this->waitingCollectors[] = $waitingCollector;
        }

        $points = 0;
        foreach ($this->waitingCollectors as $collector) {
            $points += $collector->collect($this->ticket);
        }

        return $points * $this->getRate();
    }

    /**
     * @inheritdoc
     */
    public function getRate()
    {
        return sqrt(count($this->waitingCollectors));
    }

    /**
     * Get discussion messages
     *
     * @return DataObject[]|MessageInterface[]
     */
    private function getDiscussionMessages()
    {
        /** @var MessageCollection $messageCollection */
        $messageCollection = $this->messageCollectionFactory->create();
        $messageCollection->addTicketFilter($this->ticket->getEntityId());
        $messageCollection->addFieldToFilter(
            MessageInterface::TYPE,
            [
                'in' => [
                    MessageType::CUSTOMER,
                    MessageType::ADMIN
                ]
            ]
        );

        return $messageCollection->getItems();
    }
}
