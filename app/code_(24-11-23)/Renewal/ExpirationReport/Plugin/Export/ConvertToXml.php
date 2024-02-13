<?php

namespace Renewal\ExpirationReport\Plugin\Export;

use Magento\Framework\Api\Search\DocumentInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Convert\Excel;
use Magento\Framework\Convert\ExcelFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Ui\Model\Export\MetadataProvider;
use Magento\Ui\Model\Export\ConvertToXml as ConvertToXmlParent;

class ConvertToXml extends ConvertToXmlParent
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
     * @var ExcelFactory
     */
    protected $excelFactory;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var array
     */
    protected $fields;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @param Filesystem $filesystem
     * @param Filter $filter
     * @param MetadataProvider $metadataProvider
     * @param ExcelFactory $excelFactory
     * @throws FileSystemException
     */
    public function __construct(
        Filesystem $filesystem,
        Filter $filter,
        MetadataProvider $metadataProvider,
        ExcelFactory $excelFactory,
        \Backend\Medical\Model\MedicalFactory $medicalFactory,
        \Magento\Quote\Model\Quote\PaymentFactory $paymentFactory,
        \ParadoxLabs\Subscriptions\Model\SubscriptionFactory $subscriptionFactory,
        \ParadoxLabs\Subscriptions\Model\LogFactory $subLogFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->filter = $filter;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->metadataProvider = $metadataProvider;
        $this->excelFactory = $excelFactory;
        $this->medicalFactory = $medicalFactory;
        $this->paymentFactory = $paymentFactory;
        $this->subscriptionFactory = $subscriptionFactory;
        $this->subLogFactory = $subLogFactory;
        $this->customerFactory = $customerFactory;
        $this->storeManager = $storeManager;
    }


    /**
     * Returns Filters with options
     *
     * @return array
     */
    protected function getOptions()
    {
        if (!$this->options) {
            $this->options = $this->metadataProvider->getOptions();
        }
        return $this->options;
    }

    /**
     * Returns DB fields list
     *
     * @return array
     * @throws LocalizedException
     */
    protected function getFields()
    {
        if (!$this->fields) {
            $component = $this->filter->getComponent();
            $this->fields = $this->metadataProvider->getFields($component);
        }
        return $this->fields;
    }

    /**
     * Returns row data
     *
     * @param DocumentInterface $document
     * @return array
     * @throws LocalizedException
     */
    public function getRowData(DocumentInterface $document)
    {
        return $this->metadataProvider->getRowData($document, $this->getFields(), $this->getOptions());
    }

    /**
     * Returns XML file
     *
     * @return array
     * @throws LocalizedException
     */
    public function afterGetXmlFile(\Magento\Ui\Model\Export\ConvertToXml $subject, $result)
    {
        $component = $this->filter->getComponent();

        $name = md5(microtime());
        $file = 'export/'. $component->getName() . $name . '.xml';

        $this->filter->prepareComponent($component);
        $this->filter->applySelectionOnTargetProvider();

        $component->getContext()->getDataProvider()->setLimit(0, 0);

        /** @var SearchResultInterface $searchResult */
        $searchResult = $component->getContext()->getDataProvider()->getSearchResult();

        /** @var DocumentInterface[] $searchResultItems */
        $searchResultItems = $searchResult->getItems();

        $this->prepareItems($component->getName(), $searchResultItems);

        /** @var SearchResultIterator $searchResultIterator */
        $searchResultIterator = $subject->iteratorFactory->create(['items' => $searchResultItems]);

        /** @var Excel $excel */
        $excel = $this->excelFactory->create(
            [
                'iterator' => $searchResultIterator,
                'rowCallback'=> [$this, 'getRowData'],
            ]
        );

        $this->directory->create('export');
        $stream = $this->directory->openFile($file, 'w+');
        $stream->lock();

        $excel->setDataHeader($this->metadataProvider->getHeaders($component));
        $excel->write($stream, $component->getName() . '.xml');

        $stream->unlock();
        $stream->close();

        return [
            'type' => 'filename',
            'value' => $file,
            'rm' => true  // can delete file after use
        ];
    }

    /**
     * @param string $componentName
     * @param array $items
     * @return void
     */
    protected function prepareItems($componentName, array $items = [])
    {

        $component = $this->filter->getComponent();

        foreach ($items as $document) {

            // =================== Custom code for Including custom columns Start here =========================

            if( $componentName == 'renewal_expiration_report_listing' )
            {
                $customerId = $document['customer_id'];
                $collection = $this->medicalFactory->create()->getCollection();
                $collection->addFieldToFilter('customer_id', $customerId);
                $collection->addFieldToFilter('question_id', 6);    // Phone Number
                $medicalItems = $collection->getFirstItem();
                //echo "<pre>"; print_r($medicalItems->getData()); exit;

                if(!empty($medicalItems->getData()))
                {
                    $document['telephone'] = $medicalItems['response'];
                }
                else
                {
                    $document['telephone'] = null;
                }
            }
            elseif( $componentName == 'reports_cc_listing' )
            {
                // Custom Field 'Telephone'
                $customerId = $document['customer_id'];
                $collection = $this->medicalFactory->create()->getCollection();
                $collection->addFieldToFilter('customer_id', $customerId);
                $collection->addFieldToFilter('question_id', 6);    // Phone Number
                $medicalItems = $collection->getFirstItem();

                if(!empty($medicalItems->getData()))
                {
                    $document['telephone'] = $medicalItems['response'];
                }

                // Custom Field 'ActiveSubscription'
                $paymentId = $document['payment_id'];

                // Getting quote_id from quote_payment table by payment_id
                $quotePayment = $this->paymentFactory->create()->load($paymentId);

                // Getting a list of quote_id from subscriptions table by quote_id and status is active.
                $subscriptionCollection = $this->subscriptionFactory->create()->getCollection();
                $subscriptionCollection->addFieldToFilter('quote_id', $quotePayment['quote_id']);
                $subscriptionCollection->addFieldToFilter('status', 'active');

                $card = 'No';
                if(!empty($subscriptionCollection->getData()))
                {
                    $card = 'Yes';
                }
                $document['active_subscription'] = $card;
            }
            elseif( $componentName == 'renewal_declinedsubscriptions_report_listing' )
            {
                // Custom Field 'Frequency'
                $document['frequency'] = $document['frequency_count'] . ' ' . $document['frequency_unit'];

            }
            elseif( $componentName == 'renewal_order_report_listing' )
            {
                // Custom Field 'order_type'
                $orderId = $document['entity_id'];
                $logCollection = $this->subLogFactory->create()->getCollection();
                $logCollection->addFieldToFilter("order_id", $orderId);

                if(!empty($logCollection->getData())){
                    $type = 'Subscription';
                }else{
                    $type = 'Onetime';
                }
                $document['order_type'] = $type;

            }
            elseif($componentName == 'renewal_order_item_report_listing'){

                // Custom Field 'order_type'
                $orderId = $document['order_id'];
                $logCollection = $this->subLogFactory->create()->getCollection();
                $logCollection->addFieldToFilter("order_id", $orderId);

                if(!empty($logCollection->getData())){
                    $type = 'Subscription';
                }else{
                    $type = 'Onetime';
                }
                $document['order_type'] = $type;
            }
            elseif( $componentName == 'customreports_batchlist_report_listing')
            {
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
                $utcDate = $document['settlement_time_local'];
                $convertedDateTime = str_replace("-","/",$utcDate);
                $settlementLocalTime = $timezoneInterface->date(new \DateTime($convertedDateTime))->format('m-d-Y H:i A');
                $document['settlement_time_local'] = $settlementLocalTime;

                // Settlement State
                if($document['settlement_state'] == 'settledSuccessfully')
                {
                    $document['settlement_state'] = 'Settled Successfully';
                }

            }
            elseif($componentName == 'customreports_batchtrans_report_listing')
            {
                if($document['transaction_status'] == 'settledSuccessfully')
                {
                    $document['transaction_status'] = 'Settled Successfully';
                }

            }
            elseif($componentName == 'customreports_impersonation_report_listing')
            {
                // Customer names
                $websiteId = $this->storeManager->getStore()->getWebsiteId();
                $customer_email = $document['customer_email'];
                $customer = $this->customerFactory->create();
                $customer->setWebsiteId($websiteId);
                $customer->loadByEmail($customer_email);
                $document['firstname'] = $customer->getFirstname().' '.$customer->getLastname();

            }

            // =================== Custom code for Including custom columns End here =========================

            $this->metadataProvider->convertDate($document, $componentName);
        }
    }

}
