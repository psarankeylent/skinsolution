<?php

namespace Ssmd\Impersonation\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class RemoveBlock implements ObserverInterface
{
    protected $_scopeConfig;

    public function __construct
    (
        ScopeConfigInterface $scopeConfig
    )
    {
        $this->_scopeConfig = $scopeConfig;
    }

    public function execute(Observer $observer)
    {
        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $observer->getLayout();
        $block = $layout->getBlock('impersonation_sales_grid'); 

        if ($block) {
            $remove = $this->_scopeConfig->getValue('impersonation/general/enable', ScopeInterface::SCOPE_STORE);
            if ($remove == 0) {
                $layout->unsetElement('impersonation_sales_grid');
            }
            else if($remove == 1)
            {

                $layout->unsetElement('adminhtml.customer.grid.container');
            }
        }
    }
}