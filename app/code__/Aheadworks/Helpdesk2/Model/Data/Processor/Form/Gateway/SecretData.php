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
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Form\Gateway;

use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;

/**
 * Class SecretData
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Form\Gateway
 */
class SecretData implements ProcessorInterface
{
    const PASSWORD_MASK = '*****';

    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        if ($data[GatewayDataInterface::CLIENT_SECRET]) {
            $data[GatewayDataInterface::CLIENT_SECRET] = self::PASSWORD_MASK;
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function prepareMetaData($meta)
    {
        return $meta;
    }
}
