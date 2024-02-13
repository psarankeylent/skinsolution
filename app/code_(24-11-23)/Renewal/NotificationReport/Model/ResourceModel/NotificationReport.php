<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\NotificationReport\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class NotificationReport extends AbstractDb
{

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init('rxschedule_log', 'id');
    }
}

