<?php

namespace Ssmd\MedicalHistory\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Ssmd\MedicalHistory\Model\ObjectConverter;

/**
 * Get Customer Responses By Order Class
 */
class GetCustomerResponsesByOrder implements \Magento\Framework\GraphQl\Query\ResolverInterface
{

    protected $questionHelper;
    protected $customerResponse;
    protected $questionResponses;
    protected $searchCriteriaBuilder;
    protected $objectConverter;
    protected $customerPrescriptionOrders;


    public function __construct(
        \Consultation\MedicalQuestions\Helper\Data $questionHelper,
        \Consultation\MedicalQuestions\Model\CustomerResponseFactory $customerResponse,
        \Consultation\MedicalQuestions\Model\QuestionResponsesFactory $questionResponses,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Enhance\Prescriptions\Model\CustomerPrescriptionOrders $customerPrescriptionOrders

    ) {
        $this->questionHelper = $questionHelper;
        $this->customerResponseFactory = $customerResponse;
        $this->questionResponses = $questionResponses;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerPrescriptionOrders = $customerPrescriptionOrders;
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

            $orderId = $args['order_id'];

            $prescriptionOrderItem = $this->customerPrescriptionOrders->getCollection()
                ->addFieldToFilter("order_id", $orderId)
                ->getFirstItem();

            //print_r($prescriptionOrderItem->getData());
            //die;
            $uniqueResponseId = $prescriptionOrderItem->getData('unique_response_id');
            $prescriptionId = $prescriptionOrderItem->getData('prescription_id');
            $customerId = $prescriptionOrderItem->getData('customer_id');


            $customerResponse = $this->customerResponseFactory->create();

            $collection = $customerResponse->getCollection();

            $collection->addFieldToFilter('unique_response_id', $uniqueResponseId);
            $collection->addFieldToFilter('response_status', 1);
            $collection->addFieldToFilter('mage_customer_id', $customerId);
            // $collection->toArray();

            // print_r($collection->getData()); exit;

            $output = [];
            foreach ($collection as $key => $value) {

                // Get Question
                $question = $this->questionHelper->getQuestionById($value['question_id']);
                $questionLabel = $question->getQuestionText();

                // Get Response Type
                $response = $this->questionHelper->getResponseTypeById($value['response_id']);
                $responseType = $response->getResponseType();

                // Get Customer Response Label
                $response = $this->questionResponses->create()->load($value['customer_response']);
                $label = $response->getLabel();


                $dataArray['question'] = $questionLabel;
                $dataArray['response_type'] = $responseType;
                $dataArray['response'] = $label;

                $dt[] = $dataArray;

                $output = ['order_id' => $orderId, 'customer_id' => $customerId, 'response' => $dt];

            }
            return $output;

        }catch(Exception $e){
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/graphQL_test.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("Error ". $e->getMessage());

            //print_r($e->getMessage());
        }

        // $requestedFields = $this->graphQL->getSubscriptionFields($info);
        $output = [];

        $output = $dataArray;

        return $output['mage_customer_id'];

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
        $requiredFields = ['order_id'];
        foreach ($requiredFields as $v) {
            if (!isset($args[ $v ]) || empty($args[ $v ])) {
                throw new GraphQlInputException(__('"%1" value must be specified', $v));
            }
        }
    }
}
