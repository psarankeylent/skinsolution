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

use Aheadworks\Helpdesk2\Model\Source\Ticket\Status;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Priority;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\CollectionFactory as MessageCollectionFactory;

/**
 * Class StatusHistory
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector
 */
class StatusHistory extends AbstractCollector
{
    const ASAP_POINTS = 50;
    const URGENT_POINTS = 100;

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
     *
     * In M1 we check previous status changing.
     * If it was (WFI => OPEN) than the following code gets executed
     * In M2 we skip it and execute it anyway since we don't store event history.
     */
    public function getPoints()
    {
        $currentStatus = $this->ticket->getStatusId();
        $currentPriority = $this->ticket->getPriorityId();

        if (($currentStatus != Status::OPEN)
            || (!in_array($currentPriority, [Priority::ASAP, Priority::URGENT]))
        ) {
            return 0;
        }

        if ($currentPriority == Priority::ASAP) {
            return self::ASAP_POINTS;
        }

        if ($currentPriority == Priority::URGENT) {
            return self::URGENT_POINTS;
        }
    }
}
