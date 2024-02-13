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
 * Gateway CRUD interface
 * @api
 */
interface GatewayRepositoryInterface
{
    /**
     * Save gateway
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface $gateway
     * @return \Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface $gateway);

    /**
     * Retrieve gateway by ID
     *
     * @param int $gatewayId
     * @return \Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function get($gatewayId);

    /**
     * Retrieve gateway list matching the specified criteria
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Aheadworks\Helpdesk2\Api\Data\GatewayDataSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete gateway
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface $gateway
     * @return bool
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface $gateway);

    /**
     * Delete gateway by ID
     *
     * @param int $gatewayId
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($gatewayId);
}
