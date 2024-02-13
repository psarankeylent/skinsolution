<?php

declare(strict_types=1);

namespace LegacySubscription\Subscriptions\Model\Resolver;

use Magento\Customer\Model\Customer;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;


/**
 * Create customer address resolver
 */
class ListLegacySubscriptions implements ResolverInterface
{
    /**
     * @var $graphQL
     */
    private $graphQL;

    /**
     * @var customerSubscriptionFactory
     */
    private $customerSubscriptionFactory;

    /**
     * constructor.
     *
     */
    public function __construct(
        \LegacySubscription\Subscriptions\Model\GraphQL $graphQL,
        \LegacySubscription\Subscriptions\Model\CustomerSubscriptionFactory $customerSubscriptionFactory
    ) {
        $this->graphQL = $graphQL;
        $this->customerSubscriptionFactory = $customerSubscriptionFactory;
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
        
        $this->graphQL->authenticate($context);

        // Option id for Legacy Subscription
        $id = isset($args['id']) ? $args['id'] : null;

        $legacySubscription = $this->customerSubscriptionFactory->create()->getCollection();
       
        $legacySubscription->addFieldToFilter('customer_id', $context->getUserId());
        if($id != null)
        {
            $legacySubscription->addFieldToFilter('id', $id);
        }

        $output = [];
        foreach ($legacySubscription as $subscription) {
            $output[] = $subscription->getData();
        }

        return $output;

    }

    
}
