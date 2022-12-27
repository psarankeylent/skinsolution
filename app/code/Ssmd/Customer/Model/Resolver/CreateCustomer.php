<?php

declare(strict_types=1);

namespace Ssmd\Customer\Model\Resolver;

use Magento\CustomerGraphQl\Model\Customer\CreateCustomerAccount;
use Magento\CustomerGraphQl\Model\Customer\ExtractCustomerData;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Newsletter\Model\Config;
use Magento\Sales\Model\Order\OrderCustomerExtractor;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Model\Delegation\Storage;

/**
 * Create customer account resolver
 */
class CreateCustomer implements ResolverInterface
{
    /**
     * @var ExtractCustomerData
     */
    private $extractCustomerData;

    /**
     * @var CreateCustomerAccount
     */
    private $createCustomerAccount;

    /**
     * @var Config
     */
    private $newsLetterConfig;

    /**
     * @var OrderCustomerExtractor
     */
    private $customerExtractor;

    /**
     * @var Storage
     */
    private $storage;

    /**
     * CreateCustomer constructor.
     *
     * @param ExtractCustomerData $extractCustomerData
     * @param CreateCustomerAccount $createCustomerAccount
     * @param Config $newsLetterConfig
     */
    public function __construct(
        ExtractCustomerData $extractCustomerData,
        CreateCustomerAccount $createCustomerAccount,
        Config $newsLetterConfig,
        OrderCustomerExtractor $customerExtractor,
        Storage $storage
    ) {
        $this->newsLetterConfig = $newsLetterConfig;
        $this->extractCustomerData = $extractCustomerData;
        $this->createCustomerAccount = $createCustomerAccount;
        $this->customerExtractor = $customerExtractor;
        $this->storage = $storage;
    }

    /**
     * @inheritdoc
     */
    public function resolve(
        Field $field,
              $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if (empty($args['input']) || !is_array($args['input'])) {
            throw new GraphQlInputException(__('"input" value should be specified'));
        }

        try {
            if (!$this->newsLetterConfig->isActive(ScopeInterface::SCOPE_STORE)) {
                $args['input']['is_subscribed'] = false;
            }
            if (isset($args['input']['date_of_birth'])) {
                $args['input']['dob'] = $args['input']['date_of_birth'];
            }

            if (isset($args['input']['order_id'])) {
                $orderId = $args['input']['order_id'];
                $customer = $this->customerExtractor->extract($orderId);

                if ($customer->getEmail() === $args['input']['email']) {
                    $mixedData =  ['__sales_assign_order_id' => $orderId];
                    $this->storage->storeNewOperation($customer, $mixedData);
                } else {
                    throw new GraphQlInputException(
                        __(
                            'Guest Order %1 doesn\'t belong to the Current Customer',
                            [$args['input']['order_id']]
                        )
                    );
                }
            }

            $customer = $this->createCustomerAccount->execute(
                $args['input'],
                $context->getExtensionAttributes()->getStore()
            );

            $data = $this->extractCustomerData->execute($customer);
            return ['customer' => $data];
        } catch (Exception $e) {
            throw new GraphQlInputException(
                __(
                    $e->getMessage()
                )
            );
        }
    }
}
