<?php

namespace Backend\MedicalGraphql\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;

class UpdateMedicalHistory implements \Magento\Framework\GraphQl\Query\ResolverInterface
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
            $message = "Result not found";

            foreach ($args['input'] as $value) {
                $this->validateInput($value);

                $medicalCollection = $this->medicalFactory->create()
                    ->getCollection()
                    ->addFieldToFilter("customer_id", $value['customer_id'])
                    ->addFieldToFilter("question_id", $value['question_id'])
                    ->getFirstItem();

                if($medicalCollection->getData('id')){
                    $data = array('response' => $value['response'], 'updated_at'=> date('Y-m-d H:i:s'));
                    $medicalCollection->addData($data);
                    $medicalCollection->save();

                    $pId = $medicalCollection->getData('id');
                    $message = "Medical History updated successfully";
                }else{
                    $data = array('customer_id' => $value['customer_id'], 'question_id' => $value['question_id'], 'question_text' => $value['question_text'], 'response' => $value['response'], 'updated_at'=> date('Y-m-d H:i:s'));
                    $medicalCollection->setData($data);
                    $medicalCollection->save();

                    $pId = $medicalCollection->getId();
                    $message = "Medical History added successfully";
                }
            }

        }catch(Exception $e){
            $message = $e->getMessage();
        }

        $output = array('id'=>$pId, 'success_message' => $message);
        return $output;

    }


    protected function validateInput(array $values)
    {
        $requiredFields = ['question_id', 'customer_id', 'question_text', 'response'];

        foreach ($values as $key => $value) {

            if (!isset($value) || empty($value)) {
                throw new GraphQlInputException(__('"%1" value must be specified', $key));
            }
        }

    }

}
