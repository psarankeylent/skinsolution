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

use Zend\Mail\Protocol\Imap;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class ImapAdapter
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email\Protocol
 */
class ImapAdapter implements AdapterInterface
{
    /**
     * @var Imap
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
        $this->protocol = $objectManager->create(Imap::class, $params);
    }

    /**
     * @inheritdoc
     */
    public function sendRequest($xoauthString)
    {
        $this->protocol->sendRequest('AUTHENTICATE', [AdapterInterface::OAUTH_NAME, $xoauthString]);
        while (true) {
            $response = "";
            $isPlus = $this->protocol->readLine($response, '+', true);
            if ($isPlus) {
                $this->protocol->sendRequest('');
            } else {
                if (preg_match("/^OK /i", $response)) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function getProtocol()
    {
        return $this->protocol;
    }
}
