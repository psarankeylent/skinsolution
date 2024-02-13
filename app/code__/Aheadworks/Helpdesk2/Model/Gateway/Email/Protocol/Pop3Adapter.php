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
namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Protocol;

use Zend\Mail\Protocol\Pop3;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class Pop3Adapter
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email\Protocol
 */
class Pop3Adapter implements AdapterInterface
{
    /**
     * @var Pop3
     */
    private $protocol;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $params
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $params
    ) {
        $this->protocol = $objectManager->create(Pop3::class, $params);
    }

    /**
     * @inheritdoc
     */
    public function sendRequest($xoauthString)
    {
        try {
            $this->protocol->request("AUTH " . AdapterInterface::OAUTH_NAME . ' ' . $xoauthString);
        } catch (\Exception $exception) {
            return false;
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function getProtocol()
    {
        return $this->protocol;
    }
}
