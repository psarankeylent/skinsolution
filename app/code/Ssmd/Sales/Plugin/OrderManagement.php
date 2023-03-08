<?php

namespace Ssmd\Sales\Plugin;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderManagementInterface;

/**
 * Class OrderManagement
 */
class OrderManagement
{

    protected $order;
    protected $customerSession;
    protected $addressFactory;
    protected $addressInterfaceFactory;

    public function __construct(
        OrderInterface $order,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressInterfaceFactory
    )
    {
        $this->order = $order;
        $this->customerSession = $customerSession;
        $this->addressFactory = $addressFactory;
        $this->addressInterfaceFactory = $addressInterfaceFactory;
    }


    /**
     * @param OrderManagementInterface $subject
     * @param OrderInterface           $order
     *
     * @return OrderInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */

    public function afterPlace(
        OrderManagementInterface $subject,
        OrderInterface $result
    ) {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/address_test.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $order = $this->order->loadByIncrementId($result->getIncrementId());
        $customerId = $order->getCustomerId();


        $shippingAddressId = $order->getShippingAddressId();
        $billingAddressId = $order->getBillingAddressId();

        $logger->info('Order bill addr '.$billingAddressId);
        $logger->info('Order ship addr '.$shippingAddressId);


        $addressData = $this->addressFactory->create()->getCollection();
        $addressData = $addressData->addFieldToFilter('parent_id', $customerId);

        $logger->info('addr count '.count($addressData->getData()));
        $addressIds = [];
        if(!empty($addressData->getData()))
        {

            foreach ($addressData as $address) {
                $addressIds[] = $address->getData('entity_id');
                if(($address->getData('entity_id') == $shippingAddressId) || ($address->getData('entity_id') == $billingAddressId) )
                {

                    $shippingAddress = $this->addressFactory->create()->load($shippingAddressId);
                    $billingAddress = $this->addressFactory->create()->load($billingAddressId);

                    // Shipping Address Save
                    $shippingAddress->setMostRecentlyUsedShipping(1);
                    $shippingAddress->save();

                    // Billing Address Save
                    $billingAddress->setMostRecentlyUsedBilling(1);
                    $billingAddress->save();

                    /*

                                        $addressRepository = $objectManager->create('\Magento\Customer\Api\AddressRepositoryInterface');
                                        $addressData = $objectManager->create('\Magento\Customer\Api\Data\AddressInterface');

                                        // Billing adddress save
                                        $addressData->setMostRecentlyUsedBilling(1)
                                                    ->setCustomerId($customerId)
                                                    ->setId(1030);

                                        // Billing adddress save
                                        $addressData->setMostRecentlyUsedShipping(1)
                                                    ->setCustomerId($customerId)
                                                    ->setId(1029);

                                        $addressRepository->save($address);*/

                }
                else
                {
                    // All other addresses
                    $addr = $this->addressFactory->create()->load($address->getData('entity_id'));

                    $addr->setMostRecentlyUsedShipping(0);
                    $addr->setMostRecentlyUsedBilling(0);
                    $addr->save();

                }
            }

            $logger->info('address Id '.json_encode($addressIds));
        }
        //echo $billingAddressId; exit;
        return $result;
    }




}
