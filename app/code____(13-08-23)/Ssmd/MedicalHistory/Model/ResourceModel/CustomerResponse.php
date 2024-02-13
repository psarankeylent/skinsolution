<?php
namespace Ssmd\MedicalHistory\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class CustomerResponse extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('customer_medical_question_responses', 'customer_response_id');
    }
}

?>
