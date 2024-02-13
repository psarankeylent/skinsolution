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

use Aheadworks\Helpdesk2\Api\Data\EmailInterface;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Api\GatewayRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Migration\Source\Helpdesk1TableNames;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Email as GatewayEmailResourceModel;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class GatewayEmails
 *
 * @package Aheadworks\Helpdesk2\Model\Migration\Step
 */
class GatewayEmails implements MigrationStepInterface
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @var GatewayRepositoryInterface
     */
    private $gatewayRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param ResourceConnection $resourceConnection
     * @param GatewayRepositoryInterface $gatewayRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        ResourceConnection $resourceConnection,
        GatewayRepositoryInterface $gatewayRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->resourceConnection = $resourceConnection;
        $this->gatewayRepository = $gatewayRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function migrate($limit)
    {
        $emailsToMigrate = $this->getEmailsToMigrate();
        $emailsToMigrate = $this->resolveGateways($emailsToMigrate);
        $migratedEntityCount = count($emailsToMigrate);

        if (!empty($emailsToMigrate)) {
            $this->saveToDataBase(GatewayEmailResourceModel::MAIN_TABLE_NAME, $emailsToMigrate);
        }

        return $migratedEntityCount . ' gateway emails were migrated';
    }

    /**
     * Get gateway emails to migrate
     *
     * @return array[]
     */
    private function getEmailsToMigrate()
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection
            ->select()
            ->from(
                ['main_table' => $connection->getTableName(Helpdesk1TableNames::DEPARTMENT_GATEWAY_EMAIL)],
                [
                    EmailInterface::ID => 'main_table.id',
                    EmailInterface::UID => 'main_table.uid',
                    EmailInterface::GATEWAY_ID => 'main_table.gateway_email',
                    EmailInterface::FROM => 'main_table.from',
                    EmailInterface::TO => 'main_table.to',
                    EmailInterface::STATUS => 'main_table.status',
                    EmailInterface::SUBJECT => 'main_table.subject',
                    EmailInterface::BODY => 'main_table.body',
                    EmailInterface::HEADERS => 'main_table.headers',
                    EmailInterface::CONTENT_TYPE => 'main_table.content_type',
                    EmailInterface::CREATED_AT => 'main_table.created_at',
                ]
            );

        return $connection->fetchAll($select);
    }

    /**
     * Resolve gateway_id by gateway_email
     *
     * @param array $emailsToMigrate
     * @return array
     */
    private function resolveGateways($emailsToMigrate)
    {
        $resolvedGateways = [];

        $processed = [];
        foreach ($emailsToMigrate as $emailData) {
            $gatewayEmail = $emailData[EmailInterface::GATEWAY_ID];
            if (!array_key_exists($gatewayEmail, $resolvedGateways)) {
                try {
                    $gateway = $this->getGatewayByEmail($gatewayEmail);
                } catch (LocalizedException $exception) {
                    continue;
                }
                $resolvedGateways[$gatewayEmail] = $gateway->getId();
            }
            $emailData[EmailInterface::GATEWAY_ID] = $resolvedGateways[$gatewayEmail];

            $processed[] = $emailData;
        }

        return $processed;
    }

    /**
     * Retrieve gateway by email
     *
     * @param string $email
     * @return \Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface
     * @throws LocalizedException
     */
    private function getGatewayByEmail($email) {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(GatewayDataInterface::EMAIL, $email)
            ->create();

        $items = $this->gatewayRepository->getList($searchCriteria)->getItems();

        return reset($items);
    }

    /**
     * Save data to database
     *
     * @param string $table
     * @param array $data
     */
    private function saveToDataBase($table, $data)
    {
        $this->resourceConnection
            ->getConnection()
            ->insertOnDuplicate($this->resourceConnection->getTableName($table), $data);
    }
}
