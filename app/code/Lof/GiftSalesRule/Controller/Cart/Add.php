<?php
/**
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this module to newer
 * versions in the future.
 *
 * @category  Lof
 * @package   Lof\GiftSalesRule
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 * @license   http://www.landofcoder.com/LICENSE-1.0.html
 */
namespace Lof\GiftSalesRule\Controller\Cart;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart;
use Magento\Quote\Api\CartRepositoryInterface;
use Psr\Log\LoggerInterface;
use Lof\GiftSalesRule\Api\GiftRuleServiceInterface;

/**
 * Controller for processing add to cart action.
 *
 * @author    Landofcoder <landofcoder@gmail.com>
 * @copyright 2020 Landofcoder
 */
class Add extends \Magento\Framework\App\Action\Action
{
    /**
     * @var Validator
     */
    protected $formKeyValidator;

    /**
     * @var GiftRuleServiceInterface
     */
    protected $giftRuleService;

    /**
     * @var CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Add constructor.
     *
     * @param Context                  $context          Context
     * @param Validator                $formKeyValidator Form key validator
     * @param GiftRuleServiceInterface $giftRuleService  Gift rule service
     * @param CartRepositoryInterface  $quoteRepository  Quote repository
     * @param Cart                     $cart             Cart
     * @param LoggerInterface          $logger           Logger
     */
    public function __construct(
        Context $context,
        Validator $formKeyValidator,
        GiftRuleServiceInterface $giftRuleService,
        CartRepositoryInterface $quoteRepository,
        Cart $cart,
        LoggerInterface $logger
    ) {
        $this->formKeyValidator = $formKeyValidator;
        $this->giftRuleService = $giftRuleService;
        $this->quoteRepository = $quoteRepository;
        $this->cart = $cart;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * Add product to shopping cart action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        if (!$this->formKeyValidator->validate($this->getRequest())) {
            $this->messageManager->addErrorMessage(
                __('Your session has expired')
            );

            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        $params = $this->getRequest()->getParams();

        if (!$this->validatePostParameters($params)) {
            $this->messageManager->addErrorMessage(
                __('Invalid gifts. We can\'t add this gift item to your shopping cart.')
            );

            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }

        $productData = $this->formatProductPostParameters($params);

        try {
             $this->giftRuleService->replaceGiftProducts(
                 $this->cart->getQuote(),
                 $productData,
                 $params['gift_rule_code'],
                 $params['gift_rule_id']
             );

            $this->quoteRepository->save($this->cart->getQuote());

            $this->messageManager->addSuccessMessage(
                __('You added gift product to your shopping cart.')
            );

            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        } catch (\Exception $e) {
            $this->logger->critical($e);
            $this->messageManager->addErrorMessage(
                __('We can\'t add this gift item to your shopping cart.')
            );

            return $this->resultRedirectFactory->create()->setPath('checkout/cart');
        }
    }

    /**
     * Validate post parameters.
     *
     * @param array $params params
     *
     * @return bool
     */
    protected function validatePostParameters(array $params): bool
    {
        $return = true;
        if (!isset($params['gift_rule_code'])
            || !isset($params['gift_rule_id'])
            || !isset($params['products'])
            || !is_array($params['products'])) {
            $return = false;
        }

        if ($return) {
            foreach ($params['products'] as $productData) {
                if (!isset($productData['qty'])) {
                    $return = false;
                }
            }
        }

        return $return;
    }

    /**
     * Format post parameters for the add to cart method.
     *
     * @param array $params params
     *
     * @return array
     */
    protected function formatProductPostParameters($params)
    {
        $filteredParams = [];
        foreach ($params['products'] as $productId => $productData) {
            if ($productData['qty']) {
                $productData['uenc'] = $params['uenc'];
                $productData['id'] = $productId;
                $productData['product'] = $productId;
                $productData['item'] = $productId;
                $filteredParams[] = $productData;
            }
        }
        return $filteredParams;
    }
}
