<?php

declare(strict_types=1);

namespace ConsultOnly\UpdateConsultOnly\Model;

class UpdateConsultOnlyManagement implements \ConsultOnly\UpdateConsultOnly\Api\UpdateConsultOnlyManagementInterface
{
    protected $consultOnlyFactory;

    public function __construct(
        \ConsultOnly\ConsultOnlyCollection\Model\ConsultOnlyFactory  $consultOnlyFactory,
        \Prescriptions\PrescriptionsCollection\Model\PrescriptionsFactory  $prescriptionsFactory
    ) {
        $this->consultOnlyFactory = $consultOnlyFactory;
        $this->prescriptionsFactory = $prescriptionsFactory;
    }
    /**
     * {@inheritdoc}
     */
    public function postUpdateConsultOnly($request)
    {
        header('Content-type:application/json;charset=utf-8');
        $response = ["success" => false];
        if(!is_array($request)){
            echo json_encode($response); die;
        }

        foreach ($request AS $requestItems){
            $customer_id        = $requestItems['customer_id'];
            $vh_prescription_id    = $requestItems['prescription_id'];
            $status             = $requestItems['status'];
            $consultation_type   = $requestItems['consultation_type'];
            $start_date         = $requestItems['start_date'];
            $exp_date           = $requestItems['exp_date'];
            $np_id              = $requestItems['np_id'];
            $np_name            = $requestItems['np_name'];
            $prescription_name  = $requestItems['prescription_name'];
        }

        $data = array('customer_id' => $customer_id, 'vh_prescription_id' => $vh_prescription_id, 'vh_status' => $status, 'start_date' => $start_date, 'expiration_date' => $exp_date,'np_id' => $np_id, 'np_name' => $np_name, 'prescription_name' => $prescription_name, 'consultation_type' => $consultation_type);
        /*
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/consult_only_data.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($data);
        */
        $consultOnlyFactory = $this->consultOnlyFactory->create()
            ->getCollection()
            ->addFieldToFilter("customer_id", $customer_id)
            ->addFieldToFilter("vh_prescription_id", $vh_prescription_id)
            ->getFirstItem();

        if($consultOnlyFactory->getData('id')){
           $consultOnlyFactory->addData($data);
           $consultOnlyFactory->save();
           $message = "ConsultOnly updated successfully";
           $consultOnlyId = $consultOnlyFactory->getId();
        }else{
           $consultOnlyFactory->setData($data);
           $consultOnlyFactory->save();
           $message = "ConsultOnly added successfully";
           $consultOnlyId = $consultOnlyFactory->getId();
        }  

        $prescription = $this->prescriptionsFactory->create()
            ->getCollection()
            ->addFieldToFilter("vh_prescription_id", $vh_prescription_id)
            ->getFirstItem();

        if($prescription->getData('id')){
            $mg_prescription_id = $prescription->getData('id');
            $updatePrescription = $this->consultOnlyFactory->create()
            ->getCollection()
            ->addFieldToFilter("id", $consultOnlyId)
            ->getFirstItem();
            $updatePrescription->addData(array('prescription_id' => $mg_prescription_id));
            $updatePrescription->save();
        }

        $response = ["success" => true, "consultOnlyAutoIncrementId" => $consultOnlyId, "message" => $message];
        echo json_encode($response); die;
    }
}

