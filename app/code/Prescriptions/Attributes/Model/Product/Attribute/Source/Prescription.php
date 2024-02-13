<?php
declare(strict_types=1);

namespace Prescriptions\Attributes\Model\Product\Attribute\Source;

class Prescription extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    protected $prescriptionsFactory;

    public function __construct(
        \Prescriptions\PrescriptionsCollection\Model\PrescriptionsFactory  $prescriptionsFactory
    ) {
        $this->prescriptionsFactory = $prescriptionsFactory;
    }

    public function getAllOptions()
    {

        $prescriptionCollection = $this->prescriptionsFactory->create()->getCollection();
        $this->options[] = ['label' => 'Please Select', 'value' => 0];
        foreach ($prescriptionCollection as $item){
            $label = $item->getData('prescription_name') . ' - '. $item->getData('class_name') . ' - '.
                $item->getData('ingredient'). ' - '.$item->getData('strength');
            $this->options[] = ['label' => $label, 'value' => $item->getData('id')];
        }

        return $this->options;
    }

}

