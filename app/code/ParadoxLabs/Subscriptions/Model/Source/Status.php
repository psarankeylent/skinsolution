<?php
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

namespace ParadoxLabs\Subscriptions\Model\Source;

/**
 * Status Class
 */
class Status extends \Magento\Catalog\Model\Product\Attribute\Source\Status
{
    const STATUS_ACTIVE = 'active';
    const STATUS_PAUSED = 'paused';
    const STATUS_CANCELED = 'canceled';
    const STATUS_COMPLETE = 'complete';
    const STATUS_PAYMENT_FAILED = 'payment_failed';

    /**
     * @var array Possible status values
     */
    protected static $allowedStatuses = [
        self::STATUS_ACTIVE         => 'Active',
        self::STATUS_PAUSED         => 'Paused',
        self::STATUS_CANCELED       => 'Canceled',
        self::STATUS_COMPLETE       => 'Complete',
        self::STATUS_PAYMENT_FAILED => 'Payment Failed',
    ];

    /**
     * @var array Billable statuses
     */
    protected static $billableStatuses = [
        self::STATUS_ACTIVE,
    ];

    /**
     * @var array Final statuses (should never change to another status once reached)
     */
    protected static $finalStatuses = [
        self::STATUS_CANCELED,
        self::STATUS_COMPLETE,
    ];

    /**
     * @var array Possible status changes (for buttons, et al.)
     *
     * Can set status to key if current status is one of [values]
     */
    protected static $allowedChangeMap = [
        self::STATUS_ACTIVE => [
            self::STATUS_PAUSED,
            self::STATUS_PAYMENT_FAILED,
        ],
        self::STATUS_PAUSED => [
            self::STATUS_ACTIVE,
            self::STATUS_PAYMENT_FAILED,
        ],
        self::STATUS_CANCELED => [
            self::STATUS_ACTIVE,
            self::STATUS_PAUSED,
            self::STATUS_PAYMENT_FAILED,
        ],
        self::STATUS_COMPLETE => [
            self::STATUS_ACTIVE,
            self::STATUS_PAUSED,
            self::STATUS_PAYMENT_FAILED,
        ],
        self::STATUS_PAYMENT_FAILED => [
            self::STATUS_ACTIVE,
            self::STATUS_PAUSED,
        ],
    ];

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Config
     */
    protected $config;

    /**
     * Status constructor.
     *
     * @param \ParadoxLabs\Subscriptions\Model\Config $config
     */
    public function __construct(
        \ParadoxLabs\Subscriptions\Model\Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Get possible status values.
     *
     * @return \string[]
     */
    public function getAllowedStatuses()
    {
        return static::getOptionArray();
    }

    /**
     * Get billable statuses
     *
     * @return string[]
     */
    public function getBillableStatuses()
    {
        return static::$billableStatuses;
    }

    /**
     * Get final statuses (ones that should never change once reached)
     *
     * @return string[]
     */
    public function getFinalStatuses()
    {
        return static::$finalStatuses;
    }

    /**
     * Get possible period values.
     *
     * @return string[]
     */
    public static function getOptionArray()
    {
        return static::$allowedStatuses;
    }

    /**
     * Check whether the given status is one of the allowed values.
     *
     * @param string $status
     * @return bool
     */
    public function isAllowedStatus($status)
    {
        if (array_key_exists($status, static::getOptionArray()) !== false) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve option array with empty value
     *
     * @return string[]
     */
    public function getAllOptions()
    {
        return $this->toOptionArray();
    }

    /**
     * Retrieve option text by option value
     *
     * @param string $optionId
     * @return string
     */
    public function getOptionText($optionId)
    {
        $options = static::getOptionArray();

        return isset($options[$optionId]) ? __($options[$optionId]) : null;
    }

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        $opts = [];

        foreach (static::getOptionArray() as $key => $value) {
            $opts[] = [
                'label' => __($value),
                'value' => $key,
            ];
        }

        return $opts;
    }

    /**
     * Check whether the given status can be set on the subscription in its current state.
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription
     * @param string $newStatus
     * @return bool
     */
    public function canSetStatus(\ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription, $newStatus)
    {
        if ($this->isAllowedStatus($newStatus) === false) {
            return false;
        }

        $oldStatus = $subscription->getStatus();

        if (isset(static::$allowedChangeMap[$newStatus])
            && in_array($oldStatus, static::$allowedChangeMap[$newStatus], true)) {
            return true;
        }

        return false;
    }

    /**
     * Check whether a customer can set the given status on the subscription in its current state.
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription
     * @param string $newStatus
     * @return bool
     */
    public function canSetStatusAsCustomer(
        \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription,
        $newStatus
    ) {
        if ($this->canSetStatus($subscription, $newStatus) === true) {
            /**
             * If our new status is a valid state, check permission (where applicable).
             */
            if ($newStatus === static::STATUS_PAUSED) {
                return $this->config->allowCustomerToPause($subscription->getStoreId());
            }

            if ($newStatus === static::STATUS_CANCELED) {
                return $this->config->allowCustomerToCancel($subscription->getStoreId());
            }

            return true;
        }

        return false;
    }
}
