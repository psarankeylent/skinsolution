<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Helpdesk2
 * @version    2.0.6
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Gateway;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Api\Data\EmailInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\AbstractResourceModel;

/**
 * Class Email
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Gateway
 */
class Email extends AbstractResourceModel
{
    /**#@+
     * Constants defined for table names
     */
    const MAIN_TABLE_NAME = 'aw_helpdesk2_gateway_email';
    const EMAIL_ATTACHMENT_TABLE_NAME = 'aw_helpdesk2_gateway_email_attachment';
    /**#@-*/

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, EmailInterface::ID);
    }

    /**
     * Check if email exists by UID
     *
     * @param string $mailUid
     * @return bool
     * @throws LocalizedException
     */
    public function isMailExistByMailUid($mailUid)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), 'COUNT(id)')
            ->where('uid = ?', $mailUid);

        return (bool)$connection->fetchOne($select);
    }

    /**
     * Get mail UIDs by gateway
     *
     * @param int $gatewayId
     * @param string $gatewayEmail
     * @return array
     * @throws LocalizedException
     */
    public function getMailUIDsByGateway($gatewayId, $gatewayEmail)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), "REPLACE(uid, '" . $gatewayEmail . "', '')")
            ->where('gateway_id = ?', $gatewayId);

        return $connection->fetchCol($select);
    }

    /**
     * Get last saved mail UID by gateway
     *
     * @param int $gatewayId
     * @param string $gatewayEmail
     * @return string
     * @throws LocalizedException
     */
    public function getLastSavedMailUIDByGateway($gatewayId, $gatewayEmail)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getMainTable(), "REPLACE(uid, '" . $gatewayEmail . "', '')")
            ->where('gateway_id = ?', $gatewayId)
            ->order('id DESC');

        return $connection->fetchOne($select);
    }
}
