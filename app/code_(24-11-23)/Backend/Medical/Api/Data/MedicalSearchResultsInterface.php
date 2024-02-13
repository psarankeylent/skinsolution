<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Backend\Medical\Api\Data;

interface MedicalSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get Medical list.
     * @return \Backend\Medical\Api\Data\MedicalInterface[]
     */
    public function getItems();

    /**
     * Set view list.
     * @param \Backend\Medical\Api\Data\MedicalInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

