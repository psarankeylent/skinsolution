<?php

namespace Ssmd\StoreCredit\Model;

use Magento\Framework\Model\AbstractModel;

/**
 * StoreCredit Model
 *
 * @author      StoreCredit
 */
class QuoteStorecredit extends AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Ssmd\StoreCredit\Model\ResourceModel\QuoteStorecredit::class);
    }
}