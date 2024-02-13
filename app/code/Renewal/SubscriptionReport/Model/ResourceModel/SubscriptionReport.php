<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\SubscriptionReport\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class SubscriptionReport extends AbstractDb
{
    protected $connectionName = 'custom';  // Custom database declareation

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('ssmd_web_customer_subscriptions', 'id');
    }

}

