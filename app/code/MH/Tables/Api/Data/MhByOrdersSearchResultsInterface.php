<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace MH\Tables\Api\Data;

interface MhByOrdersSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get mh_by_orders list.
     * @return \MH\Tables\Api\Data\MhByOrdersInterface[]
     */
    public function getItems();

    /**
     * Set question_id list.
     * @param \MH\Tables\Api\Data\MhByOrdersInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}

