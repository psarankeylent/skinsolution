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
namespace Aheadworks\Helpdesk2\Model;

use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department as DepartmentResourceModel;
use Aheadworks\Helpdesk2\Model\Data\Processor\Model\ProcessorInterface;
use Magento\Framework\Validator\ValidatorInterface;

/**
 * Class Gateway
 *
 * @package Aheadworks\Helpdesk2\Model
 */
class Gateway extends AbstractModel implements GatewayDataInterface
{
    /**
     * @var ProcessorInterface
     */
    private $objectDataProcessor;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param ProcessorInterface $objectDataProcessor
     * @param ValidatorInterface $validator
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ProcessorInterface $objectDataProcessor,
        ValidatorInterface $validator,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->objectDataProcessor = $objectDataProcessor;
        $this->validator = $validator;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(DepartmentResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getID()
    {
        return $this->getData(self::ID);
    }

    /**
     * @inheritdoc
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * @inheritdoc
     */
    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * @inheritdoc
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * @inheritdoc
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * @inheritdoc
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * @inheritdoc
     */
    public function getDefaultStoreId()
    {
        return $this->getData(self::DEFAULT_STORE_ID);
    }

    /**
     * @inheritdoc
     */
    public function setDefaultStoreId($defaultStoreId)
    {
        return $this->setData(self::DEFAULT_STORE_ID, $defaultStoreId);
    }

    /**
     * @inheritdoc
     */
    public function getType()
    {
        return $this->getData(self::TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setType($gatewayType)
    {
        return $this->setData(self::TYPE, $gatewayType);
    }

    /**
     * @inheritdoc
     */
    public function getGatewayProtocol()
    {
        return $this->getData(self::GATEWAY_PROTOCOL);
    }

    /**
     * @inheritdoc
     */
    public function setGatewayProtocol($gatewayProtocol)
    {
        return $this->setData(self::GATEWAY_PROTOCOL, $gatewayProtocol);
    }

    /**
     * @inheritdoc
     */
    public function getHost()
    {
        return $this->getData(self::HOST);
    }

    /**
     * @inheritdoc
     */
    public function setHost($host)
    {
        return $this->setData(self::HOST, $host);
    }

    /**
     * @inheritdoc
     */
    public function getAuthorizationType()
    {
        return $this->getData(self::AUTHORIZATION_TYPE);
    }

    /**
     * @inheritdoc
     */
    public function setAuthorizationType($authorizationType)
    {
        return $this->setData(self::AUTHORIZATION_TYPE, $authorizationType);
    }

    /**
     * @inheritdoc
     */
    public function getEmail()
    {
        return $this->getData(self::EMAIL);
    }

    /**
     * @inheritdoc
     */
    public function setEmail($email)
    {
        return $this->setData(self::EMAIL, $email);
    }

    /**
     * @inheritdoc
     */
    public function getLogin()
    {
        return $this->getData(self::LOGIN);
    }

    /**
     * @inheritdoc
     */
    public function setLogin($login)
    {
        return $this->setData(self::LOGIN, $login);
    }

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return $this->getData(self::PASSWORD);
    }

    /**
     * @inheritdoc
     */
    public function setPassword($password)
    {
        return $this->setData(self::PASSWORD, $password);
    }

    /**
     * @inheritdoc
     */
    public function getClientId()
    {
        return $this->getData(self::CLIENT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setClientId($clientId)
    {
        return $this->setData(self::CLIENT_ID, $clientId);
    }

    /**
     * @inheritdoc
     */
    public function getClientSecret()
    {
        return $this->getData(self::CLIENT_SECRET);
    }

    /**
     * @inheritdoc
     */
    public function setClientSecret($clientSecret)
    {
        return $this->setData(self::CLIENT_SECRET, $clientSecret);
    }

    /**
     * @inheritdoc
     */
    public function getSecurityProtocol()
    {
        return $this->getData(self::SECURITY_PROTOCOL);
    }

    /**
     * @inheritdoc
     */
    public function setSecurityProtocol($securityProtocol)
    {
        return $this->setData(self::SECURITY_PROTOCOL, $securityProtocol);
    }

    /**
     * @inheritdoc
     */
    public function getPort()
    {
        return $this->getData(self::PORT);
    }

    /**
     * @inheritdoc
     */
    public function setPort($port)
    {
        return $this->setData(self::PORT, $port);
    }

    /**
     * @inheritdoc
     */
    public function getAccessToken()
    {
        return $this->getData(self::ACCESS_TOKEN);
    }

    /**
     * @inheritdoc
     */
    public function setAccessToken($accessToken)
    {
        return $this->setData(self::ACCESS_TOKEN, $accessToken);
    }

    /**
     * @inheritdoc
     */
    public function getIsVerified()
    {
        return $this->getData(self::IS_VERIFIED);
    }

    /**
     * @inheritdoc
     */
    public function setIsVerified($isVerified)
    {
        return $this->setData(self::IS_VERIFIED, $isVerified);
    }

    /**
     * @inheritdoc
     */
    public function getIsDeleteFromHost()
    {
        return $this->getData(self::IS_DELETE_FROM_HOST);
    }

    /**
     * @inheritdoc
     */
    public function setIsDeleteFromHost($isDeleteFromHost)
    {
        return $this->setData(self::IS_DELETE_FROM_HOST, $isDeleteFromHost);
    }

    /**
     * @inheritdoc
     */
    public function beforeSave()
    {
        $this->objectDataProcessor->prepareModelBeforeSave($this);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function afterLoad()
    {
        $this->objectDataProcessor->prepareModelAfterLoad($this);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * @inheritdoc
     */
    public function setExtensionAttributes(
        \Aheadworks\Helpdesk2\Api\Data\GatewayDataExtensionInterface $extensionAttributes
    ) {
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }

    /**
     * @inheritdoc
     */
    protected function _getValidationRulesBeforeSave()
    {
        return $this->validator;
    }
}
