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
namespace Aheadworks\Helpdesk2\Model;

use Magento\Backend\Model\UrlInterface as BackendUrlInterface;
use Magento\Framework\Url as FrontendUrlInterface;

/**
 * Class UrlBuilder
 *
 * @package Aheadworks\Helpdesk2\Model
 */
class UrlBuilder
{
    /**
     * @var BackendUrlInterface
     */
    private $backendUrlBuilder;

    /**
     * @var FrontendUrlInterface
     */
    private $frontendUrlBuilder;

    /**
     * @param BackendUrlInterface $backendUrlBuilder
     * @param FrontendUrlInterface $frontendUrlBuilder
     */
    public function __construct(
        BackendUrlInterface $backendUrlBuilder,
        FrontendUrlInterface $frontendUrlBuilder
    ) {
        $this->backendUrlBuilder = $backendUrlBuilder;
        $this->frontendUrlBuilder = $frontendUrlBuilder;
    }

    /**
     * Generate ticket external link
     *
     * @param string $externalLinkHash
     * @return string
     */
    public function getTicketExternalLink($externalLinkHash)
    {
        return $this->frontendUrlBuilder->getUrl('customer/ticket/external', ['key' => $externalLinkHash]);
    }

    /**
     * Generate backend customer profile link
     *
     * @param int $customerId
     * @return string
     */
    public function getBackendCustomerProfileLink($customerId)
    {
        return $this->backendUrlBuilder->getUrl('customer/index/edit', ['id' => $customerId]);
    }

    /**
     * Generate backend ticket view link
     *
     * @param int $ticketId
     * @return string
     */
    public function getBackendTicketViewLink($ticketId)
    {
        return $this->backendUrlBuilder->getUrl('customer/support/view', ['entity_id' => $ticketId]);
    }

    /**
     * Generate ticket close link
     *
     * @param string $externalLinkHash
     * @return string
     */
    public function getTicketCloseLink($externalLinkHash)
    {
        //return $this->frontendUrlBuilder->getUrl('customer/support/close', ['key' => $externalLinkHash]);
        return $this->frontendUrlBuilder->getUrl('customer/ticket/close', ['key' => $externalLinkHash]);
    }

    /**
     * Generate ticket reopen link
     *
     * @param string $externalLinkHash
     * @return string
     */
    public function getTicketReopenLink($externalLinkHash)
    {
        return $this->frontendUrlBuilder->getUrl('customer/support/reopen', ['key' => $externalLinkHash]);
    }

    /**
     * Generate ticket create link
     *
     * @param array $params
     * @return string
     */
    public function getTicketCreateLink($params = [])
    {
        return $this->frontendUrlBuilder->getUrl('customer/support/createTicket', $params);
    }

    /**
     * Generate ticket create link from backend
     *
     * @param array $params
     * @return string
     */
    public function getTicketCreateLinkFromBackend($params = [])
    {
        return $this->backendUrlBuilder->getUrl('aw_helpdesk2/ticket/create', $params);
        //return $this->backendUrlBuilder->getUrl('customer/support/create', $params);
    }

    /**
     * Generate download attachment link for frontend
     *
     * @param string $externalLinkHash
     * @param int $attachmentId
     * @return string
     */
    public function getFrontendDownloadAttachmentLink($externalLinkHash, $attachmentId)
    {
        return $this->frontendUrlBuilder->getUrl(
            'customer/ticket_attachment/download',
            [
                'key' => $externalLinkHash,
                'attachment_id' => $attachmentId
            ]
        );
    }

    /**
     * Generate download attachment link for backend
     *
     * @param int $attachmentId
     * @return string
     */
    public function getBackendDownloadAttachmentLink($attachmentId)
    {
        return $this->backendUrlBuilder->getUrl(
            'aw_helpdesk2/ticket_attachment/download',
            [
                'attachment_id' => $attachmentId
            ]
        );
    }

    /**
     * Generate ticket customer rating link
     *
     * @param string $externalLinkHash
     * @param int $rating
     * @return string
     */
    public function getTicketCustomerRatingLink($externalLinkHash, $rating)
    {
        return $this->frontendUrlBuilder->getUrl(
            'customer/ticket/rate',
            [
                'key' => $externalLinkHash,
                'rating' => $rating
            ]
        );
    }

    /**
     * Generate order link for backend
     *
     * @param int $orderId
     * @return string
     */
    public function getBackendOrderLink($orderId)
    {
        return $this->backendUrlBuilder->getUrl(
            'sales/order/view',
            [
                'order_id' => $orderId
            ]
        );
    }

    /**
     * Generate order link for frontend
     *
     * @param int $orderId
     * @return string
     */
    public function getFrontendOrderLink($orderId)
    {
        return $this->frontendUrlBuilder->getUrl(
            'sales/order/view',
            [
                'order_id' => $orderId
            ]
        );
    }

    /**
     * Generate DoNotShowAgain link for backend
     *
     * @return string
     */
    public function getBackendMigrationNotificationDoNotShowAgain()
    {
        return $this->backendUrlBuilder->getUrl(
            'aw_helpdesk2/migration/DoNotShowAgain'
        );
    }
}
