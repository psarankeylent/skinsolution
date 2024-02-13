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
namespace Aheadworks\Helpdesk2\Model\Department;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Model\Ticket\Search\Builder as SearchBuilder;

/**
 * Class TicketChecker
 *
 * @package Aheadworks\Helpdesk2\Model\Department
 */
class TicketChecker
{
    /**
     * @var SearchBuilder
     */
    private $searchBuilder;

    /**
     * @param SearchBuilder $searchBuilder
     */
    public function __construct(
        SearchBuilder $searchBuilder
    ) {
        $this->searchBuilder = $searchBuilder;
    }

    /**
     * Check if department has tickets assigned to it
     *
     * @param int $departmentId
     * @return bool
     * @throws LocalizedException
     */
    public function hasTicketsAssigned($departmentId)
    {
        $searchCriteriaBuilder = $this->searchBuilder->getSearchCriteriaBuilder();
        $this->searchBuilder->addDepartmentFilter($departmentId);
        $searchCriteriaBuilder
            ->setCurrentPage(1)
            ->setPageSize(1);
        $tickets = $this->searchBuilder->searchTickets();

        return count($tickets) > 0;
    }
}
