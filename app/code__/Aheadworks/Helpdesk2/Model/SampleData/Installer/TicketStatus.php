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
use Aheadworks\Helpdesk2\Api\Data\TicketStatusInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketStatusInterfaceFactory;
use Aheadworks\Helpdesk2\Api\TicketStatusRepositoryInterface;
use Aheadworks\Helpdesk2\Model\SampleData\Reader;

/**
 * Class TicketStatus
 *
 * @package Aheadworks\Helpdesk2\Model\SampleData\Installer
 */
class TicketStatus implements SampleDataInstallerInterface
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
     * @var TicketStatusInterfaceFactory
     */
    private $ticketStatusFactory;

    /**
     * @var TicketStatusRepositoryInterface
     */
    private $ticketStatusRepository;

    /**
     * @var Reader
     */
    private $reader;

    /**
     * @var string
     */
    private $fileName = 'Aheadworks_Helpdesk2::fixtures/ticket_status.csv';

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DataObjectHelper $dataObjectHelper
     * @param Reader $reader
     * @param TicketStatusInterfaceFactory $ticketStatusFactory
     * @param TicketStatusRepositoryInterface $ticketStatusRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataObjectHelper $dataObjectHelper,
        Reader $reader,
        TicketStatusInterfaceFactory $ticketStatusFactory,
        TicketStatusRepositoryInterface $ticketStatusRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->reader = $reader;
        $this->ticketStatusFactory = $ticketStatusFactory;
        $this->ticketStatusRepository = $ticketStatusRepository;
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
            if (!$this->ifExists($row[TicketStatusInterface::LABEL])) {
                $this->createTicketStatus($row);
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
            ->addFilter(TicketStatusInterface::LABEL, $label)
            ->setCurrentPage(1)
            ->setPageSize(1);
        $statusList = $this->ticketStatusRepository->getList(
            $this->searchCriteriaBuilder->create()
        )->getItems();

        return count($statusList) > 0;
    }

    /**
     * Create ticket status
     *
     * @param array $row
     * @throws LocalizedException
     */
    private function createTicketStatus($row)
    {
        /** @var TicketStatusInterface $ticketStatus */
        $ticketStatus = $this->ticketStatusFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $ticketStatus,
            $row,
            TicketStatusInterface::class
        );

        $this->ticketStatusRepository->save($ticketStatus);
    }
}
