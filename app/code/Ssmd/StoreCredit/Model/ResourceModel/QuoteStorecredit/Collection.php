<?php

namespace Ssmd\StoreCredit\Model\ResourceModel\QuoteStorecredit;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * QuoteStoreCredit Resource Model Collection
 *
 * @author      QuoteStoreCredit
 */
class Collection extends AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Ssmd\StoreCredit\Model\QuoteStorecredit', 'Ssmd\StoreCredit\Model\ResourceModel\QuoteStorecredit');
    }
}