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
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket\Frontend;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;

/**
 * Class Customer
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket\Frontend
 */
class Customer implements ProcessorInterface
{
    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param CustomerSession $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function prepareEntityData($data)
    {
        $customerId = $this->customerSession->getCustomerId();
        if ($customerId) {
            $customer = $this->customerRepository->getById($customerId);
            $data[TicketInterface::CUSTOMER_ID] = $customer->getId();
            $data[TicketInterface::CUSTOMER_NAME] =
                $data[TicketInterface::CUSTOMER_NAME] ?? $customer->getFirstname() . ' ' . $customer->getLastname();
            $data[TicketInterface::CUSTOMER_EMAIL] = $data[TicketInterface::CUSTOMER_EMAIL] ?? $customer->getEmail();
        }

        return $data;
    }
}
