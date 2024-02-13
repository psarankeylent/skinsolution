<?php

namespace Backend\InlineEdit\Controller\Adminhtml\Product;

use \Magento\Framework\Serialize\SerializerInterface;

class InlineEdit extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var \Magento\Catalog\Model\ProductFactory $productFactory
     */
    protected $productFactory;

    /**
    * @param \Magento\Backend\App\Action\Context $context
    * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    * @param \Magento\Catalog\Model\ProductFactory $productFactory
    */

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        parent::__construct($context);
        $this->jsonFactory          = $jsonFactory;
        $this->_productFactory      = $productFactory;
        $this->productRepository    = $productRepository;
        $this->authSession          = $authSession;
    }

    /**
     * @return \Magento\Framework\Controller\Result\JsonFactory
     */
    public function execute()
    {
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')],
                'error' => true,
            ]);
        }

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/backend_user_action.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("Inline Qty updated by : ".$this->authSession->getUser()->getUsername());

        $additionalcontentData  = '';

        foreach ($postItems as $productValues)
        {
            $productId  = $productValues['entity_id'];
            $qty        = $productValues['qty'];
            $additionalcontentData  = $productValues['product_additionalcontent'];
        }

        if($productId){

            try {
                $product = $this->_productFactory->create();
                $product->load($productId);

                $product->setStockData(['qty' => $qty, 'is_in_stock' => 1]);
                $product->setQuantityAndStockStatus(['qty' => $qty, 'is_in_stock' => 1]);

                $this->productRepository->save($product);

		$logger->info("        Product ID:".$product->getSku()."   SKU: ".$qty);

            } catch (\Magento\Framework\Model\Exception $e) {
                $messages = $this->messageManager->addError($e->getMessage());
            }
        }

        $this->updateProductText($productId, $additionalcontentData);
        
        return $resultJson->setData([
            'messages' => json_encode($messages),
            'error' => $error
        ]);
        
    }

    public function updateProductText($productId, $additionalContent)
    {
        $objectManager  = \Magento\Framework\App\ObjectManager::getInstance();
        $resource       = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection     = $resource->getConnection();
        $tableName      = $resource->getTableName('catalog_product_entity_text'); 
        $attributeId    = 406;

        $updateData     = [
            'value' => $additionalContent
        ];

        $where = [
            'entity_id = ?' => $productId,
            'attribute_id = ?' => $attributeId
        ];

        $connection->update($tableName, $updateData, $where);
    }

}

