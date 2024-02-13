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
namespace Aheadworks\Helpdesk2\Model\Data\Command\Gateway;

use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterfaceFactory;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType\DefaultModel;

/**
 * Class CheckConnection
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Gateway
 */
class CheckConnection implements CommandInterface
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var GatewayDataInterfaceFactory
     */
    private $gatewayDataFactory;

    /**
     * DefaultModel
     */
    private $defaultAuthModel;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param DefaultModel $defaultAuthModel
     * @param GatewayDataInterfaceFactory $gatewayDataFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        DefaultModel $defaultAuthModel,
        GatewayDataInterfaceFactory $gatewayDataFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->defaultAuthModel = $defaultAuthModel;
        $this->gatewayDataFactory = $gatewayDataFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        /** @var GatewayDataInterface $gateway */
        $gateway = $this->gatewayDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $gateway,
            $data,
            GatewayDataInterface::class
        );

        $this->defaultAuthModel->getConnection($gateway);
        return $gateway;
    }
}
