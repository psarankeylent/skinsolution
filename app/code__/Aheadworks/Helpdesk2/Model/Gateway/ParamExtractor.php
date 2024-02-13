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
namespace Aheadworks\Helpdesk2\Model\Gateway;

use Magento\Framework\Reflection\DataObjectProcessor;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;

/**
 * Class ParamExtractor
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway
 */
class ParamExtractor
{
    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var array
     */
    private $paramMapper;

    /**
     * @param DataObjectProcessor $dataObjectProcessor
     * @param array $paramMapper
     */
    public function __construct(
        DataObjectProcessor $dataObjectProcessor,
        array $paramMapper = []
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->paramMapper = $paramMapper;
    }

    /**
     * Prepare list of params to make connection
     *
     * @param GatewayDataInterface $gateway
     * @return array
     */
    public function extract($gateway)
    {
        $gatewayData = $this->dataObjectProcessor->buildOutputDataArray(
            $gateway,
            GatewayDataInterface::class
        );

        $params = [];
        foreach ($this->paramMapper as $connectionParam => $gatewayParam) {
            if (isset($gatewayData[$gatewayParam]) && !empty($gatewayData[$gatewayParam])) {
                $params[$connectionParam] = $gatewayData[$gatewayParam];
            } else {
                throw new \InvalidArgumentException(
                    sprintf('Argument: %s - is required', $gatewayParam)
                );
            }
        }

        return $params;
    }
}
