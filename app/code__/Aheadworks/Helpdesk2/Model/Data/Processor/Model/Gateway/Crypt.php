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

use Magento\Framework\Encryption\EncryptorInterface;
use Aheadworks\Helpdesk2\Model\Gateway;
use Aheadworks\Helpdesk2\Model\Data\Processor\Model\ProcessorInterface;

/**
 * Class Crypt
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Model\Gateway
 */
class Crypt implements ProcessorInterface
{
    /**
     * @var EncryptorInterface
     */
    private $encryptor;

    /**
     * @param EncryptorInterface $encryptor
     */
    public function __construct(
        EncryptorInterface $encryptor
    ) {
        $this->encryptor = $encryptor;
    }

    /**
     * Prepare model before save
     *
     * @param Gateway $gateway
     * @return Gateway
     */
    public function prepareModelBeforeSave($gateway)
    {
        if ($gateway->getClientSecret()) {
            $gateway->setClientSecret($this->encryptor->encrypt($gateway->getClientSecret()));
        }
        if ($gateway->getPassword()) {
            $gateway->setPassword($this->encryptor->encrypt($gateway->getPassword()));
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
        if ($gateway->getClientSecret()) {
            $gateway->setClientSecret($this->encryptor->decrypt($gateway->getClientSecret()));
        }
        if ($gateway->getPassword()) {
            $gateway->setPassword($this->encryptor->decrypt($gateway->getPassword()));
        }

        return $gateway;
    }
}
