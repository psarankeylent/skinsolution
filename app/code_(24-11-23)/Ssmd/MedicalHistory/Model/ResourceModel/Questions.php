<?php
namespace Ssmd\MedicalHistory\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Questions extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('medical_questions', 'question_id');
    }
}

?>
