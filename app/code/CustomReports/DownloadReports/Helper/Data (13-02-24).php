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

/*use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;*/

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
       // ini_set('memory_limit', '3000M');

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/download_reports_helper.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Line 119 helper file');

        $date = new \DateTime();
        $toDate     = $date->format('Y-m-d H:i:s');
        $fromDate   = $date->modify("-30 days")->format('Y-m-d H:i:s');  // 1 month


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

                if(is_object($filterValue))
                {
                    if( ($filterValue->from != "" && $filterValue->to != "") )
                    {
                        $fromDate = $filterValue->from;
                        $toDate   = $filterValue->to;
                        
                        $fromDate = date("Y-m-d 00:00:00", strtotime($fromDate));
                        $toDate   = date("Y-m-d 23:59:59", strtotime($toDate));

                        if($filterKey == 'created_at')
                        {
                            $logger->info('created date '.$fromDate.' === '.$toDate);
                            $collection->addFieldToFilter('main_table.'.$filterKey, ['from' => $fromDate, 'to'=>$toDate]);
                            //echo "<pre>"; print_r($collection->getData()); exit;

                        }
                        elseif($filterKey == 'invoice_created')
                        {
                            $logger->info('invoice created filter '.$fromDate.' === '.$toDate);
                            $collection->addFieldToFilter('sig.created_at', ['from' => $fromDate, 'to'=>$toDate]);
                        }
                        elseif($filterKey == 'ship_date')
                        {
                            $logger->info('ship date filter '.$fromDate.' === '.$toDate);
                            $collection->addFieldToFilter('soship.'.$filterKey, ['from' => $fromDate, 'to'=>$toDate]);
                        }
                    }
                }
                else
                {
                    if($filterKey == 'region')
                    {
                        $logger->info('Region '.$filterValue);
                        $collection->addFieldToFilter('soa.'.$filterKey, $filterValue);
                    }
                    elseif($filterKey == 'status')
                    {
                        $logger->info('Status '.$filterValue);

                        $collection->addFieldToFilter('main_table.'.$filterKey, $filterValue);
                        //$collection->addFieldToFilter('main_table.created_at', array('from'=>$fromDate, 'to'=>$toDate)); // filter only 1month because of complete orders have lacks of records.
                    }
                    else
                    {
                        $logger->info('else Filter '.$filterValue);
                        $collection->addFieldToFilter('main_table.'.$filterKey, $filterValue);
                    }
                }
            }
            //$collection->setPageSize(500);
        }
        else
        {
            //$collection->addFieldToFilter('main_table.created_at', array('from'=>$fromDate, 'to'=>$toDate));
        }
    
        $collection->setPageSize(100)
                    ->setCurPage(1);
        /*echo $collection->getSize();
        echo "<br>";
        echo "<pre>"; print_r($collection->getData()); exit;*/
        

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
            $count_rec=0;
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
                    $itemData[] = $item['customer_name'];
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
                }
            }

            $content = [];
            $content['type'] = 'filename'; // must keep filename
            $content['value'] = $filepath;
            $content['rm'] = '1'; //remove csv from media folder

            // Send email
            $this->sendEmail($adminName, $adminEmail, $filepath,$downloadReportId);
            return true;

        }
        catch(\Exception $e){
            return false;
        }

    }
    public function orderItemReport($key, $value)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/download_reports_helper.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info('Line 286 order item report');

        $date = new \DateTime();
        $toDate     = $date->format('Y-m-d H:i:s');
        $fromDate   = $date->modify("-30 days")->format('Y-m-d H:i:s');

        $collection = $this->orderItemFactory->create()->getCollection();

        $collection->getSelect()
            ->joinLeft(array('so'=> 'sales_order'),
                'main_table.order_id = so.entity_id', array('so.increment_id','so.status','so.customer_email','so.shipping_amount','so.discount_amount as order_level_discount',"CONCAT(so.customer_firstname, ' ', so.customer_lastname) as customer_name"))
            ->joinLeft( array('soa'=> 'sales_order_address'),
                'so.entity_id=soa.parent_id', array('soa.region'))
            ->joinLeft( array('sig'=> 'sales_invoice_grid'),
                'sig.order_id = main_table.order_id', array('sig.increment_id as invoice_number','sig.created_at as invoice_created'))
            ->joinLeft(array('soship'=> 'order_shipment'),
                'soship.increment_id = so.increment_id', array('soship.ship_date','soship.shipment_cost','soship.shipped_from'));

        $collection->addFieldToFilter('soa.address_type','shipping');


        // ============================ Filters Code Start ====================================

        $filters = $value->getFilters();
        if(!empty($filters))
        {
            $d_filters = (array)json_decode($filters); // Object convert into array after decode object
            //echo "<pre>"; print_r($d_filters);

            foreach ($d_filters as $filterKey => $filterValue) {

                if(is_object($filterValue))
                {
                    if( ($filterValue->from != "" && $filterValue->to != "") )
                    {
                        $fromDate = $filterValue->from;
                        $toDate   = $filterValue->to;
                        
                        $fromDate = date("Y-m-d 00:00:00", strtotime($fromDate));
                        $toDate   = date("Y-m-d 23:59:59", strtotime($toDate));

                        if($filterKey == 'created_at')
                        {
                            $collection->addFieldToFilter('so.'.$filterKey, ['from' => $fromDate, 'to'=>$toDate]);
                        }
                        elseif($filterKey == 'invoice_created')
                        {
                            $collection->addFieldToFilter('sig.created_at', ['from' => $fromDate, 'to'=>$toDate]);
                        }
                        elseif($filterKey == 'ship_date')
                        {
                            $collection->addFieldToFilter('soship.'.$filterKey, ['from' => $fromDate, 'to'=>$toDate]);
                        }
                    }
                }
                else
                {
                    if($filterKey == 'status' && $filterValue != "")
                    {
                        if($filterValue == 'complete')
                        {
                            $collection->addFieldToFilter('so.created_at', ['from' => $fromDate, 'to'=>$toDate]);
                            // filter only 1month because of complete orders have lacks of records.
                        }
                        $collection->addFieldToFilter('so.status', $filterValue);
                    }
                    elseif($filterKey == 'region' && $filterValue != "")
                    {
                        $collection->addFieldToFilter('soa.region', $filterValue);
                    }
                    else
                    {
                        $collection->addFieldToFilter('main_table.'.$filterKey, $filterValue);
                    }
                }
            }

            $collection->setPageSize(500);
        }
        else
        {
            $collection->addFieldToFilter('so.created_at', ['from' => $fromDate, 'to' => $toDate]);
        }

        // ========================= Filters Code End =============================================

        $createdDate      = $value->getCreatedAt();
        $downloadReportId = $value->getId();
        $adminName         = $value->getRequestedName();
        $adminEmail        = $value->getRequestedEmail();
        $collection       = $collection->getData();

        $this->createCsvForOrderItemReport($collection, $createdDate, $downloadReportId,$adminName, $adminEmail);

    }
    public function createCsvForOrderItemReport($collection, $createdDate, $downloadReportId,$adminName, $adminEmail)
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

            $columns = ['Order Number','Customer Name','Customer Email','Sku','Item Name','Qty','Item Price','Item Cost','Item Level Discount','Order Level Discount','Item Tax','Shipping Amount','Item Total','Order Status','Shipping State','Order Date','Invoice Date','Ship Date','Order Type','Warehouse','Shipping Cost',];

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
                    $itemData[] = $item['customer_name'];
                    $itemData[] = $item['customer_email'];
                    $itemData[] = $item['sku'];
                    $itemData[] = $item['name'];
                    $itemData[] = $item['qty_ordered'];
                    $itemData[] = $item['price'];
                    $itemData[] = $item['base_cost'];
                    $itemData[] = $item['discount_amount'];
                    $itemData[] = $item['order_level_discount'];
                    $itemData[] = $item['tax_invoiced'];
                    $itemData[] = $item['shipping_amount'];
                    $itemData[] = $item['row_total'];
                    $itemData[] = $item['status'];
                    $itemData[] = $item['region'];
                    $itemData[] = $item['created_at'];
                    $itemData[] = $item['invoice_created'];
                    $itemData[] = $item['ship_date'];
                    $itemData[] = $this->getOrderType($item['order_id']);
                    $itemData[] = $item['shipped_from'];
                    $itemData[] = $item['shipment_cost'];

                    $stream->writeCsv($itemData);
                }

            }

            $content = [];
            $content['type'] = 'filename'; // must keep filename
            $content['value'] = $filepath;
            $content['rm'] = '1'; //remove csv from media folder

            // Send email
            $this->sendEmail($adminName, $adminEmail, $filepath,$downloadReportId);
            return true;

        }
        catch(\Exception $e){
            return false;
        }

    }

    public function sendEmail($adminName="", $adminEmail="", $file_path=null,$downloadReportId=0)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/email_send_csv.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        // Getting file size
        $filePath = $this->dir->getPath('var').'/'.$file_path;
        $file_size = filesize($filePath).' Bytes';

        try {
            $logger->info('sending email.. ');
            $templateId = 52;
            $this->inlineTranslation->suspend();
            $sender = [
                'name'  => $this->scopeConfig->getValue('trans_email/ident_support/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
                'email' => $this->scopeConfig->getValue('trans_email/ident_support/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            ];

            $transport = $this->transportBuilder
                ->setTemplateIdentifier($templateId)
                ->setTemplateOptions(
                    [
                        'area' => \Magento\Framework\App\Area::AREA_ADMINHTML,
                        'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars([
                    'admin_name'    => $adminName,
                ])
                ->setFrom($sender)
                ->addTo($adminEmail)
                ->getTransport();


            //$transport->sendMessage();

            $filenm = explode('/', $file_path);
            $downloadReports = $this->downloadReportsFactory->create()->load($downloadReportId);
            $downloadReports
                ->setData('download_status',1)
                ->setData('email_sent_status',1)
                ->setData('status',1)
                ->setData('filesize',$file_size)
                ->setData('filepath',$filenm[1])
                ->save();

            echo "Success!"; exit;

        } catch (\Exception $e) {
            $logger->info('Error while sending an email '.$e->getMessage());
        }
        $this->inlineTranslation->resume();

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
