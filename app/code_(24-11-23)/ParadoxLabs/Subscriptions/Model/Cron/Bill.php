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

namespace ParadoxLabs\Subscriptions\Model\Cron;

/**
 * Bill Class
 */
class Bill
{
    /**
     * @var \ParadoxLabs\Subscriptions\Model\ResourceModel\Subscription\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var \ParadoxLabs\Subscriptions\Helper\Data
     */
    protected $helper;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Service\Subscription
     */
    protected $subscriptionService;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $dateProcessor;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $consoleOutputStream;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Config
     */
    protected $config;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Source\Status
     */
    protected $statusSource;

    /**
     * Bill constructor.
     *
     * @param \ParadoxLabs\Subscriptions\Model\ResourceModel\Subscription\CollectionFactory $collectionFactory
     * @param \ParadoxLabs\Subscriptions\Helper\Data $helper
     * @param \ParadoxLabs\Subscriptions\Model\Service\Subscription $subscriptionService *Proxy
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $dateProcessor
     * @param \ParadoxLabs\Subscriptions\Model\Config $config
     * @param \ParadoxLabs\Subscriptions\Model\Source\Status $statusSource
     */
    public function __construct(
        \ParadoxLabs\Subscriptions\Model\ResourceModel\Subscription\CollectionFactory $collectionFactory,
        \ParadoxLabs\Subscriptions\Helper\Data $helper,
        \ParadoxLabs\Subscriptions\Model\Service\Subscription $subscriptionService,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $dateProcessor,
        \ParadoxLabs\Subscriptions\Model\Config $config,
        \ParadoxLabs\Subscriptions\Model\Source\Status $statusSource = null
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->helper = $helper;
        $this->subscriptionService = $subscriptionService;
        $this->dateProcessor = $dateProcessor;
        $this->config = $config;
        // BC preservation -- argument added in 3.2.0
        $this->statusSource = $statusSource ?: \Magento\Framework\App\ObjectManager::getInstance()->get(
            \ParadoxLabs\Subscriptions\Model\Source\Status::class
        );
    }

    /**
     * Run subscriptions billing (entry point for cron, with active check).
     *
     * @return void
     */
    public function runSubscriptionsCron()
    {
        if ($this->config->scheduledBillingIsEnabled() === true) {
            $this->runSubscriptions();
        }
    }

    /**
     * Run subscriptions billing.
     *
     * @return void
     */
    public function runSubscriptions()
    {
        if ($this->config->moduleIsActive() !== true) {
            return;
        }

        if ($this->config->groupSameDaySubscriptions() === true) {
            $this->runCombined();
        } else {
            $this->runSingle();
        }
    }

    /**
     * Run due subscriptions (single mode)
     *
     * @return $this
     */
    protected function runSingle()
    {
        $subscriptions = $this->loadSingleSubscriptions();

        if (!empty($subscriptions)) {
            $this->log(
                __(
                    'CRON-single: Running %1 subscriptions.',
                    count($subscriptions)
                )
            );

            $billed = 0;
            $failed = 0;

            foreach ($subscriptions as $subscription) {
                try {
                    $success = $this->subscriptionService->generateOrder([$subscription]);
                } catch (\Throwable $e) {
                    $success = false;

                    $this->log(
                        __(
                            'CRON-single: Subscription %1 failed. Error: %2',
                            $subscription->getIncrementId(),
                            $e->getMessage()
                        )
                    );
                }

                if ($success === true) {
                    $billed++;
                } else {
                    $failed++;
                }
            }

            $this->log(
                __(
                    'CRON-single: Ran subscriptions; %1 billed, %2 failed.',
                    $billed,
                    $failed
                )
            );
        }

        return $this;
    }

    /**
     * Run due subscriptions (combined mode -- group multiple from same day)
     *
     * @return $this
     */
    protected function runCombined()
    {
        $subscriptions = $this->loadCombinedSubscriptions();

        if (!empty($subscriptions)) {
            $this->log(
                __(
                    'CRON-multi: Checking %1 subscriptions.',
                    count($subscriptions)
                )
            );

            $groups = [];

            $billed = 0;
            $failed = 0;

            /**
             * Form all pending subscriptions for the day into groups.
             */
            foreach ($subscriptions as $subscription) {
                $key = $this->subscriptionService->hashFulfillmentInfo($subscription);

                if (!isset($groups[$key])) {
                    $groups[$key] = [];
                }

                $groups[$key][] = $subscription;
            }

            /**
             * Bill each group iff at least one is due.
             */
            foreach ($groups as $key => $group) {
                $this->runCombinedGroup($group, $billed, $failed);
            }

            $this->log(
                __(
                    'CRON-multi: Ran subscriptions; %1 billed, %2 failed.',
                    $billed,
                    $failed
                )
            );
        }

        return $this;
    }

    /**
     * Check the given combined subscription group for billing eligibility, and run it if valid.
     *
     * @param array $group
     * @param int $billed
     * @param int $failed
     * @return void
     */
    protected function runCombinedGroup($group, &$billed, &$failed)
    {
        $due = false;

        /** @var \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription */
        foreach ($group as $subscription) {
            if (strtotime((string)$subscription->getNextRun()) <= time()) {
                $due = true;
                break;
            }
        }

        if ($due === true) {
            try {
                $success = $this->subscriptionService->generateOrder($group);
            } catch (\Throwable $e) {
                $success = false;

                $ids = [];
                foreach ($group as $subscription) {
                    $ids[] = $subscription->getIncrementId();
                }

                $this->log(
                    __(
                        'CRON-multi: Group [%1] failed. Error: %2',
                        implode(',', $ids),
                        $e->getMessage()
                    )
                );
            }

            if ($success === true) {
                $billed += count($group);
            } else {
                $failed += count($group);
            }
        }
    }

    /**
     * Set console output stream. Used when run from command line.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return $this
     */
    public function setConsoleOutput(\Symfony\Component\Console\Output\OutputInterface $output)
    {
        $this->consoleOutputStream = $output;

        return $this;
    }

    /**
     * Write to log, and to screen if CLI.
     *
     * @param mixed $message
     * @return $this
     */
    protected function log($message)
    {
        $this->helper->log('subscriptions', $message);

        if ($this->consoleOutputStream !== null) {
            $this->consoleOutputStream->writeln((string)$message);
        }

        return $this;
    }

    /**
     * Get eligible subscriptions for individual billing.
     *
     * @return \ParadoxLabs\Subscriptions\Model\ResourceModel\Subscription\Collection
     */
    protected function loadSingleSubscriptions()
    {
        $now = $this->dateProcessor->date(null, null, false);

        /** @var \ParadoxLabs\Subscriptions\Model\ResourceModel\Subscription\Collection $subscriptions */
        $subscriptions = $this->collectionFactory->create();
        $subscriptions->addFieldToFilter(
            'next_run',
            [
                'lteq' => $now->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT),
            ]
        );
        $subscriptions->addFieldToFilter(
            'status',
            [
                'in' => $this->statusSource->getBillableStatuses(),
            ]
        );

        return $subscriptions;
    }

    /**
     * Get potentially eligible subscriptions for combined billing (grouping by fulfillment info).
     *
     * @return \ParadoxLabs\Subscriptions\Model\ResourceModel\Subscription\Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function loadCombinedSubscriptions()
    {
        $tomorrow = $this->dateProcessor->convertConfigTimeToUtc(
            'tomorrow',
            \Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT
        );

        /** @var \ParadoxLabs\Subscriptions\Model\ResourceModel\Subscription\Collection $subscriptions */
        $subscriptions = $this->collectionFactory->create();
        $subscriptions->addFieldToFilter(
            'next_run',
            [
                'lt' => $tomorrow,
            ]
        );
        $subscriptions->addFieldToFilter(
            'status',
            [
                'in' => $this->statusSource->getBillableStatuses(),
            ]
        );

        return $subscriptions;
    }
}
