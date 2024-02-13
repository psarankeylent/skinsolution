<?php

namespace Ssmd\MedicalHistory\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * Get Customer Medical History By Unique ID Class
 */
class GetCustomerMedicalHistoryByUniqueId implements \Magento\Framework\GraphQl\Query\ResolverInterface
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

        $this->validateInput($args);

        try{

            // Get Customer Medical History by unique_id
            $collection = $this->customerMedicalHistoryFactory->create()->getCollection();
            $collection->addFieldToFilter('unique_id', $args['unique_id']);

            $output = [];
            foreach ($collection as $customerMedicalHistory) {
                $output[] = $customerMedicalHistory->getData();
            }

            //print_r($data); exit;


            $output = ['medicalHistoryData' => $output];

            return $output;

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
    protected function validateInput(array $args)
    {
        $requiredFields = ['unique_id'];
        foreach ($requiredFields as $v) {
            if (!isset($args[ $v ]) || empty($args[ $v ])) {
                throw new GraphQlInputException(__('"%1" value must be specified', $v));
            }
        }
    }
}
