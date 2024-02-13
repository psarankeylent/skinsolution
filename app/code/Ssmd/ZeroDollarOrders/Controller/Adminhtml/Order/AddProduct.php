<?php
/**
 * @package Ssmd_ZeroDollarOrders
 * @version 1.0.0
 * @category magento-module
 */
declare(strict_types=1);

namespace Ssmd\ZeroDollarOrders\Controller\Adminhtml\Order;
/**
 * AddProduct class
 */
class AddProduct extends \Magento\Backend\App\Action
{

    /**
     * resultPageFactory variable
     *
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * jsonHelper variable
     *
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;
    /**
     * productRepository variable
     *
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * Constructor function
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->productRepository = $productRepository;
        $this->jsonHelper = $jsonHelper;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        // Get SKU by request
        $sku = $this->getRequest()->getPostValue('sku') ?? null;
        try {
            // Load Product by SKU
            $product = $this->productRepository->get($sku);
            if(!$product) {
                $data = ["status" => false,"msg" => "Product not found!"];
            } elseif(!$product->isSaleable()){
                $data = ["status" => false,"msg" => "Product Is Not Saleable!"];
            } else {
                $data = [
                    "status" => true,
                    "prescription" => $product->getPrescription() ? $product->getAttributeText('prescription'): null,
                    "name" => $product->getName(),
                    "sku" => $product->getSku(),
                    "final_price" => $product->getFinalPrice(),
                    "special_price" => $product->getSpecialPrice(),
                    "id" => $product->getId()
                ];
            }
            // Return JSON response
            return $this->jsonResponse($data);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonResponse($e->getMessage());
        }
    }

    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }
}
