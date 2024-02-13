<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Backend\Medical\Model\Data;

use Backend\Medical\Api\Data\MedicalInterface;

class Medical extends \Magento\Framework\Api\AbstractExtensibleObject implements MedicalInterface
{

    /**
     * Get ID
     * @return string|null
     */
    public function getMedicalId()
    {
        return $this->_get(self::ID);
    }

    /**
     * Set ID
     * @param string $medicalId
     * @return \Backend\Medical\Api\Data\MedicalInterface
     */
    public function setMedicalId($medicalId)
    {
        return $this->setData(self::ID, $medicalId);
    }

    /**
     * Get view
     * @return string|null
     */
    public function getView()
    {
        return $this->_get(self::VIEW);
    }

    /**
     * Set view
     * @param string $view
     * @return \Backend\Medical\Api\Data\MedicalInterface
     */
    public function setView($view)
    {
        return $this->setData(self::VIEW, $view);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Backend\Medical\Api\Data\MedicalExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Backend\Medical\Api\Data\MedicalExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Backend\Medical\Api\Data\MedicalExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}

