<?php

namespace Renewal\CcReports\Block\Adminhtml;

use Magento\Framework\Exception\LocalizedException;

class CcReports extends \Magento\Backend\Block\Template
{
    protected $request;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\RequestInterface $request
    ) {
        parent::__construct($context);
        $this->request = $request;
    }
    public function getDays()
    {
        $days = $this->request->getParam('days');
        if($days)
        {
            return "Displaying : ".$days." days";
        }
        else
        {
            return "Displaying : All days";
        }
    }
}

