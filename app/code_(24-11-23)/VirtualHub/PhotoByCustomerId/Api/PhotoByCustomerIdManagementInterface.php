<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace VirtualHub\PhotoByCustomerId\Api;

interface PhotoByCustomerIdManagementInterface
{

    /**
     * GET for PhotoByCustomerId api
     * @param string $customerId
     * @return mixed
     */
    public function getPhotoByCustomerId($customerId);
}

