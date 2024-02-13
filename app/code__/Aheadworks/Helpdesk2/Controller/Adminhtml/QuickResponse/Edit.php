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
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\QuickResponse;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page as ResultPage;
use Aheadworks\Helpdesk2\Api\QuickResponseRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface;

/**
 * Class Edit
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\QuickResponse
 */
class Edit extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::quick_responses';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var QuickResponseRepositoryInterface
     */
    private $quickResponseRepository;

    /**
     * @param Context $context
     * @param QuickResponseRepositoryInterface $quickResponseRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        QuickResponseRepositoryInterface $quickResponseRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->quickResponseRepository = $quickResponseRepository;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Edit action
     *
     * @return ResultPage|ResultRedirect
     */
    public function execute()
    {
        $quickResponseId = (int)$this->getRequest()->getParam(QuickResponseInterface::ID);
        if ($quickResponseId) {
            try {
                $quickResponse = $this->quickResponseRepository->get($quickResponseId);
                $pageTitle = __('Edit "%1" quick response', $quickResponse->getTitle());
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('This quick response doesn\'t exist')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/');

                return $resultRedirect;
            }
        } else {
            $pageTitle = __('New quick response');
        }
        /** @var ResultPage $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Aheadworks_Helpdesk2::quick_responses')
            ->getConfig()->getTitle()->prepend($pageTitle);

        return $resultPage;
    }
}
