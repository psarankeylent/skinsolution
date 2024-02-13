<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CustomReports\StoreCreditReport\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class StoreCreditReport extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('storecredit', 'id');
    }

}

