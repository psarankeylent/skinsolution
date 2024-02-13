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
namespace Aheadworks\Helpdesk2\Model\ThirdPartyModule;

use Magento\Framework\Module\ModuleListInterface;

/**
 * Class ModuleChecker
 *
 * @package Aheadworks\Helpdesk2\Model\ThirdPartyModule
 */
class ModuleChecker
{
    /**
     * @var ModuleListInterface
     */
    private $moduleList;

    /**
     * @param ModuleListInterface $moduleList
     */
    public function __construct(
        ModuleListInterface $moduleList
    ) {
        $this->moduleList = $moduleList;
    }

    /**
     * Check if Aheadworks Coupon Code Generator module enabled
     *
     * @return bool
     */
    public function isAwCouponCodeGeneratorEnabled()
    {
        return $this->moduleList->has('Aheadworks_Coupongenerator');
    }

    /**
     * Check if Aheadworks Customer Attributes module enabled
     *
     * @return bool
     */
    public function isAwCustomerAttributesEnabled()
    {
        return $this->moduleList->has('Aheadworks_CustomerAttributes');
    }

    /**
     * Check if Help Desk module enabled
     *
     * @return bool
     */
    public function isAwHelpDesk1Enabled()
    {
        return $this->moduleList->has('Aheadworks_Helpdesk');
    }
}
