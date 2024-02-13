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
 * Ticket priority CRUD interface
 *
 * Interface TicketPriorityRepositoryInterface
 * @api
 */
interface TicketPriorityRepositoryInterface
{
    /**
     * Save ticket priority
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\TicketPriorityInterface $priority
     * @return \Aheadworks\Helpdesk2\Api\Data\TicketPriorityInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Aheadworks\Helpdesk2\Api\Data\TicketPriorityInterface $priority);

    /**
     * Retrieve ticket priority by ID
     *
     * @param int $priorityId
     * @return \Aheadworks\Helpdesk2\Api\Data\TicketPriorityInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($priorityId);

    /**
     * Retrieve ticket priority list matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Helpdesk2\Api\Data\TicketPrioritySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);
}
