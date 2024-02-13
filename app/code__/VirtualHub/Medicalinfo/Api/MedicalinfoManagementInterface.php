<?php

declare(strict_types=1);

namespace VirtualHub\Medicalinfo\Api;

interface MedicalinfoManagementInterface
{

    /**
     * GET for Medicalinfo api
     * @param string $incrementId
     * @return mixed
     */
    public function getMedicalinfo($incrementId);
}

