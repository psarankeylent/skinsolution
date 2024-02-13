<?php

declare(strict_types=1);

namespace Ssmd\StoreCredit\Controller\Adminhtml\Index;

class Index extends \Magento\Backend\App\Action
{

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

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
        \Ssmd\StoreCredit\Model\StorecreditFactory $storecreditFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerNotesFactory = $customerNotesFactory;
        $this->adminSession = $adminSession;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->storecreditFactory = $storecreditFactory;
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

        //echo "<pre>";
        //print_r($params);
        //exit;

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
            $storecreditFactory->setCreatedAt(date('Y-m-d h:i:s'));
            $storecreditFactory->save();

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


