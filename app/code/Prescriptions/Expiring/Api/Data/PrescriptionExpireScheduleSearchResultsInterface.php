<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Prescriptions\Expiring\Api\Data;

interface PrescriptionExpireScheduleSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get prescription_expire_schedule list.
     * @return \Prescriptions\Expiring\Api\Data\PrescriptionExpireScheduleInterface[]
     */
    public function getItems();

    /**
     * Set name list.
     * @param \Prescriptions\Expiring\Api\Data\PrescriptionExpireScheduleInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

