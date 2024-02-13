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
namespace Aheadworks\Helpdesk2\Model\Data\Validator\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Magento\Framework\Validator\AbstractValidator;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class Order
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Validator\Ticket
 */
class Order extends AbstractValidator
{
    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        OrderRepositoryInterface $orderRepository
    ) {
        $this->orderRepository = $orderRepository;
    }

    /**
     * Check if agent is correct
     *
     * @param TicketInterface $ticket
     * @return bool
     * @throws \Exception
     */
    public function isValid($ticket)
    {
        $this->_clearMessages();

        $orderId = $ticket->getOrderId();
        if ($orderId) {
            try {
                $order = $this->orderRepository->get($orderId);
                if ($order->getCustomerId() != $ticket->getCustomerId()) {
                    $this->_addMessages([__('The order does not belong to the customer')]);
                }
            } catch (\Magento\Framework\Exception\NoSuchEntityException $exception) {
                $this->_addMessages([__('The order does not exists')]);
            }
        }

        return empty($this->getMessages());
    }
}
