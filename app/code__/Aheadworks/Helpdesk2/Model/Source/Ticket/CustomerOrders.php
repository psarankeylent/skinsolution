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
namespace Aheadworks\Helpdesk2\Model\Source\Ticket;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Convert\DataObject;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class CustomerOrders
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Ticket
 */
class CustomerOrders implements OptionSourceInterface
{
    const UNASSIGNED = '';

    /**
     * @var int
     */
    private $customerId;

    /**
     * @var array
     */
    private $options;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchBuilder;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var DataObject
     */
    private $objectConverter;

    /**
     * @param int $customerId
     * @param OrderRepositoryInterface $orderRepository
     * @param SearchCriteriaBuilder $searchBuilder
     * @param FilterBuilder $filterBuilder
     * @param DataObject $objectConverter
     */
    public function __construct(
        int $customerId,
        OrderRepositoryInterface $orderRepository,
        SearchCriteriaBuilder $searchBuilder,
        FilterBuilder $filterBuilder,
        DataObject $objectConverter
    ) {
        $this->customerId = $customerId;
        $this->orderRepository = $orderRepository;
        $this->searchBuilder = $searchBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->objectConverter = $objectConverter;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        if (null === $this->options) {
            $filter = $this->filterBuilder
                ->setField(OrderInterface::CUSTOMER_ID)
                ->setConditionType('eq')
                ->setValue($this->customerId)
                ->create();
            $criteria = $this->searchBuilder
                ->addFilter($filter)
                ->create();
            $searchResults = $this->orderRepository->getList($criteria);

            $this->options = $this->objectConverter->toOptionArray(
                $searchResults->getItems(),
                OrderInterface::ENTITY_ID,
                OrderInterface::INCREMENT_ID
            );
            array_unshift(
                $this->options,
                [
                    'value' => self::UNASSIGNED,
                    'label' =>__('Unassigned')
                ]
            );
        }

        return $this->options;
    }
}
