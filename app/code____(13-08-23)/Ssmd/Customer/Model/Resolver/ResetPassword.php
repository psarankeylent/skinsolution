<?php

declare(strict_types=1);

namespace Ssmd\Customer\Model\Resolver;

use Magento\CustomerGraphQl\Model\Customer\CreateCustomerAccount;
use Magento\CustomerGraphQl\Model\Customer\ExtractCustomerData;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;
use Magento\Integration\Api\CustomerTokenServiceInterface;
use Magento\Newsletter\Model\Config;
use Magento\Sales\Model\Order\OrderCustomerExtractor;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Model\Delegation\Storage;
use Magento\Customer\Api\CustomerRepositoryInterface;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Customer\Model\AccountManagement;

/**
 * Create customer account resolver
 */
class ResetPassword implements ResolverInterface
{
    /**
     * @var CustomerTokenServiceInterface
     */
    private $customerTokenService;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var AccountManagementInterface
     */
    private $accountManagement;

    /**
     * ResetPasswordResolver constructor.
     *
     * @param AccountManagementInterface $accountManagement
     */
    public function __construct(
        AccountManagementInterface $accountManagement,
        CustomerRepositoryInterface $customerRepository,
        CustomerTokenServiceInterface $customerTokenService
    ) {
        $this->accountManagement = $accountManagement;
        $this->customerRepository = $customerRepository;
        $this->customerTokenService = $customerTokenService;
    }

    /**
     * @inheritDoc
     */
    public function resolve(Field $field, $context, $info, array $value = null, array $args = null)
    {
        $customerId = $args['input']['customer_id'];
        $resetToken = $args['input']['reset_token'];
        $newPassword = $args['input']['new_password'];

        if ($customerId && $this->customerRepository->getById($customerId)) {
            $customer = $this->customerRepository->getById($customerId);
            $email = $customer->getEmail();
        } else {
            throw new GraphQlInputException(__('Something went wrong while saving the new password.'));
        }

        try {
            $this->accountManagement->resetPassword($email, $resetToken, $newPassword);

            $token = $this->customerTokenService->createCustomerAccessToken($email, $newPassword);

            return [
                'status' => 'success',
                'message' => 'Password reset successfully.',
                'token' => $token,
                'firstname' => $customer->getFirstname(),
                'lastname' => $customer->getLastname(),
                'email' => $email,
            ];

        } catch (\Exception $e) {
            throw new GraphQlInputException(__($e->getMessage()));
        }


    }
}
