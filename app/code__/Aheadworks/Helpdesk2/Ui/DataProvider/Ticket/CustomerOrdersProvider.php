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
namespace Aheadworks\Helpdesk2\Ui\DataProvider\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Config;
use Aheadworks\Helpdesk2\Model\Source\Config\Order\Status as OrderStatusSource;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\AuthorizationInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class CustomerOrdersProvider
 *
 * @package Aheadworks\Helpdesk2\Ui\DataProvider\Ticket
 */
class CustomerOrdersProvider extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param TicketRepositoryInterface $ticketRepository
     * @param RequestInterface $request
     * @param Config $config
     * @param AuthorizationInterface $authorization
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        TicketRepositoryInterface $ticketRepository,
        RequestInterface $request,
        Config $config,
        AuthorizationInterface $authorization,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->ticketRepository = $ticketRepository;
        $this->request = $request;
        $this->config = $config;
        $this->authorization = $authorization;
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getData()
    {
        if (!$this->authorization->isAllowed('Magento_Sales::sales_order')) {
            return $this->toArray([], 0);
        }

        $ticketId = $this->request->getParam($this->getRequestFieldName());
        $ticket = $this->getTicket($ticketId);

        $this->collection
            ->addFieldToFilter(
                [
                    OrderInterface::CUSTOMER_ID,
                    OrderInterface::CUSTOMER_EMAIL
                ],
                [
                    ['eq' => $ticket->getCustomerId()],
                    ['eq' => $ticket->getCustomerEmail()]
                ]
            )
            ->setOrder(OrderInterface::CREATED_AT, Collection::SORT_ORDER_DESC);

        $displayedStatuses = $this->config->getBackendTicketPageDisplayedOrderStatuses();
        if (!in_array(OrderStatusSource::ALL, $displayedStatuses)) {
            $this->collection
                ->addFieldToFilter(OrderInterface::STATUS, ['in' => $displayedStatuses]);
        }

        $this->limitEntities();

        return $this->toArray($this->collection->getItems(), $this->collection->getSize());
    }

    /**
     * Limit collection
     */
    private function limitEntities()
    {
        $displayedCount = $this->config->getBackendTicketPageDisplayedOrdersCount();
        if ($displayedCount) {
            $frame = $this->getRequestedFrame();
            $this->setLimit(0, $displayedCount * $frame);
        }
    }

    /**
     * Retrieve requested frame
     *
     * @return int
     */
    private function getRequestedFrame()
    {
        return (int)$this->request->getParam('frame', 1);
    }

    /**
     * Retrieve ticket by ID
     *
     * @param int $ticketId
     * @return TicketInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getTicket($ticketId)
    {
        return $this->ticketRepository->getById($ticketId);
    }

    /**
     * Convert collection items to correct array
     *
     * @param array $collection
     * @return array
     */
    private function toArray($items, $total)
    {
        $arrItems = [];
        $arrItems['items'] = [];
        foreach ($items as $item) {
            $arrItems['items'][] = $item->toArray();
        }

        $displayed = $this->config->getBackendTicketPageDisplayedOrdersCount();
        $loaded = count($arrItems['items']);

        $arrItems['totalRecords'] = $total;
        $arrItems['isShowButton'] = $total && $displayed && $total > $loaded;

        return $arrItems;
    }
}
