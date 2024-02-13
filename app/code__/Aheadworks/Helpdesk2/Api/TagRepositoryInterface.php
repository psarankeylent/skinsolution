<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Helpdesk2
 * @version    2.0.6
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Helpdesk2\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Tag CRUD interface
 * @api
 */
interface TagRepositoryInterface
{
    /**
     * Save tag
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\TagInterface $tag
     * @return \Aheadworks\Helpdesk2\Api\Data\TagInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\Helpdesk2\Api\Data\TagInterface $tag);

    /**
     * Retrieve tag
     *
     * @param int $tagId
     * @return \Aheadworks\Helpdesk2\Api\Data\TagInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($tagId);

    /**
     * Retrieve tags matching the specified criteria
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Helpdesk2\Api\Data\TagSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * Delete tag
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\TagInterface $tag
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Aheadworks\Helpdesk2\Api\Data\TagInterface $tag);

    /**
     * Delete tag by ID
     *
     * @param int $tagId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($tagId);
}
