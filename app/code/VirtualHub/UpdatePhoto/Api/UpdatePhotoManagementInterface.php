<?php

declare(strict_types=1);

namespace VirtualHub\UpdatePhoto\Api;

interface UpdatePhotoManagementInterface
{

    /**
     * POST for UpdatePhoto api
     * @param mixed $request
     * @return mixed
     */
    public function updatePhoto($request);
}

