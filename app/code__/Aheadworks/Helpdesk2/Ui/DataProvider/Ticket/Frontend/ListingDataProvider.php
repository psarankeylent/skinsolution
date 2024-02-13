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
namespace Aheadworks\Helpdesk2\Ui\DataProvider\Ticket\Frontend;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\Session as CustomerSession;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;

/**
 * Class ListingDataProvider
 *
 * @package Aheadworks\Helpdesk2\Ui\DataProvider\Ticket\Frontend
 */
class ListingDataProvider extends DataProvider
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param ReportingInterface $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param CustomerSession $customerSession
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        ReportingInterface $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        CustomerSession $customerSession,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
        $this->customerSession = $customerSession;
    }

    /**
     * @inheritdoc
     */
    public function getSearchCriteria()
    {
        $customer = $this->customerSession->getCustomer();
        if ($customer->getId()) {
            $this->addCustomerFilter($customer);
        } else {
            $this->addEmptyFilter();
        }
        return parent::getSearchCriteria();
    }

    /**
     * Add customer filter
     *
     * @param CustomerInterface|Customer $customer
     */
    private function addCustomerFilter($customer)
    {
        $filter = $this->filterBuilder
            ->setField(TicketInterface::CUSTOMER_ID)
            ->setValue($customer->getId())
            ->setConditionType('eq')->create();
        $this->addFilter($filter);
        $filter = $this->filterBuilder
            ->setField(TicketInterface::CUSTOMER_EMAIL)
            ->setValue($customer->getEmail())
            ->setConditionType('eq')->create();
        $this->addFilter($filter);
    }

    /**
     * Add empty filter
     */
    private function addEmptyFilter()
    {
        $filter = $this->filterBuilder
            ->setField(TicketInterface::CUSTOMER_ID)
            ->setValue(0)
            ->setConditionType('eq')->create();
        $this->addFilter($filter);
    }
}
