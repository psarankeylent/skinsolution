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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\Option;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department\Option\Repository;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\Option
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var Repository
     */
    private $departmentOptionRepository;

    /**
     * @param Repository $departmentOptionRepository
     */
    public function __construct(
        Repository $departmentOptionRepository
    ) {
        $this->departmentOptionRepository = $departmentOptionRepository;
    }

    /**
     * Perform action on relation/extension attribute
     *
     * @param DepartmentInterface $entity
     * @param array $arguments
     * @return object
     * @throws \Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if (!$entity->getId()) {
            return $entity;
        }

        $this->departmentOptionRepository->deleteByDepartmentId($entity->getId());
        $this->departmentOptionRepository->save($entity->getOptions(), $entity->getId());

        return $entity;
    }
}
