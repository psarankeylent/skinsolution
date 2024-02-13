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
namespace Aheadworks\Helpdesk2\Plugin\Eav\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\DataObject;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\DB\Sql\UnionExpression;
use Magento\Eav\Model\Config as EavConfig;
use Magento\Eav\Model\ResourceModel\ReadSnapshot;
use Magento\Store\Model\Store;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Attribute as AttributeModel;

/**
 * Class ReadSnapshotPlugin
 *
 * @package Aheadworks\Helpdesk2\Plugin\Eav\Model
 */
class ReadSnapshotPlugin
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var EavConfig
     */
    private $config;

    /**
     * @param MetadataPool $metadataPool
     * @param EavConfig $config
     */
    public function __construct(
        MetadataPool $metadataPool,
        EavConfig $config
    ) {
        $this->metadataPool = $metadataPool;
        $this->config = $config;
    }

    /**
     * Extend Eav ReadSnapshot by adding data from ticket attributes with global scope
     *
     * @param ReadSnapshot $subject
     * @param array $entityData
     * @param string $entityType
     * @return array
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterExecute(ReadSnapshot $subject, array $entityData, $entityType)
    {
        if (!in_array($entityType, [TicketInterface::class], true)) {
            return $entityData;
        }

        $metadata = $this->metadataPool->getMetadata($entityType);
        $connection = $metadata->getEntityConnection();
        $globalAttributes = [];
        $attributesMap = [];
        $eavEntityType = $metadata->getEavEntityType();
        $attributes = null === $eavEntityType
            ? []
            : $this->config->getEntityAttributes($eavEntityType, new DataObject($entityData));

        /** @var AttributeModel $attribute */
        foreach ($attributes as $attribute) {
            if (!$attribute->isStatic() && $attribute->isScopeGlobal()) {
                $globalAttributes[$attribute->getBackend()->getTable()][] = $attribute->getAttributeId();
                $attributesMap[$attribute->getAttributeId()] = $attribute->getAttributeCode();
            }
        }

        if ($globalAttributes) {
            $selects = [];
            foreach ($globalAttributes as $table => $attributeIds) {
                $select = $connection->select()
                    ->from(
                        ['t' => $table],
                        ['value' => 't.value', 'attribute_id' => 't.attribute_id']
                    )
                    ->where($metadata->getLinkField() . ' = ?', $entityData[$metadata->getLinkField()])
                    ->where('attribute_id' . ' in (?)', $attributeIds)
                    ->where('store_id = ?', Store::DEFAULT_STORE_ID);
                $selects[] = $select;
            }
            $unionSelect = new UnionExpression($selects, Select::SQL_UNION_ALL);
            foreach ($connection->fetchAll($unionSelect) as $attributeValue) {
                $entityData[$attributesMap[$attributeValue['attribute_id']]] = $attributeValue['value'];
            }
        }

        return $entityData;
    }
}
