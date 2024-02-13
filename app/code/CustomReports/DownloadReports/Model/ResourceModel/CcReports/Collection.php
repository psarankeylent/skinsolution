<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CustomReports\DownloadReports\Model\ResourceModel\CcReports;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;


class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \CustomReports\DownloadReports\Model\CcReports::class,
            \CustomReports\DownloadReports\Model\ResourceModel\CcReports::class
        );
    }

}


