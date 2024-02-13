<?php

declare(strict_types=1);

namespace Backend\Medical\Controller\Adminhtml\Medical;

class SaveMH extends \Magento\Backend\App\Action
{
    /**
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context
    ) {
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

        $resultJsonFactory = $this->_objectManager->create('Magento\Framework\Controller\Result\JsonFactory');
        $resultJson = $resultJsonFactory->create();
        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $id = $this->getRequest()->getParam('id');

            $model = $this->_objectManager->create(\Backend\Medical\Model\Medical::class)->load($id);
            if (!$model->getId() && $id) {

                $resultJson->setData([
                    'error' => 1,
                    'message' => "Error found.",
                    'status' =>  0
                ]);
            }
            try {
                $model->setResponse($data['response'])
                    ->setUpdatedAt(date('Y-m-d H:i:s'))
                    ->save();

                $resultJson->setData([
                    'error' => 0,
                    'message' => "Field Updated.",
                    'status' =>  1
                ]);

            } catch (\Exception $e) {
                $resultJson->setData([
                    'error' => 1,
                    'message' => $e->getMessage(),
                    'status' =>  0
                ]);
            }

            return $resultJson;
        }
    }

    /*
    public function sendToVH($customerId)
    {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/medical_history.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $request['customer_id'] = $customerId;
        $bearerToken = $this->configHelper->getVirtualHubBearerToken();
        if ($bearerToken['success'] == True) {
            $token = $bearerToken['token'];
            $vhUrl = $this->configHelper->getUpdateMedicalHistory();
            $headers = ["Content-Type" => "application/json", "Authorization" => 'Bearer ' . $token];
            $this->curl->setHeaders($headers);
            $this->curl->post($vhUrl, json_encode($request));
            $response = $this->curl->getBody();
            $logger->info($response);
        } 

    }
    */
    


}



