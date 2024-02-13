<?php

declare(strict_types=1);

namespace VirtualHub\MhByCustomerId\Api;

interface MhByCustomerIdManagementInterface
{

    /**
     * GET for mhByCustomerId api
     * @param string $customerId
     * @return mixed
     */
    public function getMhByCustomerId($customerId);
}

