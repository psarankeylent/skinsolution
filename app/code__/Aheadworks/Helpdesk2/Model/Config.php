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

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 *
 * @package Aheadworks\Helpdesk2\Model
 */
class Config
{
    const XML_PATH_GENERAL_PRIMARY_DEPARTMENT = 'aw_helpdesk2/general/primary_department';
    const XML_PATH_BACKEND_TICKET_PAGE_COUNT_OF_DISPLAYED_TICKETS =
        'aw_helpdesk2/backend_ticket_page/count_of_displayed_tickets';
    const XML_PATH_BACKEND_TICKET_PAGE_COUNT_OF_DISPLAYED_ORDERS =
        'aw_helpdesk2/backend_ticket_page/count_of_displayed_orders';
    const XML_PATH_BACKEND_TICKET_PAGE_DISPLAYED_ORDER_STATUSES =
        'aw_helpdesk2/backend_ticket_page/displayed_order_statuses';
    const XML_PATH_FRONTEND_IS_ENABLED_CONTACT_FORM_INTEGRATION =
        'aw_helpdesk2/frontend/is_enabled_contact_form_integration';
    const XML_PATH_FRONTEND_IS_ALLOWED_TO_ATTACH_FILES = 'aw_helpdesk2/frontend/is_allowed_to_attach_files';
    const XML_PATH_FRONTEND_MAX_UPLOAD_FILE_SIZE = 'aw_helpdesk2/frontend/max_upload_file_size';
    const XML_PATH_FRONTEND_ALLOWED_FILE_EXTENSIONS = 'aw_helpdesk2/frontend/allowed_file_extensions';
    const XML_PATH_TICKET_ESCALATION_IS_ENABLED = 'aw_helpdesk2/ticket_escalation/is_ticket_escalation_enabled';
    const XML_PATH_TICKET_ESCALATION_SUPERVISOR_EMAILS = 'aw_helpdesk2/ticket_escalation/supervisor_emails';
    const XML_PATH_TICKET_ESCALATION_EMAIL_TEMPLATE = 'aw_helpdesk2/ticket_escalation/email_template';
    const XML_PATH_IS_ENABLED_CUSTOMER_RATING =
        'aw_helpdesk2/general/is_enabled_customer_rating';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get primary department
     *
     * @param int|null $storeId
     * @return string
     */
    public function getPrimaryDepartment($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_PRIMARY_DEPARTMENT,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get display tickets count on backend ticket page
     *
     * @return int
     */
    public function getBackendTicketPageDisplayedTicketsCount()
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_BACKEND_TICKET_PAGE_COUNT_OF_DISPLAYED_TICKETS
        );
    }

    /**
     * Get display orders count on backend ticket page
     *
     * @return int
     */
    public function getBackendTicketPageDisplayedOrdersCount()
    {
        return (int)$this->scopeConfig->getValue(
            self::XML_PATH_BACKEND_TICKET_PAGE_COUNT_OF_DISPLAYED_ORDERS
        );
    }

    /**
     * Get display order statuses
     *
     * @return array
     */
    public function getBackendTicketPageDisplayedOrderStatuses()
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_BACKEND_TICKET_PAGE_DISPLAYED_ORDER_STATUSES
        );
        if (is_string($value)) {
            $value = explode(',', $value);
        } elseif (null == $value) {
            $value = [];
        }

        return $value;
    }

    /**
     * Is enabled contact form integration
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabledContactFormIntegration($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_FRONTEND_IS_ENABLED_CONTACT_FORM_INTEGRATION,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Is enabled customer rating
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabledCustomerRating($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_IS_ENABLED_CUSTOMER_RATING,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if it's allowed to attach files
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isAllowedToAttachFiles($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_FRONTEND_IS_ALLOWED_TO_ATTACH_FILES,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Retrieve max upload file size
     *
     * @param int|null $storeId
     * @return int
     */
    public function getMaxUploadFileSize($storeId = null)
    {
        $fileSizeMb = (int)$this->scopeConfig->getValue(
            self::XML_PATH_FRONTEND_MAX_UPLOAD_FILE_SIZE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return $fileSizeMb * 1024 * 1024;
    }

    /**
     * Retrieve allowed file extensions
     *
     * @param int|null $storeId
     * @return array
     */
    public function getAllowedFileExtensions($storeId = null)
    {
        $extensions = $this->scopeConfig->getValue(
            self::XML_PATH_FRONTEND_ALLOWED_FILE_EXTENSIONS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return empty($extensions) ? [] : explode(',', $extensions);
    }

    /**
     * Is ticket escalation enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isTicketEscalationEnabled($storeId = null)
    {
        return (bool)$this->scopeConfig->getValue(
            self::XML_PATH_TICKET_ESCALATION_IS_ENABLED,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Get escalation supervisor emails
     *
     * @param int|null $storeId
     * @return array
     */
    public function getEscalationSupervisorEmails($storeId = null)
    {
        $data = $this->scopeConfig->getValue(
            self::XML_PATH_TICKET_ESCALATION_SUPERVISOR_EMAILS,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        return !empty($data) ? array_map('trim', explode(',', $data)) : [];
    }

    /**
     * Get escalation email template
     *
     * @param int|null $storeId
     * @return string
     */
    public function getEscalationEmailTemplate($storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_TICKET_ESCALATION_EMAIL_TEMPLATE,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
