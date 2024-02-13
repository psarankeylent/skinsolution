<?php
declare(strict_types=1);

namespace VirtualHub\UpdateContact\Api;

interface UpdateContactManagementInterface
{

    /**
     * POST for UpdateContact api
     * @param mixed $request
     * @return mixed
     */
    public function postUpdateContact($request);
}

