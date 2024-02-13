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
namespace Aheadworks\Helpdesk2\Model\Automation\Search;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Api\SortOrder;
use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;
use Aheadworks\Helpdesk2\Api\AutomationRepositoryInterface;

/**
 * Class Builder
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Search
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
     * @var AutomationRepositoryInterface
     */
    private $automationRepository;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     * @param AutomationRepositoryInterface $automationRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder,
        AutomationRepositoryInterface $automationRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
        $this->automationRepository = $automationRepository;
    }

    /**
     * Get automations
     *
     * @return AutomationInterface[]
     * @throws LocalizedException
     */
    public function searchAutomations()
    {
        return $this->automationRepository
            ->getList($this->buildSearchCriteria())
            ->getItems();
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
        $this->searchCriteriaBuilder->addFilter(AutomationInterface::IS_ACTIVE, 1);
        return $this;
    }

    /**
     * Add event filter
     *
     * @param string $event
     * @return $this
     */
    public function addEventFilter($event)
    {
        $this->searchCriteriaBuilder->addFilter(AutomationInterface::EVENT, $event);
        return $this;
    }

    /**
     * Add priority sorting
     *
     * @param string $direction
     * @return $this
     */
    public function addPrioritySorting($direction = SortOrder::SORT_ASC)
    {
        $sortOrder = $this->sortOrderBuilder
            ->setField(AutomationInterface::PRIORITY)
            ->setDirection($direction)
            ->create();

        $this->searchCriteriaBuilder->addSortOrder($sortOrder);
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
