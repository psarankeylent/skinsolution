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
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\UrlBuilder;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Directory\Model\Country;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class Order
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket
 */
class Order implements ProcessorInterface
{
    const IS_ORDER_ASSIGNED = 'is_order_assigned';
    const ORDER = 'order';
    const URL = 'url';
    const TEXT = 'text';

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param UrlBuilder $urlBuilder
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        UrlBuilder $urlBuilder,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        $data[self::IS_ORDER_ASSIGNED] = false;

        $orderId = $data[TicketInterface::ORDER_ID];
        if ($orderId) {
            $order = $this->getOrder($orderId);
            $data[self::IS_ORDER_ASSIGNED] = true;
            $data[self::ORDER][self::TEXT] = $order->getIncrementId();
            $data[self::ORDER][self::URL] = $this->urlBuilder->getBackendOrderLink($order->getEntityId());
        }

        return $data;
    }

    /**
     * Retrieve order by ID
     *
     * @param string $orderId
     * @return OrderInterface|null
     */
    private function getOrder($orderId)
    {
        try {
            return $this->orderRepository->get($orderId);
        } catch (NoSuchEntityException $exception) {
            return null;
        }
    }

    /**
     * @inheritdoc
     */
    public function prepareMetaData($meta)
    {
        return $meta;
    }
}
