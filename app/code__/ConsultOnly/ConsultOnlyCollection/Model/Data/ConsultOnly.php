<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ConsultOnly\ConsultOnlyCollection\Model\Data;

use ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyInterface;

class ConsultOnly extends \Magento\Framework\Api\AbstractExtensibleObject implements ConsultOnlyInterface
{

    /**
     * Get consultonly_id
     * @return string|null
     */
    public function getConsultonlyId()
    {
        return $this->_get(self::CONSULTONLY_ID);
    }

    /**
     * Set consultonly_id
     * @param string $consultonlyId
     * @return \ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyInterface
     */
    public function setConsultonlyId($consultonlyId)
    {
        return $this->setData(self::CONSULTONLY_ID, $consultonlyId);
    }

    /**
     * Get name
     * @return string|null
     */
    public function getName()
    {
        return $this->_get(self::NAME);
    }

    /**
     * Set name
     * @param string $name
     * @return \ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyInterface
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}

