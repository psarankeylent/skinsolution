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
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Rejecting\Pattern;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page as ResultPage;
use Aheadworks\Helpdesk2\Api\RejectingPatternRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface;

/**
 * Class Edit
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Rejecting\Pattern
 */
class Edit extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::rejecting_patterns';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var RejectingPatternRepositoryInterface
     */
    private $patternRepository;

    /**
     * @param Context $context
     * @param RejectingPatternRepositoryInterface $patternRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        RejectingPatternRepositoryInterface $patternRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->patternRepository = $patternRepository;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Edit action
     *
     * @return ResultPage|ResultRedirect
     */
    public function execute()
    {
        $patternId = (int)$this->getRequest()->getParam(RejectingPatternInterface::ID);
        if ($patternId) {
            try {
                $pattern = $this->patternRepository->get($patternId);
                $pageTitle = __('Edit "%1" pattern', $pattern->getTitle());
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('This pattern doesn\'t exist')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/');

                return $resultRedirect;
            }
        } else {
            $pageTitle = __('New pattern');
        }
        /** @var ResultPage $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Aheadworks_Helpdesk2::rejecting_patterns')
            ->getConfig()->getTitle()->prepend($pageTitle);

        return $resultPage;
    }
}
