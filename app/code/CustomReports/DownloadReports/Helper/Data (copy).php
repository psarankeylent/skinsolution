<?php

namespace CustomReports\DownloadReports\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

// Email notification
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\StoreManagerInterface;

use Magento\Framework\App\Filesystem\DirectoryList;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;

class Data extends AbstractHelper
{
    /**
     * @var UiComponentFactory
     */
    protected $factory;

    /**
     * @var UiComponentInterface[]
     */
    protected $components = [];


    protected $customer;
    protected $customerRepository;
    protected $orderFactory;
    protected $orderItemFactory;
    protected $medicalFactory;
    protected $scopeConfig;
    protected $transportBuilder;
    protected $state;
    protected $storeManager;
    protected $downloadReportsFactory;
    protected $fileFactory;
    protected $filesystem;
    protected $urlBuilder;
    protected $batchListReportFactory;
    protected $impersonationFactory;
    protected $authUnsettledTransFactory;
    protected $storeCreditReportFactory;
    protected $subscriptionReportFactory;
    protected $subscriptionFactory;
    protected $declinedSubscriptionsReportM1;
    protected $ccReportsFactory;
    protected $expirationReportFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,    
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        \CustomReports\DownloadReports\Model\BatchListReportFactory $batchListReportFactory,
        \CustomReports\DownloadReports\Model\ImpersonationFactory $impersonationFactory,
        \CustomReports\DownloadReports\Model\AuthUnsettledTransFactory $authUnsettledTransFactory,
        \CustomReports\DownloadReports\Model\StoreCreditReportFactory $storeCreditReportFactory,
        \CustomReports\DownloadReports\Model\SubscriptionReportFactory $subscriptionReportFactory,
        \ParadoxLabs\Subscriptions\Model\SubscriptionFactory $subscriptionFactory,
        \CustomReports\DownloadReports\Model\DeclinedSubscriptionsReportM1Factory $declinedSubscriptionsReportM1,
        \CustomReports\DownloadReports\Model\CcReportsFactory $ccReportsFactory,
        \CustomReports\DownloadReports\Model\ExpirationReportFactory $expirationReportFactory,
        \Backend\Medical\Model\MedicalFactory $medicalFactory,
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        StateInterface $state,
        StoreManagerInterface $storeManager,
        \CustomReports\DownloadReports\Model\DownloadReportsFactory $downloadReportsFactory,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Filesystem $filesystem,
        UiComponentFactory $factory,
        \Magento\Ui\Component\MassAction\Filter $filter,
        DirectoryList $dir,
        \Magento\Backend\Model\UrlInterface $urlBuilder

    ) {
        parent::__construct($context);
        $this->customer = $customer;
        $this->customerRepository = $customerRepository;
        $this->orderFactory = $orderFactory;
        $this->orderItemFactory = $orderItemFactory;
        $this->batchListReportFactory = $batchListReportFactory;
        $this->impersonationFactory = $impersonationFactory;
        $this->authUnsettledTransFactory = $authUnsettledTransFactory;
        $this->storeCreditReportFactory = $storeCreditReportFactory;
        $this->subscriptionReportFactory = $subscriptionReportFactory;
        $this->subscriptionFactory = $subscriptionFactory;
        $this->declinedSubscriptionsReportM1 = $declinedSubscriptionsReportM1;
        $this->ccReportsFactory = $ccReportsFactory;
        $this->expirationReportFactory = $expirationReportFactory;
        $this->medicalFactory = $medicalFactory;
        $this->scopeConfig = $scopeConfig;
        $this->inlineTranslation = $state;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->downloadReportsFactory = $downloadReportsFactory;
        $this->_fileFactory = $fileFactory;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR); // MEDIA Directory Path
        $this->factory = $factory;
        $this->filter = $filter;
        $this->dir = $dir;
        $this->urlBuilder = $urlBuilder;
       
    }
    public function orderReport($key, $value)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/email_send_csv.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $date = new \DateTime();
        $toDate     = $date->format('Y-m-d H:i:s');
        $fromDate   = $date->modify("-1095 days")->format('Y-m-d H:i:s');  // 3 yeaers

        $temp=0;
        $count=0;

        // Order collections filter with filters saved
        $collection = $this->orderFactory->create()->getCollection();

        $collection->getSelect()
            ->joinLeft(array('so'=> 'sales_order'),
                'main_table.entity_id = so.entity_id', array("CONCAT(so.customer_firstname, ' ', so.customer_lastname) as customer_name"))
            ->joinLeft(array('soa'=> 'sales_order_address'),
                'soa.parent_id = main_table.entity_id', array('soa.region'))
            ->joinLeft( array('sig'=> 'sales_invoice_grid'),
                'sig.order_id = main_table.entity_id', array('sig.increment_id as invoice_number','sig.created_at as invoice_created'))
            ->joinLeft(array('soship'=> 'order_shipment'),
                'soship.increment_id = main_table.increment_id', array('soship.ship_date','soship.shipment_cost','soship.shipped_from'));

        $collection->addFieldToFilter('soa.address_type','shipping');

        // ============================ Filters Code Start ====================================

        $filters = $value->getFilters();
        if(!empty($filters))
        {
            $d_filters = (array)json_decode($filters); // Object convert into array after decode object
            //echo "<pre>"; print_r($d_filters);

            foreach ($d_filters as $filterKey => $filterValue) {

                if($filterKey == 'status' && $filterValue != "")
                {
                    $collection->addFieldToFilter('main_table.status', $filterValue);
                }
                if($filterKey == 'increment_id' && $filterValue != "")
                {
                    $collection->addFieldToFilter('main_table.increment_id', $filterValue);
                }
                if($filterKey == 'region' && $filterValue != "")
                {
                    $collection->addFieldToFilter('soa.region', $filterValue);
                }
                if($filterKey == 'customer_email' && $filterValue != "")
                {
                    $collection->addFieldToFilter('main_table.customer_email', $filterValue);
                }
                if($filterKey == 'created_at' && ($filterValue->from != "" && $filterValue->to != "") )
                {
                    if(is_object($filterValue))
                    {
                        $from = $filterValue->from;
                        $to   = $filterValue->to;
                        $collection->addFieldToFilter('main_table.created_at', ['from' => $from, 'to'=>$to]);
                    }
                }
                if($filterKey == 'invoice_created' && ($filterValue->from != "" && $filterValue->to != "") )
                {
                    if(is_object($filterValue))
                    {
                        $from = $filterValue->from;
                        $to   = $filterValue->to;
                        $collection->addFieldToFilter('sig.created_at', ['from' => $from, 'to'=>$to]);
                    }
                }
                if($filterKey == 'ship_date' && ($filterValue->from != "" && $filterValue->to != "") )
                {
                    if(is_object($filterValue))
                    {
                        $from = $filterValue->from;
                        $to   = $filterValue->to;
                        $collection->addFieldToFilter('soship.ship_date', ['from' => $from, 'to'=>$to]);
                    }
                }
            }

            // Maps filters
            $collection->addFilterToMap('entity_id', 'main_table.entity_id');
            $collection->addFilterToMap('created_at', 'main_table.created_at');
            $collection->addFilterToMap('increment_id', 'main_table.increment_id');
            $collection->addFilterToMap('invoice_number', 'sig.increment_id');
            $collection->addFilterToMap('customer_email', 'main_table.customer_email');
            $collection->addFilterToMap('customer_firstname', 'main_table.customer_firstname');

            $collection->addFilterToMap('invoice_created', 'sig.created_at');
            $collection->addFilterToMap('ship_date', 'soship.ship_date');
            $collection->addFilterToMap('ship_cost', 'soship.ship_cost');
            $collection->addFilterToMap('warehouse', 'soship.warehouse');

            $collection->addFieldToFilter('soa.address_type','shipping');
        }
        else
        {
            //$collection->addFieldToFilter('main_table.created_at', array('from'=>$fromDate, 'to'=>$toDate));
        }
       
        // ========================= Filters Code End =============================================

        $createdDate       = $value->getCreatedAt();
        $downloadReportId  = $value->getId();
        $adminName         = $value->getRequestedName();
        $adminEmail        = $value->getRequestedEmail();
        $collection = $collection->getData();
        $this->createCsvForOrderReport($collection, $createdDate,$downloadReportId, $adminName, $adminEmail);

    }
    public function createCsvForOrderReport($collection, $createdDate, $downloadReportId, $adminName, $adminEmail)
    {
        try{

            $createdDate = explode(' ',$createdDate);
            $date = $createdDate[0];
            $time = strtotime($createdDate[1]);
            $this->directory->create('exportcsv');          // Create exportcsv directory into pub/media/
            $filepath = 'exportcsv/order_report_' .$date."_".$time. '.csv'; // at Directory path Create a Folder Export and FIle

            $stream = $this->directory->openFile($filepath, 'w+');
            $stream->lock();

            //column name dispay in your CSV

            $columns = ['Order Number','Order Status','Order Total','Sales Tax','Discounts','Shipping','# of Items','Shipping State','Customer Name','Customer Email','Order Date','Invoice Date','Ship Date','Invoice #','Total (Invoiced)Paid','Shipment Cost','Warehouse','Order Type',];

            foreach ($columns as $column)
            {
                $header[] = $column; //storecolumn in Header array
            }

            $stream->writeCsv($header);

            $name = "";
            $email= "";
            if(!empty($collection))
            {
                foreach($collection as $item){

                    $itemData = [];
                    // column name must same as in your Database Table
                    $itemData[] = $item['increment_id'];
                    $itemData[] = $item['status'];
                    $itemData[] = $item['grand_total'];
                    $itemData[] = $item['tax_amount'];
                    $itemData[] = $item['discount_amount'];
                    $itemData[] = $item['shipping_amount'];
                    $itemData[] = $item['total_qty_ordered'];
                    $itemData[] = $item['region'];
                    $itemData[] = $item['customer_firstname'].' '.$item['customer_lastname'];
                    $itemData[] = $item['customer_email'];
                    $itemData[] = $item['created_at'];
                    $itemData[] = $item['invoice_created'];
                    $itemData[] = $item['ship_date'];
                    $itemData[] = $item['invoice_number'];
                    $itemData[] = $item['total_paid'];
                    $itemData[] = $item['shipment_cost'];
                    $itemData[] = $item['shipped_from'];

                    // Getting Order Type
                    $orderId   = $item['entity_id'];
                    $orderType = $this->getOrderType($orderId);
                    $itemData[] = $orderType;

                    $stream->writeCsv($itemData);
                    $name  = $item['customer_firstname'].' '.$item['customer_lastname'];
                    $email = $item['customer_email'];

                }
            }

            $content = [];
            $content['type'] = 'filename'; // must keep filename
            $content['value'] = $filepath;
            $content['rm'] = '1'; //remove csv from media folder

            // Send email
            $this->sendEmail($adminName, $adminEmail, $subject='', $filepath,$downloadReportId);
            return true;

        }
        catch(\Exception $e){
            return false;
        }

    }
    public function orderItemReport($key, $value)
    {
        $date = new \DateTime();
        $toDate     = $date->format('Y-m-d H:i:s');
        $fromDate   = $date->modify("-30 days")->format('Y-m-d H:i:s');

        $temp=0;
        $count=0;

        $collection = $this->orderItemFactory->create()->getCollection();

        $collection->getSelect()
            ->joinLeft(array('so'=> 'sales_order'),
                'main_table.order_id = so.entity_id', array('so.increment_id','so.entity_id','so.status','so.customer_email',"CONCAT(so.customer_firstname, ' ', so.customer_lastname) as customer_name"))
            ->joinLeft( array('soa'=> 'sales_order_address'),
                'so.entity_id=soa.parent_id', array('soa.region'))
            ->joinLeft( array('sig'=> 'sales_invoice_grid'),
                'sig.order_id = main_table.order_id', array('sig.increment_id as invoice_number','sig.created_at as invoice_created'))
            ->joinLeft(array('soship'=> 'order_shipment'),
                'soship.increment_id = so.increment_id', array('soship.ship_date','soship.shipment_cost','soship.shipped_from'));

        $collection->addFieldToFilter('soa.address_type','shipping');


        // ============================ Filters Code Start ====================================

        $filters = $value->getFilters();
        //echo "<pre>"; print_r($filters); exit;
        if(!empty($filters))
        {
            $d_filters = (array)json_decode($filters); // Object convert into array after decode object
            //echo "<pre>"; print_r($d_filters);

            foreach ($d_filters as $filterKey => $filterValue) {

                if($filterKey == 'status' && $filterValue != "")
                {
                    $collection->addFieldToFilter('so.status', $filterValue);
                }
                if($filterKey == 'sku' && $filterValue != "")
                {
                    $collection->addFieldToFilter('main_table.sku', $filterValue);
                }
                if($filterKey == 'region' && $filterValue != "")
                {
                    $collection->addFieldToFilter('soa.region', $filterValue);
                }
                if($filterKey == 'created_at' && ($filterValue->from != "" && $filterValue->to != "") )
                {
                    if(is_object($filterValue))
                    {
                        $fromDate = $filterValue->from;
                        $toDate   = $filterValue->to;
                        $collection->addFieldToFilter('so.created_at', ['from' => $fromDate, 'to'=>$toDate]);
                    }
                }

            }

            // Maps filters
            $collection->addFilterToMap('created_at', 'so.created_at');
            $collection->addFilterToMap('increment_id', 'so.increment_id');
            $collection->addFilterToMap('status', 'so.status');
            $collection->addFilterToMap('customer_email', 'so.customer_email');
            $collection->addFilterToMap('customer_firstname', 'so.customer_firstname');
            $collection->addFilterToMap('sku', 'main_table.sku');
            $collection->addFilterToMap('region', 'soa.region');

        }
        else
        {
            //$collection->addFieldToFilter('so.created_at', ['from' => $fromDate, 'to' => $toDate]);
        }

        // ========================= Filters Code End =============================================

        $createdDate      = $value->getCreatedAt();
        $downloadReportId = $value->getId();
        $adminName         = $value->getRequestedName();
        $adminEmail        = $value->getRequestedEmail();

        $objectManager    = \Magento\Framework\App\ObjectManager::getInstance();
        foreach ($collection as $value) {           
            
            $productCollection = $objectManager->create('Magento\Catalog\Model\Product')->load($value->getData('product_id'));
            $cost = $productCollection->getCost();

            $data['increment_id'] = $value->getData('increment_id');
            $data['customer_name'] = $value->getData('customer_name');
            $data['customer_email'] = $value->getData('customer_email');
            $data['sku'] = $value->getData('sku');
            $data['name'] = $value->getData('name');
            $data['qty_ordered'] = $value->getData('qty_ordered');
            $data['price'] = $value->getData('price');
            $data['cost'] = $cost;
            $data['tax_amount'] = $value->getData('tax_amount');
            $data['row_total'] = $value->getData('row_total');
            $data['status'] = $value->getData('status');
            $data['region'] = $value->getData('region');
            $data['created_at'] = $value->getData('created_at');
            $data['invoice_created'] = $value->getData('invoice_created');
            $data['ship_date'] = $value->getData('ship_date');
            $data['invoice_number'] = $value->getData('invoice_number');
            $data['shipped_from'] = $value->getData('shipped_from');    
            $data['order_type'] = $this->getOrderType($value->getData('entity_id'));
           
            $newDataArr[] = $data;

        }

        $this->createCsvForOrderItemReport($newDataArr, $createdDate, $downloadReportId,$adminName, $adminEmail);

    }
    public function createCsvForOrderItemReport($newDataArr, $createdDate, $downloadReportId,$adminName, $adminEmail)
    {
        try{
            $createdDate = explode(' ',$createdDate);
            $date = $createdDate[0];
            $time = strtotime($createdDate[1]);
            $this->directory->create('exportcsv');          // Create exportcsv directory into pub/media/
            $filepath = 'exportcsv/order_item_report_' .$date."_".$time. '.csv'; // at Directory path Create a Folder Export and FIle

            $stream = $this->directory->openFile($filepath, 'w+');
            $stream->lock();

            //column name dispay in your CSV

            $columns = ['Order Number','Customer Name','Customer Email','Sku','Item Name','Qty','Item Price','Item Cost','Item Tax','Item Total','Order Status','Shipping State','Order Date','Invoice Date','Ship Date','Invoice #','Warehouse','Order Type',];

            foreach ($columns as $column)
            {
                $header[] = $column; //storecolumn in Header array
            }

            $stream->writeCsv($header);
            if(!empty($newDataArr))
            {
                foreach($newDataArr as $item){

                    $itemData = [];
                    // column name must same as in your Database Table
                    $itemData[] = $item['increment_id'];
                    $itemData[] = $item['customer_name'];
                    $itemData[] = $item['customer_email'];
                    $itemData[] = $item['sku'];
                    $itemData[] = $item['name'];
                    $itemData[] = $item['qty_ordered'];
                    $itemData[] = $item['price'];
                    $itemData[] = $item['cost'];
                    $itemData[] = $item['tax_amount'];
                    $itemData[] = $item['row_total'];
                    $itemData[] = $item['status'];
                    $itemData[] = $item['region'];
                    $itemData[] = $item['created_at'];
                    $itemData[] = $item['invoice_created'];
                    $itemData[] = $item['ship_date'];
                    $itemData[] = $item['invoice_number'];
                    $itemData[] = $item['shipped_from'];
                    $itemData[] = $item['order_type'];

                    $stream->writeCsv($itemData);
                }

            }

            $content = [];
            $content['type'] = 'filename'; // must keep filename
            $content['value'] = $filepath;
            $content['rm'] = '1'; //remove csv from media folder

            // Send email
            $this->sendEmail($adminName, $adminEmail, $subject='', $filepath,$downloadReportId);
            return true;

        }
        catch(\Exception $e){
            return false;
        }

    }
    public function batchListReport($key, $value)
    {
        // Collections filter with filters saved
        $collection = $this->batchListReportFactory->create()->getCollection();
        
        // ============================ Filters Code Start ====================================

        $filters = $value->getFilters();                      
        if(!empty($filters))
        {
            $d_filters = (array)json_decode($filters); // Object convert into array after decode object
            
            foreach ($d_filters as $filterKey => $filterValue) {
                //echo "<pre>";  print_r($d_filters); //exit;  
                if($filterKey == 'batch_process_date' && ($filterValue->from != "" && $filterValue->to != "") )
                {    
                    if(is_object($filterValue))
                    {

                        $from = $filterValue->from;
                        $to   = $filterValue->to;
                        $collection->addFieldToFilter('batch_process_date', ['from' => $from, 'to'=>$to]);
                    }
                }
                else
                {
                    if(isset($filterValue) && $filterValue != "")
                    {
                        $collection->addFieldToFilter($filterKey, $filterValue);
                    }
                }                
            } 
            //echo $collection->getSelect(); exit; 
        }

        // ========================= Filters Code End =============================================

        $createdDate       = $value->getCreatedAt();
        $downloadRportsId  = $value->getId();
        $adminName         = $value->getRequestedName();
        $adminEmail        = $value->getRequestedEmail();
        $collection = $collection->getData(); 
        $this->createCsvForBatchListReport($collection, $createdDate,$downloadRportsId,$adminName, $adminEmail);
    }
    public function createCsvForBatchListReport($collection, $createdDate,$downloadRportsId,$adminName, $adminEmail)
    {
        try{
 
            $createdDate = explode(' ',$createdDate);
            $date = $createdDate[0];
            $time = strtotime($createdDate[1]);
            $this->directory->create('exportcsv');          // Create exportcsv directory into pub/media/
            $filepath = 'exportcsv/batch_list_report_' .$date."_".$time. '.csv'; // at Directory path Create a Folder Export and FIle

            $stream = $this->directory->openFile($filepath, 'w+');
            $stream->lock();

            //column name dispay in your CSV 

            $columns = ['Batch ID','Settlement Time UTC','Settlement Time Local','Batch Process Date','Settlement State','Payment Method','Market Type','Product','Batch Process Status','Create Date',];

            foreach ($columns as $column) 
            {
                $header[] = $column; //storecolumn in Header array
            }
            $stream->writeCsv($header);
            $name = "";
            $email= "";
            if(!empty($collection))
            {
                foreach($collection as $item){

                    $itemData = [];
                    // column name must same as in your Database Table
                   
                    $itemData[] = $item['batch_id'];
                    $itemData[] = $item['settlement_time_utc'];
                    $itemData[] = $this->getBatchlistSattlementTimeLocal($item['settlement_time_local']);
                    $itemData[] = $item['batch_process_date'];
                    $itemData[] = $this->getBatchlistSattlementState($item['settlement_state']);
                    $itemData[] = $item['payment_method'];
                    $itemData[] = $item['market_type'];
                    $itemData[] = $item['product'];
                    $itemData[] = $item['batch_process_status'];
                    $itemData[] = $item['create_date'];

                    $stream->writeCsv($itemData);
                    
                }                
            }
            
            //echo "<pre>"; print_r($collection); exit;  
            $content = [];
            $content['type'] = 'filename'; // must keep filename
            $content['value'] = $filepath;
            $content['rm'] = '1'; //remove csv from media folder

            // Send email
            $this->sendEmail($adminName, $adminEmail, $subject='', $filepath,$downloadRportsId);
            return true;

        }
        catch(\Exception $e){
            return false;
        }    
    }
    public function impersonationReport($key, $value)
    {
        // Collections filter with filters saved
        $collection = $this->impersonationFactory->create()->getCollection();

        // ============================ Filters Code Start ====================================

        $filters = $value->getFilters();                       
        if(!empty($filters))
        {
            $d_filters = (array)json_decode($filters); // Object convert into array after decode object
            
            foreach ($d_filters as $filterKey => $filterValue) {
        
                if($filterKey == 'created_date' && ($filterValue->from != "" && $filterValue->to != "") )
                {    
                    if(is_object($filterValue))
                    {
                        $from = $filterValue->from;
                        $to   = $filterValue->to;
                        $collection->addFieldToFilter('created_date', ['from' => $from, 'to'=>$to]);
                    }
                }
                else
                {
                    if(isset($filterValue) && $filterValue != "")
                    {
                        $collection->addFieldToFilter($filterKey, $filterValue);
                    }
                }                
            }
        }

        // ========================= Filters Code End =============================================

        $createdDate = $value->getCreatedAt();
        $downloadRportsId = $value->getId();
        $adminName         = $value->getRequestedName();
        $adminEmail        = $value->getRequestedEmail();
        $collection = $collection->getData();
        $this->createCsvForImpersonationReport($collection, $createdDate,$downloadRportsId,$adminName, $adminEmail);
    }
    public function createCsvForImpersonationReport($collection, $createdDate,$downloadRportsId,$adminName, $adminEmail)
    {
        try{
 
            $createdDate = explode(' ',$createdDate);
            $date = $createdDate[0];
            $time = strtotime($createdDate[1]);
            $this->directory->create('exportcsv');          // Create exportcsv directory into pub/media/
            $filepath = 'exportcsv/impersonation_report_' .$date."_".$time. '.csv'; // at Directory path Create a Folder Export and FIle

            $stream = $this->directory->openFile($filepath, 'w+');
            $stream->lock();

            //column name dispay in your CSV 

            $columns = ['ID','Support Agent','Customer Email','Created Date',];

            foreach ($columns as $column) 
            {
                $header[] = $column; //storecolumn in Header array
            }
            $stream->writeCsv($header);
           
            if(!empty($collection))
            {
                foreach($collection as $item){

                    $itemData = [];
                    // column name must same as in your Database Table
                   
                    $itemData[] = $item['id'];
                    $itemData[] = $item['username'];
                    $itemData[] = $item['customer_email'];
                    //$name = $this->getCustomerNameByEmail($item['customer_email']);
                    //$itemData[] = $name;
                    $itemData[] = $item['created_date'];

                    $stream->writeCsv($itemData);
                }

            }
             
            //echo "<pre>"; print_r($collection); exit;  
            $content = [];
            $content['type'] = 'filename'; // must keep filename
            $content['value'] = $filepath;
            $content['rm'] = '1'; //remove csv from media folder

            // Send email
            $this->sendEmail($adminName, $adminEmail, $subject='', $filepath,$downloadRportsId);
            return true;

        }
        catch(\Exception $e){
            return false;
        }    
    }
    public function authUnsettledTransReport($key, $value)
    {
        // Collections filter with filters saved
        $collection = $this->authUnsettledTransFactory->create()->getCollection();

        // ============================ Filters Code Start ====================================

        $filters = $value->getFilters();                   
        if(!empty($filters))
        {
            $d_filters = (array)json_decode($filters); // Object convert into array after decode object
            
            
            foreach ($d_filters as $filterKey => $filterValue) {
                if(isset($filterValue) && $filterValue != "")
                {
                    $collection->addFieldToFilter($filterKey, $filterValue);
                }                
            } 
           // echo "<pre>"; print_r($collection->getData()); exit; 
        }

        // ========================= Filters Code End =============================================

        $createdDate = $value->getCreatedAt();
        $downloadRportsId = $value->getId();
        $adminName         = $value->getRequestedName();
        $adminEmail        = $value->getRequestedEmail();
        $collection = $collection->getData();
        $this->createCsvForAuthUnsettledTransReport($collection, $createdDate,$downloadRportsId,$adminName, $adminEmail);
    }
    public function createCsvForAuthUnsettledTransReport($collection, $createdDate,$downloadRportsId,$adminName, $adminEmail)
    {
        try{

            $createdDate = explode(' ',$createdDate);
            $date = $createdDate[0];
            $time = strtotime($createdDate[1]);
            $this->directory->create('exportcsv');          // Create exportcsv directory into pub/media/
            $filepath = 'exportcsv/auth_unsettled_trans_report_' .$date."_".$time. '.csv'; // at Directory path Create a Folder Export and FIle

            $stream = $this->directory->openFile($filepath, 'w+');
            $stream->lock();

            //column name dispay in your CSV 

            $columns = ['Trans ID','Submit Time UTC','Submit Time Local','Transaction Status','Subscription ID','Generate Order','First Name','Last Name','Account Type','Account Number','Settle Amount','Market Type','Product','Pay Num','Invoice Number',];

            foreach ($columns as $column) 
            {
                $header[] = $column; //storecolumn in Header array
            }
            $stream->writeCsv($header);
            if(!empty($collection))
            {
                foreach($collection as $item){

                    $itemData = [];
                    // column name must same as in your Database Table
                   
                    $itemData[] = $item['trans_id'];
                    $itemData[] = $item['submit_time_utc'];            
                    $itemData[] = $item['submit_time_local'];
                    $itemData[] = $item['transaction_status'];
                    $itemData[] = $item['subscription_id'];
                    $itemData[] = $item['generate_order'];
                    $itemData[] = $item['first_name'];
                    $itemData[] = $item['last_name'];
                    $itemData[] = $item['account_type'];
                    $itemData[] = $item['account_number'];
                    $itemData[] = $item['settle_amount'];
                    $itemData[] = $item['market_type'];
                    $itemData[] = $item['product'];
                    $itemData[] = $item['pay_num'];
                    $itemData[] = $item['invoice_number'];

                    $stream->writeCsv($itemData);                    
                }                
            }
            
            //echo "<pre>"; print_r($collection); exit;  
            $content = [];
            $content['type'] = 'filename'; // must keep filename
            $content['value'] = $filepath;
            $content['rm'] = '1'; //remove csv from media folder

            // Send email
            $this->sendEmail($adminName, $adminEmail, $subject='', $filepath,$downloadRportsId);
            return true;

        }
        catch(\Exception $e){
            return false;
        }    
    }
    public function storeCreditUsageReport($key, $value)
    { 
        $from=null;
        $to=null;
        $temp=0;

        // Order collections filter with filters saved
        $collection = $this->orderFactory->create()->getCollection();
                
        $collection->getSelect()
                ->join(
                    ['quote_sc' => $collection->getResource()->getTable('quote_storecredit')],
                    ('`main_table`.quote_id = `quote_sc`.quote_id'),
                    ['quote_sc.applied_storecredit'])     // InnerJoin because of only those orders will display which has store credit.
                ->joinLeft(
                    ['so' => $collection->getResource()->getTable('sales_order')],
                    ('`main_table`.entity_id = `so`.entity_id'),
                    ["CONCAT(so.customer_firstname, ' ', so.customer_lastname) as name"]);

        // ============================ Filters Code Start ====================================

        $filters = $value->getFilters(); 
        //echo "<pre>"; print_r($filters); exit;                       
        if(!empty($filters))
        {
            $d_filters = (array)json_decode($filters); // Object convert into array after decode object
            //echo "<pre>"; print_r($d_filters); 
            
            foreach ($d_filters as $filterKey => $filterValue) {

                if($filterKey == 'status' && $filterValue != "")
                {
                    $collection->addFieldToFilter('main_table.status', $filterValue);
                    $collection->addFilterToMap('status', 'main_table.status');
                }
                if($filterKey == 'customer_email' && $filterValue != "")
                {
                    $collection->addFieldToFilter('main_table.customer_email', $filterValue);
                    $collection->addFilterToMap('customer_email', 'main_table.customer_email');
                }
                if($filterKey == 'created_at' && ($filterValue->from != "" && $filterValue->to != "") )
                {
                    if(is_object($filterValue))
                    {
                        $from = $filterValue->from;
                        $to   = $filterValue->to;
                        $collection->addFieldToFilter('main_table.created_at', ['from' => $from, 'to'=>$to]);
                        $collection->addFilterToMap('created_at', 'main_table.created_at');
                    }
                }
            }
        }

        // ========================= Filters Code End =============================================

        $createdDate = $value->getCreatedAt();
        $downloadRportsId = $value->getId();
        $adminName         = $value->getRequestedName();
        $adminEmail        = $value->getRequestedEmail();
        $collection        = $collection->getData();
        
        $this->createCsvForSCUsageReport($collection, $createdDate, $downloadRportsId,$adminName, $adminEmail);
        
    }
    public function createCsvForSCUsageReport($collection, $createdDate, $downloadRportsId,$adminName, $adminEmail)
    {        
        try{

            $createdDate = explode(' ',$createdDate);
            $date = $createdDate[0];
            $time = strtotime($createdDate[1]);
            $this->directory->create('exportcsv');          // Create exportcsv directory into pub/media/
            $filepath = 'exportcsv/SCUsage_report_' .$date."_".$time. '.csv'; // at Directory path Create a Folder Export and FIle

            $stream = $this->directory->openFile($filepath, 'w+');
            $stream->lock();

            //column name dispay in your CSV 

            $columns = ['created_at','status','customer_firstname','customer_email','Order total with store credit','Store credit used',];

                foreach ($columns as $column) 
                {
                    $header[] = $column; //storecolumn in Header array
                }

            $stream->writeCsv($header);

            if(!empty($collection))
            {
                foreach($collection as $item){

                    $itemData = [];
                    // column name must same as in your Database Table 
                    $itemData[] = $item['created_at'];
                    $itemData[] = $item['status'];
                    $itemData[] = $item['name'];
                    $itemData[] = $item['customer_email'];
                    $itemData[] = $item['grand_total'];
                    $itemData[] = $item['applied_storecredit'];

                    $stream->writeCsv($itemData);
                }
            }
            
            //echo "<pre>"; print_r($collection); exit;  
            $content = [];
            $content['type'] = 'filename'; // must keep filename
            $content['value'] = $filepath;
            $content['rm'] = '1'; //remove csv from media folder

            // Send email
            $this->sendEmail($adminName, $adminEmail,$subject='Store Credit Was Used', $filepath, $downloadRportsId);
            return true;

        }
        catch(\Exception $e){
            return false;
        }    

    }
    public function reconciliationReport($key, $value)
    {
        // Collections filter with filters saved
        $collection = $this->orderFactory->create()->getCollection();

        $collection->getSelect()
            ->joinLeft(
                ['s' => $collection->getResource()->getTable('order_shipment')],
                ('`main_table`.increment_id = `s`.increment_id'),
                ['s.ship_date','s.shipment_cost','s.shipped_from'])
            ->joinLeft(
                ['i' => $collection->getResource()->getTable('sales_invoice')],
                ('`main_table`.entity_id = `i`.order_id'),
                ['i.created_at as paid_date', 'i.tax_amount'])
            ->joinLeft(
                ['p' => $collection->getResource()->getTable('sales_order_payment')],
                ('`main_table`.entity_id = `p`.parent_id'),
                ['p.amount_paid'])
            ->joinLeft(
                ['a' => $collection->getResource()->getTable('sales_order_address')],
                ('`main_table`.shipping_address_id = `a`.entity_id'),
                ['a.region']);

        
        $collection->addFieldToFilter('main_table.status', ['like' => 'complete']);
        $collection->addFieldToFilter('main_table.increment_id', ['nlike' => '91%']);
        $collection->addFieldToFilter('main_table.increment_id', ['nlike' => '3%']);
        $collection->addFieldToFilter('main_table.increment_id', ['nlike' => '1%']);

        $collection->addFilterToMap('increment_id', 'main_table.increment_id');
        $collection->addFilterToMap('created_at', 'main_table.created_at');
        $collection->addFilterToMap('paid_date', 'i.created_at');
        $collection->addFilterToMap('ship_date', 's.ship_date');
        $collection->addFilterToMap('shipment_cost', 's.shipment_cost');
        $collection->addFilterToMap('shipped_from', 's.shipped_from');
        $collection->addFilterToMap('created_at', 'i.created_at');
        $collection->addFilterToMap('tax_amount', 'i.tax_amount');
        $collection->addFilterToMap('amount_paid', 'p.amount_paid');
        $collection->addFilterToMap('region', 'a.region');

        
        // ============================ Filters Code Start ====================================

        $filters = $value->getFilters(); 
        //echo "<pre>"; print_r($filters); print_r($collection->getData()); exit;                       
        if(!empty($filters))
        {
            $d_filters = (array)json_decode($filters); // Object convert into array after decode object
            
            foreach ($d_filters as $filterKey => $filterValue) {
                //echo "<pre>";  print_r($d_filters); exit;  
                if($filterKey == 'created_at' && ($filterValue->from != "" && $filterValue->to != "") )
                {    
                    if(is_object($filterValue))
                    {
                        $from = $filterValue->from;
                        $to   = $filterValue->to;
                        $collection->addFieldToFilter('created_at', ['from' => $from, 'to'=>$to]);
                    }
                }
                elseif($filterKey == 'ship_date' && ($filterValue->from != "" && $filterValue->to != "") )
                {    
                    if(is_object($filterValue))
                    {
                        $from = $filterValue->from;
                        $to   = $filterValue->to;
                        $collection->addFieldToFilter('ship_date', ['from' => $from, 'to'=>$to]);
                    }
                }
                elseif($filterKey == 'paid_date' && ($filterValue->from != "" && $filterValue->to != "") )
                {    
                    if(is_object($filterValue))
                    {
                        $from = $filterValue->from;
                        $to   = $filterValue->to;
                        $collection->addFieldToFilter('paid_date', ['from' => $from, 'to'=>$to]);
                    }
                }
                else
                {
                    if(isset($filterValue) && $filterValue != "")
                    {
                        $collection->addFieldToFilter($filterKey, $filterValue);
                    }
                }                
            }         
            //echo "<pre>"; print_r($collection->getData());exit; 
        }

        // ========================= Filters Code End =============================================

        $createdDate = $value->getCreatedAt();
        $downloadRportsId = $value->getId();
        $adminName         = $value->getRequestedName();
        $adminEmail        = $value->getRequestedEmail();
        $collection = $collection->getData();
        $this->createCsvForReconciliationReport($collection, $createdDate,$downloadRportsId,$adminName, $adminEmail);
    }
    public function createCsvForReconciliationReport($collection, $createdDate,$downloadRportsId,$adminName, $adminEmail)
    {
        try{

            $createdDate = explode(' ',$createdDate);
            $date = $createdDate[0];
            $time = strtotime($createdDate[1]);
            $this->directory->create('exportcsv');          // Create exportcsv directory into pub/media/
            $filepath = 'exportcsv/reconciliation_report_' .$date."_".$time. '.csv'; // at Directory path Create a Folder Export and FIle

            $stream = $this->directory->openFile($filepath, 'w+');
            $stream->lock();

            //column name dispay in your CSV 

            $columns = ['Order Number','Order Date','Ship Date','Invoice(Paid) Date','Order Total','Discount Amount','Tax Amount',
        'Amount Paid','Shipping Amount','Shipment Cost','Order Status','Shipping State','Shipping From',];


            foreach ($columns as $column) 
            {
                $header[] = $column; //storecolumn in Header array
            }

            $stream->writeCsv($header);
            if(!empty($collection))
            {
                foreach($collection as $item){

                    $itemData = [];
                    // column name must same as in your Database Table
                   
                    $itemData[] = $item['increment_id'];
                    $itemData[] = $item['created_at'];
                    $itemData[] = $item['ship_date'];
                    $itemData[] = $item['paid_date'];
                    $itemData[] = $item['grand_total'];
                    $itemData[] = $item['discount_amount'];
                    $itemData[] = $item['tax_amount'];
                    $itemData[] = $item['amount_paid'];
                    $itemData[] = $item['shipping_amount'];
                    $itemData[] = $item['shipment_cost'];
                    $itemData[] = $item['state'];
                    $itemData[] = $item['region'];
                    $itemData[] = $item['shipped_from'];

                    $stream->writeCsv($itemData);                 
                }     

            }
             
            //echo "<pre>"; print_r($collection); exit;  
            $content = [];
            $content['type'] = 'filename'; // must keep filename
            $content['value'] = $filepath;
            $content['rm'] = '1'; //remove csv from media folder

            // Send email
            $this->sendEmail($adminName, $adminEmail, $subject='', $filepath,$downloadRportsId);
            return true;

        }
        catch(\Exception $e){
            return false;
        }    
    }
    public function storeCreditReport($key, $value)
    {
        // Collections filter with filters saved
        $collection = $this->storeCreditReportFactory->create()->getCollection();

        $select = $collection->getSelect();
        $select = $select->reset(\Zend_Db_Select::COLUMNS);
        $select->columns(array('id','customer_id', 'amount','comments','created_at'));

        $select->group('customer_id')
            ->where('id IN((SELECT max(id) FROM `storecredit` group by customer_id))');

        $collection->getSelect()
            ->joinLeft(
                ('customer_entity'),
                '`main_table`.customer_id = `customer_entity`.entity_id',
                [
                    'firstname'      => "CONCAT(customer_entity.firstname, ' ', customer_entity.lastname)",
                    'email'         => "customer_entity.email"
                ]
            );

        $collection->addFilterToMap('id', 'main_table.id');
        $collection->addFilterToMap('created_at', 'main_table.created_at');
        $collection->addFilterToMap('firstname', 'customer_entity.firstname');
        $collection->addFilterToMap('lastname', 'customer_entity.lastname');
        $collection->addFilterToMap('email', 'customer_entity.email');

        // ============================ Filters Code Start ====================================

        $filters = $value->getFilters(); 
        //echo "<pre>"; print_r($filters); exit;                       
        if(!empty($filters))
        {
            $d_filters = (array)json_decode($filters); // Object convert into array after decode object
            //echo "<pre>"; print_r($d_filters); 
            
            foreach ($d_filters as $filterKey => $filterValue) {

                if($filterKey == 'created_at' && ($filterValue->from != "" && $filterValue->to != "") )
                {
                    if(is_object($filterValue))
                    {
                        $from = $filterValue->from;
                        $to   = $filterValue->to;
                        $collection->addFieldToFilter('created_at', ['from' => $from, 'to'=>$to]);
                    }
                }
                else
                {
                    if(isset($filterValue) && $filterValue != "")
                    {
                        $collection->addFieldToFilter($filterKey, $filterValue);
                    }
                }
                
            }
        }

        // ========================= Filters Code End =============================================

        $createdDate = $value->getCreatedAt();
        $downloadRportsId = $value->getId();
        $adminName         = $value->getRequestedName();
        $adminEmail        = $value->getRequestedEmail();
        $collection = $collection->getData();
        $this->createCsvForStoreCreditReport($collection, $createdDate,$downloadRportsId,$adminName, $adminEmail);
    }
    public function createCsvForStoreCreditReport($collection, $createdDate,$downloadRportsId,$adminName, $adminEmail)
    {
        try{

            $createdDate = explode(' ',$createdDate);
            $date = $createdDate[0];
            $time = strtotime($createdDate[1]);
            $this->directory->create('exportcsv');          // Create exportcsv directory into pub/media/
            $filepath = 'exportcsv/store_credit_report_' .$date."_".$time. '.csv'; // at Directory path Create a Folder Export and FIle

            $stream = $this->directory->openFile($filepath, 'w+');
            $stream->lock();

            //column name dispay in your CSV 

            $columns = ['Customer Name','Customer Email','Amount','Comments','Created Date',];

            foreach ($columns as $column) 
            {
                $header[] = $column; //storecolumn in Header array
            }
            $stream->writeCsv($header);
            if(!empty($collection))
            {
                foreach($collection as $item){

                    $itemData = [];
                    // column name must same as in your Database Table
                   
                    $customerId = $item['customer_id'];
                    $itemData[] = $this->getCustomerName($customerId);                    
                    $itemData[] = $this->getCustomerEmail($customerId);
                    $itemData[] = $item['amount'];            
                    $itemData[] = $item['comments'];
                    $itemData[] = $item['created_at'];

                    $stream->writeCsv($itemData);
                }                
            }
            
            //echo "<pre>"; print_r($collection); exit;  
            $content = [];
            $content['type'] = 'filename'; // must keep filename
            $content['value'] = $filepath;
            $content['rm'] = '1'; //remove csv from media folder

            // Send email
            $this->sendEmail($adminName, $adminEmail, $subject='', $filepath,$downloadRportsId);
            return true;

        }
        catch(\Exception $e){
            return false;
        }    
    }
    public function subscriptionReport($key, $value)
    {
        // Collections filter with filters saved
        $collection = $this->subscriptionReportFactory->create()->getCollection();

        $collection->getSelect()->joinLeft(
            ['web_cust' => $collection->getResource()->getTable('ssmd_web_customers')],
            '`main_table`.customer_id = `web_cust`.customer_id',
            ['email', 'name']
        );

        $collection->addFilterToMap('id', 'main_table.id');
        $collection->addFilterToMap('name', 'web_cust.name');
        $collection->addFilterToMap('email', 'web_cust.email');

        // ============================ Filters Code Start ====================================

        $filters = $value->getFilters();                      
        if(!empty($filters))
        {
            $d_filters = (array)json_decode($filters); // Object convert into array after decode object
            //echo "<pre>"; print_r($d_filters); 
            
            foreach ($d_filters as $filterKey => $filterValue) {

                if($filterKey == 'create_date' && ($filterValue->from != "" && $filterValue->to != "") )
                {
                    if(is_object($filterValue))
                    {
                        $from = $filterValue->from;
                        $to   = $filterValue->to;
                        $collection->addFieldToFilter('create_date', ['from' => $from, 'to'=>$to]);
                    }
                }
                else
                {
                    if(isset($filterValue) && $filterValue != "")
                    {
                        $collection->addFieldToFilter($filterKey, $filterValue);
                    }
                }
               /* if($filterKey == 'name' && $filterValue != "")
                {
                    $collection->addFieldToFilter($filterKey, $filterValue);
                }
                if($filterKey == 'email' && $filterValue != "")
                {
                    $collection->addFieldToFilter('email', $filterValue);
                }*/
                
            }

            //echo "<pre>"; print_r($collection->getData()); exit;
        }

        // ========================= Filters Code End =============================================

        $createdDate = $value->getCreatedAt();
        $downloadRportsId = $value->getId();
        $adminName         = $value->getRequestedName();
        $adminEmail        = $value->getRequestedEmail();
        $collection = $collection->getData();
        $this->createCsvForSubscriptionReport($collection, $createdDate,$downloadRportsId,$adminName, $adminEmail);
    }
    public function createCsvForSubscriptionReport($collection, $createdDate,$downloadRportsId,$adminName, $adminEmail)
    {
        try{

            $createdDate = explode(' ',$createdDate);
            $date = $createdDate[0];
            $time = strtotime($createdDate[1]);
            $this->directory->create('exportcsv');          // Create exportcsv directory into pub/media/
            $filepath = 'exportcsv/subscription_report_' .$date."_".$time. '.csv'; // at Directory path Create a Folder Export and FIle


            $stream = $this->directory->openFile($filepath, 'w+');
            $stream->lock();

            //column name dispay in your CSV 

            $columns = ['Profile ID','Status','Amount','Discount Amount','Title','Period Length','Period Unit','Regular Price','Trial Price','Sku','Subscription Date','Last Updated Date','Source System','Tax Amount','Tax Rate','Shipping State','Email','Customer Name',];

            foreach ($columns as $column) 
            {
                $header[] = $column; //storecolumn in Header array
            }
            $stream->writeCsv($header);
            if(!empty($collection))
            {
                foreach($collection as $item){

                    $itemData = [];

                    // column name must same as in your Database Table
                    $itemData[] = $item['profile_id'];
                    $itemData[] = $item['status'];
                    $itemData[] = $item['amount'];
                    $itemData[] = $item['discount_amount'];
                    $itemData[] = $item['title'];
                    $itemData[] = $item['period_length'];
                    $itemData[] = $item['period_unit'];
                    $itemData[] = $item['regular_price'];
                    $itemData[] = $item['trial_price'];
                    $itemData[] = $item['sku'];
                    $itemData[] = $item['create_date'];
                    $itemData[] = $item['last_update_date'];
                    $itemData[] = $item['source_system'];
                    $itemData[] = $item['tax_amount'];
                    $itemData[] = $item['tax_rate'];
                    $itemData[] = $item['shipping_state'];
                    $itemData[] = $item['email'];
                    $itemData[] = $item['name'];

                    $stream->writeCsv($itemData);

                }  

            }
            
            //echo "<pre>"; print_r($collection); exit;  
            $content = [];
            $content['type'] = 'filename'; // must keep filename
            $content['value'] = $filepath;
            $content['rm'] = '1'; //remove csv from media folder

            // Send email
            $this->sendEmail($adminName, $adminEmail, $subject='Subscription Report', $filepath,$downloadRportsId);
            return true;

        }
        catch(\Exception $e){
            return false;
        }    
    }
    public function declinedSubscriptionsM2Report($key, $value)
    {
        // Collections filter with filters saved
        $collection = $this->subscriptionFactory->create()->getCollection();

        $collection->getSelect()
            ->join('paradoxlabs_subscription_log', 'main_table.entity_id = paradoxlabs_subscription_log.subscription_id', array('*'))
            ->join('customer_entity', 'main_table.customer_id = customer_entity.entity_id', array('email'))
            ->group('paradoxlabs_subscription_log.subscription_id');
        //  'group by' is used because of subs_id have multiple values in a table 'paradoxlabs_subscription_log'.
        $collection->addFieldToFilter('paradoxlabs_subscription_log.status', ['eq' => 'payment_failed']);
       // $collection->setOrder('paradoxlabs_subscription_log.subscription_id', 'DESC');

        // Map fields to avoid ambiguous column errors on filtering
        $collection->addFilterToMap('entity_id', 'main_table.entity_id');
        $collection->addFilterToMap('customer_id', 'main_table.customer_id');
        $collection->addFilterToMap('updated_at', 'main_table.updated_at');
        $collection->addFilterToMap('frequency', 'main_table.frequency_count');
        $collection->addFilterToMap('status', 'main_table.status');
        $collection->addFilterToMap('description', 'main_table.description');

        // ============================ Filters Code Start ====================================

        $filters = $value->getFilters(); 
        //echo "<pre>"; print_r($filters); exit;                       
        if(!empty($filters))
        {
            $d_filters = (array)json_decode($filters); // Object convert into array after decode object
            //echo "<pre>"; print_r($d_filters); 
            
            foreach ($d_filters as $filterKey => $filterValue) {

                if($filterKey == 'updated_at' && ($filterValue->from != "" && $filterValue->to != "") )
                {
                    if(is_object($filterValue))
                    {
                        $from = $filterValue->from;
                        $to   = $filterValue->to;
                        $collection->addFieldToFilter('updated_at', ['from' => $from, 'to'=>$to]);
                        //$collection->addFilterToMap('updated_at', 'main_table.updated_at');
                    }
                }
                else
                {
                    if(isset($filterValue) && $filterValue != "")
                    {
                        $collection->addFieldToFilter($filterKey, $filterValue);
                    }
                }                
            }

            //echo "<pre>"; print_r($collection->getData()); exit;
        }

        // ========================= Filters Code End =============================================

        $createdDate = $value->getCreatedAt();
        $downloadRportsId = $value->getId();
        $adminName         = $value->getRequestedName();
        $adminEmail        = $value->getRequestedEmail();
        $collection = $collection->getData();

        $this->createCsvForDeclinedSubsM2Report($collection, $createdDate,$downloadRportsId,$adminName, $adminEmail);
    }
    public function createCsvForDeclinedSubsM2Report($collection, $createdDate,$downloadRportsId,$adminName, $adminEmail)
    {
        try{

            $createdDate = explode(' ',$createdDate);
            $date = $createdDate[0];
            $time = strtotime($createdDate[1]);
            $this->directory->create('exportcsv');          // Create exportcsv directory into pub/media/
            $filepath = 'exportcsv/legacy_declined_subscription_m2_report_' .$date."_".$time. '.csv'; // at Directory path Create a Folder Export and FIle


            $stream = $this->directory->openFile($filepath, 'w+');
            $stream->lock();

            //column name dispay in your CSV 

            $columns = ['Subscription ID','Create Date','Payment Status','Customer Name','Customer Email','Order Number','Frequency','Run Count','Description',];

            foreach ($columns as $column) 
            {
                $header[] = $column; //storecolumn in Header array
            }
            $stream->writeCsv($header);
            if(!empty($collection))
            {
                foreach($collection as $item){

                    $itemData = [];
                    // column name must same as in your Database Table
                    $itemData[] = $item['increment_id'];
                    $itemData[] = $item['updated_at'];
                    $itemData[] = $item['status'];
                    
                    $customerId = $item['customer_id'];
                    $itemData[] = $this->getCustomerName($customerId);
                    
                    $itemData[] = $item['email'];
                    $itemData[] = $item['order_increment_id'];
                    $itemData[] = $item['frequency_count'].' '.$item['frequency_unit'];               
                    $itemData[] = $item['run_count'];
                    $itemData[] = $item['description'];

                    $stream->writeCsv($itemData);
                }                
            }
            
            //echo "<pre>"; print_r($collection); exit;  
            $content = [];
            $content['type'] = 'filename'; // must keep filename
            $content['value'] = $filepath;
            $content['rm'] = '1'; //remove csv from media folder

            // Send email
            $this->sendEmail($adminName, $adminEmail, $subject='', $filepath,$downloadRportsId);
            return true;

        }
        catch(\Exception $e){
            return false;
        }    
    }
    public function declinedSubscriptionsM1Report($key, $value)
    {
        // Collections filter with filters saved
        $collection = $this->declinedSubscriptionsReportM1->create()->getCollection();

        $collection->getSelect()
            ->joinLeft('mg_aw_sarp2_profile', 'main_table.subscription_id = mg_aw_sarp2_profile.reference_id', array(''))
            ->joinLeft('customer_entity', 'mg_aw_sarp2_profile.customer_id = customer_entity.entity_id', array('email'));

        $collection->addFieldToFilter('subscription_id', ['neq' => null]);
        $collection->addFieldToFilter('generate_order', ['eq' => 'no']);

        // ============================ Filters Code Start ====================================

        $filters = $value->getFilters(); 
        //echo "<pre>"; print_r($filters); exit;                       
        if(!empty($filters))
        {
            $d_filters = (array)json_decode($filters); // Object convert into array after decode object
            //echo "<pre>"; print_r($d_filters); 
            
            foreach ($d_filters as $filterKey => $filterValue) {
                
                if(isset($filterValue) && $filterValue != "")
                {
                    $collection->addFieldToFilter($filterKey, $filterValue);
                }
            }
        }

        // ========================= Filters Code End =============================================

        $createdDate = $value->getCreatedAt();
        $downloadRportsId = $value->getId();
        $adminName         = $value->getRequestedName();
        $adminEmail        = $value->getRequestedEmail();
        $collection = $collection->getData();

        $this->createCsvForDeclinedSubsM1Report($collection, $createdDate,$downloadRportsId,$adminName, $adminEmail);
    }
    public function createCsvForDeclinedSubsM1Report($collection, $createdDate,$downloadRportsId,$adminName, $adminEmail)
    {
        try{

            $createdDate = explode(' ',$createdDate);
            $date = $createdDate[0];
            $time = strtotime($createdDate[1]);
            $this->directory->create('exportcsv');          // Create exportcsv directory into pub/media/
            $filepath = 'exportcsv/legacy_declined_subscription_m1_report_' .$date."_".$time. '.csv'; // at Directory path Create a Folder Export and FIle


            $stream = $this->directory->openFile($filepath, 'w+');
            $stream->lock();

            //column name dispay in your CSV 

            $columns = ['Subscription ID','First Name','Last Name','Email','Account Type','Account Number','Settle Amount',];

            foreach ($columns as $column) 
            {
                $header[] = $column; //storecolumn in Header array
            }
            $stream->writeCsv($header);
            if(!empty($collection))
            {
                foreach($collection as $item){

                    $itemData = [];
                    // column name must same as in your Database Table
                    $itemData[] = $item['subscription_id'];
                    $itemData[] = $item['first_name'];
                    $itemData[] = $item['last_name'];
                    $itemData[] = $item['email'];
                    $itemData[] = $item['account_type'];               
                    $itemData[] = $item['account_number'];
                    $itemData[] = $item['settle_amount'];

                    $stream->writeCsv($itemData);
                }                
            }
            
            //echo "<pre>"; print_r($collection); exit;  
            $content = [];
            $content['type'] = 'filename'; // must keep filename
            $content['value'] = $filepath;
            $content['rm'] = '1'; //remove csv from media folder

            // Send email
            $this->sendEmail($adminName, $adminEmail, $subject='', $filepath,$downloadRportsId);
            return true;

        }
        catch(\Exception $e){
            return false;
        }    
    }
    public function ccExpirationReport($key, $value)
    {
        $now                = new \DateTime();
        $toDate             = $now->format('Y-m-d 00:00:00');
        $experationDays     = $now->modify("+30days")->format('Y-m-d 23:59:59');

        // Collections filter with filters saved
        $collection = $this->ccReportsFactory->create()->getCollection();
                
        $collection
            ->getSelect()
            ->join(['ps' => 'paradoxlabs_subscription'], 'main_table.customer_id = ps.customer_id', ['quote_id','customer_id','status','next_run'])
            ->join(['itm' => 'quote_item'], 'itm.quote_id = ps.quote_id', ['name','sku'])
            ->joinLeft(
                ('customer_entity'),
                'main_table.customer_id = customer_entity.entity_id',
                [
                    'firstname'          => "CONCAT(customer_entity.firstname, ' ', customer_entity.lastname)",
                    'email'              => "customer_entity.email"
                ])
            ->where('ps.status = ?', 'active')
            ->where('main_table.expires >= ?', $toDate)
            ->where('main_table.expires <= ?', $experationDays)
            ->group('main_table.id');

        $collection->addFilterToMap('id', 'main_table.id');
        $collection->addFilterToMap('expires', 'main_table.expires');
        $collection->addFilterToMap('firstname', 'customer_entity.firstname');
        $collection->addFilterToMap('email', 'customer_entity.email');
       
        // ============================ Filters Code Start ====================================

        $filters = $value->getFilters(); 
        //echo "<pre>"; print_r($filters); exit;                       
        if(!empty($filters))
        {
            $d_filters = (array)json_decode($filters); // Object convert into array after decode object
            //echo "<pre>"; print_r($d_filters); 
            
            foreach ($d_filters as $filterKey => $filterValue) {

                if($filterKey == 'expires' && ($filterValue->from != "" && $filterValue->to != "") )
                {
                    if(is_object($filterValue))
                    {
                        $from = $filterValue->from;
                        $to   = $filterValue->to;
                        $collection->addFieldToFilter('main_table.expires', ['from' => $from, 'to'=>$to]);
                    }
                }
                else
                {
                    if(isset($filterValue) && $filterValue != "")
                    {
                        $collection->addFieldToFilter($filterKey, $filterValue);
                    }
                }
                
            }

            //echo "<pre>"; print_r($collection->getData()); exit;
        }

        // ========================= Filters Code End =============================================

        $createdDate = $value->getCreatedAt();
        $downloadRportsId  = $value->getId();
        $adminName         = $value->getRequestedName();
        $adminEmail        = $value->getRequestedEmail();
        $collection = $collection->getData();
        $this->createCsvForCcExpReport($collection, $createdDate,$downloadRportsId,$adminName, $adminEmail);
    }
    public function createCsvForCcExpReport($collection, $createdDate,$downloadRportsId,$adminName, $adminEmail)
    {
        try{
            
            $createdDate = explode(' ',$createdDate);
            $date = $createdDate[0];
            $time = strtotime($createdDate[1]);
            $this->directory->create('exportcsv');          // Create exportcsv directory into pub/media/
            $filepath = 'exportcsv/cc_expiration_report_' .$date."_".$time. '.csv'; // at Directory path Create a Folder Export and FIle

            $stream = $this->directory->openFile($filepath, 'w+');
            $stream->lock();

            //column name dispay in your CSV 

            $columns = ['Customer Name','Customer Email','Subscription Start Date','Subscription Next Run','Expiration Date','Product Name','Telephone',];

                foreach ($columns as $column) 
                {
                    $header[] = $column; //storecolumn in Header array
                }

            $stream->writeCsv($header);
            if(!empty($collection))
            {
                foreach($collection as $item){

                    $itemData = [];
                    // column name must same as in your Database Table
                    $itemData[] = $item['firstname'];
                    $itemData[] = $item['email'];
                    $itemData[] = $item['created_at'];
                    $itemData[] = $item['next_run'];
                    $itemData[] = $item['expires'];
                    $itemData[] = $item['name'];

                    $telephone = $this->getTelephone($item['customer_id']);
                    $itemData[] = $telephone;

                    $stream->writeCsv($itemData);
                }                
            }

            $content = [];
            $content['type'] = 'filename'; // must keep filename
            $content['value'] = $filepath;
            $content['rm'] = '1'; //remove csv from media folder

            // Send email
            $this->sendEmail($adminName, $adminEmail, $subject='cc Expiration Report', $filepath,$downloadRportsId);
            return true;

        }
        catch(\Exception $e){
            return false;
        }    
    }
    public function prescriptionExpirationReport($key, $value)
    {
        // Collections filter with filters saved
        $collection = $this->expirationReportFactory->create()->getCollection();
                
        $collection->getSelect()
            ->joinLeft(array('cust_ent'=> 'customer_entity'),
                'main_table.customer_id = cust_ent.entity_id', array("CONCAT(cust_ent.firstname, ' ', cust_ent.lastname) as name",'email'));

        $collection->addFilterToMap('id', 'main_table.id');
        $collection->addFilterToMap('prescription_name', 'main_table.prescription_name');
        $collection->addFilterToMap('vh_status', 'main_table.vh_status');
        $collection->addFilterToMap('consultation_type', 'main_table.consultation_type');
        $collection->addFilterToMap('expiration_date', 'main_table.expiration_date');

        // ============================ Filters Code Start ====================================

        $filters = $value->getFilters();                      
        if(!empty($filters))
        {
            $d_filters = (array)json_decode($filters); // Object convert into array after decode object
            
            foreach ($d_filters as $filterKey => $filterValue) {

                if($filterKey == 'name' && $filterValue != "")
                {
                    $collection->addFieldToFilter('customer_entity.name', $filterValue);
                }
                if($filterKey == 'email' && $filterValue != "")
                {
                    $collection->addFieldToFilter('customer_entity.email', $filterValue);
                }
                if($filterKey == 'prescription_name' && $filterValue != "")
                {
                    $collection->addFieldToFilter('main_table.prescription_name', $filterValue);
                }
                if($filterKey == 'vh_status' && $filterValue != "")
                {
                    $collection->addFieldToFilter('main_table.vh_status', $filterValue);
                }
                if($filterKey == 'consultation_type' && $filterValue != "")
                {
                    $collection->addFieldToFilter('main_table.consultation_type', $filterValue);
                }
                if($filterKey == 'expiration_date' && ($filterValue->from != "" && $filterValue->to != "") )
                {
                    if(is_object($filterValue))
                    {
                        $from = $filterValue->from;
                        $to   = $filterValue->to;
                        $collection->addFieldToFilter('main_table.expiration_date', ['from' => $from, 'to'=>$to]);
                    }
                }
                
            }
        }

        // ========================= Filters Code End =============================================

        $createdDate = $value->getCreatedAt();
        $downloadRportsId = $value->getId();
        $adminName         = $value->getRequestedName();
        $adminEmail        = $value->getRequestedEmail();
        $collection = $collection->getData();
        $this->createCsvForPrescriptionExpReport($collection, $createdDate,$downloadRportsId,$adminName, $adminEmail);
    }
    public function createCsvForPrescriptionExpReport($collection, $createdDate,$downloadRportsId,$adminName, $adminEmail)
    {
        try{

            $createdDate = explode(' ',$createdDate);
            $date = $createdDate[0];
            $time = strtotime($createdDate[1]);
            $this->directory->create('exportcsv');          // Create exportcsv directory into pub/media/
            $filepath = 'exportcsv/prescription_expiration_report_' .$date."_".$time. '.csv'; // at Directory path Create a Folder Export and FIle
            
            $stream = $this->directory->openFile($filepath, 'w+');
            $stream->lock();

            //column name dispay in your CSV 

            $columns = ['Name','Email','Prescription Name','Start Date','Expiration Date','VH Status','Consultation Type','Telephone',];

            foreach ($columns as $column) 
            {
                $header[] = $column; //storecolumn in Header array
            }
            $stream->writeCsv($header);
            if(!empty($collection))
            {
                foreach($collection as $item){

                    $itemData = [];
                    // column name must same as in your Database Table
                    $itemData[] = $item['name'];
                    $itemData[] = $item['email'];
                    $itemData[] = $item['prescription_name'];
                    $itemData[] = $item['start_date'];
                    $itemData[] = $item['expiration_date'];
                    $itemData[] = $item['vh_status'];
                    $itemData[] = $item['consultation_type'];

                    //$telephone = $this->getTelephone($item['customer_id']);
                    $itemData[] = $this->getTelephone($item['customer_id']);

                    $stream->writeCsv($itemData);
                }                
            }
            
            //echo "<pre>"; print_r($collection); exit;  
            $content = [];
            $content['type'] = 'filename'; // must keep filename
            $content['value'] = $filepath;
            $content['rm'] = '1'; //remove csv from media folder

            // Send email
            $this->sendEmail($adminName, $adminEmail, $subject='Prescription Expiration Report', $filepath,$downloadRportsId);
            return true;

        }
        catch(\Exception $e){
            return false;
        }    
    }
    public function sendEmail($adminName="", $adminEmail="", $subject="", $file_path=null,$downloadReportId=0)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/email_send_csv.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        
        // Getting file size
        $filePath = $this->dir->getPath('var').'/'.$file_path;
        $file_size = filesize($filePath).' Bytes';
        //$file_size = $this->convertToReadableSize($file_size);

        try {
            
            // Send Mail functionality starts from here 

            $from = $this->scopeConfig->getValue('trans_email/ident_support/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $nameFrom = $this->scopeConfig->getValue('trans_email/ident_support/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $to     = $adminEmail;
            $nameTo = $adminName;
            
            $body = "<div>&nbsp;&nbsp;&nbsp;&nbsp;</div>
            <div>
                Hello ".$adminName.",<br>
                Now you can download your report from admin panel from Menu Reports->Download Reports link<br>
            </div><p>&nbsp;&nbsp;&nbsp;</p><br>
            <div>Thanks,</div>
            <div>Skinsolutions.md</div>";
            
            if($subject == "")
            {
                $subject="skinsolutions.md";
            }
            $email = new \Zend_Mail();
            $email->setSubject($subject); 
            $email->setBodyHtml($body);     // use it to send html data
            //$email->setBodyText($body);   // use it to send simple text data
            $email->setFrom($from, $nameFrom);
            $email->addTo($to, $nameTo);
           
            
            // Send email
            try{
                //if($email->send())
                //{
                    // If email send successfull, then mark record as 1.
                    $filenm = explode('/', $file_path);
                    $downloadReports = $this->downloadReportsFactory->create()->load($downloadReportId);
                    $downloadReports
                                   ->setData('download_status',1)
                                   ->setData('email_sent_status',1)
                                   ->setData('filesize',$file_size)
                                   ->setData('filepath',$filenm[1])
                                   ->save();

                //}
            }catch(\Exception $e){
                $logger->info('Error while sending an email '.$e->getMessage());
            }
            
            echo $body;
            //echo "Email Sent"; exit;

        } catch (\Exception $e) {
           echo $e->getMessage(); exit;
        }       
    }
       // Return subscription type.
    public function getOrderType($orderId)
    {
        $objectManager    = \Magento\Framework\App\ObjectManager::getInstance();
        $subscriptionLog  = $objectManager->create("ParadoxLabs\Subscriptions\Model\Log");

        $subscriptionLogCollection = $subscriptionLog->getCollection();
        $subscriptionLogCollection->addFieldToFilter("order_id", $orderId);

        if(!empty($subscriptionLogCollection->getData())){
            $type = 'Subscription';
        }else{
            $type = 'Onetime';
        }
        return $type;
    }

    public function getTelephone($customerId)
    {
        $collection = $this->medicalFactory->create()->getCollection();
        $collection->addFieldToFilter('customer_id', $customerId);
        $collection->addFieldToFilter('question_id', 6);    // Phone Number
        $items = $collection->getFirstItem();

        if(!empty($items->getData()))
        {
            $telephone = $items['response'];
        }
        else{
            $telephone = null;
        }

        return $telephone;
    }
    public function getCustomerName($customerId)
    {
        $name="";
        $customer = $this->customer->load($customerId);
        $name     = $customer->getFirstname().' '.$customer->getLastname();

        return $name;
    }
    public function getCustomerNameByEmail($customer_email)
    {
        $websiteID = 1;        
        $name="";
        if($customer_email != "")
        {
            //echo $customer_email; exit;
            $customer = $this->customerRepository->get($customer_email,$websiteID);
            $name = $customer->getFirstname();
        }       
        return $name;
    }
    public function getCustomerEmail($customerId)
    {
        $email="";
        $customer = $this->customer->load($customerId);
        $email     = $customer->getEmail();

        return $email;
    }
    public function getCustomerIdFromSubscription($subscriptionId)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $subscription  = $objectManager->get('\ParadoxLabs\Subscriptions\Model\SubscriptionFactory')->create()->load($subscriptionId);
        $customerId    = $subscription->getCustomerId();
    
        if(isset($customerId) && $customerId != "")
        {
            return $customerId;
        }
        return null;
    }
    public function getBatchlistSattlementTimeLocal($settlement_time_local)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $timezoneInterface = $objectManager->get('Magento\Framework\Stdlib\DateTime\TimezoneInterface');

        if (isset($settlement_time_local)) {
            $utcDate = $settlement_time_local;
            $convertedDateTime = str_replace("-","/",$utcDate);

            $settlementLocalTime = $timezoneInterface->date(new \DateTime($convertedDateTime))->format('m-d-Y H:i A');
             return $settlementLocalTime;
        }

        return null;
    }

    public function getBatchlistSattlementState($settlement_state)
    {
        if (isset($settlement_state)) {
            if($settlement_state == 'settledSuccessfully')
            {
                $settlement_state = 'Settled Successfully';
            }
            if($settlement_state == 'refundSettledSuccessfully')
            {
                $settlement_state = 'Refund Settled Successfully';
            }

            return $settlement_state;
        }
        return null;
    }
    public function convertToReadableSize($size){
      $base = log($size) / log(1024);
      $suffix = array("", "KB", "MB", "GB", "TB");
      $f_base = floor($base);
      return round(pow(1024, $base - floor($base)), 1) . $suffix[$f_base];
    }
    public function getBaseUrl()
    {
        return $this->urlBuilder->getUrl('downloadreports/download/downloadcsv');
    }
}