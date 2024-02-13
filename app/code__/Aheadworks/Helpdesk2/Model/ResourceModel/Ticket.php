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
namespace Aheadworks\Helpdesk2\Model\ResourceModel;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\Data\MessageAttachmentInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Grid as TicketGridResourceModel;
use Aheadworks\Helpdesk2\Model\Source\Department\AgentList as AgentListSource;
use Aheadworks\Helpdesk2\Model\Ticket as TicketModel;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Message as MessageResource;
use Magento\Eav\Model\Entity\AbstractEntity;
use Magento\Eav\Model\Entity\Context;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Ticket
 *
 * @package Aheadworks\Helpdesk2\Model\ResourceModel
 */
class Ticket extends AbstractEntity
{
    /**#@+
     * Constants defined for table names
     */
    const TICKET_ENTITY_TABLE_NAME = 'aw_helpdesk2_ticket_entity';
    const TICKET_ENTITY_INT_TABLE_NAME = 'aw_helpdesk2_ticket_entity_int';
    const TICKET_ENTITY_VARCHAR_TABLE_NAME = 'aw_helpdesk2_ticket_entity_varchar';
    const TICKET_ENTITY_TEXT_TABLE_NAME = 'aw_helpdesk2_ticket_entity_text';
    const TICKET_EAV_ATTRIBUTE_TABLE_NAME = 'aw_helpdesk2_ticket_eav_attribute';
    const TICKET_OPTION_TABLE_NAME = 'aw_helpdesk2_ticket_option';
    /**#@-*/

    /**
     * EntityManager
     */
    private $entityManager;

    /**
     * @param Context $context
     * @param EntityManager $entityManager
     * @param array $data
     */
    public function __construct(
        Context $context,
        EntityManager $entityManager,
        $data = []
    ) {
        $this->entityManager = $entityManager;
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    public function getEntityType()
    {
        if (empty($this->_type)) {
            $this->setType(TicketModel::ENTITY);
        }

        return parent::getEntityType();
    }

    /**
     * Save an object
     *
     * @param AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function save(AbstractModel $object)
    {
        $object->validateBeforeSave();
        $object->beforeSave();
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * Load an object
     *
     * @param AbstractModel $object
     * @param int $objectId
     * @param string $field
     * @return $this
     */
    public function load($object, $objectId, $field = null)
    {
        if (!empty($objectId)) {
            $this->entityManager->load($object, $objectId, []);
            $object->afterLoad();
        }
        return $this;
    }

    /**
     * Delete an object
     *
     * @param AbstractModel $object
     * @return $this
     * @throws \Exception
     */
    public function delete($object)
    {
        $this->entityManager->delete($object);
        return $this;
    }

    /**
     * Get ticket ID by external link
     *
     * @param string $link
     * @return int|false
     */
    public function getTicketIdByExternalLink($link)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getEntityTable(), $this->getIdFieldName())
            ->where('external_link = :external_link');

        return $connection->fetchOne($select, ['external_link' => $link]);
    }

    /**
     * Get ticket ID by UID
     *
     * @param string $uid
     * @return int|false
     */
    public function getTicketIdByUid($uid)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from($this->getEntityTable(), $this->getIdFieldName())
            ->where('uid = :uid');

        return $connection->fetchOne($select, ['uid' => $uid]);
    }

    /**
     * Get ticket attachments
     *
     * @param int $ticketId
     * @return array
     */
    public function getTicketAttachments($ticketId)
    {
        $connection = $this->getConnection();
        $select = $connection->select()
            ->from(['e' => $this->getEntityTable()], [])
            ->join(
                ['message_tbl' => $this->getTable(MessageResource::MAIN_TABLE_NAME)],
                'e.entity_id = message_tbl.ticket_id',
                []
            )->join(
                ['attach_tbl' => $this->getTable(MessageResource::ATTACHMENT_TABLE_NAME)],
                'message_tbl.id = attach_tbl.message_id',
                [MessageAttachmentInterface::FILE_NAME, MessageAttachmentInterface::FILE_PATH]
            )
            ->where('e.entity_id = ?', $ticketId);

        return $connection->fetchAll($select);
    }

    /**
     * Reset relation with user after deleting it
     *
     * @param int $agentId
     */
    public function resetAgentId($agentId)
    {
        $connection = $this->getConnection();
        $tableEntity = $this->getEntityTable();
        $tableGrid = TicketGridResourceModel::MAIN_TABLE_NAME;

        $connection->update(
            $tableEntity,
            [TicketInterface::AGENT_ID => AgentListSource::NOT_ASSIGNED_VALUE],
            [TicketInterface::AGENT_ID . ' = ?' => $agentId]
        );
        $connection->update(
            $tableGrid,
            [TicketInterface::AGENT_ID => AgentListSource::NOT_ASSIGNED_VALUE],
            [TicketInterface::AGENT_ID . ' = ?' => $agentId]
        );
    }
}
