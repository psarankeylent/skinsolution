<?php

namespace Ssmd\CustomerPhotos\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * Save Customer Photos Info Class
 */
class SaveCustomerPhotos implements \Magento\Framework\GraphQl\Query\ResolverInterface
{
    
    protected $graphQL;
    protected $customerPhotosFactory;

    public function __construct(
        \VirtualHub\Config\Helper\Config $configHelper,
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Ssmd\CustomerPhotos\Model\GraphQL $graphQL,
        \Ssmd\CustomerPhotos\Model\CustomerPhotosFactory $customerPhotosFactory

    ) {
        $this->configHelper = $configHelper;
        $this->curl = $curl;
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
        $this->graphQL->authenticate($context);

        $this->validateInput($args);
       
        try{

            $customerId = $args['input']['customer_id'];
            
            $customerPhotosModel = $this->customerPhotosFactory->create();

            $customerPhotosModel->setData('customer_id', $args['input']['customer_id'])
                                ->setData('photo_type', $args['input']['photo_type'])
                                ->setData('path', $args['input']['path'])
                                ->setData('source_system', $args['input']['source_system'])
                                ->setData('status', $args['input']['status'])
                                ->setData('created_at', date('Y-m-d h:i:s'))
                                ->save();

            $lastInsertedId = $customerPhotosModel->getId();

            $data = $this->customerPhotosFactory->create()->load($lastInsertedId);
            //print_r($data->getData()); exit;

            $output = [];
            $output = $data->getData();


            // code for pushing photo to zeelify //

                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/medical_photo_frontend.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
        
                $request['customer_id'] = $customerId;
                $bearerToken = $this->configHelper->getVirtualHubBearerToken();
                if ($bearerToken['success'] == True) {
                    $token = $bearerToken['token'];
                    $vhUrl = $this->configHelper->getUpdatePhoto();
                    $headers = ["Content-Type" => "application/json", "Authorization" => 'Bearer ' . $token];
                    $this->curl->setHeaders($headers);
                    $this->curl->post($vhUrl, json_encode($request));
                    $response = $this->curl->getBody();
                    $logger->info($response);
                }

            // code END //


            return $output;
   
        }catch(Exception $e){
            //$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/graphQL_test.log');
            //$logger = new \Zend\Log\Logger();
            //$logger->addWriter($writer);
            //$logger->info("Error ". $e->getMessage());
            
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
        $requiredFields = ['customer_id', 'photo_type', 'path', 'source_system', 'status'];

        foreach ($requiredFields as $v) {
            if (!isset($args['input'][ $v ]) || empty($args['input'][ $v ])) {
                throw new GraphQlInputException(__('"%1" value must be specified', $v));
            }
        }
    }
}

