<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Lof
 * @package   Lof\GiftSalesRule
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 * @license   http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\GiftSalesRule\Model\Rule\Action\Discount;

use Magento\Checkout\Model\Session as checkoutSession;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Rule\Action\Discount\AbstractDiscount;
use Magento\SalesRule\Model\Rule\Action\Discount\Data as DiscountData;
use Magento\SalesRule\Model\Rule\Action\Discount\DataFactory;
use Magento\SalesRule\Model\Validator;
use Lof\GiftSalesRule\Api\GiftRuleRepositoryInterface;
use Lof\GiftSalesRule\Helper\Cache as GiftRuleCacheHelper;
use Lof\GiftSalesRule\Helper\GiftRule as GiftRuleHelper;
use Lof\GiftSalesRule\Model\GiftRule;

/**
 * Class Offer Product Per Price Range
 *
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 */
class OfferProductPerPriceRange extends AbstractDiscount
{
    /**
     * @var checkoutSession
     */
    protected $checkoutSession;

    /**
     * @var GiftRuleCacheHelper
     */
    protected $giftRuleCacheHelper;

    /**
     * @var GiftRuleHelper
     */
    protected $giftRuleHelper;

    /**
     * @var GiftRuleRepositoryInterface
     */
    protected $giftRuleRepository;

    /**
     * OfferProductPerPriceRange constructor.
     *
     * @param Validator                   $validator           Validator
     * @param DataFactory                 $discountDataFactory Discount data factory
     * @param PriceCurrencyInterface      $priceCurrency       Price currency
     * @param checkoutSession             $checkoutSession     Checkout session
     * @param GiftRuleCacheHelper         $giftRuleCacheHelper Gift rule cache helper
     * @param GiftRuleHelper              $giftRuleHelper      Gift rule helper
     * @param GiftRuleRepositoryInterface $giftRuleRepository  Gift rule repository
     */
    public function __construct(
        Validator $validator,
        DataFactory $discountDataFactory,
        PriceCurrencyInterface $priceCurrency,
        checkoutSession $checkoutSession,
        GiftRuleCacheHelper $giftRuleCacheHelper,
        GiftRuleHelper $giftRuleHelper,
        GiftRuleRepositoryInterface $giftRuleRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->giftRuleCacheHelper = $giftRuleCacheHelper;
        $this->giftRuleHelper = $giftRuleHelper;
        $this->giftRuleRepository = $giftRuleRepository;

        parent::__construct(
            $validator,
            $discountDataFactory,
            $priceCurrency
        );
    }

    /**
     * @param Rule         $rule Rule
     * @param AbstractItem $item Item
     * @param float        $qty  Qty
     *
     * @return DiscountData
     * @SuppressWarnings(PHPMD.ElseExpression)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function calculate($rule, $item, $qty)
    {
        /** @var \Magento\SalesRule\Model\Rule\Action\Discount\Data $discountData */
        $discountData = $this->discountFactory->create();

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $item->getQuote();

        $calculateId = 'calculate_gift_rule_'.$rule->getRuleId();
        if (!$quote->getData($calculateId)) {
            // Set only for performance (not save in DB).
            $quote->setData($calculateId, true);

            /**
             * Rules load by collection => extension attributes not present in rule entity
             */
            /** @var GiftRule $giftRule */
            $giftRule = $this->giftRuleRepository->getById($rule->getRuleId());

            if ($quote->getGrandTotal() >= $giftRule->getPriceRange()) {
                $range = $this->giftRuleHelper->getRange($quote, $giftRule);

                // Save active gift rule in session.
                $giftRuleSessionData = $this->checkoutSession->getGiftRules();
                $giftRuleSessionData[$rule->getRuleId()] = $rule->getRuleId() . '_' . $range;
                $this->checkoutSession->setGiftRules($giftRuleSessionData);

                $this->giftRuleCacheHelper->saveCachedGiftRule(
                    $rule->getRuleId() . '_' . $range,
                    $rule,
                    $giftRule
                );
            } else {
                // Save active gift rule in session.
                $giftRuleSessionData = $this->checkoutSession->getGiftRules();
                if (isset($giftRuleSessionData[$rule->getRuleId()])) {
                    unset($giftRuleSessionData[$rule->getRuleId()]);
                }
                $this->checkoutSession->setGiftRules($giftRuleSessionData);
            }
        }

        return $discountData;
    }
}
