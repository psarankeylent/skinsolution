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
 * @package    Bup
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Bup\Api;

/**
 * Interface UserProfileRepositoryInterface
 * @api
 */
interface UserProfileRepositoryInterface
{
    /**
     * Save user profile
     *
     * @param \Aheadworks\Bup\Api\Data\UserProfileInterface $userProfile
     * @return \Aheadworks\Bup\Api\Data\UserProfileInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Aheadworks\Bup\Api\Data\UserProfileInterface $userProfile);

    /**
     * Retrieve user profile by ID
     *
     * @param int $id
     * @return \Aheadworks\Bup\Api\Data\UserProfileInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($id);

    /**
     * Retrieve user profile list matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Bup\Api\Data\UserProfileSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}