<?php
declare(strict_types=1);

namespace VirtualHub\UpdateDateOfBirth\Api;

interface UpdateDateOfBirthManagementInterface
{

    /**
     * POST for UpdateDateOfBirth api
     * @param mixed $request
     * @return mixed
     */
    public function postUpdateDateOfBirth($request);
}

