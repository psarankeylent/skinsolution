<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\NotificationSchedule\Model;

use Magento\Framework\Model\AbstractModel;
use Renewal\NotificationSchedule\Api\Data\NotificationScheduleInterface;

class NotificationSchedule extends AbstractModel implements NotificationScheduleInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Renewal\NotificationSchedule\Model\ResourceModel\NotificationSchedule::class);
    }

    /**
     * @inheritDoc
     */
    public function getNotificationscheduleId()
    {
        return $this->getData(self::NOTIFICATIONSCHEDULE_ID);
    }

    /**
     * @inheritDoc
     */
    public function setNotificationscheduleId($notificationscheduleId)
    {
        return $this->setData(self::NOTIFICATIONSCHEDULE_ID, $notificationscheduleId);
    }

    /**
     * @inheritDoc
     */
    public function getIntervelDays()
    {
        return $this->getData(self::INTERVEL_DAYS);
    }

    /**
     * @inheritDoc
     */
    public function setIntervelDays($intervelDays)
    {
        return $this->setData(self::INTERVEL_DAYS, $intervelDays);
    }

    /**
     * @inheritDoc
     */
    public function getExperationDays()
    {
        return $this->getData(self::EXPERATION_DAYS);
    }

    /**
     * @inheritDoc
     */
    public function setExperationDays($experationDays)
    {
        return $this->setData(self::EXPERATION_DAYS, $experationDays);
    }

    /**
     * @inheritDoc
     */
    public function getLastRun()
    {
        return $this->getData(self::LAST_RUN);
    }

    /**
     * @inheritDoc
     */
    public function setLastRun($lastRun)
    {
        return $this->setData(self::LAST_RUN, $lastRun);
    }

    /**
     * @inheritDoc
     */
    public function getNextRun()
    {
        return $this->getData(self::NEXT_RUN);
    }

    /**
     * @inheritDoc
     */
    public function setNextRun($nextRun)
    {
        return $this->setData(self::NEXT_RUN, $nextRun);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }
}

