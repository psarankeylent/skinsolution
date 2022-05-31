<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Ssmd\Attributes\Model\ResourceModel\Prescriptions;

/**
 * WPPagess Collection
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Initialize resource collection
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Ssmd\Attributes\Model\Prescriptions', 'Ssmd\Attributes\Model\ResourceModel\Prescriptions');
    }
}
