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
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Department;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Page as ResultPage;
use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;

/**
 * Class Edit
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Department
 */
class Edit extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::departments';

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var DepartmentRepositoryInterface
     */
    private $departmentRepository;

    /**
     * @param Context $context
     * @param DepartmentRepositoryInterface $departmentRepository
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        Context $context,
        DepartmentRepositoryInterface $departmentRepository,
        PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->departmentRepository = $departmentRepository;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Edit action
     *
     * @return ResultPage|ResultRedirect
     */
    public function execute()
    {
        $departmentId = (int)$this->getRequest()->getParam(DepartmentInterface::ID);
        if ($departmentId) {
            try {
                $department = $this->departmentRepository->get($departmentId);
                $pageTitle = __('Edit "%1" department', $department->getName());
            } catch (NoSuchEntityException $exception) {
                $this->messageManager->addExceptionMessage(
                    $exception,
                    __('This department doesn\'t exist')
                );
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/*/');

                return $resultRedirect;
            }
        } else {
            $pageTitle = __('New department');
        }
        /** @var ResultPage $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Aheadworks_Helpdesk2::departments')
            ->getConfig()->getTitle()->prepend($pageTitle);

        return $resultPage;
    }
}
