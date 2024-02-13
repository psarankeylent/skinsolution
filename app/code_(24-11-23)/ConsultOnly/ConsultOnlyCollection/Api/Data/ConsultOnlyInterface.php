<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace ConsultOnly\ConsultOnlyCollection\Api\Data;

interface ConsultOnlyInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{

    const CONSULTONLY_ID = 'consultonly_id';
    const NAME = 'name';

    /**
     * Get consultonly_id
     * @return string|null
     */
    public function getConsultonlyId();

    /**
     * Set consultonly_id
     * @param string $consultonlyId
     * @return \ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyInterface
     */
    public function setConsultonlyId($consultonlyId);

    /**
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return \ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyInterface
     */
    public function setName($name);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \ConsultOnly\ConsultOnlyCollection\Api\Data\ConsultOnlyExtensionInterface $extensionAttributes
    );
}

