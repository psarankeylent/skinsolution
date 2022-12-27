<?php declare(strict_types=1);
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\Subscriptions\Plugin\Paypal\Braintree\Gateway\Request\ThreeDSecureDataBuilder;

use Magento\Framework\App\Area;
use PayPal\Braintree\Gateway\Request\ThreeDSecureDataBuilder;

class Plugin
{
    /**
     * @var \Magento\Framework\App\State $state
     */
    private $appState;

    /**
     * TransactionSourceDataBuilder constructor
     *
     * @param \Magento\Framework\App\State $appState
     */
    public function __construct(\Magento\Framework\App\State $appState)
    {
        $this->appState = $appState;
    }

    /**
     * Never require 3D Secure validation of cron-initiated transactions.
     *
     * @param \PayPal\Braintree\Gateway\Request\ThreeDSecureDataBuilder $subject
     * @param $result
     * @return array
     */
    public function afterBuild(ThreeDSecureDataBuilder $subject, $result): array
    {
        if (isset($result['options']['threeDSecure']['required'])
            && $this->appState->getAreaCode() === Area::AREA_CRONTAB) {
            $result['options']['threeDSecure']['required'] = false;
        }

        return $result;
    }
}
