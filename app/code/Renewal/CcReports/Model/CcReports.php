<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\CcReports\Model;

use Magento\Framework\Model\AbstractModel;

class CcReports extends AbstractModel
{
    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Renewal\CcReports\Model\ResourceModel\CcReports::class);
    }

}

