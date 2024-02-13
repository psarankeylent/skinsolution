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
namespace Aheadworks\Helpdesk2\Model\Config\Backend;

use Aheadworks\Helpdesk2\Model\Source\Config\Order\Status as OrderStatusSource;
use Magento\CatalogInventory\Model\System\Config\Backend\Minqty;

/**
 * Class OrderStatuses
 *
 * @package Aheadworks\Helpdesk2\Model\Config\Backend
 */
class OrderStatuses extends \Magento\Framework\App\Config\Value
{
    /**
     * Prepare value before save
     *
     * @return $this
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if (in_array(OrderStatusSource::ALL, $value)) {
            $this->setValue([OrderStatusSource::ALL]);
        }
        parent::beforeSave();

        return $this;
    }
}
