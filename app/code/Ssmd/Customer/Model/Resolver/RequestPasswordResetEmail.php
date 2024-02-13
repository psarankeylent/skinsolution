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
use Magento\Newsletter\Model\Config;
use Magento\Sales\Model\Order\OrderCustomerExtractor;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Model\Delegation\Storage;


use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\Resolver\ContextInterface;
use Magento\Framework\GraphQl\Query\Resolver\Value;
use Magento\Customer\Model\AccountManagement;

/**
 * Create customer account resolver
 */
class RequestPasswordResetEmail implements ResolverInterface
{
    /**
     * @var AccountManagementInterface
     */
    private $accountManagement;

    /**
     * RequestPasswordResetEmailResolver constructor.
     *
     * @param AccountManagementInterface $accountManagement
     */
    public function __construct(AccountManagementInterface $accountManagement)
    {
        $this->accountManagement = $accountManagement;
    }

    /**
     * @inheritDoc
     */
    public function resolve(Field $field, $context, $info, array $value = null, array $args = null)
    {
        $email = $args['email'];

        if ($email) {
            if (!\Zend_Validate::is($email, \Magento\Framework\Validator\EmailAddress::class)) {
                throw new GraphQlInputException(__('The email address is incorrect. Verify the email address and try again.'));
            }
            try {
                $this->accountManagement->initiatePasswordReset($email,AccountManagement::EMAIL_RESET);
            } catch (NoSuchEntityException $exception) {
                throw new GraphQlInputException(__('There is no customer account with this email'));
            } catch (\Exception $e) {
                throw new GraphQlInputException(__($e->getMessage()));
            }
        } else {
            throw new GraphQlInputException(__('Please enter your email.'));
        }

        return [
            'status' => 'success',
            'message' => 'Password reset email has been sent successfully.',
        ];
    }
}
