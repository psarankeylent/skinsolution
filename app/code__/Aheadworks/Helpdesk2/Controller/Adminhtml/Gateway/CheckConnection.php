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
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Gateway;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\Json as ResultJson;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;

/**
 * Class CheckConnection
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Gateway
 */
class CheckConnection extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::gateways';

    /**
     * @var JsonFactory
     */
    private $resultJsonFactory;

    /**
     * @var CommandInterface
     */
    private $checkConnectionCommand;

    /**
     * @param Context $context
     * @param CommandInterface $checkConnectionCommand
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        CommandInterface $checkConnectionCommand,
        JsonFactory $resultJsonFactory
    ) {
        $this->checkConnectionCommand = $checkConnectionCommand;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var ResultJson $resultJson */
        $resultJson = $this->resultJsonFactory->create();
        $gatewayParams = $this->getRequest()->getParams();

        try {
            $this->checkConnectionCommand->execute($gatewayParams);
            $result = [
                'error'     => false,
                'message'   => __('Success')
            ];
        } catch (\Exception $e) {
            $result = [
                'error'     => true,
                'message'   => __($e->getMessage())
            ];
        }

        return $resultJson->setData($result);
    }
}
