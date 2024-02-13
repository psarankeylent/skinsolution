<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\DeclinedSubscriptionsReportM1\Model;

use Magento\Framework\Model\AbstractModel;

class DeclinedSubscriptionsReportM1 extends AbstractModel
{
    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Renewal\DeclinedSubscriptionsReportM1\Model\ResourceModel\DeclinedSubscriptionsReportM1::class);
    }

}

