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
namespace Lof\GiftSalesRule\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Lof\GiftSalesRule\Api\Data\GiftRuleInterface;
use Lof\GiftSalesRule\Api\Data\GiftRuleSearchResultsInterface;

/**
 * GiftRule repository interface.
 *
 * @api
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 */
interface GiftRuleRepositoryInterface
{
    /**
     * Get a giftrule by ID.
     *
     * @param int $entityId Entity id
     * @return \Lof\GiftSalesRule\Api\Data\GiftRuleInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($entityId);

    /**
     * Get the giftrules matching the specified criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria Search criteria
     * @return \Lof\GiftSalesRule\Api\Data\GiftRuleSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria = null);

    /**
     * Save the GiftRule.
     *
     * @param GiftRuleInterface $giftRule Gift rule
     * @return \Lof\GiftSalesRule\Api\Data\GiftRuleInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(GiftRuleInterface $giftRule);

    /**
     * Delete a giftrule by ID.
     *
     * @param int $entityId Entity id
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($entityId);
}
