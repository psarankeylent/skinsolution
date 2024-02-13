<?php

declare(strict_types=1);

namespace ConsultOnly\UpdateConsultOnly\Api;

interface UpdateConsultOnlyManagementInterface
{

    /**
     * POST for UpdateConsultOnly api
     * @param mixed $request
     * @return mixed
     */
    public function postUpdateConsultOnly($request);
}

