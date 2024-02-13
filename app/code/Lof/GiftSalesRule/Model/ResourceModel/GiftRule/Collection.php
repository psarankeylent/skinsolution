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
namespace Lof\GiftSalesRule\Model\ResourceModel\GiftRule;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Lof\GiftSalesRule\Api\Data\GiftRuleInterface;

/**
 * GiftRule collection.
 *
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 */
class Collection extends AbstractCollection
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return $this->_toOptionArray(GiftRuleInterface::RULE_ID, GiftRuleInterface::RULE_ID);
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init(
            \Lof\GiftSalesRule\Model\GiftRule::class,
            \Lof\GiftSalesRule\Model\ResourceModel\GiftRule::class
        );
    }
}
