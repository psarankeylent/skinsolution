<?php

namespace Ssmd\CustomerNotes\Controller\Adminhtml\Index;

class Index extends \Magento\Backend\App\Action
{

    protected $resultPageFactory;
    protected $customerNotesFactory;
    protected $adminSession;
    protected $resultJsonFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Ssmd\CustomerNotes\Model\CustomerNotesFactory $customerNotesFactory,
        \Magento\Backend\Model\Auth\Session $adminSession,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->customerNotesFactory = $customerNotesFactory;
        $this->adminSession = $adminSession;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $params = $this->getRequest()->getPostValue();

        /*echo "<pre>";
        print_r($params);
        exit;*/

        try{
            $status=1;
            $error=0;

            $customerNoteFactory = $this->customerNotesFactory->create();

            date_default_timezone_set("America/Los_Angeles");

            $customerNoteFactory->setAdminUserId($params['admin_user_id']);
            $customerNoteFactory->setMageCustomerId($params['mage_customer_id']);
            $customerNoteFactory->setCustomerNote($params['customer_note']);
            $customerNoteFactory->setCreatedAt(date('Y-m-d h:i:s'));
            $customerNoteFactory->save();

        }catch(\Exception $e){
            $status=0;
            $error=1;
        }

        $resultJson = $this->resultJsonFactory->create();
        $data = $customerNoteFactory->getData();

        $data['admin_user'] = $this->adminSession->getUser()->getUsername();
        //echo "<pre>"; print_r($data); exit;

        echo $str = "<div class='divider'>
                <div class='note-list-by'>
                    <span><strong>By : </strong></span>
                    <span>".$data['admin_user']."</span>
                    <span>&nbsp;&nbsp;</span>
                    <span>|</span>
                    <span>&nbsp;&nbsp;</span>
                    <span><strong>On : </strong></span>
                    <span>".date('jS F, Y G:i A', strtotime($data['created_at']))."</span>
                </div>

                <div class='note-list-content'>
                   <span>".$data['customer_note']."</span>
                </div>
            </div>";
        exit;


        $resultJson->setData($data);
        return $resultJson;


    }

    //protected function _isAllowed()
    //{
    //return true;
    //}
}
