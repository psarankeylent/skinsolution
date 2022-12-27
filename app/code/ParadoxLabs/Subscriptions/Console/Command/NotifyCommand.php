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

namespace ParadoxLabs\Subscriptions\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * NotifyCommand Class
 */
class NotifyCommand extends Command
{
    /**
     * @var \ParadoxLabs\Subscriptions\Model\Cron\Notify
     */
    protected $command;

    /**
     * @var \Magento\Framework\App\State
     */
    protected $appState;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Config
     */
    protected $config;

    /**
     * Constructor
     *
     * @param \ParadoxLabs\Subscriptions\Model\Cron\Notify $command
     * @param \Magento\Framework\App\State $appState
     * @param \ParadoxLabs\Subscriptions\Model\Config $config
     */
    public function __construct(
        \ParadoxLabs\Subscriptions\Model\Cron\Notify $command,
        \Magento\Framework\App\State $appState,
        \ParadoxLabs\Subscriptions\Model\Config $config
    ) {
        parent::__construct();

        $this->command = $command;
        $this->appState = $appState;
        $this->config = $config;
    }

    /**
     * Set up command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('subscriptions:notify')
            ->setDescription('Notify upcoming subscriptions within the threshold.');

        parent::configure();
    }

    /**
     * Execute the command
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($this->config->moduleIsActive() !== true || $this->config->billingNoticesAreEnabled() !== true) {
            $output->writeln((string)__('The module or billing notices are disabled. Aborting.'));
            return;
        }

        $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_CRONTAB);

        $output->writeln((string)__('Notifying any upcoming subscriptions within the threshold.'));

        $startTime = microtime(true);

        $this->command->setConsoleOutput($output);
        $this->command->notifyUpcomingSubscriptions();

        $output->writeln((string)__('Total runtime: %1 sec.', microtime(true) - $startTime));
    }
}
