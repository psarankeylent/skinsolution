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
namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType\Google;

use Zend\Mail\Protocol\Imap;
use Zend\Mail\Protocol\Pop3;
use Zend\Mail\Storage\AbstractStorage;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Model\Gateway\Email\ProtocolFactory;
use Aheadworks\Helpdesk2\Model\Gateway\Email\StorageFactory;
use Aheadworks\Helpdesk2\Model\Gateway\ParamExtractor;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Protocol\AdapterInterface;

/**
 * Class Connector
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType\Google
 */
class Connector
{
    const ACCESS_TOKEN_INDEX = 'access_token';

    /**
     * @var ProtocolFactory
     */
    private $protocolFactory;

    /**
     * @var StorageFactory
     */
    private $storageFactory;

    /**
     * @var ParamExtractor
     */
    private $paramExtractor;

    /**
     * @param ProtocolFactory $protocolFactory
     * @param ParamExtractor $paramExtractor
     * @param StorageFactory $storageFactory
     */
    public function __construct(
        ProtocolFactory $protocolFactory,
        ParamExtractor $paramExtractor,
        StorageFactory $storageFactory
    ) {
        $this->protocolFactory = $protocolFactory;
        $this->paramExtractor = $paramExtractor;
        $this->storageFactory = $storageFactory;
    }

    /**
     * Get connection to gateway
     *
     * @param GatewayDataInterface $gateway
     * @return AbstractStorage|null
     */
    public function getConnection($gateway)
    {
        if ($gateway->getIsVerified()) {
            $protocolAdapter = $this->getProtocolAdapter($gateway);
            if ($this->isConnectionValid($protocolAdapter, $gateway)) {
                return $this->storageFactory->createByProtocolObject(
                    $gateway->getGatewayProtocol(),
                    $protocolAdapter->getProtocol()
                );
            }
        }

        return null;
    }

    /**
     * Is gateway valid
     *
     * @param GatewayDataInterface $gateway
     * @return bool
     */
    public function isGatewayValid($gateway)
    {
        $protocolAdapter = $this->getProtocolAdapter($gateway);
        return $this->isConnectionValid($protocolAdapter, $gateway);
    }

    /**
     * Is connection valid
     *
     * @param AdapterInterface $protocolAdapter
     * @param GatewayDataInterface $gateway
     * @return bool
     */
    private function isConnectionValid($protocolAdapter, $gateway)
    {
        $accessToken = $gateway->getAccessToken();
        $xoauthString = $this->constructAuthString(
            $gateway->getEmail(),
            $accessToken[self::ACCESS_TOKEN_INDEX]
        );

        return $protocolAdapter->sendRequest($xoauthString);
    }

    /**
     * Get protocol adapter
     *
     * @param GatewayDataInterface $gateway
     * @return AdapterInterface
     */
    private function getProtocolAdapter($gateway)
    {
        $params = $this->paramExtractor->extract($gateway);
        return $this->protocolFactory->create($params);
    }

    /**
     * Build an OAuth2 authentication string for the given email address and access token
     *
     * @param string $user
     * @param string $accessToken
     * @return string
     */
    private function constructAuthString($user, $accessToken)
    {
        return base64_encode("user=$user\1auth=Bearer $accessToken\1\1");
    }
}
