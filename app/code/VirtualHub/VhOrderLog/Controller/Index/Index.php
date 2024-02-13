<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace VirtualHub\VhOrderLog\Controller\Index;

use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;

class Index implements HttpGetActionInterface
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * Constructor
     *
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \VirtualHub\VhOrderLog\Model\VhOrderLogFactory  $vhOrderLogFactory,
        PageFactory $resultPageFactory)
    {
        $this->vhOrderLogFactory = $vhOrderLogFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        
        $vhOrderLogCollection = $this->vhOrderLogFactory->create()->getCollection();
        echo "<pre>";
        print_r($vhOrderLogCollection->getData());
        exit;


        return $this->resultPageFactory->create();
    }
}

