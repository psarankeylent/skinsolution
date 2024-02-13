<?php

namespace TrackingActivityLog\TrackAdminUser\Observer;

use Magento\Framework\Event\ObserverInterface;

class SaveProductDataObserver implements ObserverInterface
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
       
        $product = $observer->getProduct();  // you will get product object
     
        $prod = $this->productRepository->getById($product->getId());
        
        $origData = [];
        $newData  = [];

        $origData = $product->getOrigData();

        // Related, upcell and crosssel products        
        $relatedProducts = $prod->getRelatedProducts();
        if (!empty($relatedProducts)) {
               
            foreach ($relatedProducts as $relatedProduct) {                 
                $related[] = $relatedProduct->getData();
            }
        }
        else
        {
            $related = array();
        }
        $origData['related_products'] = $related;

        $upSellProducts = $prod->getUpSellProducts();

        if (!empty($upSellProducts)) {
            
            foreach ($upSellProducts as $upSellProduct) {
                $upsell[] = $upSellProduct->getData();        
            }
        }
        else
        {
            $upsell = array();
        }
        $origData['up_sell_products'] = $upsell;

        $crossSellProducts = $prod->getCrossSellProducts();

        if (!empty($crossSellProducts)) {
           
            foreach ($crossSellProducts as $crossSellProduct) {
                $crosssell[] = $crossSellProduct->getData();        
            }
        }
        else
        {
            $crosssell = array();
        }
        $origData['cross_sell_products'] = $crosssell;
   
        // Stock Information
        $productStock = $this->stockItemRepository->get($product->getId());
        $stockDetails = $productStock->getOrigData();

       // $manage_stock = $productStock->getOrigData('manage_stock');

        $stockData['qty'] = $stockDetails['qty'];
        $stockData['is_in_stock'] = $stockDetails['is_in_stock'];
        $stockData['manage_stock']= $stockDetails['manage_stock'];
        $stockData['notify_stock_qty'] = $stockDetails['notify_stock_qty'];
        $stockData['backorders'] = $stockDetails['backorders'];
        $stockData['min_qty'] = $stockDetails['min_qty'];
        $stockData['use_config_min_qty'] = $stockDetails['use_config_min_qty'];
        $stockData['is_qty_decimal'] = $stockDetails['is_qty_decimal'];
        $stockData['is_decimal_divided'] = $stockDetails['is_decimal_divided'];
        $stockData['use_config_manage_stock'] = $stockDetails['use_config_manage_stock'];
        $stockData['max_sale_qty'] = $stockDetails['max_sale_qty'];
        $stockData['min_sale_qty'] = $stockDetails['min_sale_qty'];
        $stockData['enable_qty_increments'] = $stockDetails['enable_qty_increments'];
        $stockData['qty_increments'] = $stockDetails['qty_increments'];
        $stockData['stock_status_changed_auto'] = $stockDetails['stock_status_changed_auto'];
        $stockData['use_config_backorders'] = $stockDetails['use_config_backorders'];
        $stockData['use_config_enable_qty_inc'] = $stockDetails['use_config_enable_qty_inc'];
        $stockData['use_config_max_sale_qty'] = $stockDetails['use_config_max_sale_qty'];
        $stockData['use_config_min_sale_qty'] = $stockDetails['use_config_min_sale_qty'];
        $stockData['use_config_notify_stock_qty'] = $stockDetails['use_config_notify_stock_qty'];
        $stockData['use_config_qty_increments'] = $stockDetails['use_config_qty_increments'];
       
        $origData['stock_data'] = $stockData;

        $newData  = $product->getData();

        // Related Products (NEw)
        if(isset($post['links']['related']))
        {
            $newData['related_products'] = $post['links']['related'];
        }
        if(isset($post['links']['upsell']))
        {
            $newData['up_sell_products'] = $post['links']['upsell'];
        }
        if(isset($post['links']['crosssell']))
        {
            $newData['cross_sell_products'] = $post['links']['crosssell'];
        }        
          
        $jsonEncodedOrigData = $this->json->serialize($origData);
        $jsonEncodedData = $this->json->serialize($newData);
       

        $adminUser = $this->adminSession->getUser()->getUsername();
       
        $actionLogsModel = $this->adminUserActionsLogsFactory->create();

        $action = '';
        if( isset($post['back']) &&  $post['back'] == 'new' )
        {
            $action = 'new';
        }
        else if( isset($post['back']) &&  $post['back'] == 'duplicate' )
        {
            $action = 'duplicate';
        }
        else if( isset($post['back']) &&  $post['back'] == 'edit' )
        {
            $action = 'edit';
        }
        else
        {
            $action = 'save & close';   
        }

        try{

            $actionLogsModel->setData('entity', 'Product')
                        ->setData('identifier', $product->getId())
                        ->setData('user_name',$adminUser)
                        ->setData('action', $action)
                        ->setData('created_at', date('Y-m-d h:i:s'))
                        ->setData('data_before_save', $jsonEncodedOrigData)
                        ->setData('data_after_save', $jsonEncodedData)
                        ->save();
        }
        catch(\Exception $e)
        {
            // Put Log here or something
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/TrackAdminUser.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("Error ".$e->getMessage());
        }


    }  


}