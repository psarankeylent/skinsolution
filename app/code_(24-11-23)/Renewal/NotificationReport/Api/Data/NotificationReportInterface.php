<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\NotificationReport\Api\Data;

interface NotificationReportInterface
{

    const ID = 'id';
    const NOTIFICATIONREPORT_ID = 'notificationreport_id';

    /**
     * Get notificationreport_id
     * @return string|null
     */
    public function getNotificationreportId();

    /**
     * Set notificationreport_id
     * @param string $notificationreportId
     * @return \Renewal\NotificationReport\NotificationReport\Api\Data\NotificationReportInterface
     */
    public function setNotificationreportId($notificationreportId);

    /**
     * Get id
     * @return string|null
     */
    public function getId();

    /**
     * Set id
     * @param string $id
     * @return \Renewal\NotificationReport\NotificationReport\Api\Data\NotificationReportInterface
     */
    public function setId($id);
}

