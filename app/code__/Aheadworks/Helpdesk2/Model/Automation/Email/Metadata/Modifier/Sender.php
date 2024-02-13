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
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier;

use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;
use Aheadworks\Helpdesk2\Model\Department\Search\GatewayResolver;

/**
 * Class Sender
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier
 */
class Sender implements ModifierInterface
{
    /**
     * @var GatewayResolver
     */
    private $gatewayResolver;

    /**
     * @var SenderResolverInterface
     */
    private $senderResolver;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param GatewayResolver $gatewayResolver
     * @param SenderResolverInterface $senderResolver
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        GatewayResolver $gatewayResolver,
        SenderResolverInterface $senderResolver,
        StoreManagerInterface $storeManager
    ) {
        $this->gatewayResolver = $gatewayResolver;
        $this->senderResolver = $senderResolver;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        $gateway = $this->gatewayResolver->resolveGatewayForDepartmentId(
            $eventData->getTicket()->getDepartmentId()
        );
        if ($gateway && $gateway->getEmail()) {
            /** @var Store $store */
            $store = $this->storeManager->getStore($eventData->getTicket()->getStoreId());
            $emailMetadata
                ->setSenderName($store->getFrontendName())
                ->setSenderEmail($gateway->getEmail());
        } else {
            $senderData = $this->senderResolver->resolve('support', $eventData->getTicket()->getStoreId());
            $emailMetadata
                ->setSenderName($senderData['name'] ?? '')
                ->setSenderEmail($senderData['email'] ?? '');
        }

        return $emailMetadata;
    }
}
