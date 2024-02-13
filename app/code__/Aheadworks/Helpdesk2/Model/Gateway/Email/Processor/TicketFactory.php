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
namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Processor;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;
use Aheadworks\Helpdesk2\Api\GatewayRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\EmailInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterfaceFactory;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Status as TicketStatus;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Priority as TicketPriority;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway as GatewayResourceModel;

/**
 * Class TicketFactory
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email\Processor
 */
class TicketFactory
{
    /**
     * @var TicketInterfaceFactory
     */
    private $ticketFactory;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var GatewayRepositoryInterface
     */
    private $gatewayRepository;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var GatewayResourceModel
     */
    private $gatewayResource;

    /**
     * @var DepartmentRepositoryInterface
     */
    private $departmentRepository;

    /**
     * @var EmailParser
     */
    private $emailParser;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param GatewayRepositoryInterface $gatewayRepository
     * @param StoreManagerInterface $storeManager
     * @param TicketInterfaceFactory $ticketFactory
     * @param GatewayResourceModel $gatewayResource
     * @param DepartmentRepositoryInterface $departmentRepository
     * @param EmailParser $emailParser
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        GatewayRepositoryInterface $gatewayRepository,
        StoreManagerInterface $storeManager,
        TicketInterfaceFactory $ticketFactory,
        GatewayResourceModel $gatewayResource,
        DepartmentRepositoryInterface $departmentRepository,
        EmailParser $emailParser
    ) {
        $this->customerRepository = $customerRepository;
        $this->gatewayRepository = $gatewayRepository;
        $this->storeManager = $storeManager;
        $this->ticketFactory = $ticketFactory;
        $this->gatewayResource = $gatewayResource;
        $this->departmentRepository = $departmentRepository;
        $this->emailParser = $emailParser;
    }

    /**
     * Create new ticket based on mail
     *
     * @param EmailInterface $mail
     * @return TicketInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function create($mail)
    {
        /** @var TicketInterface $ticket */
        $ticket = $this->ticketFactory->create();
        $gateway = $this->gatewayRepository->get($mail->getGatewayId());
        $email = $this->emailParser->parseFromSubject($mail->getFrom());

        try {
            $storeId = $gateway->getDefaultStoreId();
            $customer = $this->customerRepository->get(
                $email,
                $this->storeManager->getStore($storeId)->getWebsiteId()
            );
            $customerId = $customer->getId();
            $name = $customer->getFirstname() . ' ' . $customer->getLastname();
        } catch (NoSuchEntityException $e) {
            $customerId = null;
            $storeId = $this->storeManager->getDefaultStoreView()->getId();
            $name = $this->parseCustomerName($mail->getFrom());
        }

        $ticket
            ->setStoreId($storeId)
            ->setCustomerName($name)
            ->setCustomerId($customerId)
            ->setCustomerEmail($email)
            ->setStatusId(TicketStatus::NEW)
            ->setPriorityId(TicketPriority::TO_DO);

        $departmentId = $this->gatewayResource->getDepartmentIdForGateway($mail->getGatewayId());
        if (!$departmentId) {
            throw new LocalizedException(__('Gateway: %1 is not assigned to any department', $gateway->getName()));
        }

        $department = $this->departmentRepository->get($departmentId);
        $ticket->setDepartmentId($departmentId);
        $ticket->setAgentId($department->getPrimaryAgentId());

        return $ticket;
    }

    /**
     * Parse customer name from mail subject
     *
     * @param string $fromSubject
     * @return string
     */
    private function parseCustomerName($fromSubject)
    {
        $email = $this->emailParser->parseFromSubject($fromSubject);
        $name = str_ireplace('<' . $email . '>', '', $fromSubject);
        return str_replace('"', '', trim($name));
    }
}
