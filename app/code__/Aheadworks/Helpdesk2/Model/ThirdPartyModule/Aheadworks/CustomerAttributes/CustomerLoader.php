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
namespace Aheadworks\Helpdesk2\Model\ThirdPartyModule\Aheadworks\CustomerAttributes;

use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Customer;
use Magento\Customer\Model\ResourceModel\Customer\Collection as CustomerCollection;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;

/**
 * Class CustomerLoader
 *
 * @package Aheadworks\Helpdesk2\Model\ThirdPartyModule\Aheadworks\CustomerAttributes
 */
class CustomerLoader
{
    /**
     * @var CustomerCollectionFactory
     */
    private $customerCollectionFactory;

    /**
     * @param CustomerCollectionFactory $customerCollectionFactory
     */
    public function __construct(
        CustomerCollectionFactory $customerCollectionFactory
    ) {
        $this->customerCollectionFactory = $customerCollectionFactory;
    }

    /**
     * Load customer with attributes by email
     *
     * @param string $customerEmail
     * @return DataObject|Customer
     * @throws LocalizedException
     */
    public function loadDataByCustomerEmail($customerEmail)
    {
        /** @var CustomerCollection $collection */
        $collection = $this->customerCollectionFactory->create();
        $collection->addAttributeToSelect('*');
        $collection->addFieldToFilter(CustomerInterface::EMAIL, $customerEmail);

        return $collection->getFirstItem();
    }
}
