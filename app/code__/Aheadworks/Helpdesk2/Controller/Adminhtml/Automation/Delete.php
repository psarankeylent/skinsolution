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
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Automation;

use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;
use Aheadworks\Helpdesk2\Api\AutomationRepositoryInterface;

/**
 * Class Delete
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Automation
 */
class Delete extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::automations';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var AutomationRepositoryInterface
     */
    private $automationRepository;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param AutomationRepositoryInterface $automationRepository
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        AutomationRepositoryInterface $automationRepository
    ) {
        parent::__construct($context);
        $this->resultPageFactory = $resultPageFactory;
        $this->automationRepository = $automationRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $automationId = (int)$this->getRequest()->getParam(AutomationInterface::ID);
        if ($automationId) {
            try {
                $this->automationRepository->deleteById($automationId);
                $this->messageManager->addSuccessMessage(__('Automation was successfully deleted'));
                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $exception) {
                $this->messageManager->addErrorMessage($exception->getMessage());
            }
        }
        $this->messageManager->addErrorMessage(__('Something went wrong while deleting the automation'));

        return $resultRedirect->setPath('*/*/');
    }
}
