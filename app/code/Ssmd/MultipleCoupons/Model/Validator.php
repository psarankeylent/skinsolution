<?php
namespace Ssmd\MultipleCoupons\Model;

use Magento\Quote\Model\Quote\Address;

class Validator extends \Magento\SalesRule\Model\Validator
{
    /**
     * Reset quote and address applied rules
     *
     * @param Address $address
     * @return \Magento\SalesRule\Model\Validator
     */
    public function reset(Address $address)
    {
        $this->validatorUtility->resetRoundingDeltas();
        $address->setBaseSubtotalWithDiscount($address->getBaseSubtotal());
        $address->setSubtotalWithDiscount($address->getSubtotal());
        if ($this->_isFirstTimeResetRun) {
            $address->setAppliedRuleIds('');
            $address->getQuote()->setAppliedRuleIds('');
            $items = $address->getQuote()->getItems();
            if ($items) {
                foreach ($items as $item) {
                    $item->setAppliedRuleIds('');
                }
            }
            $this->_isFirstTimeResetRun = false;
        }

        return $this;
    }
}