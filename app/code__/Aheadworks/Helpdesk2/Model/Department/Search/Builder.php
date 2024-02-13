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
namespace Aheadworks\Helpdesk2\Model\Department\Search;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\SortOrder;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;

/**
 * Class Builder
 *
 * @package Aheadworks\Helpdesk2\Model\Department\Search
 */
class Builder
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var DepartmentRepositoryInterface
     */
    private $departmentRepository;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param DepartmentRepositoryInterface $departmentRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        DepartmentRepositoryInterface $departmentRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->departmentRepository = $departmentRepository;
    }

    /**
     * Get departments
     *
     * @param int|null $storeId
     * @return DepartmentInterface[]
     * @throws LocalizedException
     */
    public function searchDepartments($storeId = null)
    {
        $ruleList = $this->departmentRepository
            ->getList($this->buildSearchCriteria(), $storeId)
            ->getItems();

        return $ruleList;
    }

    /**
     * Retrieves search criteria builder
     *
     * @return SearchCriteriaBuilder
     */
    public function getSearchCriteriaBuilder()
    {
        return $this->searchCriteriaBuilder;
    }

    /**
     * Add is active filter
     *
     * @return $this
     */
    public function addIsActiveFilter()
    {
        $this->searchCriteriaBuilder->addFilter(DepartmentInterface::IS_ACTIVE, 1);
        return $this;
    }

    /**
     * Add sorting by sort order
     *
     * @param string $direction
     * @return $this
     */
    public function addSortingBySortOrder($direction = SortOrder::SORT_ASC)
    {
        $sortOrder = $this->sortOrderBuilder
            ->setField(DepartmentInterface::SORT_ORDER)
            ->setDirection($direction)
            ->create();

        $this->searchCriteriaBuilder->addSortOrder($sortOrder);
        return $this;
    }

    /**
     * Add is active filter
     *
     * @param int $storeId
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        $this->searchCriteriaBuilder->addFilter(DepartmentInterface::STORE_IDS, $storeId);
        return $this;
    }

    /**
     * Build search criteria
     *
     * @return SearchCriteria
     */
    private function buildSearchCriteria()
    {
        return $this->searchCriteriaBuilder->create();
    }
}
