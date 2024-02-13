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
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post\Gateway;

use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\Gateway\SecretData as SecretDataFormProcessor;

/**
 * Class SecretData
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Post\Gateway
 */
class SecretData implements ProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        if ($data[GatewayDataInterface::CLIENT_SECRET]
            && $data[GatewayDataInterface::CLIENT_SECRET] == SecretDataFormProcessor::PASSWORD_MASK
        ) {
            unset($data[GatewayDataInterface::CLIENT_SECRET]);
        }

        return $data;
    }
}
