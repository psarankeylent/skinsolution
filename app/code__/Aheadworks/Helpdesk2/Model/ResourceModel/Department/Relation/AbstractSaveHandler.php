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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation;

use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;

/**
 * Class AbstractSaveHandler
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Department\Relation
 */
abstract class AbstractSaveHandler extends AbstractHandler
{
    /**
     * Remove old child entity by ID
     *
     * @param int $id
     * @return int
     * @throws \Exception
     */
    protected function deleteOldChildEntityById($id)
    {
        return $this->getConnection()->delete(
            $this->tableName,
            ['department_id = ?' => $id]
        );
    }

    /**
     * Insert child entity
     *
     * @param array $toInsert
     * @return $this
     * @throws \Exception
     */
    protected function insertChildEntity($toInsert)
    {
        if (!empty($toInsert)) {
            $this->getConnection()->insertMultiple($this->tableName, $toInsert);
        }

        return $this;
    }
}
