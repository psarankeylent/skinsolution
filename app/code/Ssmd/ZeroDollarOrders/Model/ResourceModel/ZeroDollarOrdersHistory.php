<?php
/**
 * Copyright © Zero Dollar Orders All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ssmd\ZeroDollarOrders\Model\ResourceModel;

class ZeroDollarOrdersHistory extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('zero_dollar_orders_history', 'history_id');
    }
}

