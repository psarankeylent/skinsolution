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
namespace Aheadworks\Helpdesk2\Model\ResourceModel\Ticket;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Ticket\GridInterface as TicketGridInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid\DataProcessor;

/**
 * Class Grid
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel\Ticket
 */
class Grid extends AbstractDb
{
    const MAIN_TABLE_NAME = 'aw_helpdesk2_ticket_grid';

    /**
     * @var DataProcessor
     */
    private $dataProcessor;

    /**
     * @param Context $context
     * @param DataProcessor $dataProcessor
     * @param string|null $connectionName
     */
    public function __construct(
        Context $context,
        DataProcessor $dataProcessor,
        $connectionName = null
    ) {
        $this->dataProcessor = $dataProcessor;
        parent::__construct($context, $connectionName);
    }

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, TicketGridInterface::ENTITY_ID);
    }

    /**
     * Refresh data in ticket grid
     *
     * @param int|TicketInterface $entity
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function refresh($entity)
    {
        $data = $entity instanceof TicketInterface
            ? $this->dataProcessor->prepareAggregatedDataByEntity($entity)
            : $this->dataProcessor->prepareAggregatedDataByEntityId($entity);

        $this->getConnection()
            ->insertOnDuplicate($this->getTable(self::MAIN_TABLE_NAME), $data);
    }
}
