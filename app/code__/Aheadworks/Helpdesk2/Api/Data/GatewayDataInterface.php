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
namespace Aheadworks\Helpdesk2\Api\Data;

use Magento\Framework\Api\ExtensibleDataInterface;

/**
 * Interface GatewayDataInterface
 * @api
 */
interface GatewayDataInterface extends ExtensibleDataInterface
{
    /**#@+
     * Constants defined for keys of the data array.
     * Identical to the name of the getter in snake case
     */
    const ID = 'id';
    const IS_ACTIVE = 'is_active';
    const NAME = 'name';
    const DEFAULT_STORE_ID = 'default_store_id';
    const TYPE = 'type';
    const GATEWAY_PROTOCOL = 'gateway_protocol';
    const HOST = 'host';
    const AUTHORIZATION_TYPE = 'authorization_type';
    const EMAIL = 'email';
    const LOGIN = 'login';
    const PASSWORD = 'password';
    const CLIENT_ID = 'client_id';
    const CLIENT_SECRET = 'client_secret';
    const SECURITY_PROTOCOL = 'security_protocol';
    const PORT = 'port';
    const ACCESS_TOKEN = 'access_token';
    const IS_VERIFIED = 'is_verified';
    const IS_DELETE_FROM_HOST = 'is_delete_from_host';
    /**#@-*/

    /**
     * Get gateway ID
     *
     * @return int
     */
    public function getId();

    /**
     * Set gateway ID
     *
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get is active
     *
     * @return bool
     */
    public function getIsActive();

    /**
     * Set is active
     *
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * Get name
     *
     * @return string
     */
    public function getName();

    /**
     * Set name
     *
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get default store ID
     *
     * @return int
     */
    public function getDefaultStoreId();

    /**
     * Set default store ID
     *
     * @param int $defaultStoreId
     * @return $this
     */
    public function setDefaultStoreId($defaultStoreId);

    /**
     * Get gateway type
     *
     * @return string
     */
    public function getType();

    /**
     * Set gateway type
     *
     * @param int $gatewayType
     * @return $this
     */
    public function setType($gatewayType);

    /**
     * Get gateway protocol
     *
     * @return string|null
     */
    public function getGatewayProtocol();

    /**
     * Set gateway protocol
     *
     * @param string $protocol
     * @return $this
     */
    public function setGatewayProtocol($protocol);

    /**
     * Get gateway host
     *
     * @return string|null
     */
    public function getHost();

    /**
     * Set gateway protocol
     *
     * @param string $host
     * @return $this
     */
    public function setHost($host);

    /**
     * Get authorization type
     *
     * @return string|null
     */
    public function getAuthorizationType();

    /**
     * Set authorization type
     *
     * @param string $authorizationType
     * @return $this
     */
    public function setAuthorizationType($authorizationType);

    /**
     * Get email
     *
     * @return string|null
     */
    public function getEmail();

    /**
     * Set email
     *
     * @param string $email
     * @return $this
     */
    public function setEmail($email);

    /**
     * Get login
     *
     * @return string|null
     */
    public function getLogin();

    /**
     * Set login
     *
     * @param string $login
     * @return $this
     */
    public function setLogin($login);

    /**
     * Get password
     *
     * @return string|null
     */
    public function getPassword();

    /**
     * Set password
     *
     * @param string $password
     * @return $this
     */
    public function setPassword($password);

    /**
     * Get client ID
     *
     * @return string|null
     */
    public function getClientId();

    /**
     * Set client ID
     *
     * @param string $clientId
     * @return $this
     */
    public function setClientId($clientId);

    /**
     * Get client secret
     *
     * @return string|null
     */
    public function getClientSecret();

    /**
     * Set client secret
     *
     * @param string $clientSecret
     * @return $this
     */
    public function setClientSecret($clientSecret);

    /**
     * Get security protocol
     *
     * @return string|null
     */
    public function getSecurityProtocol();

    /**
     * Set security protocol
     *
     * @param string $securityProtocol
     * @return $this
     */
    public function setSecurityProtocol($securityProtocol);

    /**
     * Get port
     *
     * @return string|null
     */
    public function getPort();

    /**
     * Set security protocol
     *
     * @param string $port
     * @return $this
     */
    public function setPort($port);

    /**
     * Get access token
     *
     * @return string|null
     */
    public function getAccessToken();

    /**
     * Set access token
     *
     * @param string $accessToken
     * @return $this
     */
    public function setAccessToken($accessToken);

    /**
     * Get is verified
     *
     * @return bool|null
     */
    public function getIsVerified();

    /**
     * Set is verified
     *
     * @param bool $isVerified
     * @return $this
     */
    public function setIsVerified($isVerified);

    /**
     * Get is delete from host
     *
     * @return bool|null
     */
    public function getIsDeleteFromHost();

    /**
     * Set is delete from host
     *
     * @param bool $isDeleteFromHost
     * @return $this
     */
    public function setIsDeleteFromHost($isDeleteFromHost);

    /**
     * Retrieve existing extension attributes object if exists
     *
     * @return \Aheadworks\Helpdesk2\Api\Data\GatewayDataExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object
     *
     * @param \Aheadworks\Helpdesk2\Api\Data\GatewayDataExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Aheadworks\Helpdesk2\Api\Data\GatewayDataExtensionInterface $extensionAttributes
    );
}
