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
namespace Aheadworks\Helpdesk2\Controller\Support;

use Magento\Framework\Phrase;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page as ResultPage;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;

/**
 * Class View
 *
 * @package Aheadworks\Helpdesk2\Controller\Support
 */
class View extends CustomerAbstractAction
{
    /**
     * @inheritdoc
     */
    public function execute()
    {
        $ticket = $this->getTicket();
        if (!$ticket) {
            $this->messageManager->addErrorMessage(__('This ticket doesn\'t exist'));
            /** ResultRedirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('customer/support');
        }

        /** @var ResultPage $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->getConfig()->getTitle()->set($this->createPageTitle($ticket));
        //$resultPage->getConfig()->getTitle()->set(__('My Messages'));
        return $resultPage;
    }

    /**
     * Get current ticket
     *
     * @return TicketInterface|null
     */
    private function getTicket()
    {
        try {
            $ticket = $this->getTicketById();
            if (!$this->isTicketBelongToCurrentCustomer($ticket)) {
                $ticket = null;
            }
        } catch (NoSuchEntityException $exception) {
            $ticket = null;
        }

        return $ticket;
    }

    /**
     * Create page title
     *
     * @param TicketInterface $ticket
     * @return Phrase
     */
    private function createPageTitle($ticket)
    {
        return __(
            '[%1] %2',
            [
                $ticket->getUid(),
                $ticket->getSubject()
            ]
        );
    }
}
