<?php
 
namespace Ssmd\StoreCredit\Controller\StoreCredit;
 
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
 
class CreditView extends Action
{
    protected $request;
    protected $balancedFactory;

    protected $storecreditFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Ssmd\StoreCredit\Model\StorecreditFactory $storecreditFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\Request\Http $request
    )
    {
        $this->request          = $request;
        $this->storecreditFactory = $storecreditFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        return parent::__construct($context);
    }

    public function execute()
    {
        $resultJson     = $this->resultJsonFactory->create();
    
        $customerId     = $this->request->getParam('customer_id');
        $comments       = $this->request->getParam('credit_txt_comments');
        $credits        = $this->request->getParam('credit_input_value');
        $currentScoreCredit = $this->request->getParam('current_score_credit');
        $totalCredits = ((int)$currentScoreCredit + (int)$credits);
        //(float)

        $storecreditFactory     = $this->storecreditFactory->create();
        $data   = array('customer_id'=>$customerId,'credits'=>$totalCredits,'comments'=>$comments);
        $storecreditFactory->setData($data);
        $storecreditFactory->save();

        return $resultJson->setData($storecreditFactory->getCredits());
    }
}