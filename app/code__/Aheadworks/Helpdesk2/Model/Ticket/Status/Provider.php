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
namespace Aheadworks\Helpdesk2\Model\Ticket\Status;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject as DataObjectConverter;
use Aheadworks\Helpdesk2\Api\Data\TicketStatusInterface;
use Aheadworks\Helpdesk2\Api\TicketStatusRepositoryInterface;

/**
 * Class Provider
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Status
 */
class Provider
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var DataObjectConverter
     */
    private $dataObjectConverter;

    /**
     * @var TicketStatusRepositoryInterface
     */
    private $ticketStatusRepository;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DataObjectConverter $dataObjectConverter
     * @param TicketStatusRepositoryInterface $ticketStatusRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataObjectConverter $dataObjectConverter,
        TicketStatusRepositoryInterface $ticketStatusRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dataObjectConverter = $dataObjectConverter;
        $this->ticketStatusRepository = $ticketStatusRepository;
    }

    /**
     * Get status list as options
     *
     * @return array
     * @throws LocalizedException
     */
    public function getListAsOptions()
    {
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $statusList = $this->ticketStatusRepository->getList($searchCriteria)->getItems();
        $id = function (TicketStatusInterface $status) {
            return (string)$status->getId();
        };
        $label = function (TicketStatusInterface $status) {
            return __($status->getLabel());
        };
        return $this->dataObjectConverter->toOptionArray(
            $statusList,
            $id,
            $label
        );
    }
}
