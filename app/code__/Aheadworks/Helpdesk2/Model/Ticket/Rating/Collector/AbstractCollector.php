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

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Rating\CollectorInterface;

/**
 * Class AbstractCollector
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Rating\Collector
 */
abstract class AbstractCollector implements CollectorInterface
{
    /**
     * @var TicketInterface
     */
    protected $ticket;

    /**
     * @var string
     */
    protected $startingTime;

    /**
     * @var float
     */
    protected $rate = 1.0;

    /**
     * Get points
     *
     * @return float
     */
    abstract public function getPoints();

    /**
     * @inheritdoc
     */
    public function collect($ticket)
    {
        $this->ticket = $ticket;
        $points = floatval($this->getPoints());
        if ($this->isNeedToApplyPointsToTicket()) {
            $ticket->setRating($ticket->getRating() + $points);
        }

        return $points;
    }

    /**
     * Is need to apply points to ticket
     *
     * @return bool
     */
    protected function isNeedToApplyPointsToTicket()
    {
        return true;
    }

    /**
     * Get rate
     *
     * @return float
     */
    public function getRate()
    {
        return $this->rate;
    }

    /**
     * Set rate
     *
     * @param float $rate
     * @return $this
     */
    public function setRate($rate)
    {
        $this->rate = (float)$rate;
        return $this;
    }

    /**
     * Get starting time
     *
     * @return int
     */
    public function getStartingTime()
    {
        return strtotime($this->startingTime);
    }

    /**
     * Set starting time
     *
     * @param string $startingTime
     * @return $this
     */
    public function setStartingTime($startingTime)
    {
        $this->startingTime = (string)$startingTime;
        return $this;
    }

    /**
     * Get ending time
     *
     * @return int
     */
    public function getEndingTime()
    {
        return time();
    }

    /**
     * Reduce points
     *
     * @param float $points
     * @return float
     */
    protected function reducePoints($points)
    {
        // Why 3600? I suppose one point = one hour
        return $points / 3600;
    }

    /**
     * Calculate points
     *
     * @return float
     */
    public function calculatePoints()
    {
        $points = ($this->getEndingTime() - $this->getStartingTime()) * $this->getRate();
        return $this->reducePoints($points);
    }
}
