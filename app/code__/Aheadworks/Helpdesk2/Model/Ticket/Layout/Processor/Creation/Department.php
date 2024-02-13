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
namespace Aheadworks\Helpdesk2\Model\Ticket\Layout\Processor\Creation;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\ProcessorInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\Renderer\CreationRendererInterface;

/**
 * Class Department
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Layout\Processor\Creation
 */
class Department implements ProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @param ArrayManager $arrayManager
     * @param UserContextInterface $userContext
     */
    public function __construct(
        ArrayManager $arrayManager,
        UserContextInterface $userContext
    ) {
        $this->arrayManager = $arrayManager;
        $this->userContext = $userContext;
    }

    /**
     * Prepare department selector
     *
     * @param array $jsLayout
     * @param CreationRendererInterface $renderer
     * @return array
     */
    public function process($jsLayout, $renderer)
    {
        $departments = $renderer->getDepartments();
        if (!empty($departments)) {
            $generalFieldsetPath = 'components/aw_helpdesk2_form/children/general/children';
            $jsLayout = $this->arrayManager->merge(
                $generalFieldsetPath,
                $jsLayout,
                [
                    TicketInterface::DEPARTMENT_ID => $this->getData($departments)
                ]
            );
        }

        return $jsLayout;
    }

    /**
     * Get department ID selector data
     *
     * @param DepartmentInterface[] $departments
     * @return array
     */
    private function getData($departments)
    {
        return [
            'component' => 'Aheadworks_Helpdesk2/js/ui/form/contact-form/department-select',
            'dataScope' => TicketInterface::DEPARTMENT_ID,
            'provider' => 'aw_helpdesk2_form_data_provider',
            'template' => 'ui/form/field',
            'elementTmpl' => 'ui/form/element/select',
            'label' => __('Request Type'),
            'validation' => [
                'validate-select' => true,
                'aw-helpdesk2__validate-department' => true
            ],
            'sortOrder' => '10',
            'options' => $this->getOptions($departments),
            'isGuestCustomer' => $this->isGuestCustomer()
        ];
    }

    /**
     * Get options
     *
     * @param DepartmentInterface[] $departments
     * @return array
     */
    private function getOptions($departments)
    {
        $departmentOptions = [];
        foreach ($departments as $department) {
            $currentLabel = $department->getCurrentStorefrontLabel();
            $departmentOptions[] = [
                'value' => $department->getId(),
                'label' => $currentLabel->getContent(),
                DepartmentInterface::IS_ALLOW_GUEST => (bool)$department->getIsAllowGuest()
            ];
        }

        return $departmentOptions;
    }

    /**
     * Check if customer is guest
     *
     * @return bool
     */
    private function isGuestCustomer()
    {
        return $this->userContext->getUserType() != $this->userContext::USER_TYPE_CUSTOMER;
    }
}
