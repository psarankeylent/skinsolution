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

use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Api\GatewayRepositoryInterface;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Delete
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Gateway
 */
class Delete extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::gateways';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var GatewayRepositoryInterface
     */
    protected $gatewayRepository;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param GatewayRepositoryInterface $gatewayRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        GatewayRepositoryInterface $gatewayRepository
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->gatewayRepository = $gatewayRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $gatewayId = (int)$this->getRequest()->getParam(GatewayDataInterface::ID);
        if ($gatewayId) {
            try {
                $this->gatewayRepository->deleteById($gatewayId);
                $this->messageManager->addSuccessMessage(__('Gateway was successfully deleted'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
        }
        $this->messageManager->addErrorMessage(__('Something went wrong while deleting the gateway'));

        return $resultRedirect->setPath('*/*/');
    }
}
