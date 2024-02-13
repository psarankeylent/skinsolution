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
namespace Aheadworks\Helpdesk2\Model\Department\Option;

use Magento\Framework\Config\Data as ConfigData;

/**
 * Class Config
 *
 * @package Aheadworks\Helpdesk2\Model\Department\Option
 */
class Config extends ConfigData
{
    /**
     * Get configuration of all registered storefront options
     *
     * @return array
     */
    public function getAll()
    {
        return $this->get();
    }

    /**
     * Retrieve types by group
     *
     * @param string $groupCode
     * @return array
     */
    public function getTypesByGroup($groupCode)
    {
        return array_keys($this->get($groupCode . '/types'));
    }
}
