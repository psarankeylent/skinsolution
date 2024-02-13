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
namespace Lof\GiftSalesRule\Plugin\Model\Rule\Condition;

use Magento\Framework\Model\AbstractModel;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\SalesRule\Model\Rule\Condition\Product as Subject;
use Lof\GiftSalesRule\Helper\GiftRule as GiftRuleHelper;

/**
 * Class ProductPlugin
 *
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Lof
 */
class ProductPlugin
{
    /**
     * @var GiftRuleHelper
     */
    protected $giftRuleHelper;

    /**
     * ProductPlugin constructor.
     *
     * @param GiftRuleHelper $giftRuleHelper Gift rule helper
     */
    public function __construct(
        GiftRuleHelper $giftRuleHelper
    ) {
        $this->giftRuleHelper = $giftRuleHelper;
    }

    /**
     * If the item is a gift item, do not validate the condition.
     *
     * @param Subject       $subject subject
     * @param bool          $result  result
     * @param AbstractModel $model   model
     *
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterValidate(
        Subject $subject,
        bool $result,
        AbstractModel $model
    ) {
        $isGiftItem = false;
        if ($model instanceof AbstractItem) {
            $isGiftItem = $this->giftRuleHelper->isGiftItem($model);
        }

        return $result && !$isGiftItem;
    }
}
