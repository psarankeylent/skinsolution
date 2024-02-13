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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Relation\Tag;

use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Tag\Relation\Loader as TagLoader;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;

/**
 * Class ReadHandler
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Relation\Tag
 */
class ReadHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    private $metadataPool;

    /**
     * @var TagLoader
     */
    private $tagLoader;

    /**
     * @param MetadataPool $metadataPool
     * @param TagLoader $tagLoader
     */
    public function __construct(
        MetadataPool $metadataPool,
        TagLoader $tagLoader
    ) {
        $this->metadataPool = $metadataPool;
        $this->tagLoader = $tagLoader;
    }

    /**
     * {@inheritdoc}
     */
    public function execute($entity, $arguments = [])
    {
        if ($entityId = (int)$entity->getId()) {
            $tagNames = $this->tagLoader->loadForOneTicket($entityId);
            $entity->setTagNames($tagNames);
        }
        return $entity;
    }
}
