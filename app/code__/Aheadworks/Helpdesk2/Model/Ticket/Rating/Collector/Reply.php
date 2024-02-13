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

use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\Collection as MessageCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\CollectionFactory as MessageCollectionFactory;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type as MessageType;

/**
 * Class Reply
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector
 */
class Reply extends AbstractCollector
{
    /**
     * @inheritdoc
     */
    protected $startingTime = '-1 hour';

    /**
     * @inheritdoc
     */
    protected $rate = 72.0;

    /**
     * @var MessageCollectionFactory
     */
    private $messageCollectionFactory;

    /**
     * @param MessageCollectionFactory $messageCollectionFactory
     */
    public function __construct(
        MessageCollectionFactory $messageCollectionFactory
    ) {
        $this->messageCollectionFactory = $messageCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function getPoints()
    {
        if (0 == $this->getAdminMessageCount()) {
            return $this->calculatePoints();
        }

        return 0;
    }

    /**
     * Get admin message count
     *
     * @return int
     */
    private function getAdminMessageCount()
    {
        /** @var MessageCollection $messageCollection */
        $messageCollection = $this->messageCollectionFactory->create();
        $messageCollection->addTicketFilter($this->ticket->getEntityId());
        $messageCollection->addMessageTypeFilter(MessageType::ADMIN);

        return $messageCollection->getSize();
    }
}
