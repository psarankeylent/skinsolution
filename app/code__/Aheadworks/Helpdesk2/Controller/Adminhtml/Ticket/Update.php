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
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Ticket;

use Aheadworks\Helpdesk2\Controller\Adminhtml\ActionWithJsonResponse;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Result\JsonDataFactory as JsonDataFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Permission\Manager as PermissionManager;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;

/**
 * Class Update
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Ticket
 */
class Update extends ActionWithJsonResponse
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::tickets';

    /**
     * @var CommandInterface
     */
    private $updateCommand;

    /**
     * @var PermissionManager
     */
    private $permissionManager;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param JsonDataFactory $jsonDataFactory
     * @param CommandInterface $updateCommand
     * @param PermissionManager $permissionManager
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        JsonDataFactory $jsonDataFactory,
        CommandInterface $updateCommand,
        PermissionManager $permissionManager
    ) {
        parent::__construct($context, $resultJsonFactory, $jsonDataFactory);
        $this->updateCommand = $updateCommand;
        $this->permissionManager = $permissionManager;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        if ($data = $this->getRequest()->getPostValue()) {
            try {
                if (isset($data[TicketInterface::ENTITY_ID]) && isset($data[PermissionManager::TICKET_ACTION])
                    && !$this->permissionManager->isAdminAbleToPerformTicketAction(
                        $data[TicketInterface::ENTITY_ID],
                        $data[PermissionManager::TICKET_ACTION]
                    )) {
                    return $this->createErrorResponse(__('Not enough permissions to update the ticket'));
                }
                $this->updateCommand->execute($data);
                return $this->createSuccessResponse(__('Ticket was successfully saved.'));
            } catch (LocalizedException $e) {
                return $this->createErrorResponse($e->getMessage());
            } catch (\RuntimeException $e) {
                return $this->createErrorResponse($e->getMessage());
            } catch (\Exception $e) {
                return $this->createErrorResponse(__('Something went wrong while saving the ticket'));
            }
        }

        return $this->createErrorResponse(__('Something went wrong while saving ticket.'));
    }
}
