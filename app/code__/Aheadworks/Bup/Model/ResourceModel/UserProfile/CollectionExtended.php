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
 * @package    Bup
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Bup\Model\ResourceModel\UserProfile;

use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\DB\Select;
use Aheadworks\Bup\Model\Source\UserProfile\Area as UserProfileArea;
use Aheadworks\Bup\Model\UserProfileMetadata;
use Aheadworks\Bup\Model\ResourceModel\UserProfile as UserProfileResource;

/**
 * Class CollectionExtended
 *
 * @package Aheadworks\Bup\Model\ResourceModel\UserProfile
 */
class CollectionExtended extends Collection
{
    /**
     * @var ExtendedSql
     */
    protected $extendedSql;

    /**
     * @var string
     */
    private $area;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param \Aheadworks\Bup\Model\ResourceModel\UserProfile\ExtendedSql $extendedSql
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        ExtendedSql $extendedSql,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->extendedSql = $extendedSql;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * @inheritdoc
     */
    public function _construct()
    {
        $this->_init(UserProfileMetadata::class, UserProfileResource::class);
    }

    /**
     * Set user profile area
     *
     * @param string $area
     */
    public function setUserProfileArea($area)
    {
        $this->area = $area;
    }

    /**
     * Get user profile area
     *
     * @return string
     */
    public function getUserProfileArea()
    {
        return $this->area ?? UserProfileArea::STOREFRONT;
    }

    /**
     * Reset collection.
     *
     * @return $this
     */
    public function reset()
    {
        $this->_reset();
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function _initSelect()
    {
        parent::_initSelect();

        $select = $this->getSelect();
        $select->reset(Select::COLUMNS);

        $this->extendedSql->extendUserProfileSelect($select, $this->getUserProfileArea());
        return $this;
    }
}
