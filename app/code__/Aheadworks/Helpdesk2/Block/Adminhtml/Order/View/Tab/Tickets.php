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
namespace Aheadworks\Helpdesk2\Block\Adminhtml\Order\View\Tab;

use Magento\Framework\AuthorizationInterface;
use Magento\Framework\View\Element\Text\ListText;
use Magento\Framework\View\Element\Context;
use Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Class Tickets
 *
 * @package Aheadworks\Helpdesk2\Block\Adminhtml\Order\View\Tab
 */
class Tickets extends ListText implements TabInterface
{
    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @param Context $context
     * @param AuthorizationInterface $authorization
     * @param array $data
     */
    public function __construct(
        Context $context,
        AuthorizationInterface $authorization,
        array $data = []
    ) {
        $this->authorization = $authorization;
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    public function getTabLabel()
    {
        return __('Help Desk Tickets');
    }

    /**
     * @inheritdoc
     */
    public function getTabTitle()
    {
        return __('Help Desk Tickets');
    }

    /**
     * @inheritdoc
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function isHidden()
    {
        return !$this->authorization->isAllowed('Aheadworks_Helpdesk2::tickets');
    }
}
