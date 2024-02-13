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

use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect as ResultRedirect;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Permission\Manager as PermissionManager;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentPermissionInterface;

/**
 * Class Reply
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Ticket
 */
class Reply extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::tickets';

    /**
     * @var CommandInterface
     */
    private $replyCommand;

    /**
     * @var ProcessorInterface
     */
    private $postDataProcessor;

    /**
     * @var PermissionManager
     */
    private $permissionManager;

    /**
     * @param Context $context
     * @param CommandInterface $replyCommand
     * @param ProcessorInterface $postDataProcessor
     * @param PermissionManager $permissionManager
     */
    public function __construct(
        Context $context,
        CommandInterface $replyCommand,
        ProcessorInterface $postDataProcessor,
        PermissionManager $permissionManager
    ) {
        parent::__construct($context);
        $this->replyCommand = $replyCommand;
        $this->postDataProcessor = $postDataProcessor;
        $this->permissionManager = $permissionManager;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        if ($data = $this->getRequest()->getPostValue()) {
            try {
                $data = $this->postDataProcessor->prepareEntityData($data);
                if (isset($data[TicketInterface::ENTITY_ID])
                    && !$this->permissionManager->isAdminAbleToPerformTicketAction(
                        $data[TicketInterface::ENTITY_ID],
                        DepartmentPermissionInterface::TYPE_UPDATE
                    )) {
                    $this->messageManager->addErrorMessage(__('Not enough permissions to send a reply'));
                    $resultRedirect = $this->resultRedirectFactory->create();
                    return $resultRedirect->setPath('*/*/');
                }
                $this->replyCommand->execute($data);
                $this->messageManager->addSuccessMessage(__('Message was successfully sent'));
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while sending the message'));
            }
        }

        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('*/*/');
    }
}
