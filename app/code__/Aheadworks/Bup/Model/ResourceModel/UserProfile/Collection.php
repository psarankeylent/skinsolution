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

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Aheadworks\Bup\Model\UserProfile;
use Aheadworks\Bup\Model\ResourceModel\UserProfile as UserProfileResource;

/**
 * Class Collection
 *
 * @package Aheadworks\Bup\Model\ResourceModel\UserProfile
 */
class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    public function _construct()
    {
        $this->_init(UserProfile::class, UserProfileResource::class);
    }
}
