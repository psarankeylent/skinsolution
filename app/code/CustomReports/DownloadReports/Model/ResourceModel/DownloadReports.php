<?php

namespace CustomReports\DownloadReports\Model\ResourceModel;

class DownloadReports extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('download_reports', 'id');
    }
}
?>
