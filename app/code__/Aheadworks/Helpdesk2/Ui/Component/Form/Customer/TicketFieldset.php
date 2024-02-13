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
namespace Aheadworks\Helpdesk2\Ui\Component\Form\Customer;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\ComponentVisibilityInterface;
use Magento\Ui\Component\Form\Fieldset;

/**
 * Class TicketFieldset
 *
 * @package Aheadworks\Helpdesk2\Ui\Component\Form\Customer
 */
class TicketFieldset extends Fieldset implements ComponentVisibilityInterface
{
    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @param ContextInterface $context
     * @param AuthorizationInterface $authorization
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        AuthorizationInterface $authorization,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->authorization = $authorization;
    }

    /**
     * Render component in case customer is modified and hide for new customer
     *
     * @return boolean
     */
    public function isComponentVisible(): bool
    {
        $isAllowed = $this->authorization->isAllowed('Aheadworks_Helpdesk2::tickets');
        $customerId = $this->context->getRequestParam('id');
        return (bool)$customerId && $isAllowed;
    }
}
