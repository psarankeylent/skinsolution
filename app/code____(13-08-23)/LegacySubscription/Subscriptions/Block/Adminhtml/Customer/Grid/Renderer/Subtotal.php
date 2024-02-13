<?php

namespace LegacySubscription\Subscriptions\Block\Adminhtml\Customer\Grid\Renderer;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;
use LegacySubscription\Subscriptions\Model\ResourceModel\CustomerSubscription\CollectionFactory;
 
class Subtotal extends AbstractRenderer
{
    private $_storeManager;
    private $collectionFactory;
 
    public function __construct(
        Context $context,
        StoreManagerInterface $storemanager,
        CollectionFactory $collectionFactory,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->_storeManager = $storemanager;
        $this->collectionFactory = $collectionFactory;
    }
 
    public function render(DataObject $row)
    {
        $amount = null;
        if ($row->getAmount()) {
            $amount = '$'.$row->getAmount();
        }
        return $amount;
    }
}