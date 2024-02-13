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
 * Automation CRUD interface
 * @api
 */
interface AutomationRepositoryInterface
{
    /**
     * Save automation
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\AutomationInterface $automation
     * @return \Aheadworks\Helpdesk2\Api\Data\AutomationInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Aheadworks\Helpdesk2\Api\Data\AutomationInterface $automation);

    /**
     * Retrieve automation by ID
     *
     * @param int $automationId
     * @return \Aheadworks\Helpdesk2\Api\Data\AutomationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($automationId);

    /**
     * Retrieve automation list matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Helpdesk2\Api\Data\AutomationSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete automation
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\AutomationInterface $automation
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Aheadworks\Helpdesk2\Api\Data\AutomationInterface $automation);

    /**
     * Delete automation by ID
     *
     * @param int $automationId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($automationId);
}
