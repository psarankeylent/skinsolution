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

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action as BackendAction;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation\Collection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Automation\CollectionFactory;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;

/**
 * Class AbstractMassAction
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Automation
 */
abstract class AbstractMassAction extends BackendAction
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::automations';

    /**
     * @var CommandInterface
     */
    protected $massActionCommand;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param CommandInterface $massActionCommand
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CommandInterface $massActionCommand
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->massActionCommand = $massActionCommand;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        try {
            /** @var Collection $collection */
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            return $this->massAction($collection);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            return $resultRedirect->setPath('*/*/index');
        }
    }

    /**
     * Performs mass action
     *
     * @param Collection $collection
     * @return ResultInterface|ResponseInterface
     */
    abstract protected function massAction(Collection $collection);
}
