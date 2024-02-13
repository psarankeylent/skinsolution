<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CustomReports\DownloadReports\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CcReports extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('paradoxlabs_stored_card', 'id');
    }

}

