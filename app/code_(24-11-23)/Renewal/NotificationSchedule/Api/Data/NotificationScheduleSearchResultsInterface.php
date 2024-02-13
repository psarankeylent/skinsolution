<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\NotificationSchedule\Api\Data;

interface NotificationScheduleSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get NotificationSchedule list.
     * @return \Renewal\NotificationSchedule\Api\Data\NotificationScheduleInterface[]
     */
    public function getItems();

    /**
     * Set intervel_days list.
     * @param \Renewal\NotificationSchedule\Api\Data\NotificationScheduleInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

