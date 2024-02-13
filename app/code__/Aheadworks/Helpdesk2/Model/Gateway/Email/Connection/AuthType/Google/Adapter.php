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

use Magento\Framework\ObjectManagerInterface;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;

/**
 * Class Adapter
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType\Google
 */
class Adapter
{
    /**
     * @var \Google_Client
     */
    private $googleClient;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param Config $config
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        Config $config
    ) {
        $this->googleClient = $objectManager->create(\Google_Client::class);
        $this->config = $config;
    }

    /**
     * Create access token
     *
     * @param GatewayDataInterface $gateway
     * @param string|null $code
     * @return array
     */
    public function createAccessToken($gateway, $code = null)
    {
        if (!$gateway->getClientId() || !$gateway->getClientSecret()) {
            throw new \InvalidArgumentException(
                sprintf('Authorization keys are not provided')
            );
        }

        $this->googleClient->setClientId($gateway->getClientId());
        $this->googleClient->setClientSecret($gateway->getClientSecret());
        $this->googleClient->setRedirectUri($this->config->getRedirectUri());
        $this->googleClient->setAccessType($this->config->getAccessType());

        if ($code) {
            $newAccessToken = $this->googleClient->fetchAccessTokenWithAuthCode($code);
            $this->googleClient->setAccessToken($newAccessToken);
        } else {
            if (!$gateway->getAccessToken()) {
                throw new \InvalidArgumentException(
                    sprintf('Please verify your google account')
                );
            }
            $this->googleClient->setAccessToken($gateway->getAccessToken());
            if ($this->googleClient->isAccessTokenExpired()) {
                $this->googleClient->fetchAccessTokenWithRefreshToken($this->googleClient->getRefreshToken());
            }
        }

        return $this->googleClient->getAccessToken();
    }

    /**
     * Get email by token
     *
     * @param array $token
     * @return string
     */
    public function getEmailByToken($token)
    {
        $this->googleClient->setAccessToken($token);
        $idToken = $this->googleClient->verifyIdToken();

        return $idToken ? $idToken['email'] : '';
    }

    /**
     * Check if access token is expired
     *
     * @param array $token
     * @return bool
     */
    public function isAccessTokenExpired($token)
    {
        $this->googleClient->setAccessToken($token);
        return $this->googleClient->isAccessTokenExpired();
    }
}
