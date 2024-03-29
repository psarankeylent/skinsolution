<?php

namespace Renewal\ExpirationReport\Block\Adminhtml;

use Magento\Framework\Exception\LocalizedException;

class AdminGrid extends \Magento\Backend\Block\Template
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
        $days = $this->request->getParam('expiration_date');
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

