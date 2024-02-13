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
namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType\Google\Connector;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType\Google\Adapter as GoogleAdapter;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType\Google\AdapterFactory as GoogleAdapterFactory;
use Aheadworks\Helpdesk2\Api\GatewayRepositoryInterface;

/**
 * Class Google
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType
 */
class Google implements ConnectionInterface
{
    /**
     * @var Connector
     */
    private $gatewayConnector;

    /**
     * @var GoogleAdapterFactory
     */
    private $adapterFactory;

    /**
     * @var GatewayRepositoryInterface
     */
    private $gatewayRepository;

    /**
     * @param Connector $gatewayConnector
     * @param GoogleAdapterFactory $adapterFactory
     * @param GatewayRepositoryInterface $gatewayRepository
     */
    public function __construct(
        Connector $gatewayConnector,
        GoogleAdapterFactory $adapterFactory,
        GatewayRepositoryInterface $gatewayRepository
    ) {
        $this->gatewayConnector = $gatewayConnector;
        $this->adapterFactory = $adapterFactory;
        $this->gatewayRepository = $gatewayRepository;
    }

    /**
     * @inheritdoc
     *
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */
    public function getConnection($gateway)
    {
        $gateway = $this->actualizeGoogleAuthToken($gateway);
        $connection = $this->gatewayConnector->getConnection($gateway);
        if (!$connection) {
            throw new LocalizedException(
                __('Connection cannot be established for gateway: %1'),
                $gateway->getName()
            );
        }

        return $connection;
    }

    /**
     * Actualize google auth token
     *
     * @param GatewayDataInterface $gateway
     * @param string|null $code
     * @return GatewayDataInterface
     * @throws CouldNotSaveException
     */
    public function actualizeGoogleAuthToken($gateway, $code = null)
    {
        /** @var GoogleAdapter $adapter */
        $adapter = $this->adapterFactory->create();
        $token = $gateway->getAccessToken();
        $needToUpdateToken = false;
        if ($token) {
            if ($adapter->isAccessTokenExpired($token)) {
                $needToUpdateToken = true;
            }
        } else {
            if (!$code) {
                throw new \InvalidArgumentException(
                    sprintf('Please verify your google account')
                );
            }
            $needToUpdateToken = true;
        }

        if ($needToUpdateToken) {
            $token = $adapter->createAccessToken($gateway, $code);
            $gateway->setAccessToken($token);
            $gateway->setEmail($adapter->getEmailByToken($token));
            $isVerified = $this->gatewayConnector->isGatewayValid($gateway);
            $gateway->setIsVerified($isVerified);
            $this->gatewayRepository->save($gateway);
        }

        return $gateway;
    }
}
