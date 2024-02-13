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
 * @copyright 2020 Lof
 * @license   http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\GiftSalesRule\Plugin\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Address;
use Magento\SalesRule\Model\Rule;
use Magento\SalesRule\Model\Utility as Subject;
use Lof\GiftSalesRule\Helper\GiftRule as GiftRuleHelper;

/**
 * Plugin Utility
 *
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Lof
 */
class UtilityPlugin
{
    /**
     * @var GiftRuleHelper
     */
    protected $giftRuleHelper;

    /**
     * UtilityPlugin constructor.
     *
     * @param GiftRuleHelper $giftRuleHelper Gift rule helper
     */
    public function __construct(
        GiftRuleHelper $giftRuleHelper
    ) {
        $this->giftRuleHelper = $giftRuleHelper;
    }

    /**
     * After can process rule: if it's a gift rule, add a custom check.
     *
     * @param Subject $subject Subject
     * @param bool    $result  Result
     * @param Rule    $rule    Model
     * @param Address $address address
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCanProcessRule(
        Subject $subject,
        bool $result,
        Rule $rule,
        Address $address
    ): bool {
        try {
            $isValidGiftRule = !$this->giftRuleHelper->isGiftRule($rule)
                || $this->giftRuleHelper->isValidGiftRule($rule, $address->getQuote());

            return $result && $isValidGiftRule;
        } catch (NoSuchEntityException $noSuchEntityException) {
            return $result;
        }
    }
}
