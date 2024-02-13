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
 * @package    Helpdesk2GraphQl
 * @version    1.0.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Helpdesk2GraphQl\Model\Resolver;

use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\Department\Search\Builder as DepartmentSearch;
use Aheadworks\Helpdesk2GraphQl\Model\ObjectConverter;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class AvailableDepartmentsResolver
 *
 * @package Aheadworks\Helpdesk2GraphQl\Model\Resolver
 */
class AvailableDepartmentsResolver extends AbstractResolver
{
    /**
     * @var DepartmentSearch
     */
    private $departmentSearch;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ObjectConverter
     */
    private $objectConverter;

    /**
     * @param DepartmentSearch $departmentSearch
     * @param StoreManagerInterface $storeManager
     * @param ObjectConverter $objectConverter
     */
    public function __construct(
        DepartmentSearch $departmentSearch,
        StoreManagerInterface $storeManager,
        ObjectConverter $objectConverter
    ) {
        $this->departmentSearch = $departmentSearch;
        $this->storeManager = $storeManager;
        $this->objectConverter = $objectConverter;
    }

    /**
     * @inheritDoc
     */
    protected function performResolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        $storeId = $args['store_id'] ?? null;
        if (!$storeId) {
            $this->storeManager->getStore()->getId();
        }

        $this->departmentSearch->addIsActiveFilter();
        $this->departmentSearch->addStoreFilter($storeId);
        $departments = $this->departmentSearch->searchDepartments($storeId);

        $data = [];
        foreach ($departments as $department) {
            $data[] = $this->objectConverter->convertToArray($department, DepartmentInterface::class);
        }

        return $data;
    }
}
