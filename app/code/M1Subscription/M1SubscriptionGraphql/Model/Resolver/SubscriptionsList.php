<?php

declare(strict_types=1);

namespace M1Subscription\M1SubscriptionGraphql\Model\Resolver;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Exception\GraphQlAuthorizationException;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Quote\Model\QuoteFactory;

class SubscriptionsList implements ResolverInterface
{
    protected $quoteFactory;
    protected $m1SubscriptionCollectionFactory;

    public function __construct(
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \M1Subscription\M1SubscriptionCollection\Model\M1SubscriptionCollectionFactory  $m1SubscriptionCollectionFactory
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->m1SubscriptionCollectionFactory = $m1SubscriptionCollectionFactory;
    }


    public function resolve(Field $field, $context, ResolveInfo $info, array $value = null, array $args = null)
    {
        /** @var ContextInterface $context */
        if ((!$context->getUserId()) || $context->getUserType() === UserContextInterface::USER_TYPE_GUEST) {
            throw new GraphQlAuthorizationException(
                __(
                    'Current customer does not have access to the resource'
                )
            );
        }

        $customer_id = $context->getUserId();

        $collections = $this->m1SubscriptionCollectionFactory->create()
            ->getCollection()
            ->addFieldToFilter("customer_id", $customer_id);

        $outputArray = [];

        foreach ($collections as $subscriptions) {

            $output['entity_id']        = $subscriptions['entity_id'];
            $output['reference_id']     = $subscriptions['reference_id'];
            $output['customer_id']      = $subscriptions['customer_id'];
            $output['status']           = $subscriptions['status'];
            $output['amount']           = $subscriptions['amount'];
            $output['create_at']        = $subscriptions['created_at'];
            
            $details            = $subscriptions->getData('details');
            $details            = unserialize($details);
            $output['sku']      = $details['order_item_info']['sku'];

            $output['title']    = $details['subscription']['type']['title'];
            $output['product_name']    = $details['order_item_info']['name'];
            $output['product_price']    = $details['order_item_info']['price'];

            $outputArray[] = $output;

        }

        return $outputArray;

    }

}


