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

use Magento\Eav\Model\Entity\Collection\AbstractCollection as EavEntityAbstractCollection;
use Magento\Framework\DataObject;

/**
 * Class AbstractEavCollection
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel
 */
abstract class AbstractEavCollection extends EavEntityAbstractCollection
{
    /**
     * @var string[]
     */
    private $linkageTableNames = [];

    /**
     * Attach relation table data to collection items
     *
     * @param string $tableName
     * @param string $columnName
     * @param string $linkageColumnName
     * @param string|array $columnNameRelationTable
     * @param string $fieldName
     * @param string|null $sortOrderField
     * @return void
     */
    protected function attachRelationTable(
        $tableName,
        $columnName,
        $linkageColumnName,
        $columnNameRelationTable,
        $fieldName,
        $sortOrderField = null
    ) {
        $ids = $this->getColumnValues($columnName);
        if (count($ids)) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from([$tableName . '_table' => $this->getTable($tableName)])
                ->where($tableName . '_table.' . $linkageColumnName . ' IN (?)', $ids);
            if ($sortOrderField) {
                $select->order($sortOrderField . ' ASC');
            }
            $result = $connection->fetchAll($select);

            foreach ($this as $item) {
                $resultIds = $this->prepareItemData(
                    $item,
                    $result,
                    $columnName,
                    $linkageColumnName,
                    $columnNameRelationTable
                );
                $item->setData($fieldName, $resultIds);
            }
        }
    }

    /**
     * Prepare item data
     *
     * @param DataObject $item
     * @param array $sqlResult
     * @param string $columnName
     * @param string $linkageColumnName
     * @param string|array $columnNameRelationTable
     * @return array
     */
    private function prepareItemData(
        $item,
        $sqlResult,
        $columnName,
        $linkageColumnName,
        $columnNameRelationTable
    ) {
        $resultIds = [];
        $id = $item->getData($columnName);
        foreach ($sqlResult as $data) {
            if ($data[$linkageColumnName] == $id) {
                if (is_array($columnNameRelationTable)) {
                    $fieldValue = [];
                    foreach ($columnNameRelationTable as $columnNameRelation) {
                        $fieldValue[$columnNameRelation] = $data[$columnNameRelation];
                    }
                    $resultIds[] = $fieldValue;
                } else {
                    $resultIds[] = $data[$columnNameRelationTable];
                }
            }
        }
        return $resultIds;
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
    protected function joinLinkageTable(
        $tableName,
        $columnName,
        $linkageColumnName,
        $columnFilter,
        $fieldName
    ) {
        if ($this->getFilter($columnFilter)) {
            $linkageTableName = $this->generateLinkageTableName($columnFilter);
            if (in_array($linkageTableName, $this->linkageTableNames)) {
                $this->addFilterToMap($columnFilter, $linkageTableName . '.' . $fieldName);
                return $this;
            }

            $this->linkageTableNames[] = $linkageTableName;
            $select = $this->getSelect();
            $select->joinLeft(
                [$linkageTableName => $this->getTable($tableName)],
                'e.' . $columnName . ' = ' . $linkageTableName . '.' . $linkageColumnName,
                []
            );

            $this->addFilterToMap($columnFilter, $linkageTableName . '.' . $fieldName);
        }

        return $this;
    }

    /**
     * Generate linkage table name for join linkage table
     *
     * @param string $field
     * @return string
     */
    protected function generateLinkageTableName($field)
    {
        return $field . '_table';
    }
}
