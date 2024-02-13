<?php

namespace Renewal\ExpirationReport\Plugin\Export;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Model\Export\MetadataProvider;
use Magento\Ui\Model\Export\ConvertToCsv as ConvertToCsvParent;

class ConvertToCsv extends ConvertToCsvParent
{
    /**
     * @var DirectoryList
     */
    protected $directory;
    /**
     * @var MetadataProvider
     */
    protected $metadataProvider;
    /**
     * @var int|null
     */
    protected $pageSize = null;
    /**
     * @var Filter
     */
    protected $filter;
    /**
     * @var TimezoneInterface
     */
    private $timezone;
    /**
     * @param Filesystem $filesystem
     * @param Filter $filter
     * @param MetadataProvider $metadataProvider
     * @param int $pageSize
     * @throws FileSystemException
     */
    public function __construct(
        \Backend\Medical\Model\MedicalFactory $medicalFactory,
        \Magento\Quote\Model\Quote\PaymentFactory $paymentFactory,
        \ParadoxLabs\Subscriptions\Model\SubscriptionFactory $subscriptionFactory,
        \ParadoxLabs\Subscriptions\Model\LogFactory $subLogFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        Filter $filter,
        MetadataProvider $metadataProvider,
        TimezoneInterface $timezone,
        $pageSize = 200
    ) {
        parent::__construct($filesystem, $filter, $metadataProvider, $pageSize);

        $this->medicalFactory = $medicalFactory;
        $this->paymentFactory = $paymentFactory;
        $this->subscriptionFactory = $subscriptionFactory;
        $this->subLogFactory = $subLogFactory;
        $this->customerFactory = $customerFactory;
        $this->storeManager = $storeManager;
        $this->filter = $filter;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->metadataProvider = $metadataProvider;
        $this->pageSize = $pageSize;
        $this->timezone = $timezone;

    }
    /**
     * Returns CSV file
     *
     * @return array
     * @throws LocalizedException
     * @throws \Exception
     */
    public function afterGetCsvFile(\Magento\Ui\Model\Export\ConvertToCsv $subject, $result)
    {
        //echo "Expiration Plugin called"; exit;
        $component = $this->filter->getComponent();
        $name = md5(microtime());
        $file = 'export/' . $component->getName() . $name . '.csv';
        $this->filter->prepareComponent($component);
        $this->filter->applySelectionOnTargetProvider();
        $dataProvider = $component->getContext()->getDataProvider();
        $fields = $this->metadataProvider->getFields($component);
        $options = $this->metadataProvider->getOptions();
        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();
        $stream->writeCsv($this->metadataProvider->getHeaders($component));
        $i = 1;
        $searchCriteria = $dataProvider->getSearchCriteria()
            ->setCurrentPage($i)
            ->setPageSize($this->pageSize);
        $totalCount = (int)$dataProvider->getSearchResult()->getTotalCount();
        while ($totalCount > 0)
        {
            $items = $dataProvider->getSearchResult()->getItems();
            if( $component->getName() == 'renewal_expiration_report_listing' )
            {
                foreach ($items as $item)
                {
                    $customerId = $item['customer_id'];
                    $collection = $this->medicalFactory->create()->getCollection();
                    $collection->addFieldToFilter('customer_id', $customerId);
                    $collection->addFieldToFilter('question_id', 6);    // Phone Number
                    $medicalItems = $collection->getFirstItem();
                    //echo "<pre>"; print_r($items->getData()); exit;

                    if(!empty($medicalItems->getData()))
                    {
                        $item['telephone'] = $medicalItems['response'];
                    }
                    else
                    {
                        $item['telephone'] = null;
                    }

                    //echo "<pre>"; print_r($item->getData()); exit;
                    $this->metadataProvider->convertDate($item, $component->getName());
                    $stream->writeCsv($this->metadataProvider->getRowData($item, $fields, $options));
                }
            }
            elseif( $component->getName() == 'reports_cc_listing' )
            {
                foreach ($items as $item)
                {
                    // Custom Field 'Telephone'
                    $customerId = $item['customer_id'];
                    $collection = $this->medicalFactory->create()->getCollection();
                    $collection->addFieldToFilter('customer_id', $customerId);
                    $collection->addFieldToFilter('question_id', 6);    // Phone Number
                    $medicalItems = $collection->getFirstItem();
                    //echo "<pre>"; print_r($items->getData()); exit;

                    if(!empty($medicalItems->getData()))
                    {
                        $item['telephone'] = $medicalItems['response'];
                    }

                    // Custom Field 'ActiveSubscription'
                    $paymentId = $item['payment_id'];

                    // Getting quote_id from quote_payment table by payment_id
                    $quotePayment = $this->paymentFactory->create()->load($paymentId);

                    // Getting a list of quote_id from subscriptions table by quote_id and status is active.
                    $subscriptionCollection = $this->subscriptionFactory->create()->getCollection();
                    $subscriptionCollection->addFieldToFilter('quote_id', $quotePayment['quote_id']);
                    $subscriptionCollection->addFieldToFilter('status', 'active');
                    //echo "<pre>"; print_r($subscriptionCollection->getData()); exit;

                    $card = 'No';
                    if(!empty($subscriptionCollection->getData()))
                    {
                        $card = 'Yes';
                    }
                    $item['active_subscription'] = $card;

                    $this->metadataProvider->convertDate($item, $component->getName());
                    $stream->writeCsv($this->metadataProvider->getRowData($item, $fields, $options));
                }
            }
            elseif( $component->getName() == 'renewal_declinedsubscriptions_report_listing' )
            {
                // Custom Field 'Frequency'
                foreach ($items as $item)
                {
                    $item['frequency'] = $item['frequency_count'] . ' ' . $item['frequency_unit'];
                    //echo "<pre>"; print_r($item->getData()); exit;
                    $this->metadataProvider->convertDate($item, $component->getName());
                    $stream->writeCsv($this->metadataProvider->getRowData($item, $fields, $options));
                }
            }
            elseif( $component->getName() == 'renewal_order_report_listing' )
            {
                // Custom Field 'order_typ'
                foreach ($items as $item)
                {
                    // echo "<pre>"; print_r($item->getData()); exit;
                    $orderId = $item['entity_id'];
                    $logCollection = $this->subLogFactory->create()->getCollection();
                    $logCollection->addFieldToFilter("order_id", $orderId);

                    if(!empty($logCollection->getData())){
                        $type = 'Subscription';
                    }else{
                        $type = 'Onetime';
                    }
                    $item['order_type'] = $type;
                    $this->metadataProvider->convertDate($item, $component->getName());
                    $stream->writeCsv($this->metadataProvider->getRowData($item, $fields, $options));
                }
            }
            elseif( $component->getName() == 'renewal_order_item_report_listing' )
            {
                // Custom Field 'order_typ'
                foreach ($items as $item)
                {
                    $orderId = $item['order_id'];
                    $logCollection = $this->subLogFactory->create()->getCollection();
                    $logCollection->addFieldToFilter("order_id", $orderId);

                    if(!empty($logCollection->getData())){
                        $type = 'Subscription';
                    }else{
                        $type = 'Onetime';
                    }
                    $item['order_type'] = $type;
                    $this->metadataProvider->convertDate($item, $component->getName());
                    $stream->writeCsv($this->metadataProvider->getRowData($item, $fields, $options));
                }
            }
            elseif( $component->getName() == 'customreports_batchlist_report_listing')
            {
                foreach ($items as $item) {

                    // Removed non required fields from export csv
                    $fieldsVal = $this->metadataProvider->getFields($component);
                    $removeFieldsArr = array('actions_stats','actions_trans'); // array of excluded fields.

                    foreach($fieldsVal as $key=>$val){
                        if(in_array($val,$removeFieldsArr)){
                            unset($fieldsVal[$key]);
                        }
                    }
                    $fields  = array_values($fieldsVal);

                    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                    $timezoneInterface = $objectManager->get('Magento\Framework\Stdlib\DateTime\TimezoneInterface');


                    // Settlement Local Time
                    $utcDate = $item['settlement_time_local'];
                    $convertedDateTime = str_replace("-","/",$utcDate);
                    $settlementLocalTime = $timezoneInterface->date(new \DateTime($convertedDateTime))->format('m-d-Y H:i A');
                    $item['settlement_time_local'] = $settlementLocalTime;

                    // Settlement State
                    if($item['settlement_state'] == 'settledSuccessfully')
                    {
                        $item['settlement_state'] = 'Settled Successfully';
                    }

                    $this->metadataProvider->convertDate($item, $component->getName());
                    $stream->writeCsv($this->metadataProvider->getRowData($item, $fields, $options));

                }
            }
            elseif($component->getName() == 'customreports_batchtrans_report_listing')
            {
                foreach ($items as $item) {
                    if($item['transaction_status'] == 'settledSuccessfully')
                    {
                        $item['transaction_status'] = 'Settled Successfully';
                    }
                    $this->metadataProvider->convertDate($item, $component->getName());
                    $stream->writeCsv($this->metadataProvider->getRowData($item, $fields, $options));
                }

            }
            elseif( $component->getName() == 'customreports_impersonation_report_listing')
            {
                foreach ($items as $item) {

                    // customer name
                    $websiteId = $this->storeManager->getStore()->getWebsiteId();
                    $customer_email = $item['customer_email'];
                    $customer = $this->customerFactory->create();
                    $customer->setWebsiteId($websiteId);
                    $customer->loadByEmail($customer_email);
                    $item['firstname'] = $customer->getFirstname().' '.$customer->getLastname();


                    $this->metadataProvider->convertDate($item, $component->getName());
                    $stream->writeCsv($this->metadataProvider->getRowData($item, $fields, $options));
                }
            }
            else
            {
                foreach ($items as $item) {
                    $this->metadataProvider->convertDate($item, $component->getName());
                    $stream->writeCsv($this->metadataProvider->getRowData($item, $fields, $options));
                }
            }
            $searchCriteria->setCurrentPage(++$i);
            $totalCount = $totalCount - $this->pageSize;
        }
        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true  // can delete file after use
        ];
    }
}
