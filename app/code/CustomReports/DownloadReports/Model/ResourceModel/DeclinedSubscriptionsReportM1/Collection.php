<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CustomReports\DownloadReports\Model\ResourceModel\DeclinedSubscriptionsReportM1;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;


class Collection extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            \CustomReports\DownloadReports\Model\DeclinedSubscriptionsReportM1::class,
            \CustomReports\DownloadReports\Model\ResourceModel\DeclinedSubscriptionsReportM1::class
        );
    }

}


