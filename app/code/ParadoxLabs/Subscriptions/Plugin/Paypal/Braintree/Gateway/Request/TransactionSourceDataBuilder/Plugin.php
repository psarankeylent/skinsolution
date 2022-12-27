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

namespace ParadoxLabs\Subscriptions\Plugin\Paypal\Braintree\Gateway\Request\TransactionSourceDataBuilder;

use Magento\Framework\App\Area;
use PayPal\Braintree\Gateway\Request\TransactionSourceDataBuilder;

class Plugin
{
    const TRANSACTION_SOURCE = 'transactionSource';

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
     * Set transactionSource to recurring if in cron area
     *
     * @param \PayPal\Braintree\Gateway\Request\TransactionSourceDataBuilder $subject
     * @param $result
     * @return array|string[]
     */
    public function afterBuild(TransactionSourceDataBuilder $subject, $result): array
    {
        if ($this->appState->getAreaCode() === Area::AREA_CRONTAB) {
            $result[self::TRANSACTION_SOURCE] = 'recurring';
        }

        return $result;
    }
}
