<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CustomReports\SubscriptionReport\Model;

use Magento\Framework\Model\AbstractModel;

class SubscriptionReport extends AbstractModel
{
    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\CustomReports\SubscriptionReport\Model\ResourceModel\SubscriptionReport::class);
    }

}

