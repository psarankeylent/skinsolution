<?php

namespace CustomReports\DownloadReports\Controller\Adminhtml\Download;

use Magento\Framework\Exception\LocalizedException;

class Index extends \Magento\Backend\App\Action
{
    protected $request;
    protected $adminSession;
    protected $downloadReportsFactory;
    protected $resultJsonFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \CustomReports\DownloadReports\Model\DownloadReportsFactory $downloadReportsFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        parent::__construct($context);
        $this->request = $request;
        $this->adminSession = $adminSession;
        $this->downloadReportsFactory = $downloadReportsFactory;
        $this->resultJsonFactory = $resultJsonFactory;
    }

    public function execute()
    {
        $filter_status = "";
        $filter_customer_email = "";
        $namespace = $this->request->getParam('namespace');
       
        $filters = $this->request->getParam('downloadFilters');
        //echo "<pre>"; print_r($filters); exit;
        if(isset($filters['placeholder']))
        {
            unset($filters['placeholder']);  //  placeholder unset from object
        }
        try {
             
            $filterArray = array();
            foreach ($filters as $key_1 => $value_1) {
                if(is_array($value_1))
                {
                    if(array_key_exists('from', $value_1) && array_key_exists('to', $value_1))
                    {
                        if($value_1['from'] != "" && $value_1['to'] != "")
                        {
                            $from =  date("Y-m-d 00:00:00", strtotime($value_1['from']));
                            $to =  date("Y-m-d 23:59:59", strtotime($value_1['to']));

                            $filterArray[$key_1] = ['from'=>$from, 'to'=>$to];
                        }                        
                    } 
                    if($key_1 == 'sku') // sku array from subscription report here.
                    {
                        $filterArray[$key_1] = $value_1;
                    }                     
                }
                else
                {
                    if(isset($value_1) && $value_1 != "")
                    {
                        $filterArray[$key_1] = $value_1;
                    }                    
                }
            } 
            //echo "<pre>"; print_r($filterArray); exit; 
            if(empty($filterArray))
            {
                $filterArray = null;
            }
            else{
                $filterArray = json_encode($filterArray);
            }     
            
            // Save data into 'download_reports' table.
            $downloadReportsModel = $this->downloadReportsFactory->create();
            $email       = $this->adminSession->getUser()->getEmail();
            $name        = $this->adminSession->getUser()->getFirstname().' '.$this->adminSession->getUser()->getLastname();
            $report_name = "";
            if($namespace == 'renewal_order_report_listing')
            {
                $report_name = 'Order Report';
            }
            elseif($namespace == 'renewal_order_item_report_listing')
            {
                $report_name = 'Order Item Report';
            }
            elseif($namespace == 'customreports_batchlist_report_listing')
            {
                $report_name = 'Authorize Settlement Report';
            }
            elseif($namespace == 'customreports_impersonation_report_listing')
            {
                $report_name = 'Impersonation Report';
            }
            elseif($namespace == 'customreports_authunsettledtrans_report_listing')
            {
                $report_name = 'Authorize Unsettled Trans Report';
            }
            elseif($namespace == 'customreports_storecreditusage_report_listing')
            {
                $report_name = 'Store Credit Usage Report';
            }
            elseif($namespace == 'customreports_reconciliation_invoice_report_listing')
            {
                $report_name = 'Reconciliation Invoice Report';
            }
            elseif($namespace == 'renewal_storecredit_report_listing')
            {
                $report_name = 'Store Credit Report';
            }
            elseif($namespace == 'subscription_report_listing')
            {
                $report_name = 'Subscription Report';
            }
            elseif($namespace == 'renewal_declinedsubscriptions_report_listing')
            {
                $report_name = 'Legacy Declined Subscription M2 Report';
            }
            elseif($namespace == 'renewal_declinedsubscriptionsm1_report_listing')
            {
                $report_name = 'Legacy Declined Subscription M1 Report';
            }
            elseif($namespace == 'reports_cc_listing')
            {
                $report_name = 'CC Expiration Report';
            }
            elseif($namespace == 'renewal_expiration_report_listing')
            {
                $report_name = 'Prescriptions Expiration Report';
            }
            $dataArray = [
                            'report_name'     => $report_name,
                            'namespace'       => $namespace,
                            'requested_email' => $email,
                            'requested_name'  => $name,
                            'filters' => $filterArray
                        ];

            $downloadReportsModel->setData($dataArray)
                                ->save();

            if($downloadReportsModel->getId())
            {
                echo "An email will be sent to you once the report is ready. All reports are available in the downloads section."; exit;
            }
        } catch (\Exception $e) {               
            $message = $e->getMessage();
            echo $message; exit;
        }         
        
    }
}

