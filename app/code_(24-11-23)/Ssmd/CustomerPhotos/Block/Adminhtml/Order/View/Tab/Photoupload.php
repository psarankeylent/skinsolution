<?php

namespace Ssmd\CustomerPhotos\Block\Adminhtml\Order\View\Tab;

class Photoupload extends \Magento\Backend\Block\Template implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    protected $_template = 'order/view/tab/photoupload.phtml';
    /**
     * @var \Magento\Framework\Registry
     */
    private $_coreRegistry;


    protected $customerPhotosFactory;
    protected $_storeManager;
    protected $_urlInterface;
    protected $requestInterface;
    protected $orderFactory;
    protected $productFactory;
    protected $formKey;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\App\RequestInterface $requestInterface,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Ssmd\MedicalHistory\Model\CustomerMedicalHistoryFactory $medicalHistoryFactory,
        \Ssmd\CustomerPhotos\Model\CustomerPhotosFactory $customerPhotosFactory,
        \Magento\Framework\Data\Form\FormKey $formKey,
        array $data = []
    ) {
        $this->_coreRegistry    = $registry;
        $this->_storeManager    = $storeManager;
        $this->_urlInterface    = $urlInterface;
        $this->requestInterface = $requestInterface;
        $this->orderFactory     = $orderFactory;
        $this->productFactory   = $productFactory;
        $this->customerPhotosFactory = $customerPhotosFactory;
        $this->medicalHistoryFactory = $medicalHistoryFactory;
        $this->formKey = $formKey;
        parent::__construct($context, $data);
    }


    /**
     * Retrieve customerPhotoOrders model instance
     *
     * @return \Ssmd\CustomerPhotos\Model\PrescriptionCustomerPhotosOrders
     */
    public function getPhotoOrderDetails()
    {
        $orderId = $this->getOrderId();

        $collection = $this->customerPhotosFactory->create()->getCollection();
        $collection->addFieldToFilter('order_id', $orderId);
        $collection->addFieldToFilter('status', 1);

        return $collection;
    }

    /* Return PostActionURl */
    public function getPostActionUrl()
    {
        return $this->_urlInterface->getBaseUrl('sales/order/view');

    }

    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }
    /**
     * Retrieve order model instance
     *
     * @return int
     *Get current id order
     */
    public function getOrderId()
    {
        return $this->getOrder()->getEntityId();
    }

    public function getCustomerId()
    {
        return $this->getOrder()->getCustomerId();
    }

    /**
     * Retrieve order increment id
     *
     * @return string
     */
    public function getOrderIncrementId()
    {
        return $this->getOrder()->getIncrementId();
    }
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Customer Photos');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Customer Photos');
    }

    /**
     * {@inheritdoc}
     */

    public function canShowTab()
    {
        /*
        $order = $this->getOrder();
        $orderId =  $order->getId();
        $uniqueId=null;

        $order = $this->orderFactory->create()->load($orderId);
        $uniqueId = $order->getData('unique_id');

        if(isset($uniqueId) && $uniqueId != "")
        {
            return true;
        }
        else
        {
            return false;
        }
        */

        $canShowTab = false;
        $orderId = $this->getOrder()->getId();
        //$orderId =  $order->getId();
        $order = $this->orderFactory->create()->load($orderId);

        foreach ($order->getAllItems() as $item){
            //$row = [];
            $productId =  $item->getProductId();
            $product = $this->productFactory->create()->load($productId);
            if($product->getPrescription()){
                $canShowTab = true;
            }

        }

        return $canShowTab;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    public function getOrderByOrderId($orderId)
    {
        return $this->orderFactory->create()->load($orderId);
    }


    public function getGovtIdPhoto($customerId,$photoType)
    {
        $collection = $this->customerPhotosFactory->create()->getCollection();
        $collection->addFieldToFilter('customer_id', $customerId);
        $collection->addFieldToFilter('photo_type', $photoType);
        $collection->addFieldToFilter('status', 1);
        $collection->setOrder('photo_id', 'DESC');
        $latestPhotoData = $collection->getFirstItem();

        return $latestPhotoData->getData();
    }

    public function getFullFacePhoto($customerId, $photoType)
    {
        $collection = $this->customerPhotosFactory->create()->getCollection();
        $collection->addFieldToFilter('customer_id', $customerId);
        $collection->addFieldToFilter('photo_type', $photoType);
        $collection->addFieldToFilter('status', 1);
        $collection->setOrder('photo_id', 'DESC');
        $latestPhotoData = $collection->getFirstItem();

        return $latestPhotoData->getData();
    }


    public function getMedicalHistoryPhotosByOrder(){
        $order = $this->getOrder();
        $orderId =  $order->getId();

        $customerId     = $order->getData('customer_id');
        $customerPhotos = $this->customerPhotosFactory->create()
            ->getCollection()
            ->addFieldToFilter("customer_id", $customerId)
            ->addFieldToFilter("status", 1);

        return $customerPhotos->getData();
    }


    // Return data array of Customer Medical Histroy Photos
    /*
    public function getMedicalHistoryPhotosByOrder(){
        $order = $this->getOrder();
        $orderId =  $order->getId();

        $photosCollection = $this->medicalHistoryFactory->create()->getCollection();
        $photosCollection->addFieldToFilter('order_number', $order->getData('increment_id'));
        $photosCollectionFirstItem = $photosCollection->getFirstItem();

        $collectionArray = $photosCollectionFirstItem->getData();

        if (!empty($collectionArray) && is_array($collectionArray)) {

            $fullFace = $collectionArray['full_face'];
            $govtId = $collectionArray['govt_id'];

            $customerPhotos = $this->customerPhotosFactory->create()->getCollection();
            $customerPhotos->addFieldToFilter('photo_id', array('in' => array($fullFace, $govtId)));

            return $customerPhotos->getData();
        }
    }
    */



    public function getFormKey()
    {
        return $this->formKey->getFormKey();
    }

}




