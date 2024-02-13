<?php
namespace Backend\SyncOrder\Plugin;

class OrderToZeelifyPlugin
{
    //protected $_request;
    protected $_urlInterface;
    protected $_viewOrder;

    public function _construct(
        \Magento\Backend\Model\UrlInterface $urlInterface,
        \Magento\Sales\Block\Adminhtml\Order\View $viewOrder
    ) {
        $this->_urlInterface = $urlInterface;
        $this->_viewOrder = $viewOrder;
    }

    public function beforePushButtons(
        \Magento\Backend\Block\Widget\Button\Toolbar\Interceptor $subject,
        \Magento\Framework\View\Element\AbstractBlock $context,
        \Magento\Backend\Block\Widget\Button\ButtonList $buttonList
    ) {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $backendUrl = $objectManager->get('\Magento\Backend\Model\UrlInterface');
        $request = $objectManager->get('\Magento\Framework\App\RequestInterface');
        $orderId = $request->getParam('order_id');
        
        $url = $backendUrl->getUrl('backend/syncorder/index/', ['order_id' => $orderId]);

        $this->_request = $context->getRequest();
        if($this->_request->getFullActionName() == 'sales_order_view'){
            $buttonList->add(
            'syncorder',
            [
                'label' => __('Sync Order'),
                'onclick' => 'setLocation("'.$url.'")',
                'class' => 'reset primary'
            ],
            -1
            );
        }

    }
}
