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
namespace Aheadworks\Helpdesk2\Model\Data\Command\Ticket;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;

/**
 * Class SaveCustomerAttribute
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Ticket
 */
class SaveCustomerAttribute implements CommandInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->customerRepository = $customerRepository;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        if (!isset($data[CustomerInterface::EMAIL])) {
            throw new \InvalidArgumentException('Customer email is required to save attribute');
        }

        $customerEmail = $data[CustomerInterface::EMAIL];
        unset($data['form_key']);
        unset($data[$data[CustomerInterface::EMAIL]]);
        if (!empty($data)) {
            $customer = $this->customerRepository->get($customerEmail);
            $this->dataObjectHelper->populateWithArray($customer, $data, CustomerInterface::class);
            $this->customerRepository->save($customer);
        }

        return true;
    }
}
