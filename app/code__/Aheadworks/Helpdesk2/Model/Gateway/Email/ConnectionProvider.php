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
namespace Aheadworks\Helpdesk2\Model\Gateway\Email;

use Magento\Framework\ObjectManagerInterface;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType\ConnectionInterface;

/**
 * Class ConnectionProvider
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email
 */
class ConnectionProvider
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ConnectionInterface[]
     */
    private $authModelList;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param ConnectionInterface[] $authModelList
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $authModelList
    ) {
        $this->objectManager = $objectManager;
        $this->authModelList = $authModelList;
    }

    /**
     * Get gateway connection
     *
     * @param GatewayDataInterface $gateway
     * @return object
     */
    public function getConnection($gateway)
    {
        if (!array_key_exists($gateway->getAuthorizationType(), $this->authModelList)) {
            throw new \InvalidArgumentException(
                sprintf('Incorrect gateway authorization type: %1', $gateway->getAuthorizationType())
            );
        }

        /** @var ConnectionInterface $connectionModel */
        $connectionModel = $this->objectManager->create(
            $this->authModelList[$gateway->getAuthorizationType()]
        );

        return $connectionModel->getConnection($gateway);
    }
}
