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
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketOptionInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentOptionInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentOptionValueInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;
use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;

/**
 * Class StorefrontOption
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket
 */
class StorefrontOption implements ProcessorInterface
{
    /**
     * @var DepartmentRepositoryInterface
     */
    private $departmentRepository;

    /**
     * @param DepartmentRepositoryInterface $departmentRepository
     */
    public function __construct(
        DepartmentRepositoryInterface $departmentRepository
    ) {
        $this->departmentRepository = $departmentRepository;
    }

    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        $ticketOptions = [];
        if (isset($data[TicketInterface::OPTIONS]) && !empty($data[TicketInterface::OPTIONS])) {
            $department = $this->departmentRepository->get(
                $data[TicketInterface::DEPARTMENT_ID],
                $data[TicketInterface::STORE_ID]
            );
            $departmentOptions = $department->getOptions();
            foreach ($data[TicketInterface::OPTIONS] as $optionId => $optionValue) {
                if (!$optionValue) {
                    continue;
                }

                $departmentOption = $this->resolveOption($optionId, $departmentOptions);
                if (!$departmentOption) {
                    continue;
                }

                $ticketOptions[] = [
                    TicketOptionInterface::ID => $optionId,
                    TicketOptionInterface::LABEL => $departmentOption->getCurrentStorefrontLabel()->getContent(),
                    TicketOptionInterface::VALUE => $this->resolveOptionValue($optionValue, $departmentOption)
                ];
            }
        }

        $data[TicketInterface::OPTIONS] = $ticketOptions;

        return $data;
    }

    /**
     * Resolve department option
     *
     * @param int $optionId
     * @param DepartmentOptionInterface[] $departmentOptions
     * @return DepartmentOptionInterface|null
     */
    private function resolveOption($optionId, $departmentOptions)
    {
        $result = null;
        foreach ($departmentOptions as $departmentOption) {
            if ($departmentOption->getId() == $optionId) {
                $result = $departmentOption;
                break;
            }
        }

        return $result;
    }

    /**
     * Resolve department option value
     *
     * @param string $optionValue
     * @param DepartmentOptionInterface $departmentOption
     * @return string
     */
    private function resolveOptionValue($optionValue, $departmentOption)
    {
        if ($departmentOption->getType() == DepartmentOptionInterface::OPTION_TYPE_DROPDOWN) {
            $optionValue = $this->prepareDepartmentOptionValues($optionValue, $departmentOption->getValues());
        }

        return $optionValue;
    }

    /**
     * Retrieve prepared department option values
     *
     * @param string $selectedValue
     * @param DepartmentOptionValueInterface[] $values
     * @return string
     */
    private function prepareDepartmentOptionValues($selectedValue, $values)
    {
        if (empty($values)) {
            return '';
        }

        $result = '';
        foreach ($values as $value) {
            if ($selectedValue == $value->getId()) {
                $result = $value->getCurrentStorefrontLabel()->getContent();
                break;
            }
        }

        return $result;
    }
}
