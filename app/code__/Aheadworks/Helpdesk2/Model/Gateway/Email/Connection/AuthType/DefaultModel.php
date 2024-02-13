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
namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType;

use Aheadworks\Helpdesk2\Model\Gateway\Email\StorageFactory;
use Aheadworks\Helpdesk2\Model\Gateway\ParamExtractor;

/**
 * Class DefaultModel
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType
 */
class DefaultModel implements ConnectionInterface
{
    /**
     * @var StorageFactory
     */
    private $storageFactory;

    /**
     * @var ParamExtractor
     */
    private $paramExtractor;

    /**
     * @param StorageFactory $storageFactory
     * @param ParamExtractor $paramExtractor
     */
    public function __construct(
        StorageFactory $storageFactory,
        ParamExtractor $paramExtractor
    ) {
        $this->storageFactory = $storageFactory;
        $this->paramExtractor = $paramExtractor;
    }

    /**
     * @inheritdoc
     */
    public function getConnection($gateway)
    {
        $params = $this->paramExtractor->extract($gateway);
        return $this->storageFactory->create($params);
    }
}
