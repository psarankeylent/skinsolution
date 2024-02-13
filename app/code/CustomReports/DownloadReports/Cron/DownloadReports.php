<?php

declare(strict_types=1);

namespace CustomReports\DownloadReports\Cron;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Framework\Api\FilterBuilder;

class DownloadReports
{

    protected $request;
    protected $downloadReportsFactory;
    protected $filter;
    protected $convertToCsv;
    protected $factory;
    protected $filterBuilder;
    protected $dataHelper;


    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \CustomReports\DownloadReports\Model\DownloadReportsFactory $downloadReportsFactory,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Magento\Ui\Model\Export\ConvertToCsv $convertToCsv,
        UiComponentFactory $factory,
        FilterBuilder $filterBuilder,
        \CustomReports\DownloadReports\Helper\Data $dataHelper

    ) {
        $this->request = $request;
        $this->downloadReportsFactory = $downloadReportsFactory;
        $this->filter = $filter;
        $this->convertToCsv = $convertToCsv;
        $this->factory = $factory;
        $this->filterBuilder = $filterBuilder;
        $this->dataHelper = $dataHelper;
       
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {  
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/download_reports_cron.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("Cron running start...");
       
        try{
            
            $downloadReportsCollection = $this->downloadReportsFactory->create()->getCollection();
            $downloadReportsCollection->addFieldToFilter('download_status', ['eq' => 0]);
            $downloadReportsCollection->addFieldToFilter('email_sent_status', ['eq' => 0]);

            if(!empty($downloadReportsCollection->getData()))
            {
                foreach ($downloadReportsCollection as $key => $value) {
                    if($value->getData('namespace') == 'renewal_order_report_listing')
                    {
                        $this->dataHelper->orderReport($key, $value);
                    }
                    elseif($value->getData('namespace') == 'renewal_order_item_report_listing')
                    {
                        $this->dataHelper->orderItemReport($key, $value);
                    }
                    elseif($value->getData('namespace') == 'customreports_batchlist_report_listing')
                    {
                        $this->dataHelper->batchListReport($key, $value);
                    }
                    elseif($value->getData('namespace') == 'customreports_impersonation_report_listing')
                    {
                        $this->dataHelper->impersonationReport($key, $value);
                    } 
                    elseif($value->getData('namespace') == 'customreports_authunsettledtrans_report_listing')
                    {
                        $this->dataHelper->authUnsettledTransReport($key, $value);
                    }
                    elseif($value->getData('namespace') == 'customreports_storecreditusage_report_listing')
                    {
                        $this->dataHelper->storeCreditUsageReport($key, $value);
                    }
                    elseif($value->getData('namespace') == 'customreports_reconciliation_invoice_report_listing')
                    {
                        $this->dataHelper->reconciliationReport($key, $value);
                    }
                    elseif($value->getData('namespace') == 'renewal_storecredit_report_listing')
                    {
                        $this->dataHelper->storeCreditReport($key, $value);
                    }
                    elseif($value->getData('namespace') == 'subscription_report_listing')
                    {
                        $this->dataHelper->subscriptionReport($key, $value);
                    }
                    elseif($value->getData('namespace') == 'renewal_declinedsubscriptions_report_listing')
                    {
                        $this->dataHelper->declinedSubscriptionsM2Report($key, $value);
                    }
                    elseif($value->getData('namespace') == 'renewal_declinedsubscriptionsm1_report_listing')
                    {
                        $this->dataHelper->declinedSubscriptionsM1Report($key, $value);
                    }
                    elseif($value->getData('namespace') == 'reports_cc_listing')
                    {
                        $this->dataHelper->ccExpirationReport($key, $value);
                    }
                    elseif($value->getData('namespace') == 'renewal_expiration_report_listing')
                    {
                        $this->dataHelper->prescriptionExpirationReport($key, $value);
                    }                 
                }
            } 

        } catch (\Exception $e) {               
            $message = $e->getMessage();
            echo $message; exit;
        }         
    }
}

