<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Prescriptions\Expiring\Controller\Adminhtml\Prescriptionexpireschedule;

class Edit extends \Prescriptions\Expiring\Controller\Adminhtml\Prescriptionexpireschedule
{

    protected $resultPageFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context, $coreRegistry);
    }

    /**
     * Edit action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('prescription_expire_schedule_id');
        $model = $this->_objectManager->create(\Prescriptions\Expiring\Model\PrescriptionExpireSchedule::class);
        
        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addErrorMessage(__('This Prescription Expire Schedule no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->_coreRegistry->register('prescriptions_expiring_prescription_expire_schedule', $model);
        
        // 3. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $id ? __('Edit Prescription Expire Schedule') : __('New Prescription Expire Schedule'),
            $id ? __('Edit Prescription Expire Schedule') : __('New Prescription Expire Schedule')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Prescription Expire Schedules'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? __('Edit Prescription Expire Schedule %1', $model->getId()) : __('New Prescription Expire Schedule'));
        return $resultPage;
    }
}

