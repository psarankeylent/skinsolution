<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Backend\Medical\Api\Data;

interface MedicalInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const VIEW = 'view';
    const id = 'id';

    /**
     * Get id
     * @return string|null
     */
    public function getMedicalId();

    /**
     * Set id
     * @param string $medicalId
     * @return \Backend\Medical\Api\Data\MedicalInterface
     */
    public function setMedicalId($medicalId);

    /**
     * Get view
     * @return string|null
     */
    public function getView();

    /**
     * Set view
     * @param string $view
     * @return \Backend\Medical\Api\Data\MedicalInterface
     */
    public function setView($view);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Backend\Medical\Api\Data\MedicalExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Backend\Medical\Api\Data\MedicalExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Backend\Medical\Api\Data\MedicalExtensionInterface $extensionAttributes
    );
}

