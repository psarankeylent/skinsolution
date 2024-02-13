<?php

namespace CustomReports\DownloadReports\Block\Adminhtml;

use Magento\Framework\View\Element\Template\Context;

class DownloadReports extends \Magento\Framework\View\Element\Template
{
    protected $downloadReportsFactory;

    public function __construct(
        Context $context,
        \CustomReports\DownloadReports\Model\DownloadReportsFactory $downloadReportsFactory,
        \Magento\Backend\Model\UrlInterface $urlBuilder
    ) {
        parent::__construct($context);
        $this->downloadReportsFactory = $downloadReportsFactory;
        $this->urlBuilder = $urlBuilder;

   }
    public function getDownloadReportsData()
    {
        $collection = $this->downloadReportsFactory->create()->getCollection();
        //$collection->addFieldToFilter('filesize', ['neq'=>null]);
        //$collection->addFieldToFilter('filepath', ['neq'=>null]);
        return $collection;
    }
    public function getBaseNewUrl()
    {
        return $this->urlBuilder->getUrl('downloadreports/download/downloadcsv');
    }
}
