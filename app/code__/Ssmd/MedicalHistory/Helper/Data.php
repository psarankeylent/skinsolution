<?php

namespace Ssmd\MedicalHistory\Helper;

/**
 * Class Data
 *
 * @package Ssmd\MedicalHistory\Helper\Data
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    
    protected $questionsFactory;
    protected $questionGroupsFactory;
    protected $questionResponsesFactory;
    protected $customerResponseFactory;
    protected $medicalHistoryFactory;
    protected $orderFactory;

    public function __construct(
        \Ssmd\MedicalHistory\Model\QuestionsFactory $questionsFactory,
        \Ssmd\MedicalHistory\Model\QuestionGroupsFactory $questionGroupsFactory,
        \Ssmd\MedicalHistory\Model\QuestionResponsesFactory $questionResponsesFactory,
        \Ssmd\MedicalHistory\Model\CustomerResponseFactory $customerResponseFactory,
        \Ssmd\MedicalHistory\Model\CustomerMedicalHistoryFactory $medicalHistoryFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory
    ) {
        $this->questionsFactory = $questionsFactory;
        $this->questionGroupsFactory = $questionGroupsFactory;
        $this->questionResponsesFactory = $questionResponsesFactory;
        $this->customerResponseFactory = $customerResponseFactory;
        $this->medicalHistoryFactory = $medicalHistoryFactory;
        $this->orderFactory = $orderFactory;
    }

    public function getQuestionById($id)
    {
        $question = $this->questionsFactory->create()->load($id);
        return $question;
    }
    public function getResponseTypeById($id)
    {
        $responseTypes = $this->questionResponsesFactory->create()->getCollection()->addFieldToFilter('response_id', $id);
        $responseTypes = $responseTypes->getFirstItem();

        return $responseTypes;
    }
    public function getGroups($questionId)
    {
        $groups = $this->questionGroupsFactory->create()->getCollection()->addFieldToFilter('question_id', $questionId);
        return $groups;
    }
    public function getResponseTypes($groupId)
    {
        $responseTypes = $this->questionResponsesFactory->create()->getCollection()->addFieldToFilter('group_id', $groupId);
        $responseTypes->setOrder('sequence', 'ASC');

        return $responseTypes;
    }

    public function getCustomerResponsesByUniqResId($uniqueRespId)
    {
        $customerResponse = $this->customerResponseFactory->create();
        $collection = $customerResponse->getCollection();        
        $collection->addFieldToFilter('unique_response_id', $uniqueRespId);

        return $collection;
    }

    public function getCustomerResponsesCollection()
    {
        $customerResponse = $this->customerResponseFactory->create()->getCollection();
        //$customerResponse->getCollection();

        return $customerResponse;
    }


    public function getOrderMedicalHistoryCollection()
    {
        return $this->medicalHistoryFactory->create()->getCollection();
    }

    public function getRXNONProductByUniqueId($orderId)
    {
        $order = $this->orderFactory->create()->load($orderId);
        $uniqueId = $order->getData('unique_id');

        $rx=null;
        if(isset($uniqueId) && $uniqueId != "")
        {
            $rx = "RX PRODUCT";
        }
        else
        {
            $rx = "NON-RX PRODUCT";
        }

        return $rx;
    }

    
}

