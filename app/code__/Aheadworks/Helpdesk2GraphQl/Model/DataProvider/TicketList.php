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
 * @package    Helpdesk2GraphQl
 * @version    1.0.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Helpdesk2GraphQl\Model\DataProvider;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2GraphQl\Model\ObjectConverter;
use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * Class Tickets
 *
 * @package Aheadworks\Helpdesk2GraphQl\Model\DataProvider
 */
class TicketList extends AbstractDataProvider
{
    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @param TicketRepositoryInterface $ticketRepository
     * @param ObjectConverter $objectConverter
     */
    public function __construct(
        TicketRepositoryInterface $ticketRepository,
        ObjectConverter $objectConverter
    ) {
        parent::__construct($objectConverter);
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * @inheritDoc
     */
    public function getListData(SearchCriteriaInterface $searchCriteria, $storeId = null)
    {
        $result = $this->ticketRepository->getList($searchCriteria);
        $this->convertResultItemsToDataArray($result, TicketInterface::class);

        return $result;
    }
}
