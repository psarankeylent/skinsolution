<?php
declare(strict_types=1);

namespace VirtualHub\UpdatePrescriptionOrder\Api;

interface UpdatePrescriptionOrderItemManagementInterface
{

    /**
     * POST for UpdatePrescriptionOrderItem api
     * @param mixed $request
     * @return mixed
     */
    public function updatePrescriptionOrderItem($request);
}

