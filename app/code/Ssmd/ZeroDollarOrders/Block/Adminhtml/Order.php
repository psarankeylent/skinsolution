<?php
/**
 * @package Ssmd_ZeroDollarOrders
 * @version 1.0.0
 * @category magento-module
 */
declare(strict_types=1);

namespace Ssmd\ZeroDollarOrders\Block\Adminhtml;
use Magento\Sales\Model\Order\Address;

/**
 * Order class
 */
class Order extends \Magento\Backend\Block\Template
{
    /**
     * coreRegistry variable
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;
    /**
     * customer variable
     *
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer = null;
    /**
     * addressRenderer variable
     *
     * @var \Magento\Sales\Model\Order\Address\Renderer
     */
    protected $addressRenderer;
    /**
     * addressFactory variable
     *
     * @var \Magento\Sales\Model\Order\AddressFactory
     */
    protected $addressFactory;

    /**
     * Constructor function
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param \Magento\Sales\Model\Order\AddressFactory $addressFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Sales\Model\Order\AddressFactory $addressFactory,
        array $data = []
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->addressRenderer = $addressRenderer;
        $this->addressFactory = $addressFactory;
        parent::__construct($context, $data);
    }

    /**
     *  Get Customer
     *
     * @return \Magento\Customer\Model\Customer
     */
    private function getCustomer() {
        if(!$this->customer){
            $this->customer = $this->coreRegistry->registry('ssmd_zerodollarorders_customer');
        }
        return $this->customer;
    }
    
    /**
     * Get Customer Name
     *
     * @return string
     */
    public function getCustomerName() {
        return $this->getCustomer()->getFirstname().' '.$this->getCustomer()->getLastname();
    }

    /**
     * Returns string with formatted address
     *
     * @param Address $address
     * @return null|string
     */
    public function getFormattedAddress(Address $address)
    {
        return $this->addressRenderer->format($address, 'html');
    }

    /**
     * Get Biiling address Id
     *
     * @return int|null
     */
    public function getBillingAddressId(){
        return $this->getCustomer()->getDefaultBilling();
    }

    /**
     * Get Shiipng address Id
     *
     * @return int|null
     */
    public function getShippingAddressId(){
        return $this->getCustomer()->getDefaultShipping();
    }

    /**
     * Get Customer Id
     *
     * @return int
     */
    public function getCustomerId(){
        return $this->getCustomer()->getId();
    }

    /**
     * Get Biiling Address
     *
     * @return Address
     */
    public function getBillingAddress(){
        return $this->addressFactory->create()->load($this->getBillingAddressId());
    }

    /**
     * Get Shiiping Address
     *
     * @return Address
     */
    public function getShippingAddress(){
        return $this->addressFactory->create()->load($this->getShippingAddressId());
    }

}

