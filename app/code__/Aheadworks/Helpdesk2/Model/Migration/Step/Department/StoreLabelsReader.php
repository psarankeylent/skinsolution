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
namespace Aheadworks\Helpdesk2\Model\Migration\Step\Department;

use Aheadworks\Helpdesk2\Api\Data\StorefrontLabelInterface;
use Aheadworks\Helpdesk2\Model\Migration\Source\Helpdesk1TableNames;
use Magento\Framework\App\ResourceConnection;

/**
 * Class StoreLabelsReader
 *
 * @package Aheadworks\Helpdesk2\Model\Migration\Step\Department
 */
class StoreLabelsReader
{
    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

    /**
     * @param ResourceConnection $resourceConnection
     */
    public function __construct(
        ResourceConnection $resourceConnection
    ) {
        $this->resourceConnection = $resourceConnection;
    }

    /**
     * Retrieve department store labels
     *
     * @param $departmentId
     * @return array[]
     */
    public function getStoreLabels($departmentId)
    {
        $connection = $this->resourceConnection->getConnection();
        $select = $connection->select()
            ->from($connection->getTableName(Helpdesk1TableNames::DEPARTMENT_LABEL), ['store_id', 'label'])
            ->where('department_id = :id')
            ->columns([
                StorefrontLabelInterface::STORE_ID => 'store_id',
                StorefrontLabelInterface::CONTENT => 'label'
            ]);

        return $connection->fetchAll($select, ['id' => $departmentId]);
    }
}
