<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\StoreCreditReport\Model;

use Magento\Framework\Model\AbstractModel;

class StoreCreditReport extends AbstractModel
{
    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Renewal\StoreCreditReport\Model\ResourceModel\StoreCreditReport::class);
    }

}

