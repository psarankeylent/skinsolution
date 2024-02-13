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
 * Get Subscriptions by customer Class
 */
class GetSubscriptionByCustomer implements \ParadoxLabs\TokenBase\Model\Api\GraphQL\ResolverInterface
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
        \ParadoxLabs\Subscriptions\Api\CustomerSubscriptionRepositoryInterface $customerSubscriptionRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \ParadoxLabs\Subscriptions\Model\Config $config
    ) {
        $this->graphQL = $graphQL;
        $this->customerSubscriptionRepository = $customerSubscriptionRepository;
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

            $subscriptions = $this->getSubscriptions($args['customer_id']);
       
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
        
        foreach ($subscriptions as $subscription) {    

            $quote = $subscription->getQuote();
            $shippingAddress = $quote->getShippingAddress();
            $billingAddress  = $quote->getBillingAddress();
            $paymentData     = $quote->getPayment()->getData();
            //print_r($paymentData); exit;
            $shippingMethod  = $shippingAddress->getShippingMethod();

            $subscription['shipping_address'] = $shippingAddress;
            $subscription['billing_address']  = $billingAddress;
            $subscription['payment_data']     = $paymentData;
            $subscription['shipping_method']  = $shippingMethod;

            $output[] = $this->graphQL->convertSubscriptionForGraphQL($subscription, $requestedFields);
        }

        return $output;
        
    }

    /**
     * Get subscription(s) for the given GraphQL request.
     *
     * @param int $customerId
     * @param int|null $id
     * @return \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getSubscriptions($customerId)
    {
        $searchCriteria = $this->searchCriteriaBuilder;
        if ($customerId !== null) {
            $searchCriteria->addFilter('customer_id', $customerId);
        }

        $subscriptions  = $this->customerSubscriptionRepository->getList(
            $customerId,
            $searchCriteria->create()
        )->getItems();

        return $subscriptions;
    }

}
