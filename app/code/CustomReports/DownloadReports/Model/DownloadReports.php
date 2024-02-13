<?php

namespace CustomReports\DownloadReports\Model;

class DownloadReports extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('CustomReports\DownloadReports\Model\ResourceModel\DownloadReports');
    }
}
?>