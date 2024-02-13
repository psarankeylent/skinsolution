<?php
namespace TrackingActivityLog\TrackAdminUser\Plugin\Customer\Address;


use Magento\Backend\App\Action;
use Magento\Customer\Api\Data\AddressInterface;
use Magento\Customer\Api\AddressRepositoryInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Psr\Log\LoggerInterface;

class DefaultBillingAddressPlugin
{
    protected $request;
    protected $customerSession;

    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Customer\Model\Session $customerSession,
        AddressRepositoryInterface $addressRepository
    ) {
        $this->request = $request;
        $this->customerSession = $customerSession;
        $this->addressRepository = $addressRepository;
    }

    public function beforeExecute(
        \Magento\Customer\Controller\Adminhtml\Address\DefaultBillingAddress $subject
    ) {

        $addressId = $this->request->getParam('id');
        $customerId     = $this->request->getParam('parent_id', false);

       if ($addressId) {
            try {
                
                $this->customerSession->setCustomerCustomDefaultBillAddress(1);

            } catch (\Exception $e) {
                $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/TrackAdminUser.log');
                $logger = new \Zend\Log\Logger();
                $logger->addWriter($writer);
                $logger->info("Error ".$e->getMessage());
            }
        } 
    }

}