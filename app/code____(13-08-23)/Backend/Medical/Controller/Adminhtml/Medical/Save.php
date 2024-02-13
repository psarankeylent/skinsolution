<?php

declare(strict_types=1);

namespace Backend\Medical\Controller\Adminhtml\Medical;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{

    protected $dataPersistor;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl,
        \VirtualHub\Config\Helper\Config $configHelper,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
    ) {
        $this->curl = $curl;
        $this->configHelper = $configHelper;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context);
    }

    /**
     * Save action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if ($data) {
            $id = $this->getRequest()->getParam('id');
        
            $model = $this->_objectManager->create(\Backend\Medical\Model\Medical::class)->load($id);
            if (!$model->getId() && $id) {
                $this->messageManager->addErrorMessage(__('This Medical no longer exists.'));
                return $resultRedirect->setPath('*/*/');
            }
        
            $model->setData($data);
        
            try {
                $model->save();

                $request['customer_id'] = $model->getData('customer_id');
                $bearerToken = $this->configHelper->getVirtualHubBearerToken();
                if ($bearerToken['success'] == True) {
                    $token = $bearerToken['token'];
                    $vhUrl = $this->configHelper->getUpdateMedicalHistory();
                    $headers = ["Content-Type" => "application/json", "Authorization" => 'Bearer ' . $token];
                    $this->curl->setHeaders($headers);
                    $this->curl->post($vhUrl, json_encode($request));
                    $response = $this->curl->getBody();
                } 

                $this->messageManager->addSuccessMessage(__('You saved the Medical.'));
                $this->dataPersistor->clear('backend_medical_medical');
        
                if ($this->getRequest()->getParam('back')) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Medical.'));
            }
        
            $this->dataPersistor->set('backend_medical_medical', $data);
            return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }
}

