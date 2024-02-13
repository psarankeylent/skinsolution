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
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Model\Gateway;

use Magento\Framework\Serialize\Serializer\Json as JsonSerializer;
use Aheadworks\Helpdesk2\Model\Gateway;
use Aheadworks\Helpdesk2\Model\Data\Processor\Model\ProcessorInterface;

/**
 * Class Serialization
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Model\Gateway
 */
class Serialization implements ProcessorInterface
{
    /**
     * @var JsonSerializer
     */
    private $jsonSerializer;

    /**
     * @param JsonSerializer $jsonSerializer
     */
    public function __construct(
        JsonSerializer $jsonSerializer
    ) {
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * Prepare model before save
     *
     * @param Gateway $gateway
     * @return Gateway
     */
    public function prepareModelBeforeSave($gateway)
    {
        if ($gateway->getAccessToken()) {
            $gateway->setAccessToken($this->jsonSerializer->serialize($gateway->getAccessToken()));
        }

        return $gateway;
    }

    /**
     * Prepare model after save
     *
     * @param Gateway $gateway
     * @return Gateway
     */
    public function prepareModelAfterLoad($gateway)
    {
        if ($gateway->getAccessToken()) {
            $gateway->setAccessToken($this->jsonSerializer->unserialize($gateway->getAccessToken()));
        }

        return $gateway;
    }
}
