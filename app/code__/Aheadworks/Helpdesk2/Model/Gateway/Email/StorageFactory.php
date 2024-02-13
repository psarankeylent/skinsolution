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

use Zend\Mail\Storage\AbstractStorage;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class StorageFactory
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email
 */
class StorageFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $storageList;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $storageList
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $storageList
    ) {
        $this->objectManager = $objectManager;
        $this->storageList = $storageList;
    }

    /**
     * Get mail storage
     *
     * @param array $params
     * @return AbstractStorage
     */
    public function create($params)
    {
        if (!array_key_exists($params['protocol'], $this->storageList)) {
            throw new \InvalidArgumentException(
                sprintf('Incorrect gateway protocol: %1', $params['protocol'])
            );
        }

        $protocol = $params['protocol'];
        return $this->objectManager->create(
            $this->storageList[$protocol],
            [
                'params' => $params
            ]
        );
    }

    /**
     * Create by protocol object
     *
     * @param string $protocolType
     * @param object $protocolObject
     * @return AbstractStorage
     */
    public function createByProtocolObject($protocolType, $protocolObject)
    {
        if (!array_key_exists($protocolType, $this->storageList)) {
            throw new \InvalidArgumentException(
                sprintf('Incorrect gateway protocol: %1', $protocolType)
            );
        }

        return $this->objectManager->create(
            $this->storageList[$protocolType],
            [
                'params' => $protocolObject
            ]
        );
    }
}
