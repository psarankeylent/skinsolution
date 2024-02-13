<?php

namespace Ssmd\StoreCredit\Block\Sales\Order;

use Ssmd\StoreCredit\Helper\Data as StoreCreditHelper;

class Totals extends \Magento\Framework\View\Element\Template
{
    /**
     * @var StoreCreditHelper
     */
    protected  $storeCredit;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param StoreCreditHelper $storeCredit
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        StoreCreditHelper $storeCredit,
        array $data = []
    ) {
        $this->storeCredit = $storeCredit;
        parent::__construct($context, $data);
    }

    /**
     * @return $this
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_order = $parent->getOrder();
        $this->_source = $parent->getSource();

        $baseDiscount = $this->storeCredit->getQuotesStoreCredit($this->_order->getQuoteId());

        if ($baseDiscount) {

            $storeCredit = new \Magento\Framework\DataObject(
                [
                    'code' => 'storecredit',
                    'strong' => false,
                    'value' => -$baseDiscount,
                    'label' => __('Store Credit'),
                ]
            );

            $parent->addTotal($storeCredit, 'storecredit');
        }

        return $this;
    }
}