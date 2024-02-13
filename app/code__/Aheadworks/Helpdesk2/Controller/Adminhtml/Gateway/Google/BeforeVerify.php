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
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Gateway\Google;

use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\Json as ResultJson;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;

/**
 * Class BeforeVerify
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Gateway\Google
 */
class BeforeVerify extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::gateways';

    const GATEWAY_DATA = 'aw_helpdesk2_gateway_data';

    /**
     * @var SessionManagerInterface
     */
    private $sessionManager;

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @param Context $context
     * @param SessionManagerInterface $sessionManager
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        SessionManagerInterface $sessionManager,
        JsonFactory $resultJsonFactory
    ) {
        $this->sessionManager = $sessionManager;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $gatewayParams = $this->getRequest()->getParams();
        if ($gatewayParams) {
            $this->sessionManager->setData(self::GATEWAY_DATA, $gatewayParams);
        }

        $result = [
            'error'     => false,
            'message'   => __('Success')
        ];

        /** @var ResultJson $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        return $resultJson->setData($result);
    }
}
