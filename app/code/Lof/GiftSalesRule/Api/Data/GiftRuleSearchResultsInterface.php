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
namespace Lof\GiftSalesRule\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * GiftRule search results data interface.
 *
 * @api
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 */
interface GiftRuleSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get giftrule items.
     *
     * @return \Lof\GiftSalesRule\Api\Data\GiftRuleInterface
     */
    public function getItems();

    /**
     * Set giftrule items.
     *
     * @param GiftRuleInterface $items Gift rule interface
     * @return $this
     */
    public function setItems(array $items);
}
