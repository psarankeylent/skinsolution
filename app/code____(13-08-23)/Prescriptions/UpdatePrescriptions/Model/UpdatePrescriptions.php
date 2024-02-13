<?php

namespace Prescriptions\UpdatePrescriptions\Model;

class UpdatePrescriptions implements \Prescriptions\UpdatePrescriptions\Api\UpdatePrescriptionsInterface
{

    protected $prescriptionsFactory;

    public function __construct(
        \Prescriptions\PrescriptionsCollection\Model\PrescriptionsFactory  $prescriptionsFactory
    ) {
        $this->prescriptionsFactory = $prescriptionsFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function updatePrescriptions($request)
    {

        header('Content-type:application/json;charset=utf-8');
        $response = ["success" => false];
        if(!is_array($request)){
            echo json_encode($response); die;
        }

        foreach ($request AS $requestItems){
            $vhPrescriptionId   = $requestItems['vh_prescription_id'];
            $prescriptionName   = $requestItems['prescription_name'];
            $className          = $requestItems['class_name'];
            $ingredient         = $requestItems['ingredient'];
            $strength           = $requestItems['strength'];
            $unit               = $requestItems['unit'];
            $sfStates           = $requestItems['sf_states'];
            $vcStates           = $requestItems['vc_states'];
            $dnsStates          = $requestItems['dns_states'];
        }

        $data = array('vh_prescription_id' => $vhPrescriptionId, 'prescription_name' => $prescriptionName, 'class_name' => $className, 'ingredient' => $ingredient,'strength' => $strength,'unit' => $unit,'sf_states' => $sfStates,'vc_states' => $vcStates,'dns_states' => $dnsStates);

        $Prescriptions = $this->prescriptionsFactory->create()
            ->getCollection()
            ->addFieldToFilter("vh_prescription_id", $vhPrescriptionId)
            ->getFirstItem();

        if($Prescriptions->getData('id')){
           $Prescriptions->addData($data);
           $Prescriptions->save();
           $message = "Prescriptions updated successfully";
        }else{
           $Prescriptions->setData($data);
           $Prescriptions->save();
           $message = "Prescriptions added successfully";
        }        

        $response = ["success" => true, "message" => $message];
        echo json_encode($response); die;
    }
}

