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
namespace Aheadworks\Helpdesk2\Model\Data\Validator\Department;

use Magento\Framework\Validator\AbstractValidator;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\Helpdesk2\Api\Data\StorefrontLabelInterface;

/**
 * Class StorefrontLabel
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Validator\Department
 */
class StorefrontLabel extends AbstractValidator
{
    /**
     * Returns true in case storefront label data is correct
     *
     * @param AbstractModel $model
     * @return bool
     * @throws \Exception
     */
    public function isValid($model)
    {
        $this->_clearMessages();

        $storeIds = [];
        /** @var StorefrontLabelInterface[] $storefrontLabels */
        $storefrontLabels = $model->getStorefrontLabels();
        if ($storefrontLabels && (is_array($storefrontLabels))) {
            foreach ($storefrontLabels as $label) {
                if (!in_array($label->getStoreId(), $storeIds)) {
                    array_push($storeIds, $label->getStoreId());
                } else {
                    $this->_addMessages(['Duplicated store view in storefront label found.']);
                }
            }
        }

        return empty($this->getMessages());
    }
}
