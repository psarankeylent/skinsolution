<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\NotificationSchedule\Api\Data;

interface NotificationScheduleInterface
{

    const LAST_RUN = 'last_run';
    const UPDATED_AT = 'updated_at';
    const NOTIFICATIONSCHEDULE_ID = 'notificationschedule_id';
    const NEXT_RUN = 'next_run';
    const EXPERATION_DAYS = 'Experation_days';
    const INTERVEL_DAYS = 'intervel_days';

    /**
     * Get notificationschedule_id
     * @return string|null
     */
    public function getNotificationscheduleId();

    /**
     * Set notificationschedule_id
     * @param string $notificationscheduleId
     * @return \Renewal\NotificationSchedule\NotificationSchedule\Api\Data\NotificationScheduleInterface
     */
    public function setNotificationscheduleId($notificationscheduleId);

    /**
     * Get intervel_days
     * @return string|null
     */
    public function getIntervelDays();

    /**
     * Set intervel_days
     * @param string $intervelDays
     * @return \Renewal\NotificationSchedule\NotificationSchedule\Api\Data\NotificationScheduleInterface
     */
    public function setIntervelDays($intervelDays);

    /**
     * Get Experation_days
     * @return string|null
     */
    public function getExperationDays();

    /**
     * Set Experation_days
     * @param string $experationDays
     * @return \Renewal\NotificationSchedule\NotificationSchedule\Api\Data\NotificationScheduleInterface
     */
    public function setExperationDays($experationDays);

    /**
     * Get last_run
     * @return string|null
     */
    public function getLastRun();

    /**
     * Set last_run
     * @param string $lastRun
     * @return \Renewal\NotificationSchedule\NotificationSchedule\Api\Data\NotificationScheduleInterface
     */
    public function setLastRun($lastRun);

    /**
     * Get next_run
     * @return string|null
     */
    public function getNextRun();

    /**
     * Set next_run
     * @param string $nextRun
     * @return \Renewal\NotificationSchedule\NotificationSchedule\Api\Data\NotificationScheduleInterface
     */
    public function setNextRun($nextRun);

    /**
     * Get updated_at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated_at
     * @param string $updatedAt
     * @return \Renewal\NotificationSchedule\NotificationSchedule\Api\Data\NotificationScheduleInterface
     */
    public function setUpdatedAt($updatedAt);
}

