<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CustomReports\BatchListReport\Model;

use Magento\Framework\Model\AbstractModel;

class BatchStatsReport extends AbstractModel
{
    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\CustomReports\BatchListReport\Model\ResourceModel\BatchStatsReport::class);
    }

}

