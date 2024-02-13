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

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\Redirect as ResultRedirect;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page as ResultPage;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Api\GatewayRepositoryInterface;

/**
 * Class Edit
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Gateway
 */
class Edit extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::gateways';

    /**
     * @var GatewayRepositoryInterface
     */
    private $gatewayRepository;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @param Context $context
     * @param GatewayRepositoryInterface $gatewayRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        GatewayRepositoryInterface $gatewayRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->gatewayRepository = $gatewayRepository;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Edit action
     *
     * @return ResultPage|ResultRedirect
     */
    public function execute()
    {
        $gatewayId = (int)$this->getRequest()->getParam(GatewayDataInterface::ID);
        if (!$gatewayId) {
            /** @var ResultRedirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $this->messageManager->addErrorMessage(__('Invalid gateway ID. Should be numeric value greater than 0'));
            return $resultRedirect->setPath('*/*/');
        }

        try {
            $gateway = $this->gatewayRepository->get($gatewayId);
        } catch (NoSuchEntityException $exception) {
            $this->messageManager->addExceptionMessage(
                $exception,
                __('This gateway doesn\'t exist')
            );
            /** @var ResultRedirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('*/*/');
        }

        /** @var ResultPage $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addHandle(['aw_helpdesk2_gateway_type_' . $gateway->getType()]);
        $resultPage->setActiveMenu('Aheadworks_Helpdesk2::gateways');
        $pageTitle = __('Edit "%1" gateway', $gateway->getName());
        $resultPage->getConfig()->getTitle()->prepend($pageTitle);

        return $resultPage;
    }
}
