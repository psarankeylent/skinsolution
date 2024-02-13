<?php

namespace Ssmd\StoreCredit\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * StoreCredit Resource Model
 *
 * @author      StoreCredit
 */
class StorecreditCustomerBalanceHistory extends AbstractDb
{
    /**
     * Initialize resource
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('storecredit_customer_balance_history', 'history_id');
    }
}