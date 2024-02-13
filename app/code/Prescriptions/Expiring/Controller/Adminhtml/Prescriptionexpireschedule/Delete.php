<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Prescriptions\Expiring\Controller\Adminhtml\Prescriptionexpireschedule;

class Delete extends \Prescriptions\Expiring\Controller\Adminhtml\Prescriptionexpireschedule
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
        $id = $this->getRequest()->getParam('prescription_expire_schedule_id');
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create(\Prescriptions\Expiring\Model\PrescriptionExpireSchedule::class);
                $model->load($id);
                $model->delete();
                // display success message
                $this->messageManager->addSuccessMessage(__('You deleted the Prescription Expire Schedule.'));
                // go to grid
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                // display error message
                $this->messageManager->addErrorMessage($e->getMessage());
                // go back to edit form
                return $resultRedirect->setPath('*/*/edit', ['prescription_expire_schedule_id' => $id]);
            }
        }
        // display error message
        $this->messageManager->addErrorMessage(__('We can\'t find a Prescription Expire Schedule to delete.'));
        // go to grid
        return $resultRedirect->setPath('*/*/');
    }
}

