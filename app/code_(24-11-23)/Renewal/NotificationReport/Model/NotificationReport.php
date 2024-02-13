<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\NotificationReport\Model;

use Magento\Framework\Model\AbstractModel;
use Renewal\NotificationReport\Api\Data\NotificationReportInterface;

class NotificationReport extends AbstractModel implements NotificationReportInterface
{

    /**
     * @inheritDoc
     */
    public function _construct()
    {
        $this->_init(\Renewal\NotificationReport\Model\ResourceModel\NotificationReport::class);
    }

    /**
     * @inheritDoc
     */
    public function getNotificationreportId()
    {
        return $this->getData(self::NOTIFICATIONREPORT_ID);
    }

    /**
     * @inheritDoc
     */
    public function setNotificationreportId($notificationreportId)
    {
        return $this->setData(self::NOTIFICATIONREPORT_ID, $notificationreportId);
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }
}

