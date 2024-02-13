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
namespace Aheadworks\Helpdesk2\Plugin\Sales\Model;

use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\ResourceModel\Order as OrderResourceModel;
use Aheadworks\Helpdesk2\Model\Service\TicketService;
use Aheadworks\Helpdesk2\Model\Source\Automation\Event;

/**
 * Class ResourceModelPlugin
 *
 * @package Aheadworks\Helpdesk2\Plugin\Sales\Model
 */
class ResourceModelPlugin
{
    /**
     * @var EventManagerInterface
     */
    private $eventManager;

    /**
     * @param EventManagerInterface $eventManager
     */
    public function __construct(
        EventManagerInterface $eventManager
    ) {
        $this->eventManager = $eventManager;
    }

    /**
     * Check for order automation
     *
     * @param OrderResourceModel $subject
     * @param OrderResourceModel $result
     * @param Order $order
     * @return OrderResourceModel
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterSave(OrderResourceModel $subject, $result, $order)
    {
        if ($order->getOrigData(OrderInterface::STATUS) != $order->getStatus()) {
            $this->eventManager->dispatch(
                Event::EVENT_NAME_PREFIX . Event::ORDER_STATUS_CHANGED,
                [
                    'order' => $order
                ]
            );
        }

        return $result;
    }
}
