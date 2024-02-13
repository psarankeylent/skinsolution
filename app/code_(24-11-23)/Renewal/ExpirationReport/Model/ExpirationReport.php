<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\ExpirationReport\Model;

use Magento\Framework\Model\AbstractModel;

class ExpirationReport extends AbstractModel
{
    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Renewal\ExpirationReport\Model\ResourceModel\ExpirationReport::class);
    }

}

