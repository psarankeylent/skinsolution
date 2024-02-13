<?php

namespace Ssmd\StoreCredit\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * StoreCredit Resource Model
 *
 * @author      StoreCredit
 */
class QuoteStorecredit extends AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('quote_storecredit', 'id');
    }
}