<?php
namespace Ssmd\MedicalHistory\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class QuestionResponses extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('medical_question_responses', 'response_id');
    }
}

?>
