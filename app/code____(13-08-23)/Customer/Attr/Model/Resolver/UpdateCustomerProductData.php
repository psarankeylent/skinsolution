<?php

namespace Customer\Attr\Model\Resolver;

use Magento\Customer\Model\CustomerFactory;


/**
 * Update customer product status resolver
 */
class UpdateCustomerProductData implements \Magento\Framework\GraphQl\Query\ResolverInterface
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

        $output=[];
        $customerId = $context->getUserId();
        if(isset($customerId) && $args['input']['customer_product_response'] != "")
        {
            $customer = $this->customerRepository->getById($customerId);
            $customer->setCustomAttribute('customer_product_response', $args['input']['customer_product_response']);
            $this->customerRepository->save($customer);

            $responseAttribute=null;
            if($customer->getCustomAttribute('customer_product_response')) {
                $responseAttribute = $customer->getCustomAttribute('customer_product_response')->getValue();
            }
            $output = ['customer_id' => $customerId, 'customer_product_response' => $responseAttribute];

        }
        return $output;

    }
}
