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
namespace Aheadworks\Helpdesk2\Model\Source\Gateway;

/**
 * Class Type
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Gateway
 */
class Type
{
    /**
     * Gateway types
     */
    const EMAIL = 'email';

    const DEFAULT_TYPE = self::EMAIL;

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::EMAIL,  'label' => __('Email Gateway')]
        ];
    }

    /**
     * Get type list
     */
    public function getTypeList()
    {
        $options = $this->toOptionArray();
        $typeList = [];
        foreach ($options as $option) {
            $typeList[] = $option['value'];
        }

        return $typeList;
    }
}
