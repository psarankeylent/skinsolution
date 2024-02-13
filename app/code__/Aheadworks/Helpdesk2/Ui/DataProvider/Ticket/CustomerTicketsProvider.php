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

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Config;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid\Backend\Collection as TicketCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid\Backend\CollectionFactory as TicketCollectionFactory;

/**
 * Class CustomerTicketsProvider
 *
 * @package Aheadworks\Helpdesk2\Ui\DataProvider\Ticket
 */
class CustomerTicketsProvider extends AbstractDataProvider
{
    /**
     * @var TicketCollection
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
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param TicketCollectionFactory $collectionFactory
     * @param TicketRepositoryInterface $ticketRepository
     * @param RequestInterface $request
     * @param Config $config
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        TicketCollectionFactory $collectionFactory,
        TicketRepositoryInterface $ticketRepository,
        RequestInterface $request,
        Config $config,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->ticketRepository = $ticketRepository;
        $this->request = $request;
        $this->config = $config;
    }

    /**
     * @inheritDoc
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getData()
    {
        $ticketId = $this->request->getParam($this->getRequestFieldName());
        $ticket = $this->getTicket($ticketId);

        $this->collection
            ->addFieldToFilter(TicketInterface::ENTITY_ID, ["neq" => $ticket->getEntityId()])
            ->addFieldToFilter(
                [
                    TicketInterface::CUSTOMER_ID,
                    TicketInterface::CUSTOMER_EMAIL
                ],
                [
                    ['eq' => $ticket->getCustomerId()],
                    ['eq' => $ticket->getCustomerEmail()]
                ]
            )
            ->setOrder(TicketInterface::ENTITY_ID, \Magento\Framework\Data\Collection::SORT_ORDER_DESC);

        $this->limitTickets();

        return $this->toArray($this->collection);
    }

    /**
     * Limit collection
     */
    private function limitTickets()
    {
        $displayedCount = $this->config->getBackendTicketPageDisplayedTicketsCount();
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
     * Convert collection to correct array
     *
     * @param TicketCollection $collection
     * @return array
     */
    private function toArray($collection)
    {
        $arrItems = [];
        $arrItems['items'] = [];
        foreach ($collection as $item) {
            $arrItems['items'][] = $item->toArray();
        }

        $total = $collection->getSize();
        $displayed = $this->config->getBackendTicketPageDisplayedTicketsCount();
        $loaded = count($arrItems['items']);

        $arrItems['totalRecords'] = $total;
        $arrItems['isShowButton'] = $total && $displayed && $total > $loaded;

        return $arrItems;
    }
}
