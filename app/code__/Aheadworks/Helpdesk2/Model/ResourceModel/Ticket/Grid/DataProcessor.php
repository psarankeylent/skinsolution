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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid;

use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\Collection as MessageCollection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message\CollectionFactory as MessageCollectionFactory;
use Aheadworks\Helpdesk2\Model\Ticket\GridInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type as MessageType;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class DataProcessor
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid
 */
class DataProcessor
{
    /**
     * @var MessageCollectionFactory
     */
    private $messageCollectionFactory;

    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param MessageCollectionFactory $messageCollectionFactory
     * @param TicketRepositoryInterface $ticketRepository
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        MessageCollectionFactory $messageCollectionFactory,
        TicketRepositoryInterface $ticketRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->messageCollectionFactory = $messageCollectionFactory;
        $this->ticketRepository = $ticketRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Prepare aggregated data by entity
     *
     * @param TicketInterface $entity
     * @return array
     */
    public function prepareAggregatedDataByEntity($entity)
    {
        /** @var MessageCollection $messageCollection */
        $messageCollection = $this->messageCollectionFactory->create();
        $messageCollection->addTicketFilter($entity->getEntityId());
        $messageCollection->addDiscussionTypeFilter();
        $messageCollection->sortByCreatedAt(SortOrder::SORT_DESC);

        /** @var MessageInterface $lastMessage */
        $lastMessage = $messageCollection->getFirstItem();
        /** @var MessageInterface $firstMessage */
        $firstMessage = $messageCollection->getLastItem();

        $orderId = $entity->getOrderId();
        $orderIncrementId = null;
        if ($orderId) {
            $orderIncrementId = $this->getOrderIncrementId($orderId);
        }

        return [
            GridInterface::ENTITY_ID => $entity->getEntityId(),
            GridInterface::UID => $entity->getUid(),
            GridInterface::RATING => $entity->getRating(),
            GridInterface::LAST_MESSAGE_DATE => $lastMessage->getCreatedAt(),
            GridInterface::LAST_MESSAGE_BY => $lastMessage->getAuthorName(),
            GridInterface::LAST_MESSAGE_TYPE => $lastMessage->getType(),
            GridInterface::DEPARTMENT_ID => $entity->getDepartmentId(),
            GridInterface::AGENT_ID => $entity->getAgentId(),
            GridInterface::SUBJECT => $entity->getSubject(),
            GridInterface::FIRST_MESSAGE_CONTENT => $firstMessage->getContent(),
            GridInterface::LAST_MESSAGE_CONTENT => $lastMessage->getContent(),
            GridInterface::CUSTOMER_NAME => $entity->getCustomerName(),
            GridInterface::CUSTOMER_EMAIL => $entity->getCustomerEmail(),
            GridInterface::CUSTOMER_ID => $entity->getCustomerId(),
            GridInterface::PRIORITY_ID => $entity->getPriorityId(),
            GridInterface::ORDER_ID => $orderId,
            GridInterface::ORDER_INCREMENT_ID => $orderIncrementId,
            GridInterface::CUSTOMER_MESSAGE_COUNT =>
                $this->getCustomerMessageCollection($entity->getEntityId())->getSize(),
            GridInterface::AGENT_MESSAGE_COUNT =>
                $this->getAgentMessageCollection($entity->getEntityId())->getSize(),
            GridInterface::MESSAGE_COUNT => $messageCollection->getSize(),
            GridInterface::STATUS_ID => $entity->getStatusId()
        ];
    }

    /**
     * Prepare aggregated data by entity ID
     *
     * @param int $entityId
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function prepareAggregatedDataByEntityId($entityId)
    {
        $entity = $this->ticketRepository->getById($entityId);
        return $this->prepareAggregatedDataByEntity($entity);
    }

    /**
     * Get customer message collection
     *
     * @param int $ticketId
     * @return MessageCollection
     */
    private function getCustomerMessageCollection($ticketId)
    {
        /** @var MessageCollection $customerMessageCollection */
        $customerMessageCollection = $this->messageCollectionFactory->create();
        $customerMessageCollection->addTicketFilter($ticketId);
        $customerMessageCollection->addMessageTypeFilter(MessageType::CUSTOMER);

        return $customerMessageCollection;
    }

    /**
     * Get agent message collection
     *
     * @param int $ticketId
     * @return MessageCollection
     */
    private function getAgentMessageCollection($ticketId)
    {
        /** @var MessageCollection $agentMessageCollection */
        $agentMessageCollection = $this->messageCollectionFactory->create();
        $agentMessageCollection->addTicketFilter($ticketId);
        $agentMessageCollection->addMessageTypeFilter(MessageType::ADMIN);

        return $agentMessageCollection;
    }

    /**
     * Get order increment id
     *
     * @param int $orderId
     * @return string|null
     */
    private function getOrderIncrementId($orderId)
    {
        return $this->orderRepository->get($orderId)->getIncrementId();
    }
}
