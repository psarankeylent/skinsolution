<?php
/**
 * Copyright © Zero Dollar Orders All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ssmd\ZeroDollarOrders\Model\ResourceModel\ZeroDollarOrdersHistory;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'history_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Ssmd\ZeroDollarOrders\Model\ZeroDollarOrdersHistory::class,
            \Ssmd\ZeroDollarOrders\Model\ResourceModel\ZeroDollarOrdersHistory::class
        );
    }
}

