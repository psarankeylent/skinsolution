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
namespace Aheadworks\Helpdesk2\Console\Command;

use Aheadworks\Helpdesk2\Model\Migration\Checker\Required as MigrationRequiredChecker;
use Aheadworks\Helpdesk2\Model\Migration\Processor as MigrationProcessor;
use Magento\Framework\Console\Cli;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RunMigration
 *
 * @package Aheadworks\Helpdesk2\Console\Command
 */
class RunMigration extends Command
{
    const CLEAN = 'clean';
    const LIMIT = 'limit';

    /**
     * @var MigrationProcessor
     */
    private $migrationProcessor;

    /**
     * @var MigrationRequiredChecker
     */
    private $migrationChecker;

    /**
     * @param MigrationProcessor $migrationProcessor
     * @param MigrationRequiredChecker $migrationRequired
     */
    public function __construct(
        MigrationProcessor $migrationProcessor,
        MigrationRequiredChecker $migrationRequired
    ) {
        parent::__construct();
        $this->migrationProcessor = $migrationProcessor;
        $this->migrationChecker = $migrationRequired;
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $options = [
            new InputOption(
                self::CLEAN,
                'c',
                InputOption::VALUE_NONE,
                'Clean help desk 2 database before migration'
            ),
            new InputOption(
                self::LIMIT,
                'l',
                InputOption::VALUE_REQUIRED,
                'Entry limit per iteration'
            )
        ];
        $this
            ->setName('aw-helpdesk2:run-migration')
            ->setDescription('Migrate data from older version of Help Desk')
            ->setDefinition($options);

        parent::configure();
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$this->migrationChecker->isMigrationRequired()) {
            $output->writeln('Help desk 1 module was not installed');
            return Cli::RETURN_FAILURE;
        }
        $flushBefore = $input->getOption(self::CLEAN) ? true : false;
        if ($flushBefore) {
            $output->writeln('Existing help desk 2 data will be cleared before migration');
        }
        $limit = $input->getOption(self::LIMIT);
        if (!$limit) {
            $output->writeln('Migration data limit is not specified. Default limit is 100000');
        } else {
            $limit = (int)$limit;
            $output->writeln($limit . ' tickets will be migrated in current run');
        }
        $output->writeln('Migration started');
        $this->migrationProcessor->process($flushBefore, $limit);
        $output->writeln('Migration finished');
        $output->writeln('Check logs for results');

        return Cli::RETURN_SUCCESS;
    }
}
