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

use Magento\Framework\Session\SessionManagerInterface;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;

/**
 * Class GoogleVerification
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Form\Gateway
 */
class GoogleVerification implements ProcessorInterface
{
    const GATEWAY_ID_TO_VERIFY = 'gateway_id_to_verify';

    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @param SessionManagerInterface $sessionManager
     */
    public function __construct(
        SessionManagerInterface $sessionManager
    ) {
        $this->sessionManager = $sessionManager;
    }

    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        if (isset($data[GatewayDataInterface::ID])) {
            $this->sessionManager->setData(self::GATEWAY_ID_TO_VERIFY, $data[GatewayDataInterface::ID]);
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
