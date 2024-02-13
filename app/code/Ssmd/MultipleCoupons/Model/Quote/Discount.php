<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ssmd\MultipleCoupons\Model\Quote;

use Magento\Framework\App\ObjectManager as ObjectManager;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\SalesRule\Api\Data\RuleDiscountInterfaceFactory;
use Magento\SalesRule\Api\Data\DiscountDataInterfaceFactory;

/**
 * SSMD Discount totals calculation model.
 */
class Discount extends \Magento\SalesRule\Model\Quote\Discount
{
    const COLLECTOR_TYPE_CODE = 'discount';

    /**
     * Discount calculation object
     *
     * @var \Magento\SalesRule\Model\Validator
     */
    protected $calculator;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager = null;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var RuleDiscountInterfaceFactory
     */
    protected $discountInterfaceFactory;

    /**
     * @var DiscountDataInterfaceFactory
     */
    protected $discountDataInterfaceFactory;

    /**
     * Collect address discount amount
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return \Magento\SalesRule\Model\Quote\Discount
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function collect(
        \Magento\Quote\Model\Quote                          $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total            $total
    )
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $quoteManager = $objectManager->create('\ParadoxLabs\Subscriptions\Model\Service\QuoteManager');

        $existingSubscription = $quoteManager->isQuoteExistingSubscription($quote);

        if($existingSubscription) {
            return $this;
        }

        $quoteDiscounts = [];
        $quoteItemDiscounts =[];
        //$quoteDiscounts['items'] = [];
        \Magento\Quote\Model\Quote\Address\Total\AbstractTotal::collect($quote, $shippingAssignment, $total);

        $store = $this->storeManager->getStore($quote->getStoreId());
        $address = $shippingAssignment->getShipping()->getAddress();

        if ($quote->currentPaymentWasSet()) {
            $address->setPaymentMethod($quote->getPayment()->getMethod());
        }

        $this->calculator->reset($address);

        $items = $shippingAssignment->getItems();

        if (!count($items)) {
            return $this;
        }

        $couponCode = $quote->getCouponCode();
        $couponArray = explode(',', $couponCode);

        foreach ($couponArray as $couponCode) {

            if ($address->hasCouponCode()) {
                $appliedCoupon = $address->getCouponCode();
                $appliedCoupons = explode(',', $appliedCoupon);
            } else {
                $appliedCoupon = '';
                $appliedCoupons = [];
            }

            $eventArgs = [
                'website_id' => $store->getWebsiteId(),
                'customer_group_id' => $quote->getCustomerGroupId(),
                'coupon_code' => $couponCode,
            ];

            $this->calculator->init($store->getWebsiteId(), $quote->getCustomerGroupId(), $couponCode);
            $this->calculator->initTotals($items, $address);

            $address->setDiscountDescription([]);
            $items = $this->calculator->sortItemsByPriority($items, $address);

            //$address->getExtensionAttributes()->setDiscounts([]);
            $addressDiscountAggregator = [];

            $totalDiscount = 0;
            $totalBaseDiscount = 0;
            /** @var \Magento\Quote\Model\Quote\Item $item */
            foreach ($items as $item) {
                /*if ($item->getOriginalCustomPrice() !== null)
			continue;*/

		if ($item->getNoDiscount() || !$this->calculator->canApplyDiscount($item)) {
                    $item->setDiscountAmount(0);
                    $item->setBaseDiscountAmount(0);

                    // ensure my children are zeroed out
                    if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                        foreach ($item->getChildren() as $child) {
                            $child->setDiscountAmount(0);
                            $child->setBaseDiscountAmount(0);
                        }
                    }
                    continue;
                }

		$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/impersonation_logger1.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info($item->getId() . ' - ' . $item->getSku() . ' - ' . $item->getOriginalCustomPrice() . ' --- original custom price');
                if ($item->getOriginalCustomPrice() !== null && $item->getAdditionalData() == 'impersonation_item') 
	        {
			$item->setDiscountAmount(0);
			$item->setBaseDiscountAmount(0);
 			continue;
		}
                
                // to determine the child item discount, we calculate the parent
                if ($item->getParentItem()) {
                    continue;
                }

                $eventArgs['item'] = $item;
                $this->eventManager->dispatch('sales_quote_address_discount_item', $eventArgs);

                if ($item->getHasChildren() && $item->isChildrenCalculated()) {
                    $this->calculator->process($item);
                    $this->distributeDiscount($item);
                    foreach ($item->getChildren() as $child) {
                        $eventArgs['item'] = $child;
                        $this->eventManager->dispatch('sales_quote_address_discount_item', $eventArgs);
                        $this->aggregateItemDiscount($child, $total);
                    }
                } else {
                    $this->calculator->process($item);
                    $this->aggregateItemDiscount($item, $total);
                }
                if ($item->getExtensionAttributes()) {
                    $this->ssmdAggregateDiscountPerRule($item, $address, $addressDiscountAggregator);
                }

                $itemDiscounts = $item->getExtensionAttributes()->getDiscounts();

                if ($itemDiscounts) {
                    foreach ($itemDiscounts as $discount) {
                        $totalDiscount += $discount->getDiscountData()->getAmount();
                        $totalBaseDiscount += $discount->getDiscountData()->getBaseAmount();
                    }
                }

                $quoteItemDiscounts[$item->getId()] = $this->buildQuoteDiscounts($item);
            }

            if ($address->hasCouponCode()) {
                $currentCouponCode = $address->getCouponCode();
                if ($appliedCoupon != $currentCouponCode)
                    $appliedCoupons[] = $currentCouponCode;
                $address->setCouponCode(implode(',', $appliedCoupons));
            }

            $this->calculator->prepareDescription($address);

            $total->setDiscountDescription($address->getDiscountDescription());
            $total->setSubtotalWithDiscount($total->getSubtotal() - $totalDiscount);
            $total->setBaseSubtotalWithDiscount($total->getBaseSubtotal() - $totalBaseDiscount);
            $total->setTotalAmount($this->getCode(), -$totalDiscount);
            $total->setBaseTotalAmount($this->getCode(), -$totalBaseDiscount);
            $address->setDiscountAmount(-$totalDiscount);
            $address->setBaseDiscountAmount(-$totalBaseDiscount);

            // SSMD Code to insert Rule Level Discounts in quote_discounts db table

            $objectManager = ObjectManager::getInstance();
            $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
            $connection = $resource->getConnection();

            $tableName = $connection->getTableName('quote_discounts');

            $quoteId = $quote->getId();
            $quoteDiscounts = [
                'quote_items' => $quoteItemDiscounts,
                'quote' => $this->getQuoteDiscountValues($quote)
                ];
            $quoteDiscounts = json_encode($quoteDiscounts);

            $sql = "INSERT INTO $tableName (quote_id, quote_discounts) VALUES ($quoteId, '$quoteDiscounts') ON DUPLICATE KEY UPDATE quote_discounts = '$quoteDiscounts'";

            $connection->query($sql);
        }

        return $this;

    }

    /**
     * Aggregate item discount information to total data and related properties
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this
     */
    protected function aggregateItemDiscount(
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        \Magento\Quote\Model\Quote\Address\Total     $total
    )
    {

        $total->setTotalAmount($this->getCode(), -$this->getItemDiscounts($item,'amount'));
        $total->setBaseTotalAmount($this->getCode(), -$this->getItemDiscounts($item,'base_amount'));

        /*$total->addTotalAmount($this->getCode(), -$this->getItemDiscounts($item,'amount'));
        $total->addBaseTotalAmount($this->getCode(), -$this->getItemDiscounts($item,'base_amount'));
        return $this;*/
    }

    /**
     * Distribute discount at parent item to children items
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @return $this
     */
    protected function distributeDiscount(\Magento\Quote\Model\Quote\Item\AbstractItem $item)
    {
        $parentBaseRowTotal = $item->getBaseRowTotal();
        $keys = [
            'discount_amount',
            'base_discount_amount',
            'original_discount_amount',
            'base_original_discount_amount',
        ];
        $roundingDelta = [];
        foreach ($keys as $key) {
            //Initialize the rounding delta to a tiny number to avoid floating point precision problem
            $roundingDelta[$key] = 0.0000001;
        }
        foreach ($item->getChildren() as $child) {
            $ratio = $parentBaseRowTotal != 0 ? $child->getBaseRowTotal() / $parentBaseRowTotal : 0;
            foreach ($keys as $key) {
                if (!$item->hasData($key)) {
                    continue;
                }
                $value = $item->getData($key) * $ratio;
                $roundedValue = $this->priceCurrency->round($value + $roundingDelta[$key]);
                $roundingDelta[$key] += $value - $roundedValue;
                $child->setData($key, $roundedValue);
            }
        }

        foreach ($keys as $key) {
            $item->setData($key, 0);
        }
        return $this;
    }

    /**
     * Add discount total information to address
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array|null
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        $result = null;
        $amount = $total->getDiscountAmount();

        if ($amount != 0) {
            $description = $total->getDiscountDescription();
            $result = [
                'code' => $this->getCode(),
                'title' => strlen($description) ? __('Discount (%1)', $description) : __('Discount'),
                'value' => $amount
            ];
        }
        return $result;
    }

    /**
     * Aggregates discount per rule
     *
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param \Magento\Quote\Api\Data\AddressInterface $address
     * @param array $addressDiscountAggregator
     * @return void
     */
    private function ssmdAggregateDiscountPerRule(
        \Magento\Quote\Model\Quote\Item\AbstractItem $item,
        \Magento\Quote\Api\Data\AddressInterface     $address,
        array                                        &$addressDiscountAggregator
    )
    {
        $discountBreakdown = $item->getExtensionAttributes()->getDiscounts();
        if ($discountBreakdown) {
            foreach ($discountBreakdown as $value) {
                /* @var \Magento\SalesRule\Api\Data\DiscountDataInterface $discount */
                $discount = $value->getDiscountData();
                $ruleLabel = $value->getRuleLabel();
                $ruleID = $value->getRuleID();
                if (isset($addressDiscountAggregator[$ruleID])) {

                    //if (isset($addressDiscountAggregator[$ruleID])) {
                    /** @var \Magento\SalesRule\Model\Data\RuleDiscount $cartDiscount */
                    $cartDiscount = $addressDiscountAggregator[$ruleID];
                    $discountData = $cartDiscount->getDiscountData();
                    $discountData->setBaseAmount($discountData->getBaseAmount() + $discount->getBaseAmount());
                    $discountData->setAmount($discountData->getAmount() + $discount->getAmount());
                    $discountData->setOriginalAmount($discountData->getOriginalAmount() + $discount->getOriginalAmount());
                    $discountData->setBaseOriginalAmount(
                        $discountData->getBaseOriginalAmount() + $discount->getBaseOriginalAmount()
                    );
                } else {
                    $data = [
                        'amount' => $discount->getAmount(),
                        'base_amount' => $discount->getBaseAmount(),
                        'original_amount' => $discount->getOriginalAmount(),
                        'base_original_amount' => $discount->getBaseOriginalAmount()
                    ];

                    $this->discountDataInterfaceFactory = ObjectManager::getInstance()->get(DiscountDataInterfaceFactory::class);
                    $discountData = $this->discountDataInterfaceFactory->create(['data' => $data]);
                    $data = [
                        'discount' => $discountData,
                        'rule' => $ruleLabel,
                        'rule_id' => $ruleID,
                    ];
                    /** @var \Magento\SalesRule\Model\Data\RuleDiscount $cartDiscount */
                    $this->discountInterfaceFactory = ObjectManager::getInstance()->get(RuleDiscountInterfaceFactory::class);
                    $cartDiscount = $this->discountInterfaceFactory->create(['data' => $data]);
                    $addressDiscountAggregator[$ruleID] = $cartDiscount;
                }
            }
        }

        $address->getExtensionAttributes()->setDiscounts($addressDiscountAggregator);
    }

    public function getItemDiscounts($item, $amountType)
    {
        $itemDiscounts = $item->getExtensionAttributes()->getDiscounts();

        $total = 0;

        if ($itemDiscounts) {
            foreach ($itemDiscounts as $itemDiscount) {
                $data = $itemDiscount->getDiscountData()->__toArray();
                $total += $data[$amountType];
            }
        }

        return $total;
    }

    protected function buildQuoteDiscounts($cartItem)
    {
        $currencyCode = $cartItem->getQuote()->getQuoteCurrencyCode();

        return [
            'price' =>  $cartItem->getPrice(),
            'row_total' => $cartItem->getRowTotal(),
            'row_total_including_tax' => $cartItem->getRowTotalInclTax(),
            'total_item_discount' => $this->getCartItemDiscountAmount($cartItem),
            'discounts' => $this->getDiscountValues($cartItem, $currencyCode)
        ];
    }

    protected function getCartItemDiscountAmount($item)
    {
        $discounts = $item->getExtensionAttributes()->getDiscounts();

        $total = 0;

        if ($discounts) {
            foreach ($discounts as $discount) {
                $total += $discount->getDiscountData()->getAmount();
            }
        }

        return $total;
    }
    /**
     * Get Discount Values
     *
     * @param Item $cartItem
     * @param string $currencyCode
     * @return array
     */
    protected function getDiscountValues($cartItem, $currencyCode)
    {
        $itemDiscounts = $cartItem->getExtensionAttributes()->getDiscounts();
        if ($itemDiscounts) {
            $discountValues=[];
            foreach ($itemDiscounts as $value) {
                $discount = [];
                $amount = [];
                /* @var \Magento\SalesRule\Api\Data\DiscountDataInterface $discountData */
                $discountData = $value->getDiscountData();
                $discountAmount = $discountData->getAmount();
                $discount['label'] = $value->getRuleLabel() ?: __('Discount');
                $amount['value'] = $discountAmount;
                $amount['currency'] = $currencyCode;
                $discount['amount'] = $amount;
                $discountValues[] = $discount;
            }
            return $discountValues;
        }
        return null;
    }

    protected function getQuoteDiscountValues(Quote $quote)
    {
        $discountValues=[];
        $address = $quote->getShippingAddress();
        $totalDiscounts = $address->getExtensionAttributes()->getDiscounts();
        if ($totalDiscounts && is_array($totalDiscounts)) {
            foreach ($totalDiscounts as $value) {
                $discount = [];
                $amount = [];
                $discount['label'] = $value->getRuleLabel() ?: __('Discount');
                /* @var \Magento\SalesRule\Api\Data\DiscountDataInterface $discountData */
                $discountData = $value->getDiscountData();
                $amount['value'] = $discountData->getAmount();
                $amount['currency'] = $quote->getQuoteCurrencyCode();
                $discount['amount'] = $amount;
                $discountValues[] = $discount;
            }
            return $discountValues;
        }
        return null;
    }
}
