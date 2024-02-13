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

namespace ParadoxLabs\Subscriptions\Observer;

class RecalculateActivatedSubscriptionObserver implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \ParadoxLabs\Subscriptions\Model\Source\Status
     */
    protected $statusSource;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Config
     */
    protected $config;

    /**
     * RecalculateActivatedSubscriptionObserver constructor.
     *
     * @param \ParadoxLabs\Subscriptions\Model\Source\Status $statusSource
     * @param \ParadoxLabs\Subscriptions\Model\Config $config
     */
    public function __construct(
        \ParadoxLabs\Subscriptions\Model\Source\Status $statusSource,
        \ParadoxLabs\Subscriptions\Model\Config $config
    ) {
        $this->statusSource = $statusSource;
        $this->config = $config;
    }

    /**
     * When a subscription is reactivated, check the schedule config and update next run date if needed.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $oldStatus    = $observer->getData('old_status');
        $newStatus    = $observer->getData('new_status');
        $subscription = $observer->getData('subscription');

        /**
         * If we don't have a subscription, or aren't switching from an inactive to active status, abort.
         */
        if ($subscription instanceof \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface === false
            || in_array($oldStatus, $this->statusSource->getBillableStatuses(), true)
            || !in_array($newStatus, $this->statusSource->getBillableStatuses(), true)) {
            return;
        }

        $behavior = $this->config->getReactivateBehavior($subscription->getStoreId());

        $this->recalculateNextRun($subscription, $behavior);
    }

    /**
     * Update next run date for the subscription based on the given behavior.
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription
     * @param string $behavior
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @see \ParadoxLabs\Subscriptions\Model\Config\ReactivateBehavior
     */
    public function recalculateNextRun(
        \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription,
        string $behavior
    ): void {
        if ($behavior === \ParadoxLabs\Subscriptions\Model\Config\ReactivateBehavior::RESET) {
            /**
             * Reset schedule: 1 month subscription due Jan 1st, reactivated Jan 15: Bill Jan 15; next run Feb 15
             * Resetting next run date means will run at next billing; next calculation will +1 frequency from that.
             */
            $subscription->setNextRun(time());
        } elseif ($behavior === \ParadoxLabs\Subscriptions\Model\Config\ReactivateBehavior::CALCULATE_NEXT
            && strtotime((string)$subscription->getNextRun()) < time()) {
            /**
             * Calculate next: 1 month subscription due Jan 1st, reactivated Jan 15: Next run Feb 1; do not bill prior
             * Calculating next run date without changing the existing run date will give the next future date on the
             * existing schedule.
             */
            $subscription->calculateNextRun();
        }
    }
}
