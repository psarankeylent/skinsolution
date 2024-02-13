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

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page as ResultPage;
use Aheadworks\Helpdesk2\Api\AutomationRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;

/**
 * Class Edit
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Automation
 */
class Edit extends Action
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
     * @param AutomationRepositoryInterface $automationRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        AutomationRepositoryInterface $automationRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->automationRepository = $automationRepository;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Edit action
     *
     * @return ResultPage|ResultRedirect
     */
    public function execute()
    {
        $automationId = (int)$this->getRequest()->getParam(AutomationInterface::ID);
        if ($automationId) {
            try {
                $automation = $this->automationRepository->get($automationId);
                $pageTitle = __('Edit "%1" automation', $automation->getName());
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('This automation doesn\'t exist')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/');

                return $resultRedirect;
            }
        } else {
            $pageTitle = __('New automation');
        }
        /** @var ResultPage $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Aheadworks_Helpdesk2::automations')
            ->getConfig()->getTitle()->prepend($pageTitle);

        return $resultPage;
    }
}
