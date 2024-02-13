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
namespace Aheadworks\Helpdesk2\Model\ResourceModel;

use Magento\Framework\DB\Select;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection as FrameworkAbstractCollection;

/**
 * Class AbstractCollection
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel
 */
class AbstractCollection extends FrameworkAbstractCollection
{
    /**
     * @var string[]
     */
    private $linkageTableNames = [];

    /**
     * Attach entity table data to collection items
     *
     * @param string|Select $table
     * @param string $columnName
     * @param string $linkageColumnName
     * @param string|array $columnNameRelationTable
     * @param string $fieldName
     * @param array $conditions
     * @param array $order
     * @param bool $setDataAsArray
     * @return $this
     * phpcs:disable Generic.Metrics.NestingLevel
     */
    protected function attachRelationTable(
        $table,
        $columnName,
        $linkageColumnName,
        $columnNameRelationTable,
        $fieldName,
        $conditions = [],
        $order = [],
        $setDataAsArray = false
    ) {
        $ids = $this->getColumnValues($columnName);
        if (count($ids)) {
            $connection = $this->getConnection();
            $select = $table instanceof Select
                ? $table
                : $connection->select()->from(['tmp_table' => $this->getTable($table)]);

            $select->where('tmp_table.' . $linkageColumnName . ' IN (?)', $ids);

            foreach ($conditions as $condition) {
                $select->where(
                    'tmp_table.' . $condition['field'] . ' ' . $condition['condition'] . ' (?)',
                    $condition['value']
                );
            }

            if (!empty($order)) {
                $select->order('tmp_table.' . $order['field'] . ' ' . $order['direction']);
            }
            /** @var \Magento\Framework\DataObject $item */
            foreach ($this as $item) {
                $result = [];
                $id = $item->getData($columnName);
                $selectedData = $connection->fetchAll($select);
                foreach ($selectedData as $data) {
                    if ($data[$linkageColumnName] == $id) {
                        if (is_array($columnNameRelationTable)) {
                            $fieldValue = [];
                            foreach ($columnNameRelationTable as $columnNameRelation) {
                                $fieldValue[$columnNameRelation] = $data[$columnNameRelation];
                            }
                            $result[] = $fieldValue;
                        } else {
                            $result[] = $data[$columnNameRelationTable];
                        }
                    }
                }
                $fieldData = $setDataAsArray ? $result : array_shift($result);
                $item->setData($fieldName, $fieldData);
            }
        }

        return $this;
    }

    /**
     * Join to linkage table if filter is applied
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $linkageColumnName
     * @param string $columnFilter
     * @param string $fieldName
     * @return $this
     */
    public function joinLinkageTable(
        $tableName,
        $columnName,
        $linkageColumnName,
        $columnFilter,
        $fieldName
    ) {
        if ($this->getFilter($columnFilter)) {
            $linkageTableName = $columnFilter . '_table';
            if (in_array($linkageTableName, $this->linkageTableNames)) {
                $this->addFilterToMap($columnFilter, $columnFilter . '_table.' . $fieldName);
                return $this;
            }

            $this->linkageTableNames[] = $linkageTableName;
            $select = $this->getSelect();
            $select->joinLeft(
                [$linkageTableName => $this->getTable($tableName)],
                'main_table.' . $columnName . ' = ' . $linkageTableName . '.' . $linkageColumnName,
                []
            );

            $this->addFilterToMap($columnFilter, $columnFilter . '_table.' . $fieldName);
        }

        return $this;
    }
}
