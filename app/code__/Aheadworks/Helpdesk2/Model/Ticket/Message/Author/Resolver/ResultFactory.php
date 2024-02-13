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
namespace Aheadworks\Helpdesk2\Model\Ticket\Message\Author\Resolver;

use Magento\Framework\ObjectManagerInterface;

/**
 * Class ResultFactory
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Message\Author\Resolver
 */
class ResultFactory
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager = null;

    /**
     * @var string
     */
    protected $instanceName = null;

    /**
     * @param ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        $instanceName = \Aheadworks\Helpdesk2\Model\Ticket\Message\Author\Resolver\Result::class
    ) {
        $this->objectManager = $objectManager;
        $this->instanceName = $instanceName;
    }

    /**
     * Create result object
     *
     * @return \Aheadworks\Helpdesk2\Model\Ticket\Message\Author\Resolver\Result
     */
    public function create()
    {
        return $this->objectManager->create($this->instanceName);
    }
}
