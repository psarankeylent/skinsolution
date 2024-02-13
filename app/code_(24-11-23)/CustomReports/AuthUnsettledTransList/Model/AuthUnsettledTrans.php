<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CustomReports\AuthUnsettledTransList\Model;

use Magento\Framework\Model\AbstractModel;

class AuthUnsettledTrans extends AbstractModel
{
    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\CustomReports\AuthUnsettledTransList\Model\ResourceModel\AuthUnsettledTrans::class);
    }

}

