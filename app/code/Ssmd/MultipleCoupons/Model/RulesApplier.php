<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Ssmd\MultipleCoupons\Model;

use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\SalesRule\Model\Quote\ChildrenValidationLocator;
use Magento\Framework\App\ObjectManager;
use Magento\SalesRule\Model\ResourceModel\Rule\Collection;
use Magento\SalesRule\Model\Rule\Action\Discount\CalculatorFactory;
use Magento\SalesRule\Model\Rule\Action\Discount\DataFactory;
use Magento\SalesRule\Api\Data\RuleDiscountInterfaceFactory;
use Magento\SalesRule\Api\Data\DiscountDataInterfaceFactory;

/**
 * Class RulesApplier
 *
 * @package Magento\SalesRule\Model\Validator
 */
class RulesApplier extends \Magento\SalesRule\Model\RulesApplier
{
    /**
     * Application Event Dispatcher
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\SalesRule\Model\Utility
     */
    protected $validatorUtility;

    /**
     * @var ChildrenValidationLocator
     */
    private $childrenValidationLocator;

    /**
     * @var CalculatorFactory
     */
    private $calculatorFactory;

    /**
     * @var \Magento\SalesRule\Model\Rule\Action\Discount\DataFactory
     */
    protected $discountFactory;

    /**
     * @var RuleDiscountInterfaceFactory
     */
    private $discountInterfaceFactory;

    /**
     * @var DiscountDataInterfaceFactory
     */
    private $discountDataInterfaceFactory;

    /**
     * @var array
     */
    private $discountAggregator;

    /**
     * RulesApplier constructor.
     * @param CalculatorFactory $calculatorFactory
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param Utility $utility
     * @param ChildrenValidationLocator|null $childrenValidationLocator
     * @param DataFactory|null $discountDataFactory
     * @param RuleDiscountInterfaceFactory|null $discountInterfaceFactory
     * @param DiscountDataInterfaceFactory|null $discountDataInterfaceFactory
     */
    public function __construct(
        \Magento\SalesRule\Model\Rule\Action\Discount\CalculatorFactory $calculatorFactory,
        \Magento\Framework\Event\ManagerInterface                       $eventManager,
        \Magento\SalesRule\Model\Utility                                $utility,
        ChildrenValidationLocator                                       $childrenValidationLocator = null,
        DataFactory                                                     $discountDataFactory = null,
        RuleDiscountInterfaceFactory                                    $discountInterfaceFactory = null,
        DiscountDataInterfaceFactory                                    $discountDataInterfaceFactory = null
    )
    {
        $this->calculatorFactory = $calculatorFactory;
        $this->validatorUtility = $utility;
        $this->_eventManager = $eventManager;
        $this->childrenValidationLocator = $childrenValidationLocator
            ?: ObjectManager::getInstance()->get(ChildrenValidationLocator::class);
        $this->discountFactory = $discountDataFactory ?: ObjectManager::getInstance()->get(DataFactory::class);
        $this->discountInterfaceFactory = $discountInterfaceFactory
            ?: ObjectManager::getInstance()->get(RuleDiscountInterfaceFactory::class);
        $this->discountDataInterfaceFactory = $discountDataInterfaceFactory
            ?: ObjectManager::getInstance()->get(DiscountDataInterfaceFactory::class);
    }

    /**
     * Apply rules to current order item
     *
     * @param AbstractItem $item
     * @param Collection $rules
     * @param bool $skipValidation
     * @param mixed $couponCode
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function applyRules($item, $rules, $skipValidation, $couponCode)
    {     
	$address = $item->getAddress();
        $appliedRuleIds = [];
        $this->discountAggregator = [];
        /* @var $rule Rule */
        foreach ($rules as $rule) {
            if (!$this->validatorUtility->canProcessRule($rule, $address)) {
                continue;
            }

            if (!$skipValidation && !$rule->getActions()->validate($item)) {
                if (!$this->childrenValidationLocator->isChildrenValidationRequired($item)) {
                    continue;
                }
                $childItems = $item->getChildren();
                $isContinue = true;
                if (!empty($childItems)) {
                    foreach ($childItems as $childItem) {
                        if ($rule->getActions()->validate($childItem)) {
                            $isContinue = false;
                        }
                    }
                }
                if ($isContinue) {
                    continue;
                }
            }

            $this->applyRule($item, $rule, $address, $couponCode);
            $appliedRuleIds[$rule->getRuleId()] = $rule->getRuleId();

            if ($rule->getStopRulesProcessing()) {
                break;
            }
        }

        return $appliedRuleIds;
    }

    /**
     * Add rule discount description label to address object
     *
     * @param Address $address
     * @param Rule $rule
     * @return $this
     */
    public function addDiscountDescription($address, $rule)
    {
        $description = $address->getDiscountDescriptionArray();
        $ruleLabel = $rule->getStoreLabel($address->getQuote()->getStore());
        $label = '';
        if ($ruleLabel) {
            $label = $ruleLabel;
        } else {
            if (strlen($address->getCouponCode())) {
                $label = $address->getCouponCode();

                if ($rule->getDescription()) {
                    $label = $rule->getDescription();
                }
            }
        }

        if (strlen($label)) {
            $description[$rule->getId()] = $label;
        }

        $address->setDiscountDescriptionArray($description);

        return $this;
    }

    /**
     * Apply Rule
     *
     * @param AbstractItem $item
     * @param Rule $rule
     * @param \Magento\Quote\Model\Quote\Address $address
     * @param mixed $couponCode
     * @return $this
     */
    protected function applyRule($item, $rule, $address, $couponCode)
    {
        $discountData = $this->getDiscountData($item, $rule, $address);
        $this->setDiscountData($discountData, $item);

        $this->maintainAddressCouponCode($address, $rule, $couponCode);
        $this->addDiscountDescription($address, $rule);

        return $this;
    }

    /**
     * Get discount Data
     *
     * @param AbstractItem $item
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return \Magento\SalesRule\Model\Rule\Action\Discount\Data
     */
    protected function getDiscountData($item, $rule, $address)
    {
        $qty = $this->validatorUtility->getItemQty($item, $rule);

        $discountCalculator = $this->calculatorFactory->create($rule->getSimpleAction());
        $qty = $discountCalculator->fixQuantity($qty, $rule);
        $discountData = $discountCalculator->calculate($rule, $item, $qty);
        $this->eventFix($discountData, $item, $rule, $qty);
        $this->validatorUtility->deltaRoundingFix($discountData, $item);
        $this->ssmdSetDiscountBreakdown($discountData, $item, $rule, $address);

        /**
         * We can't use row total here because row total not include tax
         * Discount can be applied on price included tax
         */

        $this->validatorUtility->minFix($discountData, $item, $qty);

        return $discountData;
    }

    /**
     * Set Discount Breakdown
     *
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData
     * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
     * @param \Magento\SalesRule\Model\Rule $rule
     * @param \Magento\Quote\Model\Quote\Address $address
     * @return $this
     */
    private function ssmdSetDiscountBreakdown($discountData, $item, $rule, $address)
    {
        if ($discountData->getAmount() > 0 && $item->getExtensionAttributes()) {
            $data = [
                'amount' => $discountData->getAmount(),
                'base_amount' => $discountData->getBaseAmount(),
                'original_amount' => $discountData->getOriginalAmount(),
                'base_original_amount' => $discountData->getBaseOriginalAmount()
            ];
            $itemDiscount = $this->discountDataInterfaceFactory->create(['data' => $data]);
            $ruleLabel = $rule->getStoreLabel($address->getQuote()->getStore()) ?: __('Discount');
            $data = [
                'discount' => $itemDiscount,
                'rule' => $ruleLabel,
                'rule_id' => $rule->getId(),
            ];
            /** @var \Magento\SalesRule\Model\Data\RuleDiscount $itemDiscount */
            $ruleDiscount = $this->discountInterfaceFactory->create(['data' => $data]);
            $this->discountAggregator[] = $ruleDiscount;

            $appliedDiscounts = $item->getExtensionAttributes()->getDiscounts();

            $keys = array_keys($this->discountAggregator);

            if ($keys)
                $appliedDiscounts[$rule->getId()] = $this->discountAggregator[$keys[count($keys)-1]];
            else
                $appliedDiscounts = [];

            $item->getExtensionAttributes()->setDiscounts($appliedDiscounts);
        }
        return $this;
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

    /**
     * Set Discount data
     *
     * @param \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData
     * @param AbstractItem $item
     * @return $this
     */
    protected function setDiscountData($discountData, $item)
    {
        $discountAmount = $this->getItemDiscounts($item, 'amount');
        $discountBaseAmount = $this->getItemDiscounts($item, 'base_amount');
        $discountOriginalDiscountAmount = $this->getItemDiscounts($item, 'original_amount');
        $discountBaseOriginalDiscountAmount = $this->getItemDiscounts($item, 'base_original_amount');

        $item->setDiscountAmount($discountAmount);
        $item->setBaseDiscountAmount($discountBaseAmount);
        $item->setOriginalDiscountAmount($discountOriginalDiscountAmount);
        $item->setBaseOriginalDiscountAmount($discountBaseOriginalDiscountAmount);

        /*$item->setDiscountAmount(666);//$discountData->getAmount());
        $item->setBaseDiscountAmount(666.5);//$discountData->getBaseAmount());
        $item->setOriginalDiscountAmount(667);//$discountData->getOriginalAmount());
        $item->setBaseOriginalDiscountAmount(668);//($discountData->getBaseOriginalAmount());*/

        return $this;
    }

    protected function setDiscountData1($discountData, $item)
    {
        $item->setDiscountAmount($this->getItemDiscounts($item,'amount'));
        $item->setBaseDiscountAmount($this->getItemDiscounts($item,'base_amount'));
        $item->setOriginalDiscountAmount($this->getItemDiscounts($item,'original_amount'));
        $item->setBaseOriginalDiscountAmount($this->getItemDiscounts($item,'original_amount'));

        return $this;
    }

    /**
     * Set Applied Rule Ids
     *
     * @param AbstractItem $item
     * @param int[] $appliedRuleIds
     * @return $this
     */
    public function setAppliedRuleIds(AbstractItem $item, array $appliedRuleIds)
    {
        $address = $item->getAddress();
        $quote = $item->getQuote();

        //$item->setAppliedRuleIds(join(',', $appliedRuleIds));
        $item->setAppliedRuleIds($this->validatorUtility->mergeIds($item->getAppliedRuleIds(), $appliedRuleIds));
        $address->setAppliedRuleIds($this->validatorUtility->mergeIds($address->getAppliedRuleIds(), $appliedRuleIds));
        $quote->setAppliedRuleIds($this->validatorUtility->mergeIds($quote->getAppliedRuleIds(), $appliedRuleIds));

        return $this;
    }
}
