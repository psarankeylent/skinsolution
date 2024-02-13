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
namespace Aheadworks\Helpdesk2\Model\Data\Validator\Gateway;

use Magento\Framework\Validator\AbstractValidator;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Model\Gateway\Search\Builder as GatewaySearchBuilder;

/**
 * Class Email
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Validator\Gateway
 */
class Email extends AbstractValidator
{
    /**
     * @var GatewaySearchBuilder
     */
    private $gatewaySearchBuilder;

    /**
     * @param GatewaySearchBuilder $gatewaySearchBuilder
     */
    public function __construct(
        GatewaySearchBuilder $gatewaySearchBuilder
    ) {
        $this->gatewaySearchBuilder = $gatewaySearchBuilder;
    }

    /**
     * Check if gateway email is already used
     *
     * @param GatewayDataInterface $gateway
     * @return bool
     * @throws \Exception
     */
    public function isValid($gateway)
    {
        $this->_clearMessages();

        $gatewayList = $this->searchGatewayByEmail($gateway->getEmail(), $gateway->getId());

        if (count($gatewayList) > 0) {
            $this->_addMessages([__('Gateway email is already used in another gateway')]);
        }

        return empty($this->getMessages());
    }

    /**
     * Search gateway by email
     *
     * @param string $gatewayEmail
     * @param int|null $idToExclude
     * @return GatewayDataInterface[]
     */
    private function searchGatewayByEmail($gatewayEmail, $idToExclude)
    {
        try {
            $this->gatewaySearchBuilder->addEmailFilter($gatewayEmail);
            if ($idToExclude) {
                $this->gatewaySearchBuilder->addGatewayExcludeFilter($idToExclude);
            }

            $gatewayList = $this->gatewaySearchBuilder->searchGateways();
        } catch (LocalizedException $exception) {
            $gatewayList = [];
        }

        return $gatewayList;
    }
}
