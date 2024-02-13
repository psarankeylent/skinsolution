<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\NotificationSchedule\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface NotificationScheduleRepositoryInterface
{

    /**
     * Save NotificationSchedule
     * @param \Renewal\NotificationSchedule\Api\Data\NotificationScheduleInterface $notificationSchedule
     * @return \Renewal\NotificationSchedule\Api\Data\NotificationScheduleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Renewal\NotificationSchedule\Api\Data\NotificationScheduleInterface $notificationSchedule
    );

    /**
     * Retrieve NotificationSchedule
     * @param string $notificationscheduleId
     * @return \Renewal\NotificationSchedule\Api\Data\NotificationScheduleInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($notificationscheduleId);

    /**
     * Retrieve NotificationSchedule matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Renewal\NotificationSchedule\Api\Data\NotificationScheduleSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete NotificationSchedule
     * @param \Renewal\NotificationSchedule\Api\Data\NotificationScheduleInterface $notificationSchedule
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Renewal\NotificationSchedule\Api\Data\NotificationScheduleInterface $notificationSchedule
    );

    /**
     * Delete NotificationSchedule by ID
     * @param string $notificationscheduleId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($notificationscheduleId);
}

