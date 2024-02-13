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
use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department\Option\Repository;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation\Option
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var Repository
     */
    private $departmentOptionRepository;

    /**
     * @param DataObjectProcessor $dataObjectProcessor
     * @param Repository $departmentOptionRepository
     */
    public function __construct(
        DataObjectProcessor $dataObjectProcessor,
        Repository $departmentOptionRepository
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->departmentOptionRepository = $departmentOptionRepository;
    }

    /**
     * Perform action on relation/extension attribute
     *
     * @param DepartmentInterface $entity
     * @param array $arguments
     * @return object
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        if (!$entity->getId()) {
            return $entity;
        }

        $options = $this->departmentOptionRepository->getByDepartmentId($entity->getId(), $arguments['store_id']);
        $entity->setOptions($options);

        return $entity;
    }
}
