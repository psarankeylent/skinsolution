<?php
declare(strict_types=1);

namespace Prescriptions\UpdatePrescriptions\Api;

interface UpdatePrescriptionsInterface
{

    /**
     * POST for updatePrescriptions api
     * @param mixed $request
     * @return mixed
     */
    public function updatePrescriptions($request);
}

