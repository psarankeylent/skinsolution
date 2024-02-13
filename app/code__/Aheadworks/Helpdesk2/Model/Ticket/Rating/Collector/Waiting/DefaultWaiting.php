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
namespace Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Waiting;

use Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\AbstractCollector;
use Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Waiting\Priority\Factory as PriorityFactory;

/**
 * Class DefaultWaiting
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector\Waiting
 */
class DefaultWaiting extends AbstractCollector
{
    /**
     * @var PriorityFactory
     */
    private $priorityFactory;

    /**
     * @param PriorityFactory $priorityFactory
     */
    public function __construct(
        PriorityFactory $priorityFactory
    ) {
        $this->priorityFactory = $priorityFactory;
    }

    /**
     * @inheritdoc
     */
    public function getPoints()
    {
        return $this->calculatePoints();
    }

    /**
     * @inheritdoc
     */
    public function getRate()
    {
        $priorityCollector = $this->priorityFactory->createByPriority($this->ticket->getPriorityId());
        return $priorityCollector->getRate() * $this->rate;
    }

    /**
     * @inheritdoc
     */
    public function getEndingPointTime()
    {
        return $this->getStartingTime();
    }

    /**
     * @inheritdoc
     */
    protected function isNeedToApplyPointsToTicket()
    {
        return false;
    }
}
