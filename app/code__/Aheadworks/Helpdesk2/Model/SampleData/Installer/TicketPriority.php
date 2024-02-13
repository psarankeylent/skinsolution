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
namespace Aheadworks\Helpdesk2\Model\SampleData\Installer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Setup\SampleData\InstallerInterface as SampleDataInstallerInterface;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Helpdesk2\Api\Data\TicketPriorityInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketPriorityInterfaceFactory;
use Aheadworks\Helpdesk2\Api\TicketPriorityRepositoryInterface;
use Aheadworks\Helpdesk2\Model\SampleData\Reader;

/**
 * Class TicketPriority
 *
 * @package Aheadworks\Helpdesk2\Model\SampleData\Installer
 */
class TicketPriority implements SampleDataInstallerInterface
{
    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var TicketPriorityInterfaceFactory
     */
    private $ticketPriorityFactory;

    /**
     * @var TicketPriorityRepositoryInterface
     */
    private $ticketPriorityRepository;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var string
     */
    private $fileName = 'Aheadworks_Helpdesk2::fixtures/ticket_priority.csv';

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DataObjectHelper $dataObjectHelper
     * @param Reader $reader
     * @param TicketPriorityInterfaceFactory $ticketPriorityFactory
     * @param TicketPriorityRepositoryInterface $ticketPriorityRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataObjectHelper $dataObjectHelper,
        Reader $reader,
        TicketPriorityInterfaceFactory $ticketPriorityFactory,
        TicketPriorityRepositoryInterface $ticketPriorityRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->reader = $reader;
        $this->ticketPriorityFactory = $ticketPriorityFactory;
        $this->ticketPriorityRepository = $ticketPriorityRepository;
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function install()
    {
        $rows = $this->reader->readFile($this->fileName);
        foreach ($rows as $row) {
            if (!$this->ifExists($row[TicketPriorityInterface::LABEL])) {
                $this->createTicketPriority($row);
            }
        }
    }

    /**
     * Check if exists
     *
     * @param string $label
     * @return bool
     * @throws LocalizedException
     */
    private function ifExists($label)
    {
        $this->searchCriteriaBuilder
            ->addFilter(TicketPriorityInterface::LABEL, $label)
            ->setCurrentPage(1)
            ->setPageSize(1);
        $priorityList = $this->ticketPriorityRepository->getList(
            $this->searchCriteriaBuilder->create()
        )->getItems();

        return count($priorityList) > 0;
    }

    /**
     * Create ticket priority
     *
     * @param array $row
     * @throws LocalizedException
     */
    private function createTicketPriority($row)
    {
        /** @var TicketPriorityInterface $ticketPriority */
        $ticketPriority = $this->ticketPriorityFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $ticketPriority,
            $row,
            TicketPriorityInterface::class
        );

        $this->ticketPriorityRepository->save($ticketPriority);
    }
}
