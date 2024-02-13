<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\OrderItemReport\Model;

use Magento\Framework\Model\AbstractModel;

class OrderItemReport extends AbstractModel
{
    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Renewal\OrderItemReport\Model\ResourceModel\OrderItemReport::class);
    }

}

