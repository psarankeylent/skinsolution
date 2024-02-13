<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Renewal\NotificationSchedule\Controller\Adminhtml\NotificationSchedule;

class Delete extends \Renewal\NotificationSchedule\Controller\Adminhtml\NotificationSchedule
{

    /**
     * Delete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('notificationschedule_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\Renewal\NotificationSchedule\Model\NotificationSchedule::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Notificationschedule.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['notificationschedule_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Notificationschedule to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}

