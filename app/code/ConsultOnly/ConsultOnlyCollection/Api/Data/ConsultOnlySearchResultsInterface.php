<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ConsultOnly\ConsultOnlyCollection\Api\Data;

interface ConsultOnlySearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get ConsultOnly list.
     * @return \ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyInterface[]
     */
    public function getItems();

    /**
     * Set name list.
     * @param \ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

