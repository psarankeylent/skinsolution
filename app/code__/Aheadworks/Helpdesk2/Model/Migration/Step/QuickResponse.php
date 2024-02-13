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
namespace Aheadworks\Helpdesk2\Model\Migration\Step;

use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface;
use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\StorefrontLabelInterface;
use Aheadworks\Helpdesk2\Api\QuickResponseRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Migration\Source\Helpdesk1TableNames;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class QuickResponse
 *
 * @package Aheadworks\Helpdesk2\Model\Migration\Step
 */
class QuickResponse implements MigrationStepInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var QuickResponseRepositoryInterface
     */
    private $quickResponseRepository;

    /**
     * @var QuickResponseInterfaceFactory
     */
    private $quickResponseFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @param ResourceConnection $resourceConnection
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param QuickResponseRepositoryInterface $quickResponseRepository
     * @param QuickResponseInterfaceFactory $quickResponseFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        QuickResponseRepositoryInterface $quickResponseRepository,
        QuickResponseInterfaceFactory $quickResponseFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->quickResponseRepository = $quickResponseRepository;
        $this->quickResponseFactory = $quickResponseFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function migrate($limit)
    {
        $responsesToMigrate = $this->getQuickResponsesToMigrate();
        $responseValuesToMigrate = $this->getQuickResponseValuesToMigrate();

        $migratedEntityCount = 0;
        foreach ($responsesToMigrate as $quickResponseData) {
            $responseId = $quickResponseData[QuickResponseInterface::ID];
            if (!$this->ifExists($responseId)) {
                $quickResponseData[QuickResponseInterface::STOREFRONT_LABELS] =
                    $responseValuesToMigrate[$responseId] ?? [];
                $this->createQuickResponse($quickResponseData);
                $migratedEntityCount++;
            }
        }

        return $migratedEntityCount . ' quick responses were migrated';
    }

    /**
     * Get quick responses to migrate
     *
     * @return array[]
     */
    private function getQuickResponsesToMigrate()
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection
            ->select()
            ->from(['main_table' => $connection->getTableName(Helpdesk1TableNames::QUICK_RESPONSE)])
            ->columns([
                QuickResponseInterface::ID => 'main_table.id',
                QuickResponseInterface::TITLE => 'main_table.title',
                QuickResponseInterface::IS_ACTIVE => 'main_table.is_active',
                QuickResponseInterface::CREATED_AT => 'main_table.created_at',
                QuickResponseInterface::UPDATED_AT => 'main_table.updated_at',
            ]);

        return $connection->fetchAll($select);
    }

    /**
     * Get quick response values to migrate
     *
     * @return array[]
     */
    private function getQuickResponseValuesToMigrate()
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection
            ->select()
            ->from(['main_table' => $connection->getTableName(Helpdesk1TableNames::QUICK_RESPONSE_TEXT)], [])
            ->columns([
                StorefrontLabelInterface::STORE_ID =>  'main_table.store_id',
                StorefrontLabelInterface::CONTENT =>  'main_table.value',
                'response_id' => 'main_table.response_id'
            ]);

        $groupedValues = [];
        foreach ($connection->fetchAll($select) as $responseTextData) {
            $groupedValues[$responseTextData['response_id']][] = $responseTextData;
        }

        return $groupedValues;
    }

    /**
     * Check if quick response already exists
     *
     * @param int $quickResponseId
     * @return bool
     * @throws LocalizedException
     */
    private function ifExists($quickResponseId)
    {
        $this->searchCriteriaBuilder
            ->addFilter(QuickResponseInterface::ID, $quickResponseId)
            ->setCurrentPage(1)
            ->setPageSize(1);
        $items = $this->quickResponseRepository->getList(
            $this->searchCriteriaBuilder->create()
        )->getItems();

        return count($items) > 0;
    }

    /**
     * Create quick response
     *
     * @param array $quickResponseData
     * @throws LocalizedException
     */
    private function createQuickResponse($quickResponseData)
    {
        /** @var QuickResponseInterface $quickResponse */
        $quickResponse = $this->quickResponseFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $quickResponse,
            $quickResponseData,
            QuickResponseInterface::class
        );

        $this->quickResponseRepository->save($quickResponse);
    }
}
