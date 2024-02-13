<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\NotificationReport\Model\ResourceModel\NotificationReport;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    /**
     * @inheritDoc
     */
    protected $_idFieldName = 'id';

    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(
            \Renewal\NotificationReport\Model\NotificationReport::class,
            \Renewal\NotificationReport\Model\ResourceModel\NotificationReport::class
        );
    }
}

