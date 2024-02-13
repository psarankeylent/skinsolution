<?php

namespace Backend\MedicalGraphql\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * Get Customer Medical History Class
 */
class GetMedicalHistory implements \Magento\Framework\GraphQl\Query\ResolverInterface
{

    protected $graphQL;
    protected $medicalFactory;

    public function __construct(
        \Backend\MedicalGraphql\Model\Api\GraphQL $graphQL,
        \Backend\Medical\Model\MedicalFactory $medicalFactory

    ) {
        $this->graphQL = $graphQL;
        $this->medicalFactory = $medicalFactory;
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

            // Get Customer Medical History
            $collection =  $this->medicalFactory->create()->getCollection();
            if(isset($args['question_id']) && $args['question_id'] != "")               // If 'question_id' provided return data for that question_id of customer.
            {
                $collection->addFieldToFilter('question_id', $args['question_id']);
                $collection->addFieldToFilter('customer_id', $context->getUserId());
            }
            else{
                $collection->addFieldToFilter('customer_id', $context->getUserId());
            }

            $output = [];
            if(!empty($collection->getData()))
            {
                foreach ($collection as $customerMedicalHistory) {
                    $output[] = $customerMedicalHistory->getData();
                }
            }
            else
            {
                $output[] = ['response' => "This customer doesn't have Medical History"];
            }


            return $output;

        }catch(Exception $e){
            /* $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/graphQL_test.log');
             $logger = new \Zend\Log\Logger();
             $logger->addWriter($writer);
             $logger->info("Error ". $e->getMessage());*/

            //print_r($e->getMessage());
        }

    }

}
