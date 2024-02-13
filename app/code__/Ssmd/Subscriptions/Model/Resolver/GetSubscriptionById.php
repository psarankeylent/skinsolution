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
 * Get Subscriptions by subscription id Class
 */
class GetSubscriptionById implements \ParadoxLabs\TokenBase\Model\Api\GraphQL\ResolverInterface
{
    /**
     * @var \ParadoxLabs\Subscriptions\Model\Api\GraphQL
     */
    protected $graphQL;

    /**
     * @var \ParadoxLabs\Subscriptions\Api\SubscriptionRepositoryInterface
     */
    protected $subscriptionRepository;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Config
     */
    protected $config;
    protected $searchCriteriaBuilder;
    
    /**
     * ChangeStatus constructor.
     *
     * @param \ParadoxLabs\Subscriptions\Model\Api\GraphQL $graphQL
     * @param \ParadoxLabs\Subscriptions\Api\CustomerSubscriptionRepositoryInterface $customerSubscriptionRepository
     * @param \ParadoxLabs\Subscriptions\Model\Config $config
     */
    public function __construct(
        \ParadoxLabs\Subscriptions\Model\Api\GraphQL $graphQL,
        \ParadoxLabs\Subscriptions\Api\SubscriptionRepositoryInterface $subscriptionRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \ParadoxLabs\Subscriptions\Model\Config $config
    ) {
        $this->graphQL = $graphQL;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->config = $config;
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

            $subscription = $this->getSubscriptions($args['entity_id']);

       
        }catch(Exception $e){
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/graphQL_test.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("Error ". $e->getMessage());
            
            //print_r($e->getMessage());
        }

        $requestedFields = $this->graphQL->getSubscriptionFields($info);

        /** @var \ParadoxLabs\Subscriptions\Model\Subscription[] $subscriptions */
        $output = [];
        //$output = $this->getTestQuoteData($subscription->toArray());

        return ['SubscriptionObj' => $subscription->toArray()];
        
        $output = $this->graphQL->convertSubscriptionForGraphQL($subscription, $requestedFields);

        return $output;
        
    }
    public function getTestQuoteData($quote)
    {
        foreach ($quote->getAllItems() as $item) {
                 $data[] = $item->getData('sku');
        }

        return $data;
    }

    /**
     * Get subscription(s) for the given GraphQL request.
     *
     * @param int $customerId
     * @param int|null $id
     * @return \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSubscriptions($id = null)
    {
        $searchCriteria = $this->searchCriteriaBuilder;
        if ($id !== null) {
            $searchCriteria->addFilter('entity_id', $id);
        }

        $subscriptions = $this->subscriptionRepository->getById(
            $id            
        );

        return $subscriptions;
    }

    
}
