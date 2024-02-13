<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\DeclinedSubscriptionsReport\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class DeclinedSubscriptionsReport extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('paradoxlabs_subscription', 'entity_id');
    }

}

