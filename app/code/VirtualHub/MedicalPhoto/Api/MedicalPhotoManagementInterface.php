<?php
declare(strict_types=1);

namespace VirtualHub\MedicalPhoto\Api;

interface MedicalPhotoManagementInterface
{

    /**
     * GET for MedicalPhoto api
     * @param string $customerId
     * @return mixed
     */
    public function medicalPhotos($incrementId);
}
