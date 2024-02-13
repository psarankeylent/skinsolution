<?php

namespace Ssmd\Subscriptions\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * Soft dependency: Supporting 2.3 GraphQL without breaking <2.3 compatibility.
 * 2.3+ implements \Magento\Framework\GraphQL; lower does not.
 */
if (!interface_exists('\ParadoxLabs\TokenBase\Model\Api\GraphQL\ResolverInterface')) {
    if (interface_exists('\Magento\Framework\GraphQl\Query\ResolverInterface')) {
        class_alias(
            '\Magento\Framework\GraphQl\Query\ResolverInterface',
            '\ParadoxLabs\TokenBase\Model\Api\GraphQL\ResolverInterface'
        );
    } else {
        class_alias(
            '\ParadoxLabs\TokenBase\Model\Api\GraphQL\FauxResolverInterface',
            '\ParadoxLabs\TokenBase\Model\Api\GraphQL\ResolverInterface'
        );
    }
}

/**
 * Change Subscription Address Class
 */
class UpdateSubscriptionAddress implements \ParadoxLabs\TokenBase\Model\Api\GraphQL\ResolverInterface
{
    /**
     * @var \ParadoxLabs\Subscriptions\Model\Api\GraphQL
     */
    protected $graphQL;

    /**
     * @var \ParadoxLabs\Subscriptions\Api\CustomerSubscriptionRepositoryInterface
     */
    protected $customerSubscriptionRepository;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Config
     */
    
    protected $subscriptionService;

   
    public function __construct(
        \ParadoxLabs\Subscriptions\Model\Api\GraphQL $graphQL,
        \ParadoxLabs\Subscriptions\Api\CustomerSubscriptionRepositoryInterface $customerSubscriptionRepository,
        \ParadoxLabs\Subscriptions\Model\Service\Subscription $subscriptionService
    ) {
        $this->graphQL = $graphQL;
        $this->customerSubscriptionRepository = $customerSubscriptionRepository;
        $this->subscriptionService = $subscriptionService;
    }

    /**
     * Fetches the data from persistence models and format it according to the GraphQL schema.
     *
     * @param \Magento\Framework\GraphQl\Config\Element\Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @throws \Exception
     * @return mixed|\Magento\Framework\GraphQl\Query\Resolver\Value
     */
    public function resolve(
        \Magento\Framework\GraphQl\Config\Element\Field $field,
        $context,
        \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $this->graphQL->authenticate($context);

        try{
                $subscription = $this->customerSubscriptionRepository->getById(
                    $context->getUserId(),
                    $args['input']['entity_id']
                );

                if($args['input']['address_type'] == strtolower('shipping'))
                {
                    $this->updateShippingAddress($subscription, $args);
                }
                else if($args['input']['address_type'] == strtolower('billing'))
                {
                    $this->updateBillingAddress($subscription, $args);
                }
                else
                {
                    throw new Exception("Please pass address type.");
                }
                
                $this->customerSubscriptionRepository->save(
                    $context->getUserId(),
                    $subscription
                );
                //echo $args['input']['address_type']; exit;
                $shipAddress = $subscription->getQuote()->getShippingAddress();
               // print_r($shipAddress->getData()); exit;

                return ['entity_id' => $args['input']['entity_id'], 'address' => $shipAddress->getData()];

                $requestedFields = $this->graphQL->getSubscriptionFields($info);
                return $this->graphQL->convertSubscriptionForGraphQL($subscription, $requestedFields);
              
       
        }catch(Exception $e){
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/graphQL_test.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("Error ". $e->getMessage());
            
            //print_r($e->getMessage());
        }

    }

    /**
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription
     * @param array $args
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    
    public function updateShippingAddress(
        \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription,
        array $args
    ) {
        /** @var \ParadoxLabs\Subscriptions\Model\Subscription $subscription */
        $quote = $subscription->getQuote();

        if ((bool)$quote->getIsVirtual() === false) {
            $addressData = [];
            if (!empty($args['input']['address'])) {
                $addressData = $args['input']['address'];
            }

            if (!empty($addressData)) {
                $this->subscriptionService->changeShippingAddress($subscription, $addressData);
            }
        }
    }

    /**
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription
     * @param array $args
     * @return void
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    
    public function updateBillingAddress(
        \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription,
        array $args
    ) {
        /** @var \ParadoxLabs\Subscriptions\Model\Subscription $subscription */
        $quote = $subscription->getQuote();

        if ((bool)$quote->getIsVirtual() === false) {
            $addressData = [];
            if (!empty($args['input']['address'])) {
                $addressData = $args['input']['address'];
            }

            if (!empty($addressData)) {
                $this->subscriptionService->changeBillingAddress($subscription, $addressData);
            }
        }
    }

}
