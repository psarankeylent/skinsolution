<?php

namespace Ssmd\StoreCredit\Block\Adminhtml\Order;

class Totals extends \Magento\Sales\Block\Adminhtml\Order\Totals
{
      /**
     * Initialize order totals array
     *
     * @return $this
     */
    protected function _initTotals()
    {
        parent::_initTotals();

        $storecredit    = '';
        $order          = $this->getSource();
        $incrementId    = $order->getIncrementId();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $storecreditOrderFactory = $objectManager->get('\Ssmd\StoreCredit\Model\StorecreditOrderFactory');
        $collection = $storecreditOrderFactory->create()
                        ->getCollection()
                        ->addFieldToFilter('increment_id',$incrementId);
        if($collection->count()) {
            $storecredit = $collection->getFirstItem()->getData()['credits'];
        }

        //$order = $this->getSource();
        
        if ($storecredit) {
            //$discountLabel = __('Store Credit (%1)', $order->getCouponCode());
            $discountLabel = __('Store Credit');

            $this->_totals['storecredit'] = new \Magento\Framework\DataObject(
                [
                    'code' => 'storecredit',
                    'value' => $storecredit,
                    'label' => $discountLabel,
                ]
            );

            return $this;
        } 
        
    }    

}