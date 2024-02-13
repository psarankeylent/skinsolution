<?php

namespace Ssmd\MultipleCoupons\Model\AdminOrder;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Model\Metadata\Form as CustomerForm;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\App\ObjectManager;
use Magento\Quote\Model\Quote\Address;
use Magento\Quote\Model\Quote\Item;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Order create model
 * @api
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.CookieAndSessionMisuse)
 * @since 100.0.2
 */
class Create extends \Magento\Sales\Model\AdminOrder\Create
{
    /**
     * Xml default email domain path
     */
    const XML_PATH_DEFAULT_EMAIL_DOMAIN = 'customer/create_account/email_domain';

    /**
     * Quote session object
     *
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $_session;

    /**
     * Quote customer wishlist model object
     *
     * @var \Magento\Wishlist\Model\Wishlist
     */
    protected $_wishlist;

    /**
     * Sales Quote instance
     *
     * @var \Magento\Quote\Model\Quote
     */
    protected $_cart;

    /**
     * Catalog Compare List instance
     *
     * @var \Magento\Catalog\Model\Product\Compare\ListCompare
     */
    protected $_compareList;

    /**
     * Re-collect quote flag
     *
     * @var boolean
     */
    protected $_needCollect;

    /**
     * Re-collect cart flag
     *
     * @var boolean
     */
    protected $_needCollectCart = false;

    /**
     * Collect (import) data and validate it flag
     *
     * @var boolean
     */
    protected $_isValidate = false;

    /**
     * Array of validate errors
     *
     * @var array
     */
    protected $_errors = [];

    /**
     * Quote associated with the model
     *
     * @var \Magento\Quote\Model\Quote
     */
    protected $_quote;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager = null;

    /**
     * @var \Magento\Sales\Model\Config
     */
    protected $_salesConfig;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * @var \Magento\Framework\DataObject\Copy
     */
    protected $_objectCopyService;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Sales\Model\AdminOrder\Product\Quote\Initializer
     */
    protected $quoteInitializer;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $addressRepository;

    /**
     * @var \Magento\Customer\Api\Data\AddressInterfaceFactory
     */
    protected $addressFactory;

    /**
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    protected $_metadataFormFactory;

    /**
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * @var \Magento\Sales\Model\AdminOrder\EmailSender
     */
    protected $emailSender;

    /**
     * @var \Magento\Quote\Model\Quote\Item\Updater
     */
    protected $quoteItemUpdater;

    /**
     * @var \Magento\Framework\DataObject\Factory
     */
    protected $objectFactory;

    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    protected $customerFactory;

    /**
     * Constructor
     *
     * @var \Magento\Customer\Model\Customer\Mapper
     */
    protected $customerMapper;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Quote\Api\CartManagementInterface
     */
    protected $quoteManagement;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var \Magento\Sales\Api\OrderManagementInterface
     */
    protected $orderManagement;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * Serializer interface instance.
     *
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $serializer;

    /**
     * @var ExtensibleDataObjectConverter
     */
    private $dataObjectConverter;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var
     */
    public $couponManagement;

    /**
     * Add coupon code to the quote
     *
     * @param string $code
     * @return $this
     */
    public function applyCoupon($code)
    {
        $code = trim((string)$code);
        $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);

        if (empty($code)) {
            $this->getQuote()->getShippingAddress()->setFreeShipping(0);
        }

        $oldCouponCode = $this->getQuote()->getCouponCode();
        $oldCouponCodes = $newCouponCodes = explode(',', $oldCouponCode);
        /*$newCouponCodes[] = $code;
        $newCouponCode = implode(',', $newCouponCodes);*/

        $quoteHasNonStackableCoupon = $this->quoteHasNonStackableCoupon($oldCouponCodes);

        if ($quoteHasNonStackableCoupon) {
            return $this;
        }

        $isStackableCoupon = $this->isStackableCoupon($code);

        if ($isStackableCoupon) {
            $newCouponCodes[] = $code;
        } else {
            $this->removeAllCouponsOnTheCart($this->getQuote()->getCartId(), $oldCouponCodes);
            $newCouponCodes = [$code];
        }

        $newCouponCode = implode(',', $newCouponCodes);

        if (!in_array($code, $oldCouponCodes)) {
            $this->getQuote()->setCouponCode($newCouponCode);
            $this->setRecollect(true);
        }

        return $this;
    }

    /**
     * Remove coupon code from the quote
     *
     * @param string $code
     * @return $this
     */
    public function removeCoupon($code)
    {
        $code = trim((string)$code);
        $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);

        if (empty($code)) {
            $this->getQuote()->getShippingAddress()->setFreeShipping(0);
        }

        $oldCouponCode = $this->getQuote()->getCouponCode();
        $oldCouponCodes = $newCouponCodes = explode(',', $oldCouponCode);
        $newCouponCode = implode(',', $newCouponCodes);

        if (($key = array_search($code, $oldCouponCodes)) !== false) {
            unset($newCouponCodes[$key]);
        }
        $newCouponCode = implode(',', $newCouponCodes);

        $this->getQuote()->setCouponCode($newCouponCode);
        $this->setRecollect(true);

        return $this;
    }

    /**
     * Parse data retrieved from request
     *
     * @param array $data
     * @return  $this
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function importPostData($data)
    {
        if (is_array($data)) {
            $this->addData($data);
        } else {
            return $this;
        }

        if (isset($data['account'])) {
            $this->setAccountData($data['account']);
        }

        if (isset($data['comment'])) {
            $this->getQuote()->addData($data['comment']);
            if (empty($data['comment']['customer_note_notify'])) {
                $this->getQuote()->setCustomerNoteNotify(false);
            } else {
                $this->getQuote()->setCustomerNoteNotify(true);
            }
        }

        if (isset($data['billing_address'])) {
            $this->setBillingAddress($data['billing_address']);
        }

        if (isset($data['shipping_address'])) {
            $this->setShippingAddress($data['shipping_address']);
        }

        if (isset($data['shipping_method'])) {
            $this->setShippingMethod($data['shipping_method']);
        }

        if (isset($data['payment_method'])) {
            $this->setPaymentMethod($data['payment_method']);
        }

        if (isset($data['coupon']['code'])) {
            $this->applyCoupon($data['coupon']['code']);
        }

        if (isset($data['remove-coupon']['code'])) {
            $this->removeCoupon($data['remove-coupon']['code']);
        }

        return $this;
    }

    /**
     * @param $couponCode
     * @return boolean
     */
    public function isStackableCoupon($couponCode)
    {
        $this->coupon = $this->_objectManager->create(\Magento\SalesRule\Model\Coupon::class);
        $this->ruleRepository = $this->_objectManager->create(\Magento\SalesRule\Api\RuleRepositoryInterface::class);


        $rule = $this->coupon->loadByCode($couponCode);

        if ($rule && $rule->getRuleId()) {
            $salesRule = $this->ruleRepository->getById($rule->getRuleId());
            $data = $salesRule->__toArray();
            return $data['is_stackable_coupon'];
        }

        return true;
    }

    public function removeAllCouponsOnTheCart($cartId, $couponCodes)
    {
        foreach ($couponCodes as $couponCode)
            $this->removeCoupon($cartId, $couponCode);
    }

    public function quoteHasNonStackableCoupon($couponCodes)
    {
        $flag = false;
        foreach ($couponCodes as $couponCode) {
            $flag = $this->isStackableCoupon($couponCode);
            if (!$flag)
                return true;
        }

        return false;
    }
}
