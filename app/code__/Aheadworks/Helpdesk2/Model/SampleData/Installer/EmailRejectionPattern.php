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
use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface;
use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterfaceFactory;
use Aheadworks\Helpdesk2\Api\RejectingPatternRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Source\RejectingPattern\Scope;

/**
 * Class Pattern
 *
 * @package Aheadworks\Helpdesk2\Model\SampleData\Installer
 */
class EmailRejectionPattern implements SampleDataInstallerInterface
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
     * @var RejectingPatternInterfaceFactory
     */
    private $patternFactory;

    /**
     * @var RejectingPatternRepositoryInterface
     */
    private $patternRepository;

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DataObjectHelper $dataObjectHelper
     * @param RejectingPatternInterfaceFactory $patternFactory
     * @param RejectingPatternRepositoryInterface $patternRepository
     */
    public function __construct(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataObjectHelper $dataObjectHelper,
        RejectingPatternInterfaceFactory $patternFactory,
        RejectingPatternRepositoryInterface $patternRepository
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->patternFactory = $patternFactory;
        $this->patternRepository = $patternRepository;
    }

    /**
     * @inheritdoc
     *
     * @throws \Exception
     */
    public function install()
    {
        if (!$this->ifExists(1)) {
            $this->createPattern($this->preparePatternData1());
        }
        if (!$this->ifExists(2)) {
            $this->createPattern($this->preparePatternData2());
        }
        if (!$this->ifExists(3)) {
            $this->createPattern($this->preparePatternData3());
        }
    }

    /**
     * Prepare sample data of pattern 1
     *
     * @return array
     */
    private function preparePatternData1()
    {
        return [
            RejectingPatternInterface::ID => 1,
            RejectingPatternInterface::TITLE => 'Auto-Submitted header',
            RejectingPatternInterface::IS_ACTIVE => 1,
            RejectingPatternInterface::SCOPE_TYPES => [
                Scope::HEADERS
            ],
            RejectingPatternInterface::PATTERN => '/(?i:auto-submitted: )(?i:(?!no)).*/m'
        ];
    }

    /**
     * Prepare sample data of pattern 2
     *
     * @return array
     */
    private function preparePatternData2()
    {
        return [
            RejectingPatternInterface::ID => 2,
            RejectingPatternInterface::TITLE => 'Having X-Spam-Flag header set to YES',
            RejectingPatternInterface::IS_ACTIVE => 1,
            RejectingPatternInterface::SCOPE_TYPES => [
                Scope::HEADERS
            ],
            RejectingPatternInterface::PATTERN => '/x-spam-flag: yes/mi'
        ];
    }

    /**
     * Prepare sample data of pattern 3
     *
     * @return array
     */
    private function preparePatternData3()
    {
        return [
            RejectingPatternInterface::ID => 3,
            RejectingPatternInterface::TITLE => 'X-Spam header',
            RejectingPatternInterface::IS_ACTIVE => 1,
            RejectingPatternInterface::SCOPE_TYPES => [
                Scope::HEADERS
            ],
            RejectingPatternInterface::PATTERN => '/^x-spam: (?!not detected).*$/mi'
        ];
    }

    /**
     * Check if pattern already exists
     *
     * @param int $patternId
     * @return bool
     * @throws LocalizedException
     */
    private function ifExists($patternId)
    {
        $this->searchCriteriaBuilder
            ->addFilter(RejectingPatternInterface::ID, $patternId)
            ->setCurrentPage(1)
            ->setPageSize(1);
        $patternList = $this->patternRepository->getList(
            $this->searchCriteriaBuilder->create()
        )->getItems();

        return count($patternList) > 0;
    }

    /**
     * Create pattern
     *
     * @param array $patternData
     * @throws LocalizedException
     */
    private function createPattern($patternData)
    {
        /** @var RejectingPatternInterface $pattern */
        $pattern = $this->patternFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $pattern,
            $patternData,
            RejectingPatternInterface::class
        );

        $this->patternRepository->save($pattern);
    }
}
