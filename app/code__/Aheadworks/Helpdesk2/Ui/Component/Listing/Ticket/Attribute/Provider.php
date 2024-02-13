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
namespace Aheadworks\Helpdesk2\Ui\Component\Listing\Ticket\Attribute;

use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SearchCriteria;
use Aheadworks\Helpdesk2\Api\TicketAttributeRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketAttributeInterface;

/**
 * Class Provider
 *
 * @package Aheadworks\Helpdesk2\Ui\Component\Listing\Ticket\Attribute
 */
class Provider
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var TicketAttributeRepositoryInterface
     */
    private $ticketAttributeRepository;

    /**
     * @var TicketAttributeInterface[]|null
     */
    private $attributes;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TicketAttributeRepositoryInterface $ticketAttributeRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TicketAttributeRepositoryInterface $ticketAttributeRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->ticketAttributeRepository = $ticketAttributeRepository;
    }

    /**
     * Get attribute list for grid
     *
     * @return TicketAttributeInterface[]|null
     */
    public function getList()
    {
        if ($this->attributes === null) {
            $this->attributes = $this->ticketAttributeRepository
                ->getList($this->buildSearchCriteria())
                ->getItems();
        }

        return $this->attributes;
    }

    /**
     * Build search criteria
     *
     * @return SearchCriteria
     */
    private function buildSearchCriteria()
    {
        return $this->searchCriteriaBuilder->addFilter('additional_table.is_visible_in_grid', 1)->create();
    }
}
