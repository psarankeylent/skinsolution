<?php

namespace TrackingActivityLog\TrackAdminUser\Observer;

use Magento\Framework\Event\ObserverInterface;

class DeleteCategoryObserver implements ObserverInterface
{
  
    private $productRepository;
    protected $categoryFactory;
    protected $stockItemRepository;
    protected $image;
    protected $json;
    protected $adminUserActionsLogsFactory;
    protected $adminSession;
    protected $request;

    public function __construct(        
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\CatalogInventory\Model\Stock\StockItemRepository $stockItemRepository,
        \Magento\Catalog\Helper\Image $image,
        \Magento\Framework\Serialize\Serializer\Json $json,
        \TrackingActivityLog\TrackAdminUser\Model\AdminUserActionsLogsFactory $adminUserActionsLogsFactory,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\Framework\App\RequestInterface $request
    )
    {
        $this->productRepository = $productRepository;
        $this->categoryFactory = $categoryFactory;
        $this->stockItemRepository = $stockItemRepository;
        $this->image = $image;
        $this->json = $json;
        $this->adminUserActionsLogsFactory = $adminUserActionsLogsFactory; 
        $this->adminSession = $adminSession;
        $this->request = $request; 
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $post = $this->request->getParams();        
        //echo "<pre>"; print_r($post); exit;
        
        $category = $observer->getCategory();  // you will get category object


        $adminUser = $this->adminSession->getUser()->getUsername();

        $actionLogsModel = $this->adminUserActionsLogsFactory->create();

        $action = '';
        $action = $this->request->getActionName();

        try{

            $actionLogsModel->setData('entity', 'Category')
                        ->setData('identifier', $category->getName())
                        ->setData('user_name',$adminUser)
                        ->setData('action', $action)
                        ->setData('created_at', date('Y-m-d h:i:s'))
                        ->setData('data_before_save', '')
                        ->setData('data_after_save', '')
                        ->save();
        }
        catch(\Exception $e)
        {
            // Put Log here or something
            //echo $e->getMessage();
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/TrackAdminUser.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("Error ".$e->getMessage());

        }


    }  


}