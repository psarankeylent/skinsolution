<?php

declare(strict_types=1);

namespace Ssmd\StoreCredit\Controller\Adminhtml\Index;

// Email notification
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;

class Index extends \Magento\Backend\App\Action
{
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    protected $customerNotesFactory;
    protected $adminSession;
    protected $resultJsonFactory;
    protected $storecreditFactory;
    protected $scopeConfig;
    protected $transportBuilder;
    protected $state;
    protected $storeManager;
    protected $creditCardExpiringModelFactory;
    protected $templateFactory;
    protected $medicalFactory;
    protected $customerFactory;

    /**
     * Constructor
     *
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Ssmd\CustomerNotes\Model\CustomerNotesFactory $customerNotesFactory,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Ssmd\StoreCredit\Model\StorecreditFactory $storecreditFactory,
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        StateInterface $state,
        StoreManagerInterface $storeManager,
        \CreditCard\Expiring\Model\CreditCardExpiringModelFactory $creditCardExpiringModelFactory,
        \Magento\Email\Model\TemplateFactory $templateFactory,
        \Backend\Medical\Model\MedicalFactory $medicalFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerNotesFactory = $customerNotesFactory;
        $this->adminSession = $adminSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storecreditFactory = $storecreditFactory;
        $this->scopeConfig = $scopeConfig;
        $this->inlineTranslation = $state;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->creditCardExpiringModelFactory = $creditCardExpiringModelFactory;
        $this->templateFactory = $templateFactory;
        $this->medicalFactory = $medicalFactory;
        $this->customerFactory = $customerFactory;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return ResultInterface
     */
    public function execute()
    {

        $params = $this->getRequest()->getPostValue();


        try{
            $status=1;
            $error=0;
            date_default_timezone_set("America/Los_Angeles");

            $customerId = $params['mage_customer_id'];

            $totalCredits = ((float)$this->getCurrentStoreCredit($customerId) + (float)$params['txt_credit']);

            if($totalCredits <0){
                return false;
            }

            $storecreditFactory  = $this->storecreditFactory->create();
            $storecreditFactory->setAdminUserId($params['admin_user_id']);
            $storecreditFactory->setCustomerId($params['mage_customer_id']);
            $storecreditFactory->setAmount($totalCredits);
            $storecreditFactory->setComments($params['txt_comments']);
            $storecreditFactory->setCreatedAt(date('Y-m-d H:i:s'));
            $storecreditFactory->save();


            // =========================== Send email code start =============================================

            if($params['notify_customer'])
            {
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/storecredit_available_email.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);

                $customer       = $this->customerFactory->create()->load($customerId);
                $customer_name  = $customer->getFirstname().' '.$customer->getLastname();
                $customer_email = $customer->getEmail();

                $loggerData = "Store Credit ".$params['txt_credit']." Email : ".$customer_email." Customer Name : ".$customer_name;

                $logger->info("===== START =====");
                $logger->info($loggerData);
                $logger->info("===== END =====");

                //$this->sendEmail($params['txt_credit'], $customer_email, $customer_name);
            }

            // =========================== Send email code end ===============================================

        }catch(\Exception $e){
            $status=0;
            $error=1;
        }

        $resultJson = $this->resultJsonFactory->create();
        $data = $storecreditFactory->getData();

        $data['admin_user'] = $this->adminSession->getUser()->getUsername();
        //echo "<pre>"; print_r($data); exit;

        echo $str = "<div class='divider'>
                <div class='note-list-by'>
                    <span ><strong>Store Credit : </strong></span>
                    <span>$".$data['amount']."</span>
                    <span>&nbsp;&nbsp;</span>
                    <span>|</span>
                    <span>&nbsp;&nbsp;</span>
                    <span><strong>By : </strong></span>
                    <span>".$data['admin_user']."</span>
                    <span>&nbsp;&nbsp;</span>
                    <span>|</span>
                    <span>&nbsp;&nbsp;</span>
                    <span><strong>On : </strong></span>
                    <span>".date('jS F, Y G:i A', strtotime($data['created_at']))."</span>
                </div>

                <div class='note-list-content'>
                   <span>".$data['comments']."</span>
                </div>
            </div>";
        exit;


        $resultJson->setData($data);

        return $resultJson;
    }

    public function sendEmail($storeCredit, $customer_email, $customer_name)
    {
        $templateId = 1000053;
        $this->inlineTranslation->suspend();
        $sender = [
            'name'  => $this->scopeConfig->getValue('trans_email/ident_support/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'email' => $this->scopeConfig->getValue('trans_email/ident_support/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
        ];

        $transport = $this->transportBuilder
            ->setTemplateIdentifier($templateId)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )
            ->setTemplateVars([
                'customer_name'      => $customer_name,
                'storecredit_amount' => $storeCredit
            ])
            ->setFrom($sender)
            ->addTo($customer_email)
            ->getTransport();


        try {
            $transport->sendMessage();

            $trackLog = $this->creditCardExpiringModelFactory->create();
            $dataToSave = array('customer_email' => $customer_email, 'email_sent' => "yes", 'notification_type'=>'Store Credit Available', 'email_message' => "");
            $trackLog->setData($dataToSave);
            $trackLog->save();


        } catch (\Exception $e) {
            $trackLog = $this->creditCardExpiringModelFactory->create();
            $dataToSave = array('customer_email' => $customer_email, 'email_sent' => "no", 'notification_type'=>'Store Credit Available','email_message' => "");
            $trackLog->setData($dataToSave);
            $trackLog->save();
        }
        $this->inlineTranslation->resume();

    }

    public function getCurrentStoreCredit($customerId){

        $collection = $this->storecreditFactory->create()
            ->getCollection()
            ->addFieldToFilter('customer_id',$customerId)
            ->setOrder('id','DESC');

        if($collection->count()) {
            return $collection->getFirstItem()->getData()['amount'];
        }
    }
}


