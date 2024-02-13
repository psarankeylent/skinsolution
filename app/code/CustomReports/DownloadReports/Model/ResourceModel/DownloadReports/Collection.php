<?php

namespace CustomReports\DownloadReports\Model\ResourceModel\DownloadReports;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    protected function _construct()
    {
        $this->_init('CustomReports\DownloadReports\Model\DownloadReports', 'CustomReports\DownloadReports\Model\ResourceModel\DownloadReports');
    }

}

?>