<?php

namespace Ssmd\MedicalHistory\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * Get Master(All) Questions List WIth Responses Class
 */
class QuestionsMasterList implements \Magento\Framework\GraphQl\Query\ResolverInterface
{

    protected $questionHelper;
    protected $questionsFactory;
    protected $questionGroupsFactory;
    protected $questionResponsesFactory;
    protected $customerResponseFactory;
    protected $connectionResource;

    public function __construct(
        \Ssmd\MedicalHistory\Model\QuestionsFactory $questionsFactory,
        \Ssmd\MedicalHistory\Model\QuestionGroupsFactory $questionGroupsFactory,
        \Ssmd\MedicalHistory\Model\QuestionResponsesFactory $questionResponsesFactory,
        \Magento\Framework\App\ResourceConnection $connectionResource
    ) {

        $this->questionsFactory = $questionsFactory;
        $this->questionGroupsFactory = $questionGroupsFactory;
        $this->questionResponsesFactory = $questionResponsesFactory;
        $this->connectionResource = $connectionResource;
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

        //$this->graphQL->authenticate($context);

        try{
            // Questions Collection
            $collection = $this->questionsFactory->create()->getCollection();

            $output = [];
            $data = [];
            $respData = [];
            $questionsListArray = [];

            foreach ($collection as $question) {

                // Getting Medical Questions Data
                $data['question_id']         = $question->getQuestionId();
                $data['question_text']       = $question->getQuestionText();
                $data['question_subtext']    = $question->getQuestionSubtext();

                // Getting Prescriptions Data From Mapping Table
                if($question->getQuestionId() != "")
                {
                    $query = "SELECT * FROM `prescription_question_mapping`
                                    WHERE `question_id` = ".$question->getQuestionId()."";

                    $result = $this->connectionResource->getConnection()->fetchAll($query);
                    if(!(empty($result)))
                    {
                        foreach ($result as $value) {
                            $data['prescriptions'][] = $value;
                        }
                    }

                }
                // Getting Medical Group Data
                $groupCollection = $this->questionGroupsFactory->create()->getCollection();
                $groupCollection->addFieldToFilter('question_id', $question->getQuestionId());

                foreach ($groupCollection as $group) {
                    $data['groups'][] = $group->getData();

                    // Getting Medical Responses Data
                    $responseCollection = $this->questionResponsesFactory->create()->getCollection();
                    $responseCollection->addFieldToFilter('group_id', $group->getGroupId());

                    //echo count($responseCollection->getData()); exit;
                    foreach ($responseCollection as $resp) {
                        $data['responses'][] = $resp->getData();
                    }

                    $questionsListArray[] = $data;
                }


            }
            print_r($questionsListArray); exit;

            $output = ['questionsAll' => $questionsListArray];

        }catch(Exception $e){
            /*$writer = new \Zend\Log\Writer\Stream(BP . '/var/log/graphQL_test.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("Error ". $e->getMessage());*/
        }

        return $output;
    }

}
