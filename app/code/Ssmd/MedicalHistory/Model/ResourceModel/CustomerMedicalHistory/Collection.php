<?php

namespace Ssmd\MedicalHistory\Model\ResourceModel\CustomerMedicalHistory;

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
        $this->_init('Ssmd\MedicalHistory\Model\CustomerMedicalHistory',
            'Ssmd\MedicalHistory\Model\ResourceModel\CustomerMedicalHistory'
        );
    }

}

?>
