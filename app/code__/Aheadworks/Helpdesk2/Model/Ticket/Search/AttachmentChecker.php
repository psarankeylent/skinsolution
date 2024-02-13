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
namespace Aheadworks\Helpdesk2\Model\Ticket\Search;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message as MessageResource;

/**
 * Class AttachmentChecker
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Search
 */
class AttachmentChecker
{
    /**
     * @var MessageResource
     */
    private $messageResource;

    /**
     * @param MessageResource $messageResource
     */
    public function __construct(
        MessageResource $messageResource
    ) {
        $this->messageResource = $messageResource;
    }

    /**
     * Check if attachment belong to ticket
     *
     * @param int $attachmentId
     * @param int $ticketId
     * @return bool
     * @throws LocalizedException
     */
    public function isAttachmentBelongToTicket($attachmentId, $ticketId)
    {
        return $ticketId == $this->messageResource->getTicketIdByAttachmentId($attachmentId);
    }
}
