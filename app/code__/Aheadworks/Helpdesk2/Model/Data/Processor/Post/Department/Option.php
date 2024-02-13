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
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post\Department;

use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentOptionInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentOptionValueInterface;
use Aheadworks\Helpdesk2\Api\Data\StorefrontLabelInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;

/**
 * Class Option
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Post\Department
 */
class Option implements ProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        $optionsData = (isset($data[DepartmentInterface::OPTIONS]) && is_array($data[DepartmentInterface::OPTIONS]))
            ? $data[DepartmentInterface::OPTIONS]
            : []
        ;
        foreach ($optionsData as &$option) {
            $this->prepareOptionValues($option);
        }
        $data[DepartmentInterface::OPTIONS] = $optionsData;
        return $data;
    }

    /**
     * Prepare department option values
     *
     * @param array $option
     */
    private function prepareOptionValues(&$option)
    {
        if (isset($option[DepartmentOptionInterface::VALUES]) && is_array($option[DepartmentOptionInterface::VALUES])) {
            foreach ($option[DepartmentOptionInterface::VALUES] as &$value) {
                $optionValueLabels = [];
                if (isset($value[DepartmentOptionInterface::STOREFRONT_LABELS])
                    && is_array($value[DepartmentOptionInterface::STOREFRONT_LABELS])
                ) {
                    foreach ($value[DepartmentOptionInterface::STOREFRONT_LABELS] as $storeId => $content) {
                        if (empty($content)) {
                            continue;
                        }
                        $optionValueLabels[] = [
                            StorefrontLabelInterface::STORE_ID => $storeId,
                            StorefrontLabelInterface::CONTENT => $content
                        ];
                    }
                }
                $value[DepartmentOptionInterface::STOREFRONT_LABELS] = $optionValueLabels;
            }
        }
    }
}
