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

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Source\Ticket\CustomerOrders;
use Aheadworks\Helpdesk2\Model\Source\Ticket\CustomerOrdersFactory;

/**
 * Class DefaultDataOnNewTicket
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket
 */
class DefaultDataOnNewTicket
{
    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var CustomerOrdersFactory
     */
    private $customerOrdersFactory;

    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param OrderRepositoryInterface $orderRepository
     * @param RequestInterface $request
     * @param CustomerOrdersFactory $customerOrdersFactory
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        OrderRepositoryInterface $orderRepository,
        RequestInterface $request,
        CustomerOrdersFactory $customerOrdersFactory,
        ArrayManager $arrayManager
    ) {
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
        $this->request = $request;
        $this->customerOrdersFactory = $customerOrdersFactory;
        $this->arrayManager = $arrayManager;
    }

    /**
     * Get default data on new ticket page
     *
     * @return array
     */
    public function getData()
    {
        $data = [];
        $customerId = $this->request->getParam('customer_id');
        if ($customerId) {
            $data = $this->getCustomerData($customerId);
            return $data;
        }
        $orderId = $this->request->getParam('order_id');
        if ($orderId) {
            $data = $this->getOrderData($orderId);
        }

        return $data;
    }

    /**
     * Get default meta data on new ticket page
     *
     * @return array
     */
    public function getMeta()
    {
        $metaData = [];
        $data = $this->getData();
        $customerId = $data[TicketInterface::CUSTOMER_ID] ?? null;
        if ($customerId) {
            /** @var CustomerOrders $optionSource */
            $optionSource = $this->customerOrdersFactory->create(['customerId' => $customerId]);
            $metaData = $this->arrayManager->set(
                'general/children/order_id/arguments/data/config',
                $metaData,
                [
                    'options' => $optionSource->toOptionArray()
                ]
            );
        }

        return $metaData;
    }

    /**
     * Get customer data
     *
     * @param int $customerId
     * @return array
     */
    private function getCustomerData($customerId)
    {
        try {
            $customer = $this->customerRepository->getById($customerId);
            $data = [
                TicketInterface::CUSTOMER_ID => $customer->getId(),
                TicketInterface::CUSTOMER_EMAIL => $customer->getEmail(),
                TicketInterface::CUSTOMER_NAME => $customer->getFirstname() . ' ' . $customer->getLastname()
            ];
        } catch (\Exception $exception) {
            $data = [];
        }

        return $data;
    }

    /**
     * Get order data
     *
     * @param int $orderId
     * @return array
     */
    private function getOrderData($orderId)
    {
        try {
            $order = $this->orderRepository->get($orderId);
            $data = $this->getCustomerData($order->getCustomerId());
            $data[TicketInterface::ORDER_ID] = $orderId;
        } catch (\Exception $exception) {
            $data = [];
        }

        return $data;
    }
}
