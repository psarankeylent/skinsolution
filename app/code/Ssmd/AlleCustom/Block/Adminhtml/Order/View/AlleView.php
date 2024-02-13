<?php

namespace Ssmd\AlleCustom\Block\Adminhtml\Order\View;

class AlleView extends \Magento\Backend\Block\Template
{
    
    protected $request;
    protected $orderFactory;
    protected $alleCustomFactory;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Ssmd\AlleCustom\Model\AlleCustomFactory $alleCustomFactory
    )
    {
        $this->request = $request;
        $this->orderFactory = $orderFactory;
        $this->alleCustomFactory = $alleCustomFactory;

        return parent::__construct($context);
    }

    public function getOrderId()
    {
        return $this->request->getParam('order_id');
    }

    public function getOrder()
    {
        $orderId = $this->getOrderId();
        if($orderId != "")
        {
            return $this->orderFactory->create()->load($orderId);
        }
        return false;
        
    }

    public function getAllMemberDetailsByOrder($incrementId)
    {
        $collection = $this->alleCustomFactory->create()->getCollection();
        $collection->addFieldToFilter('increment_id', $incrementId);
        $collection->addFieldToFilter('is_bdn', 1);

        //echo "<pre>"; print_r($collection->getData()); exit;

        if(!empty($collection->getData()))
        {
           return $collection;
        }
        return null;

    }

}
