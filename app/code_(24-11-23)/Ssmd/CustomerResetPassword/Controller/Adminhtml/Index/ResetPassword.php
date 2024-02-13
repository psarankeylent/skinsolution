<?php
 
namespace Ssmd\CustomerResetPassword\Controller\Adminhtml\Index;
 
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\Action\Action;
 
class ResetPassword extends Action
{
    protected $request;
    protected $customerRegistry;
    protected $storeManager;
    protected $customerRepository;
    protected $encryptor;
    protected $messageManager;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magento\Framework\Message\ManagerInterface $messageManager
    )
    {
        $this->request = $request;
        $this->customerRegistry = $customerRegistry;
        $this->storeManager = $storeManager;
        $this->customerRepository = $customerRepository;
        $this->encryptor = $encryptor;
        $this->messageManager = $messageManager;
        return parent::__construct($context);
    }

    public function execute()
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/reset_password.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("Resetting password");

        $customerId     = $this->request->getParam('customer_id');
        $password       = $this->request->getParam('reset_password');
        try{
            if( (isset($customerId) && $customerId != null)&&(isset($password) && $password != null) )
            {
                
                $logger->info("CustomerID ".$customerId);
                $logger->info("pass ".$password);

                $customer = $this->customerRepository->getById($customerId);
                $customerSecure = $this->customerRegistry->retrieveSecureData($customerId);
                $customerSecure->setRpToken(null);
                $customerSecure->setRpTokenCreatedAt(null);
                $customerSecure->setPasswordHash($this->encryptor->getHash($password, true));

                $this->customerRepository->save($customer);

                $logger->info("pass Hash ".$this->encryptor->getHash($password, true));

                $this->messageManager->addSuccessMessage('Customer Password Updated Successfully.');   
            }
            else
            {
               $logger->info("plz provide pass");
                $this->messageManager->addErrorMessage('Please provide a password.');  
            }

            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath(
                        'customer/index/edit',
                        [
                            'id'         => $customerId,
                            //'active_tab' => 'customer_resetpassword_tab'
                        ]
            );
            $logger->info("Success");
            return $resultRedirect;

        }
        catch(\Exception $e){
            $logger->info("Error while resetting pass ".$e->getMessage());
            print_r($e->getMessage());
        }

    }
}