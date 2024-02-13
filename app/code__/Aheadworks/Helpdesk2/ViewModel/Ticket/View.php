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
namespace Aheadworks\Helpdesk2\ViewModel\Ticket;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\Renderer\ViewRendererInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\Renderer\ViewRendererInterfaceFactory;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\TicketLocator;

/**
 * Class View
 *
 * @package Aheadworks\Helpdesk2\ViewModel\Ticket
 */
class View implements ArgumentInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var TicketLocator
     */
    private $ticketLocator;

    /**
     * @var ViewRendererInterfaceFactory
     */
    private $viewRendererFactory;

    /**
     * @param StoreManagerInterface $storeManager
     * @param RequestInterface $request
     * @param TicketLocator $ticketLocator
     * @param ViewRendererInterfaceFactory $viewRendererFactory
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        RequestInterface $request,
        TicketLocator $ticketLocator,
        ViewRendererInterfaceFactory $viewRendererFactory
    ) {
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->ticketLocator = $ticketLocator;
        $this->viewRendererFactory = $viewRendererFactory;
    }

    /**
     * Get ticket view renderer
     *
     * @return ViewRendererInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getTicketViewRenderer()
    {
        /** @var ViewRendererInterface $renderer */
        $renderer = $this->viewRendererFactory->create();
        $renderer
            ->setStoreId($this->storeManager->getStore()->getId())
            ->setTicket($this->ticketLocator->getTicket($this->request));

        return $renderer;
    }
}
