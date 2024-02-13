<?php

namespace Ssmd\MedicalHistory\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * Save Customer Medical History Class
 */
class SaveCustomerMedicalHistory implements \Magento\Framework\GraphQl\Query\ResolverInterface
{

    protected $graphQL;
    protected $customerMedicalHistoryFactory;

    public function __construct(
        \Ssmd\MedicalHistory\Model\Api\GraphQL $graphQL,
        \Ssmd\MedicalHistory\Model\CustomerMedicalHistoryFactory $customerMedicalHistoryFactory

    ) {
        $this->graphQL = $graphQL;
        $this->customerMedicalHistoryFactory = $customerMedicalHistoryFactory;
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


        try{

            $output = [];
            $customerMedicalHistoryModel = $this->customerMedicalHistoryFactory->create();

            foreach ($args['input'] as $key => $value) {

                $this->validateInput($value);

                $customerMedicalHistoryModel->setData($value)
                                            ->setCreatedAt(date('Y-m-d H:i:s'))
                                            ->setUpdatedAt(date('Y-m-d H:i:s'))
                                            ->save();

                $lastInsertedId = $customerMedicalHistoryModel->getId();

                $data = $this->customerMedicalHistoryFactory->create()->load($lastInsertedId);
                $output[] = $data->getData();
            }

            $output = ['customerMedicalHistory' => $output];

            return $output;

             //print_r($output); exit;





        }catch(Exception $e){
           /* $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/graphQL_test.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("Error ". $e->getMessage());*/

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
        $requiredFields = ['question_id', 'customer_id', 'question_text', 'response', 'unique_id', 'cart_id', 'status', 'order_number','full_face','govt_id'];

        foreach ($values as $key => $value) {

            if (!isset($value) || empty($value)) {
                throw new GraphQlInputException(__('"%1" value must be specified', $key));
            }
        }

    }
}

