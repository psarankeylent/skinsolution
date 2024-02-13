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
namespace Aheadworks\Helpdesk2\Model\Gateway\Search;

use Aheadworks\Helpdesk2\Api\GatewayRepositoryInterface;
use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;

/**
 * Class Builder
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Search
 */
class Builder
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var GatewayRepositoryInterface
     */
    private $gatewayRepository;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param GatewayRepositoryInterface $gatewayRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        GatewayRepositoryInterface $gatewayRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->gatewayRepository = $gatewayRepository;
    }

    /**
     * Search gateways according to prepared search criteria
     *
     * @return GatewayDataInterface[]
     * @throws LocalizedException
     */
    public function searchGateways()
    {
        $searchResults = $this->gatewayRepository
            ->getList($this->buildSearchCriteria());

        return $searchResults->getItems();
    }

    /**
     * Retrieves search criteria builder
     *
     * @return SearchCriteriaBuilder
     */
    public function getSearchCriteriaBuilder()
    {
        return $this->searchCriteriaBuilder;
    }

    /**
     * Add email filter
     *
     * @param string $gatewayEmail
     * @return $this
     */
    public function addEmailFilter($gatewayEmail)
    {
        $this->getSearchCriteriaBuilder()->addFilter(GatewayDataInterface::EMAIL, $gatewayEmail);
        return $this;
    }

    /**
     * Add filter to exclude specific gateway by its id
     *
     * @param int $gatewayId
     * @return $this
     */
    public function addGatewayExcludeFilter($gatewayId)
    {
        $this->getSearchCriteriaBuilder()->addFilter(GatewayDataInterface::ID, $gatewayId, 'neq');
        return $this;
    }

    /**
     * Build search criteria
     *
     * @return SearchCriteria
     */
    private function buildSearchCriteria()
    {
        return $this->searchCriteriaBuilder->create();
    }
}
