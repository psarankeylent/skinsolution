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
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Gateway;

use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Backend\Model\View\Result\Page as ResultPage;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Model\Source\Gateway\Type as GatewayTypeSource;

/**
 * Class NewAction
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Gateway
 */
class NewAction extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::gateways';

    /**
     * @var ForwardFactory
     */
    private $resultForwardFactory;

    /**
     * @var PageFactory
     */
    private $resultPageFactory;

    /**
     * @var GatewayTypeSource
     */
    private $gatewayTypeSource;

    /**
     * @param Context $context
     * @param ForwardFactory $resultForwardFactory
     * @param PageFactory $resultPageFactory
     * @param GatewayTypeSource $gatewayTypeSource
     */
    public function __construct(
        Context $context,
        ForwardFactory $resultForwardFactory,
        PageFactory $resultPageFactory,
        GatewayTypeSource $gatewayTypeSource
    ) {
        parent::__construct($context);
        $this->resultForwardFactory = $resultForwardFactory;
        $this->resultPageFactory = $resultPageFactory;
        $this->gatewayTypeSource = $gatewayTypeSource;
    }

    /**
     * Create new gateway page
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $gatewayType = $this->getRequest()->getParam(GatewayDataInterface::TYPE);
        $typeList = $this->gatewayTypeSource->getTypeList();
        if (!$gatewayType || !in_array($gatewayType, $typeList)) {
            return $this->resultForwardFactory->create()->forward('noroute');
        }

        /** @var ResultPage $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addHandle(['aw_helpdesk2_gateway_type_' . $gatewayType]);
        $resultPage->setActiveMenu('Aheadworks_Helpdesk2::gateways');
        $resultPage->getConfig()->getTitle()->prepend(__('New Gateway'));

        return $resultPage;
    }
}
