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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Email;

use Aheadworks\Helpdesk2\Api\Data\EmailInterface;
use Aheadworks\Helpdesk2\Api\Data\EmailAttachmentInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\AbstractCollection;
use Aheadworks\Helpdesk2\Model\Gateway\Email as GatewayEmailModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Email as GatewayEmailResourceModel;
use Aheadworks\Helpdesk2\Model\Source\Gateway\Email\Status as EmailStatus;

/**
 * Class Collection
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Gateway
 */
class Collection extends AbstractCollection
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(GatewayEmailModel::class, GatewayEmailResourceModel::class);
    }

    /**
     * Add unprocessed filter
     *
     * @return $this
     */
    public function addUnprocessedFilter()
    {
        $this->addFieldToFilter(EmailInterface::STATUS, EmailStatus::UNPROCESSED);
        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function _afterLoad()
    {
        $this->attachRelationTable(
            GatewayEmailResourceModel::EMAIL_ATTACHMENT_TABLE_NAME,
            EmailInterface::ID,
            'email_id',
            [EmailAttachmentInterface::FILE_NAME, EmailAttachmentInterface::FILE_PATH],
            EmailInterface::ATTACHMENTS,
            [],
            [],
            true
        );

        return parent::_afterLoad();
    }
}
