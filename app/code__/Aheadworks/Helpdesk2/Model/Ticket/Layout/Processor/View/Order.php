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
namespace Aheadworks\Helpdesk2\Model\Ticket\Layout\Processor\View;

use Magento\Framework\Stdlib\ArrayManager;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\OrderRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\Renderer\ViewRendererInterface;
use Aheadworks\Helpdesk2\Model\UrlBuilder;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket\Order as TicketOrder;

/**
 * Class Order
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Layout\Processor\View
 */
class Order implements ProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @param ArrayManager $arrayManager
     * @param UrlBuilder $urlBuilder
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        ArrayManager $arrayManager,
        UrlBuilder $urlBuilder,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->arrayManager = $arrayManager;
        $this->urlBuilder = $urlBuilder;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Prepare ticket order data
     *
     * @param array $jsLayout
     * @param ViewRendererInterface $renderer
     * @return array
     */
    public function process($jsLayout, $renderer)
    {
        $formDataProvider = 'components/aw_helpdesk2_config_provider';
        $data[TicketOrder::IS_ORDER_ASSIGNED] = false;
        $orderId = $renderer->getTicket()->getOrderId();
        if ($orderId) {
            $data = $this->getOrderData($orderId);
        }

        $jsLayout = $this->arrayManager->merge(
            $formDataProvider,
            $jsLayout,
            [
                'data' => $data
            ]
        );

        return $jsLayout;
    }

    /**
     * Prepare order data
     *
     * @param string $orderId
     * @return array
     */
    private function getOrderData($orderId)
    {
        try {
            $order = $this->orderRepository->get($orderId);
            $data[TicketOrder::IS_ORDER_ASSIGNED] = true;
            $data[TicketOrder::ORDER][TicketOrder::TEXT] = $order->getIncrementId();
            $data[TicketOrder::ORDER][TicketOrder::URL] = $this->urlBuilder->getFrontendOrderLink(
                $order->getEntityId()
            );
        } catch (NoSuchEntityException $exception) {
            $data = [];
        }

        return $data;
    }
}
