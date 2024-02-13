<?php

namespace Ssmd\CustomerPhotos\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * Update Customer Photos Info Class
 */
class UpdateCustomerPhotosByCustomer implements \Magento\Framework\GraphQl\Query\ResolverInterface
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
        //echo "called"; exit;
        //$this->graphQL->authenticate($context);

        try{
            
            $output = [];            

            $customerPhotosModel = $this->customerPhotosFactory->create();

            foreach ($args['input'] as $key => $value) {
                //print_r($value); exit;

                $this->validateInput($value);
                
                if(isset($value['photo_id']) && $value['photo_id'] != "")
                {
                   // echo $value['photo_id']; exit;
                    $customeModel = $customerPhotosModel->load($value['photo_id']);
                
                    $customeModel->setData($value)
                                ->setCreatedAt(date('Y-m-d h:i:s'))
                                ->save();

                    $data = $customerPhotosModel->load($value['photo_id']);
                    $output[] = $data->getData();
                }
                
            }
           // print_r($output); exit;
            $output = ['updateCustomerPhotos' => $output];

            return $output;
            
       
        }catch(Exception $e){
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/graphQL_test.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("Error ". $e->getMessage());
            
            //print_r($e->getMessage());
        }

        
    }

    /**
     * Validate GraphQL request input.
     *
     * @param array $args
     * @return void
     * @throws \Magento\Framework\GraphQl\Exception\GraphQlInputException
     */
    protected function validateInput(array $values)
    {
        foreach ($values as $key => $value) { 
           
            if (!isset($value)) {
                throw new GraphQlInputException(__('"%1" value must be specified', $key));
            }
        }
    }
}
