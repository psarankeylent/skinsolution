<?php

declare(strict_types=1);

namespace Ssmd\Customer\Model\Resolver;

use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\ForgotPasswordToken\ConfirmCustomerByToken;

/**
 * Validate Password Token resolver
 */
class ValidateResetPasswordToken implements ResolverInterface
{
    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var \Magento\Customer\Model\ForgotPasswordToken\ConfirmCustomerByToken
     */
    private $confirmByToken;

    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * RequestPasswordResetEmailResolver constructor.
     *
     * @param AccountManagementInterface $accountManagement
     */
    public function __construct(
        AccountManagementInterface $accountManagement,
        ConfirmCustomerByToken $confirmByToken,
        CustomerRepositoryInterface $customerRepository
    )
    {
        $this->accountManagement = $accountManagement;
        $this->customerRepository = $customerRepository;
        $this->confirmByToken = $confirmByToken;
    }

    /**
     * @inheritDoc
     */
    public function resolve(Field $field, $context, $info, array $value = null, array $args = null)
    {
        $customerId = $args['input']['customer_id'];
        $resetPasswordToken = $args['input']['rp_token'];

        try {
            $this->accountManagement->validateResetPasswordLinkToken($customerId, $resetPasswordToken);
            $this->confirmByToken->resetCustomerConfirmation($customerId);

            $customer = $this->customerRepository->getById($customerId);
            $this->accountManagement->changeResetPasswordLinkToken($customer, $resetPasswordToken);
        }  catch (\Exception $e) {
            throw new GraphQlInputException(__('Your password reset link has expired.'));
        }

        return [
            'status' => 'success',
            'message' => 'Reset password link has been verified successfully.',
        ];
    }
}
