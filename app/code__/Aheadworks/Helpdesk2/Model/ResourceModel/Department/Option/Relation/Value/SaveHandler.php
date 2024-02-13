<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Helpdesk2
 * @version    2.0.6
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Department\Option\Relation\Value;

use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Aheadworks\Helpdesk2\Api\Data\DepartmentOptionInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentOptionValueInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentOptionValueInterfaceFactory;
use Aheadworks\Helpdesk2\Model\Department\Option\Config as OptionConfig;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Department\Option\Relation\Value
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var EntityManager
     */
    private $entityManager;

    /**
     * @var OptionConfig
     */
    private $optionConfig;

    /**
     * @param EntityManager $entityManager
     * @param OptionConfig $optionConfig
     */
    public function __construct(
        EntityManager $entityManager,
        OptionConfig $optionConfig
    ) {
        $this->entityManager = $entityManager;
        $this->optionConfig = $optionConfig;
    }

    /**
     * Perform action on relation/extension attribute
     *
     * @param DepartmentOptionInterface $entity
     * @param array $arguments
     * @return object
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($this->isAllowSaveOptionValues($entity)) {
            $this->saveOptionValues($entity);
        }

        return $entity;
    }

    /**
     * Check if allow to save option values
     *
     * @param DepartmentOptionInterface $entity
     * @return bool
     */
    private function isAllowSaveOptionValues($entity)
    {
        $selectTypeOptions = $this->optionConfig
            ->getTypesByGroup(DepartmentOptionInterface::OPTION_GROUP_SELECT);

        return in_array($entity->getType(), $selectTypeOptions);
    }

    /**
     * Save option values
     *
     * @param DepartmentOptionInterface $entity
     * @throws CouldNotSaveException
     */
    private function saveOptionValues($entity)
    {
        $values = $entity->getValues();
        /** @var DepartmentOptionValueInterface $value */
        foreach ($values as $value) {
            try {
                $value
                    ->setId(null)
                    ->setOptionId($entity->getId());
                $this->entityManager->save($value);
            } catch (\Exception $e) {
                throw new CouldNotSaveException(__('Could not save option values.'));
            }
        }
    }
}
