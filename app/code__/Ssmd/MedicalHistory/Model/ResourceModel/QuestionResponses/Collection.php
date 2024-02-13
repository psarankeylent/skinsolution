<?php

namespace Ssmd\MedicalHistory\Model\ResourceModel\QuestionResponses;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Ssmd\MedicalHistory\Model\QuestionResponses',
            'Ssmd\MedicalHistory\Model\ResourceModel\QuestionResponses'
        );
    }

}

?>
