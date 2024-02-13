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
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Directory\Model\Country;
use Magento\Directory\Model\CountryFactory;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\UrlBuilder;

/**
 * Class Customer
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket
 */
class Customer implements ProcessorInterface
{
    const IS_REGISTERED_CUSTOMER = 'is_registered_customer';
    const CUSTOMER = 'customer';
    const PROFILE_URL = 'backend_profile_url';
    const COUNTRY = 'country';

    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var CountryFactory
     */
    private $countryFactory;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param DataObjectProcessor $dataObjectProcessor
     * @param UrlBuilder $urlBuilder
     * @param CountryFactory $countryFactory
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        DataObjectProcessor $dataObjectProcessor,
        UrlBuilder $urlBuilder,
        CountryFactory $countryFactory
    ) {
        $this->customerRepository = $customerRepository;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->urlBuilder = $urlBuilder;
        $this->countryFactory = $countryFactory;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function prepareEntityData($data)
    {
        $data[self::IS_REGISTERED_CUSTOMER] = false;

        $customerEmail = $data[TicketInterface::CUSTOMER_EMAIL];
        $customerId = $data[TicketInterface::CUSTOMER_ID];

        $customer = $this->getCustomerByEmail($customerEmail);
        if (!$customer) {
            $customer = $this->getCustomerById($customerId);
        }

        if ($customer) {
            $data[self::CUSTOMER] = $this->dataObjectProcessor->buildOutputDataArray(
                $customer,
                CustomerInterface::class
            );
            $data[self::CUSTOMER][self::PROFILE_URL] =
                $this->urlBuilder->getBackendCustomerProfileLink($customer->getId());
            $data[self::CUSTOMER][self::COUNTRY] = $this->getCustomerCountry($customer);
            $data[self::IS_REGISTERED_CUSTOMER] = true;
        }

        return $data;
    }

    /**
     * Retrieve customer by Email
     *
     * @param string $customerEmail
     * @return CustomerInterface|null
     * @throws LocalizedException
     */
    private function getCustomerByEmail($customerEmail)
    {
        try {
            return $this->customerRepository->get($customerEmail);
        } catch (NoSuchEntityException $exception) {
            return null;
        }
    }

    /**
     * Retrieve customer by Id
     *
     * @param string $customerEmail
     * @return CustomerInterface|null
     * @throws LocalizedException
     */
    private function getCustomerById($customerId)
    {
        try {
            return $this->customerRepository->getById($customerId);
        } catch (NoSuchEntityException $exception) {
            return null;
        }
    }

    /**
     * Get customer country
     *
     * @param CustomerInterface $customer
     * @return string
     */
    private function getCustomerCountry($customer)
    {
        foreach ($customer->getAddresses() as $address) {
            if ($address->isDefaultShipping()) {
                /** @var Country $country */
                $country = $this->countryFactory->create();
                return $country->loadByCode($address->getCountryId())->getName();
            }
        }

        return '';
    }

    /**
     * @inheritdoc
     */
    public function prepareMetaData($meta)
    {
        return $meta;
    }
}
