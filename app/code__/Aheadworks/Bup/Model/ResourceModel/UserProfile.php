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
namespace Aheadworks\Bup\Model\ResourceModel;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\Context as DbContext;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Aheadworks\Bup\Api\Data\UserProfileInterface;
use Aheadworks\Bup\Model\Source\UserProfile\Area as UserProfileArea;
use Aheadworks\Bup\Model\ResourceModel\UserProfile\ExtendedSql;

/**
 * Class UserProfile
 *
 * @package Aheadworks\Bup\Model\ResourceModel
 */
class UserProfile extends AbstractDb
{
    /**
     * Constant defined for table name
     */
    const MAIN_TABLE_NAME = 'aw_bup_user_profile';

    /**
     * @var ExtendedSql
     */
    protected $extendedSql;

    /**
     * @var string
     */
    private $area;

    /**
     * @param DbContext $context
     * @param ExtendedSql $extendedSql
     * @param null $connectionName
     */
    public function __construct(
        DbContext $context,
        ExtendedSql $extendedSql,
        $connectionName = null
    ) {
        $this->extendedSql = $extendedSql;
        parent::__construct($context, $connectionName);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_isPkAutoIncrement = false;
        $this->_init(self::MAIN_TABLE_NAME, UserProfileInterface::USER_ID);
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
     * Load extended user profile with admin user info
     *
     * @param int $userId
     * @return array
     * @throws LocalizedException
     */
    public function loadExtended($userId)
    {
        if ($userId) {
            $connection = $this->getConnection();
            $select = $connection->select()
                ->from(['main_table' => $this->getTable($this->getMainTable())], [])
                ->where('admin_user.user_id = ?', $userId);

            $this->extendedSql->extendUserProfileSelect($select, $this->getUserProfileArea());
            $result = $this->getConnection()->fetchRow($select);
        } else {
            $result = [];
        }

        return $result;
    }
}
