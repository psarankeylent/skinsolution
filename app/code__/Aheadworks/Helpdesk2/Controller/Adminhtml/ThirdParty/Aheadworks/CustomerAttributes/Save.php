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
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\ThirdParty\Aheadworks\CustomerAttributes;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Backend\App\Action\Context;
use Aheadworks\Helpdesk2\Controller\Adminhtml\ActionWithJsonResponse;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Result\JsonDataFactory as JsonDataFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Permission\Manager as PermissionManager;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentPermissionInterface;

/**
 * Class Save
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\ThirdParty\Aheadworks\CustomerAttributes
 */
class Save extends ActionWithJsonResponse
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Magento_Customer::manage';

    /**
     * @var CommandInterface
     */
    private $saveCommand;

    /**
     * @var PermissionManager
     */
    private $permissionManager;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param JsonDataFactory $jsonDataFactory
     * @param CommandInterface $saveCommand
     * @param PermissionManager $permissionManager
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        JsonDataFactory $jsonDataFactory,
        CommandInterface $saveCommand,
        PermissionManager $permissionManager
    ) {
        parent::__construct($context, $resultJsonFactory, $jsonDataFactory);
        $this->saveCommand = $saveCommand;
        $this->permissionManager = $permissionManager;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $error = __('Something went wrong while saving customer attribute');
        if ($data = $this->getRequest()->getPostValue()) {
            try {
                if (isset($data['ticket_id']) && isset($data[PermissionManager::TICKET_ACTION])
                    && !$this->permissionManager->isAdminAbleToPerformTicketAction(
                        $data['ticket_id'],
                        $data[PermissionManager::TICKET_ACTION]
                    )) {
                    return $this->createErrorResponse(__('Not enough permissions save customer attribute'));
                }
                $this->saveCommand->execute($data);
                return $this->createSuccessResponse(__('Customer attribute was successfully saved'));
            } catch (LocalizedException $e) {
                return $this->createErrorResponse($e->getMessage());
            } catch (\RuntimeException $e) {
                return $this->createErrorResponse($e->getMessage());
            } catch (\Exception $e) {
                return $this->createErrorResponse($error);
            }
        }

        return $this->createErrorResponse($error);
    }
}
