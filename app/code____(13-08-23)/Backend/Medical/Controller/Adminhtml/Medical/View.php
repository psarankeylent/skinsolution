<?php
namespace Backend\Medical\Controller\Adminhtml\Medical;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;


class View extends Action
{
    protected $resultRawFactory;
    protected $layoutFactory;

    public function __construct(
       Context $context,
       \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
       \Magento\Framework\View\LayoutFactory $layoutFactory
    ) {
       $this->resultRawFactory = $resultRawFactory;
       $this->layoutFactory = $layoutFactory;
       parent::__construct($context);
    }

    public function execute()
    {
       $content = $this->layoutFactory->create()
           ->createBlock(
               \Backend\Medical\Block\Adminhtml\Medical\View::class
           );

       /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
       $resultRaw = $this->resultRawFactory->create();
       return $resultRaw->setContents($content->toHtml());
    }
}
