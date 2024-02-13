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
namespace Aheadworks\Helpdesk2\Controller\Adminhtml;

use Aheadworks\Helpdesk2\Api\Data\Result\JsonDataInterface;
use Aheadworks\Helpdesk2\Model\Result\JsonDataFactory as JsonDataFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

/**
 * Class ActionWithJsonResponse
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Ticket
 */
abstract class ActionWithJsonResponse extends Action
{
    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var JsonDataFactory
     */
    private $jsonDataFactory;

    /**
     * @param Context $context
     * @param JsonFactory $resultJsonFactory
     * @param JsonDataFactory $jsonDataFactory
     */
    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        JsonDataFactory $jsonDataFactory
    ) {
        parent::__construct($context);
        $this->resultJsonFactory = $resultJsonFactory;
        $this->jsonDataFactory = $jsonDataFactory;
    }

    /**
     * Create json data object
     * 
     * @return \Aheadworks\Helpdesk2\Model\Result\JsonData
     */
    protected function createJsonDataObject()
    {
        return $this->jsonDataFactory->create();
    }

    /**
     *  Create json result object
     * 
     * @param JsonDataInterface $jsonData
     * @param int|null $httpResponseCode
     * @return \Magento\Framework\Controller\Result\Json
     */
    protected function createResponse(JsonDataInterface $jsonData, $httpResponseCode = null)
    {
        $result = $this->resultJsonFactory->create();
        $result->setData($jsonData);
        if ($httpResponseCode) {
            $result->setHttpResponseCode($httpResponseCode);
        }

        return $result;
    }

    /**
     * Create success json result object
     *
     * @param \Magento\Framework\Phrase|string $message
     * @param array $data
     * @param int|null $httpResponseCode
     * @return \Magento\Framework\Controller\Result\Json
     */
    protected function createSuccessResponse($message = '', $data = [], $httpResponseCode = null)
    {
        $jsonData = $this->jsonDataFactory->create();
        $jsonData
            ->addMessage($message)
            ->setData($data);
        
        return $this->createResponse($jsonData, $httpResponseCode);
    }

    /**
     * Create error json result object
     *
     * @param \Magento\Framework\Phrase|string $message
     * @param array $data
     * @param int $httpResponseCode
     * @return \Magento\Framework\Controller\Result\Json
     */
    protected function createErrorResponse($message, $data = [], $httpResponseCode = null)
    {
        $jsonData = $this->jsonDataFactory->create();
        $jsonData
            ->setError()
            ->addMessage($message)
            ->setData($data);

        return $this->createResponse($jsonData, $httpResponseCode);
    }
}
