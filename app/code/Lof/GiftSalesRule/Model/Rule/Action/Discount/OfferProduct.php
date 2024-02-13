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
use Lof\GiftSalesRule\Model\GiftRule;

/**
 * Class OfferProduct
 *
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 */
class OfferProduct extends AbstractDiscount
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
     * @var GiftRuleRepositoryInterface
     */
    protected $giftRuleRepository;

    /**
     * OfferProduct constructor.
     *
     * @param Validator                   $validator           Validator
     * @param DataFactory                 $discountDataFactory Discount data factory
     * @param PriceCurrencyInterface      $priceCurrency       Price currency
     * @param checkoutSession             $checkoutSession     Checkout session
     * @param GiftRuleCacheHelper         $giftRuleCacheHelper Gift rule cache helper
     * @param GiftRuleRepositoryInterface $giftRuleRepository  Gift rule repository
     */
    public function __construct(
        Validator $validator,
        DataFactory $discountDataFactory,
        PriceCurrencyInterface $priceCurrency,
        checkoutSession $checkoutSession,
        GiftRuleCacheHelper $giftRuleCacheHelper,
        GiftRuleRepositoryInterface $giftRuleRepository
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->giftRuleCacheHelper = $giftRuleCacheHelper;
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
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
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

            /** @var GiftRule $giftRule */
            $giftRule = $this->giftRuleRepository->getById($rule->getRuleId());

            // Save active gift rule in session.
            $giftRuleSessionData = $this->checkoutSession->getGiftRules();
            $giftRuleSessionData[$rule->getRuleId()] = $rule->getRuleId();
            $this->checkoutSession->setGiftRules($giftRuleSessionData);

            $this->giftRuleCacheHelper->saveCachedGiftRule(
                $rule->getRuleId(),
                $rule,
                $giftRule
            );
        }

        return $discountData;
    }
}
