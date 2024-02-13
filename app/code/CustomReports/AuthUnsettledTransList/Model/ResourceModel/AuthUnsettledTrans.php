<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace CustomReports\AuthUnsettledTransList\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class AuthUnsettledTrans extends AbstractDb
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('authnet_unsettled_transaction_list', 'trans_id');
    }

}

