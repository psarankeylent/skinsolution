<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ssmd\MultipleCoupons\Model\Rule\Action\Discount;

class ByPercent extends \Magento\SalesRule\Model\Rule\Action\Discount\ByPercent
{
    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param float $qty
     * @return Data
     */
    public function calculate($rule, $item, $qty)
    {
        $rulePercent = min(100, $rule->getDiscountAmount());
        $discountData = $this->_calculate($rule, $item, $qty, $rulePercent);

        return $discountData;
    }

    /**
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param float $qty
     * @param float $rulePercent
     * @return Data
     */
    protected function _calculate($rule, $item, $qty, $rulePercent)
    {
        /** @var \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData */
        $discountData = $this->discountFactory->create();

        $itemPrice = $this->validator->getItemPrice($item);
        $baseItemPrice = $this->validator->getItemBasePrice($item);
        $itemOriginalPrice = $this->validator->getItemOriginalPrice($item);
        $baseItemOriginalPrice = $this->validator->getItemBaseOriginalPrice($item);

        $_rulePct = $rulePercent / 100;

        /*$discountData->setAmount(($qty * $itemPrice - $item->getDiscountAmount()) * $_rulePct);
        $discountData->setBaseAmount(($qty * $baseItemPrice - $item->getBaseDiscountAmount()) * $_rulePct);
        $discountData->setOriginalAmount(($qty * $itemOriginalPrice - $item->getDiscountAmount()) * $_rulePct);
        $discountData->setBaseOriginalAmount(
            ($qty * $baseItemOriginalPrice - $item->getBaseDiscountAmount()) * $_rulePct
        );*/

        $discountData->setAmount(($qty * $itemPrice - $this->getItemsPreviousDiscounts($item, 'amount', $rule->getId())) * $_rulePct);
        $discountData->setBaseAmount(($qty * $baseItemPrice - $this->getItemsPreviousDiscounts($item, 'base_amount', $rule->getId())) * $_rulePct);
        $discountData->setOriginalAmount(($qty * $itemOriginalPrice - $this->getItemsPreviousDiscounts($item, 'original_amount', $rule->getId())) * $_rulePct);
        $discountData->setBaseOriginalAmount(
            ($qty * $baseItemOriginalPrice - $this->getItemsPreviousDiscounts($item, 'base_original_amount', $rule->getId())) * $_rulePct
        );

        if (!$rule->getDiscountQty() || $rule->getDiscountQty() > $qty) {
            $discountPercent = min(100, $item->getDiscountPercent() + $rulePercent);
            $item->setDiscountPercent($discountPercent);
        }

        return $discountData;
    }

    public function getItemsPreviousDiscounts($item, $amountType, $ruleId)
    {
        $itemDiscounts = $item->getExtensionAttributes()->getDiscounts();

        $total = 0;

        if ($itemDiscounts) {
            foreach ($itemDiscounts as $itemDiscount) {
                if ($ruleId == $itemDiscount->getRuleId())
                    break;
                $data = $itemDiscount->getDiscountData()->__toArray();
                $total += $data[$amountType];
            }
        }

        return $total;
    }
}
