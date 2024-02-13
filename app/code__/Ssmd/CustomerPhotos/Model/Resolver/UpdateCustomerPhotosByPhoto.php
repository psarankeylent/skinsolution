<?php

namespace Ssmd\CustomerPhotos\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * Update Customer Photos Info By PHotoID Class
 */
class UpdateCustomerPhotosByPhoto implements \Magento\Framework\GraphQl\Query\ResolverInterface
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

        $this->validateInput($args);

        try{
            
            $customerPhotosModel = $this->customerPhotosFactory->create();

            if (isset($args['input']['photo_id']) && $args['input']['photo_id'] != "") {

                $customerPhotosModel->load($args['input']['photo_id']);

                $customerPhotosModel->setData($args['input'])
                                    ->setData('created_at', date('Y-m-d h:i:s'))
                                    ->save();     
            }
            else{
                throw new GraphQlInputException(__('"%1" value must be specified', $args['input']['photo_id']));
            }
            

            $data = $this->customerPhotosFactory->create()->load($args['input']['photo_id']);
            //print_r($data->getData()); exit;

            $output = [];
            $output = $data->getData();

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
    protected function validateInput(array $args)
    {
        $requiredFields = ['photo_id', 'customer_id'];

        foreach ($requiredFields as $v) {
            if (!isset($args['input'][ $v ]) || empty($args['input'][ $v ])) {
                throw new GraphQlInputException(__('"%1" value must be specified', $v));
            }
        }
    }
}
