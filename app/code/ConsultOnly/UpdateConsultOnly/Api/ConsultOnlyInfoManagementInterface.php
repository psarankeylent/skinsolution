<?php

declare(strict_types=1);

namespace ConsultOnly\UpdateConsultOnly\Api;

interface ConsultOnlyInfoManagementInterface
{

    /**
     * GET for ConsultOnlyInfo api
     * @param string $customerId
     * @return mixed
     */
    public function getConsultOnlyInfo($customerId);
}

