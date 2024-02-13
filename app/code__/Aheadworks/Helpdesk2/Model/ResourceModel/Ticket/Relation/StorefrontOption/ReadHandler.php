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

use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketOptionInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\TicketOptionInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket as TicketResourceModel;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Relation\StorefrontOption
 */
class ReadHandler implements ExtensionInterface
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
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var TicketOptionInterfaceFactory
     */
    private $ticketOptionFactory;

    /**
     * @param MetadataPool $metadataPool
     * @param ResourceConnection $resourceConnection
     * @param DataObjectHelper $dataObjectHelper
     * @param TicketOptionInterfaceFactory $ticketOptionFactory
     */
    public function __construct(
        MetadataPool $metadataPool,
        ResourceConnection $resourceConnection,
        DataObjectHelper $dataObjectHelper,
        TicketOptionInterfaceFactory $ticketOptionFactory
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->metadataPool = $metadataPool;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->ticketOptionFactory = $ticketOptionFactory;
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function execute($entity, $arguments = [])
    {
        /** @var TicketInterface $entity */
        if (!$entity->getEntityId()) {
            return $entity;
        }

        $connection = $this->resourceConnection->getConnectionByName(
            $this->metadataPool->getMetadata(TicketInterface::class)->getEntityConnectionName()
        );
        $select = $connection->select()
            ->from($this->resourceConnection->getTableName(TicketResourceModel::TICKET_OPTION_TABLE_NAME))
            ->where('ticket_id = :id');
        $optionRows = $connection->fetchAll($select, ['id' => $entity->getEntityId()]);

        $options = [];
        foreach ($optionRows as $optionRow) {
            /** @var TicketOptionInterface $option */
            $option = $this->ticketOptionFactory->create();
            $this->dataObjectHelper->populateWithArray($option, $optionRow, TicketOptionInterface::class);
            $options[] = $option;
        }
        $entity->setOptions($options);

        return $entity;
    }
}
