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

use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\ProcessorInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentOptionInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentOptionValueInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\Renderer\CreationRendererInterface;

/**
 * Class StorefrontOption
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Layout\Processor\Creation
 */
class StorefrontOption implements ProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @param ArrayManager $arrayManager
     */
    public function __construct(
        ArrayManager $arrayManager
    ) {
        $this->arrayManager = $arrayManager;
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
            $formDataProvider = 'components/aw_helpdesk2_config_provider';
            $jsLayout = $this->arrayManager->merge(
                $formDataProvider,
                $jsLayout,
                [
                    'data' => [
                        'storefront_options' => $this->getStorefrontOptions($departments)
                    ]
                ]
            );
        }

        return $jsLayout;
    }

    /**
     * Get storefront options data
     *
     * @param DepartmentInterface[] $departments
     * @return array
     */
    private function getStorefrontOptions($departments)
    {
        $result = [];
        foreach ($departments as $department) {
            $options = $department->getOptions();
            foreach ($options as $option) {
                $result[$department->getId()][] = [
                    'id' => $option->getId(),
                    'type' => $option->getType(),
                    'label' => $option->getCurrentStorefrontLabel()->getContent(),
                    'is_required' => $option->getIsRequired(),
                    'options' => $this->resolveSelectOptions($option)
                ];
            }
        }

        return $result;
    }

    /**
     * Resolve select options for specified department option
     *
     * @param DepartmentOptionInterface $departmentOption
     * @return array
     */
    private function resolveSelectOptions($departmentOption)
    {
        $options = [];
        if ($departmentOption->getType() == DepartmentOptionInterface::OPTION_TYPE_DROPDOWN) {
            $options = $this->prepareDepartmentOptionValues($departmentOption->getValues());
        }

        return $options;
    }

    /**
     * Retrieve prepared department option values
     *
     * @param DepartmentOptionValueInterface[] $values
     * @return array
     */
    private function prepareDepartmentOptionValues($values)
    {
        $preparedValues = [];
        if (empty($values)) {
            return $preparedValues;
        }

        foreach ($values as $value) {
            $preparedValues[] = [
                'value' => $value->getId(),
                'label' => $value->getCurrentStorefrontLabel()->getContent()
            ];
        }

        return $preparedValues;
    }
}
