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

use Aheadworks\Helpdesk2\Model\Gateway\Email\Protocol\AdapterInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class ProtocolFactory
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email
 */
class ProtocolFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var array
     */
    private $protocolList;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param array $protocolList
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        array $protocolList
    ) {
        $this->objectManager = $objectManager;
        $this->protocolList = $protocolList;
    }

    /**
     * Get mail protocol
     *
     * @param array $params
     * @return AdapterInterface
     */
    public function create($params)
    {
        if (!array_key_exists($params['protocol'], $this->protocolList)) {
            throw new \InvalidArgumentException(
                sprintf('Incorrect gateway protocol: %1', $params['protocol'])
            );
        }

        $protocol = $params['protocol'];
        return $this->objectManager->create(
            $this->protocolList[$protocol],
            [
                'params' => $params
            ]
        );
    }
}
