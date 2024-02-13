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
 * Update Shipping Method Class
 */
class UpdateShippingMethod implements \ParadoxLabs\TokenBase\Model\Api\GraphQL\ResolverInterface
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
    protected $subscriptionRepository;
    protected $statusSource;

    /**
     * ChangeStatus constructor.
     *
     * @param \ParadoxLabs\Subscriptions\Model\Api\GraphQL $graphQL
     * @param \ParadoxLabs\Subscriptions\Api\CustomerSubscriptionRepositoryInterface $customerSubscriptionRepository
     * @param \ParadoxLabs\Subscriptions\Model\Config $config
     * @param \ParadoxLabs\Subscriptions\Model\Source\Status $statusSource
     */
    public function __construct(
        \ParadoxLabs\Subscriptions\Model\Api\GraphQL $graphQL,
        \ParadoxLabs\Subscriptions\Api\CustomerSubscriptionRepositoryInterface $customerSubscriptionRepository,
        \ParadoxLabs\Subscriptions\Api\SubscriptionRepositoryInterface $subscriptionRepository,
        \ParadoxLabs\Subscriptions\Model\Config $config,
        \ParadoxLabs\Subscriptions\Model\Source\Status $statusSource
    ) {
        $this->graphQL = $graphQL;
        $this->customerSubscriptionRepository = $customerSubscriptionRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->config = $config;
        $this->statusSource = $statusSource;
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

            $subscription = $this->subscriptionRepository->getById($args['entity_id']);
            $quote = $subscription->getQuote();
            $shippingAddress = $quote->getShippingAddress();
            $oldShippingmMethod = $shippingAddress->getShippingMethod();

            $model='';
            
            if(!empty($oldShippingmMethod))
            {
                
                $shippingAddress->setCollectShippingRates(true)
                                ->collectShippingRates()
                                ->setShippingMethod($args['shipping_method'])
                                ->setShippingDescription($args['shipping_description']);
                                //->setShippingAmount(3);

                $quote->setTotalsCollectedFlag(true)
                              ->collectTotals()
                              ->save();

                
            }
            else
            {
                throw new \Exception('Shipping Method Not Found');
            }


            return ['SubscriptionObj' => $subscription->toArray()];
       
        }catch(Exception $e){
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/graphQL_test.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("Error ". $e->getMessage());
            
            //print_r($e->getMessage());
        }

    }


}
