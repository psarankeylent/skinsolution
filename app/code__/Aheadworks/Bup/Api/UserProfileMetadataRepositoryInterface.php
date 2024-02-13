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
 * Interface UserProfileMetadataRepositoryInterface
 * @api
 */
interface UserProfileMetadataRepositoryInterface
{
    /**
     * Retrieve user profile metadata by ID
     *
     * @param int $id
     * @param string $area
     * @return \Aheadworks\Bup\Api\Data\UserProfileMetadataInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($id, $area = \Aheadworks\Bup\Model\Source\UserProfile\Area::STOREFRONT);

    /**
     * Retrieve user profile metadata list matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param string $area
     * @return \Aheadworks\Bup\Api\Data\UserProfileSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria,
        $area = \Aheadworks\Bup\Model\Source\UserProfile\Area::STOREFRONT
    );
}