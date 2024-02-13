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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Relation\StorefrontOption;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\App\ResourceConnection;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketOptionInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket as TicketResourceModel;

/**
 * Class SaveHandler
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Relation\StorefrontOption
 */
class SaveHandler implements ExtensionInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(MetadataPool $metadataPool, ResourceConnection $resourceConnection)
    {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        /** @var TicketInterface $entity */
        $entityId = $entity->getEntityId();
        if (!$entityId || !is_array($entity->getOptions())) {
            return $entity;
        }
        $dataToInsert = [];
        /** @var TicketOptionInterface $option */
        foreach ($entity->getOptions() as $option) {
            $dataToInsert[] = [
                TicketOptionInterface::TICKET_ID => $entityId,
                TicketOptionInterface::ID => $option->getId(),
                TicketOptionInterface::LABEL => $option->getLabel(),
                TicketOptionInterface::VALUE => $option->getValue()
            ];
        }

        if ($dataToInsert) {
            $connection = $this->resourceConnection->getConnectionByName(
                $this->metadataPool->getMetadata(TicketInterface::class)->getEntityConnectionName()
            );
            $connection->insertMultiple(
                $this->resourceConnection->getTableName(TicketResourceModel::TICKET_OPTION_TABLE_NAME),
                $dataToInsert
            );
        }

        return $entity;
    }
}
