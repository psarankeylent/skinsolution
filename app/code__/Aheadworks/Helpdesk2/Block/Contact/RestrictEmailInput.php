<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Helpdesk2
 * @version    2.0.6
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Helpdesk2\Block\Contact;

use Magento\Authorization\Model\UserContextInterface;
use Magento\Framework\View\Element\Template;

/**
 * Class RestrictEmailInput
 *
 * @package Aheadworks\Helpdesk2\Block\Contact
 */
class RestrictEmailInput extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_Helpdesk2::contact/restrict_email_input.phtml';

    /**
     * @var UserContextInterface
     */
    private $userContext;

    /**
     * @param Template\Context $context
     * @param UserContextInterface $userContext
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        UserContextInterface $userContext,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->userContext = $userContext;
    }

    /**
     * @inheritDoc
     */
    protected function _toHtml()
    {
        return $this->userContext->getUserType() == $this->userContext::USER_TYPE_CUSTOMER
            ? parent::_toHtml()
            : '';
    }
}
