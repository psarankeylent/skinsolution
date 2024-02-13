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
namespace Aheadworks\Bup\Api\Data;

use Magento\User\Api\Data\UserInterface as MagentoUserInterface;

/**
 * Interface UserInterface
 *
 * @package Aheadworks\Bup\Api\Data
 */
interface UserInterface extends MagentoUserInterface
{
    /**
     * Profile for magento backend user
     */
    const AW_BUP_USER_PROFILE = 'aw_bup_user_profile';
}
