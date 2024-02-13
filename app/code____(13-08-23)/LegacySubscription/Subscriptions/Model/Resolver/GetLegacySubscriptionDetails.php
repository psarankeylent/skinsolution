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
 *  Getting Legacy Subscriptions Details resolver
 */
class GetLegacySubscriptionDetails implements ResolverInterface
{
    /**
     * @var $graphQL
     */
    private $graphQL;

    private $subscriptionProfileFactory;

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
        \M1Subscription\Orders\Model\SubscriptionProfileFactory  $subscriptionProfileFactory,
        \LegacySubscription\Subscriptions\Model\CustomerSubscriptionFactory $customerSubscriptionFactory
    ) {
        $this->graphQL = $graphQL;
        $this->subscriptionProfileFactory = $subscriptionProfileFactory;
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
        $profileId = isset($args['profile_id']) ? $args['profile_id'] : null;

        $collection = $this->subscriptionProfileFactory->create()->getCollection();
        $collection->addFieldToFilter('reference_id', $profileId);

        $output = [];
        if(!empty($collection->getData()))
        {
            foreach ($collection as $value) {
                $details = $value->getData('details');
                $data = unserialize($details);
                
                //$quoteId = $data['entity_id'];


                $output['billing_address']  = $data['billing_address'];
                $output['shipping_address'] = $data['shipping_address'];
                $output['payment_method']   = $data['method_code'];
            }
        }

        return $output;

    }

    
}
