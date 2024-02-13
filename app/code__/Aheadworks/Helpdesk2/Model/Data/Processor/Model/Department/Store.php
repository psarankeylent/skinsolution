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
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Model\Department;

use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\Department;
use Aheadworks\Helpdesk2\Model\Data\Processor\Model\ProcessorInterface;
use Magento\Store\Model\Store as MagentoStore;

/**
 * Class Store
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Model\Department
 */
class Store implements ProcessorInterface
{
    /**
     * Prepare model before save
     *
     * @param Department $department
     * @return Department
     */
    public function prepareModelBeforeSave($department)
    {
        if (in_array(MagentoStore::DEFAULT_STORE_ID, $department[DepartmentInterface::STORE_IDS])) {
            $department[DepartmentInterface::STORE_IDS] = [MagentoStore::DEFAULT_STORE_ID];
        }

        return $department;
    }

    /**
     * Prepare model after save
     *
     * @param Department $department
     * @return Department
     */
    public function prepareModelAfterLoad($department)
    {
        return $department;
    }
}
