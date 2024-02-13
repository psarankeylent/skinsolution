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

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\Model\View\Result\Redirect as ResultRedirect;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\Collection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\CollectionFactory;
use Aheadworks\Helpdesk2\Plugin\Customer\Block\Adminhtml\PersonalInfoTabPlugin;

/**
 * Class MassDelete
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Ticket
 */
class MassDelete extends AbstractMassAction
{
    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    protected function massAction(Collection $collection)
    {
        $deletedRecords = 0;
        $ticket = $collection->getFirstItem();
        $customerId = $this->getCustomerId($ticket);
        $orderId = $this->getOrderId($ticket);
        foreach ($collection->getAllIds() as $ticketId) {
            $this->massActionCommand->execute([TicketInterface::ENTITY_ID => $ticketId]);
            $deletedRecords++;
        }

        if ($deletedRecords) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) have been deleted.', $deletedRecords));
        } else {
            $this->messageManager->addSuccessMessage(__('No records have been deleted.'));
        }

        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        if ($this->getRequest()->getParam('redirect-to-customer')) {
            $resultRedirect->setPath(
                'customer/index/edit',
                [
                    'id' => $customerId,
                    PersonalInfoTabPlugin::PARAM_TO_TRIGGER => PersonalInfoTabPlugin::PARAM_VALUE
                ]
            );
        } elseif ($this->getRequest()->getParam('redirect-to-order')) {
            $resultRedirect->setPath(
                'sales/order/view',
                [
                    'order_id' => $orderId,
                    PersonalInfoTabPlugin::PARAM_TO_TRIGGER => PersonalInfoTabPlugin::PARAM_VALUE
                ]
            );
        } else {
            $resultRedirect->setPath('*/*/');
        }

        return $resultRedirect;
    }

    /**
     * Retrieve customer id from first ticket
     *
     * @param TicketInterface|DataObject $ticket
     * @return int|null
     */
    private function getCustomerId($ticket)
    {
        return $ticket && $ticket->getCustomerId()
            ? $ticket->getCustomerId()
            : null;
    }

    /**
     * Retrieve order id from first ticket
     *
     * @param TicketInterface|DataObject $ticket
     * @return int|null
     */
    private function getOrderId($ticket)
    {
        return $ticket && $ticket->getOrderId()
            ? $ticket->getOrderId()
            : null;
    }
}
