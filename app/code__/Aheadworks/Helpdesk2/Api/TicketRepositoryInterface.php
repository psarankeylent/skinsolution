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
namespace Aheadworks\Helpdesk2\Api;

/**
 * Ticket CRUD interface
 * @api
 */
interface TicketRepositoryInterface
{
    /**
     * Save ticket
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\TicketInterface $ticket
     * @return \Aheadworks\Helpdesk2\Api\Data\TicketInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Aheadworks\Helpdesk2\Api\Data\TicketInterface $ticket);

    /**
     * Retrieve ticket by ID
     *
     * @param int $ticketId
     * @param bool $reload returns non cached version of ticket
     * @return \Aheadworks\Helpdesk2\Api\Data\TicketInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($ticketId, $reload = false);

    /**
     * Retrieve ticket by internal unique ID
     *
     * @param string $uid
     * @return \Aheadworks\Helpdesk2\Api\Data\TicketInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByUid($uid);

    /**
     * Retrieve ticket by external link
     *
     * @param string $externalLink
     * @return \Aheadworks\Helpdesk2\Api\Data\TicketInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByExternalLink($externalLink);

    /**
     * Retrieve ticket list matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Helpdesk2\Api\Data\TicketSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete ticket
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\TicketInterface $ticket
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Aheadworks\Helpdesk2\Api\Data\TicketInterface $ticket);

    /**
     * Delete ticket by ID
     *
     * @param int $ticketId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($ticketId);
}
