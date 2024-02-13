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

/**
 * Department CRUD interface
 * @api
 */
interface DepartmentRepositoryInterface
{
    /**
     * Save department
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\DepartmentInterface $department
     * @return \Aheadworks\Helpdesk2\Api\Data\DepartmentInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Aheadworks\Helpdesk2\Api\Data\DepartmentInterface $department);

    /**
     * Retrieve department by ID
     *
     * @param int $departmentId
     * @param int|null $storeId
     * @return \Aheadworks\Helpdesk2\Api\Data\DepartmentInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($departmentId, $storeId = null);

    /**
     * Retrieve department list matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param int|null $storeId
     * @return \Aheadworks\Helpdesk2\Api\Data\DepartmentSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria, $storeId = null);

    /**
     * Delete department
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\DepartmentInterface $department
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Aheadworks\Helpdesk2\Api\Data\DepartmentInterface $department);

    /**
     * Delete department by ID
     *
     * @param int $departmentId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($departmentId);
}
