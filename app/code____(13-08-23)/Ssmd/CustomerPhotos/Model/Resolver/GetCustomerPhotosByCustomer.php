<?php

namespace Ssmd\CustomerPhotos\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * Get Customer Photos By Customer Class
 */
class GetCustomerPhotosByCustomer implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    
    protected $graphQL;
    protected $customerPhotosFactory;

    public function __construct(
        \Ssmd\CustomerPhotos\Model\GraphQL $graphQL,
        \Ssmd\CustomerPhotos\Model\CustomerPhotosFactory $customerPhotosFactory

    ) {
        $this->graphQL = $graphQL;
        $this->customerPhotosFactory = $customerPhotosFactory;
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

       // $this->validateInput($args);
       
        try{

            
            $orderId = $args['customer_id'];

            // Get Customer Photos by customer_id
            $collection = $this->customerPhotosFactory->create()->getCollection();
            $collection->addFieldToFilter('customer_id', $args['customer_id']);

            $output = [];   
            foreach ($collection as $photo) {
                $output[] = $photo->getData();
            }
          
            //print_r($output); exit;
            
            $output = ['photoDetails' => $output];
           
            return $output;

       
        }catch(Exception $e){
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/graphQL_test.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("Error ". $e->getMessage());
            
            //print_r($e->getMessage());
        }

    }

}
