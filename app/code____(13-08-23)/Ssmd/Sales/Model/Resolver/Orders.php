<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ssmd\Sales\Model\Resolver;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\GraphQl\Model\Query\ContextInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactoryInterface;
use Prescriptions\PrescriptionsCollection\Model\PrescriptionsFactory;
use ParadoxLabs\Subscriptions\Model\ResourceModel\Log\CollectionFactory as LogCollectionFactory;

/**
 * Ssmd Sales Orders data resolver
 */
class Orders implements ResolverInterface
{
    /**
     * @var CollectionFactoryInterface
     */
    private $collectionFactory;

    protected $prescriptionsFactory;

    protected $logCollectionFactory;

    /**
     * @param CollectionFactoryInterface $collectionFactory
     */
    public function __construct(
        CollectionFactoryInterface $collectionFactory,
        PrescriptionsFactory  $prescriptionsFactory,
        LogCollectionFactory $logCollectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->prescriptionsFactory = $prescriptionsFactory;
        $this->logCollectionFactory = $logCollectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
              $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        /** @var ContextInterface $context */
        if (false === $context->getExtensionAttributes()->getIsCustomer()) {
            throw new GraphQlAuthorizationException(__('The current customer isn\'t authorized.'));
        }

        $items = [];
        $orders = $this->collectionFactory->create($context->getUserId());

        /** @var \Magento\Sales\Model\Order $order */
        foreach ($orders as $order) {
		
            $items[] = [
                'id' => $order->getId(),
                'increment_id' => $order->getIncrementId(),
                'order_number' => $order->getIncrementId(),
                'created_at' => $order->getCreatedAt(),
                'grand_total' => $order->getGrandTotal(),
                'status' => $order->getStatus(),
                'shipping_description' => $order->getShippingDescription(),
                'billing_address' => serialize(json_encode($order->getBillingAddress()->getData())),
                'shipping_address' => serialize(json_encode($order->getShippingAddress()->getData())),
                'order_items' => $this->getOrderItems($order),
                'tax' => $order->getFullTaxInfo(),
                'order_info' => serialize(json_encode($order->getData())),
                'payment_details' => $order->getPayment()?serialize(json_encode($order->getPayment()->getData())):'',
            ];
        }        return ['items' => $items];
    }

    protected function getPrescriptionId($prescriptionId)
    {
        $prescription = $this->prescriptionsFactory->create()
            ->load($prescriptionId, 'id');

        if ($prescription->getId()) {
            return $prescription->getData();
        }

        return null;
    }

    protected function getOrderItems($order)
    {
        $items = [];
        foreach ($order->getItems() as $item) {
            $orderItem = $item->getData();
            $orderItem['product'] = $item->getProduct()->getData();
            if (isset($orderItem['product']['prescription'])) {
                $orderItem['product']['prescription_info'] = $this->getPrescriptionId($orderItem['product']['prescription']);
            }

            //if ($orderItem['product']['subscription_active'] == 1) {
                $orderItem['product']['subscription_info'] =  $this->getSubscriptionInfo($order->getIncrementId());
            //}

            $items[] = $orderItem;

        }
        return serialize(json_encode($items));
    }

    protected function getSubscriptionInfo($incrementId)
    {
        $logCollection = $this->logCollectionFactory->create();
        $logCollection->addFieldToFilter('order_increment_id', $incrementId);

        foreach ($logCollection as $log) {
            return $log->getData();
        }

        return null;
    }
}

