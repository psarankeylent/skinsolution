<?php

namespace Ssmd\Attributes\Model\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;
use Ssmd\Attributes\Model\Prescriptions as PrescriptionsModel;

class Prescriptions extends AbstractSource
{
    protected $optionFactory;
    public $prescriptions;

    public function __construct(
        PrescriptionsModel $prescriptions
    ) {
        $this->prescriptions = $prescriptions;
    }

    public function getAllOptions()
    {
        $prescriptionCollection = $this->prescriptions->getCollection()
            ->addFieldToFilter('status', 1);
        $this->_options[] = ['label' => 'Please Select', 'value' => 0];
        foreach ($prescriptionCollection as $_item){
            $label = $_item->getData('prescription_flow') . ' - '. $_item->getData('prescription_class') . ' - '.
                $_item->getData('strength');

            if($_item->getData('dosage_data') != ""){
                $label .=' - '. $_item->getData('dosage_data');
            }
            $this->_options[] = ['label' => $label, 'value' => $_item->getData('prescription_id')];
        }

        return $this->_options;
    }
}
