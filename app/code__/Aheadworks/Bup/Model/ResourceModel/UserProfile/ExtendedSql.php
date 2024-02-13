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

use Magento\Framework\DB\Select;
use Magento\Framework\App\ResourceConnection;
use Aheadworks\Bup\Api\Data\UserProfileMetadataInterface;
use Aheadworks\Bup\Model\Source\UserProfile\Area as UserProfileArea;

/**
 * Class ExtendedSql
 *
 * @package Aheadworks\Bup\Model\ResourceModel\UserProfile
 */
class ExtendedSql
{
    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @param ResourceConnection $resource
     */
    public function __construct(
        ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    /**
     * Extend user profile select
     *
     * @param Select $sqlSelect
     * @param string $area
     */
    public function extendUserProfileSelect($sqlSelect, $area = UserProfileArea::STOREFRONT)
    {
        $sqlSelect->columns([
            UserProfileMetadataInterface::USER_ID => 'admin_user.user_id',
            UserProfileMetadataInterface::STATUS => $this->prepareStatusColumn($area),
            UserProfileMetadataInterface::DISPLAY_NAME => $this->prepareDisplayNameColumn($area),
            UserProfileMetadataInterface::EMAIL => 'main_table.email',
            UserProfileMetadataInterface::PHONE_NUMBER => 'main_table.phone_number',
            UserProfileMetadataInterface::IMAGE => 'main_table.image',
            UserProfileMetadataInterface::ADDITIONAL_INFORMATION => 'main_table.additional_information'
        ]);

        $sqlSelect->joinRight(
            ['admin_user' => $this->resource->getTableName('admin_user')],
            'main_table.user_id = admin_user.user_id',
            []
        );

        if ($area == UserProfileArea::HELPDESK2) {
            $sqlSelect->where('admin_user.is_active = ?', 1);
        }
    }

    /**
     * Prepare status column
     *
     * @param string $area
     * @return string
     */
    private function prepareStatusColumn($area)
    {
        return $area == UserProfileArea::HELPDESK2
            ? 'main_table.status'
            : 'IF(main_table.status = 1 AND admin_user.is_active = 1, 1, 0)';
    }

    /**
     * Prepare display name column
     *
     * @param string $area
     * @return string
     */
    private function prepareDisplayNameColumn($area)
    {
        return $area == UserProfileArea::STOREFRONT
            ? 'main_table.display_name'
            : 'CONCAT(admin_user.firstname, " ", admin_user.lastname)';
    }
}
