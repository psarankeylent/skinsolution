<?php

namespace LegacySubscription\Subscriptions\Block\Adminhtml\Customer\Grid\Renderer;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;
use LegacySubscription\Subscriptions\Model\ResourceModel\CustomerSubscription\CollectionFactory;
 
class CreatedDate extends AbstractRenderer
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
        $createdDate = "";
        
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $timezone = $objectManager->get('\Magento\Framework\Stdlib\DateTime\TimezoneInterface');
        

        if ($row->getCreateDate()) {
            $createdDate = $row->getCreateDate();
            $createdDate = $timezone->date($createdDate)->format('M d, Y, H:i:s A');
        }
        

        
        return $createdDate;
    }
}