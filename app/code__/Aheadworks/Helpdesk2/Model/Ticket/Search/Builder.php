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
namespace Aheadworks\Helpdesk2\Model\Ticket\Search;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;

/**
 * Class Builder
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Search
 */
class Builder
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TicketRepositoryInterface $ticketRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TicketRepositoryInterface $ticketRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * Get tickets
     *
     * @return TicketInterface[]
     * @throws LocalizedException
     */
    public function searchTickets()
    {
        $ruleList = $this->ticketRepository
            ->getList($this->buildSearchCriteria())
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
     * Add department filter
     *
     * @param int $departmentId
     * @return $this
     */
    public function addDepartmentFilter($departmentId)
    {
        $this->searchCriteriaBuilder->addFilter(TicketInterface::DEPARTMENT_ID, $departmentId);
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
