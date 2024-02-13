<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\NotificationReport\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface NotificationReportRepositoryInterface
{

    /**
     * Save NotificationReport
     * @param \Renewal\NotificationReport\Api\Data\NotificationReportInterface $notificationReport
     * @return \Renewal\NotificationReport\Api\Data\NotificationReportInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Renewal\NotificationReport\Api\Data\NotificationReportInterface $notificationReport
    );

    /**
     * Retrieve NotificationReport
     * @param string $notificationreportId
     * @return \Renewal\NotificationReport\Api\Data\NotificationReportInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($notificationreportId);

    /**
     * Retrieve NotificationReport matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Renewal\NotificationReport\Api\Data\NotificationReportSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete NotificationReport
     * @param \Renewal\NotificationReport\Api\Data\NotificationReportInterface $notificationReport
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Renewal\NotificationReport\Api\Data\NotificationReportInterface $notificationReport
    );

    /**
     * Delete NotificationReport by ID
     * @param string $notificationreportId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($notificationreportId);
}

