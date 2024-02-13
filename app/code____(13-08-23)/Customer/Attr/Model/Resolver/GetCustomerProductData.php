<?php

namespace Customer\Attr\Model\Resolver;

/**
 * Get customer address resolver
 */
class GetCustomerProductData implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    protected $customerRepository;
    protected $graphQL;

    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Customer\Attr\Model\Api\GraphQL $graphQL
    ) {
        $this->customerRepository = $customerRepository;
        $this->graphQL = $graphQL;
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

        $customerId = $context->getUserId();

        $output = []; 
        if(isset($customerId) && $customerId != null)
        {
            $customer = $this->customerRepository->getById($customerId);
            $customer_product_response = null;

            if(($customerId == $customer->getId()))
            {
                $output['customer_id'] = $customerId;

                if($customer->getCustomAttribute('customer_product_response'))
                {
                    $customer_product_response = $customer->getCustomAttribute('customer_product_response')->getValue();
                }
                $output['customer_product_response'] = $customer_product_response;
            }
        }

        return $output;

    }

}
