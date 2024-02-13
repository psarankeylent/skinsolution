<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\NotificationReport\Api\Data;

interface NotificationReportSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get NotificationReport list.
     * @return \Renewal\NotificationReport\Api\Data\NotificationReportInterface[]
     */
    public function getItems();

    /**
     * Set id list.
     * @param \Renewal\NotificationReport\Api\Data\NotificationReportInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

