<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\DeclinedSubscriptionsReport\Model;

use Magento\Framework\Model\AbstractModel;

class DeclinedSubscriptionsReport extends AbstractModel
{
    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Renewal\DeclinedSubscriptionsReport\Model\ResourceModel\DeclinedSubscriptionsReport::class);
    }

}

