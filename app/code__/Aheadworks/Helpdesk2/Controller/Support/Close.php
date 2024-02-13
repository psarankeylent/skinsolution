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

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Controller\TicketAbstract;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;

/**
 * Class Close
 *
 * @package Aheadworks\Helpdesk2\Controller\Support
 */
class Close extends TicketAbstract
{
    /**
     * @var CommandInterface
     */
    private $closeCommand;

    /**
     * @param Context $context
     * @param TicketRepositoryInterface $ticketRepository
     * @param CommandInterface $closeCommand
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        TicketRepositoryInterface $ticketRepository,
        CommandInterface $closeCommand,
        Session $customerSession
    ) {
        parent::__construct($context, $ticketRepository, $customerSession);
        $this->closeCommand = $closeCommand;
        $this->customerSession = $customerSession;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        try {
            $ticket = $this->getTicketByExternalLink();
            $this->closeCommand->execute(['ticket' => $ticket]);
            $this->messageManager->addSuccessMessage(__('Ticket was closed'));
            if ($this->customerSession->authenticate()) {
                $resultRedirect->setPath('*/*/view', ['id' => $ticket->getEntityId()]);
            } else {
                $resultRedirect->setPath('*/*/external', ['key' => $ticket->getExternalLink()]);
            }
            return $resultRedirect;
        } catch (NoSuchEntityException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while closing the ticket'));
        }

        return $resultRedirect->setRefererOrBaseUrl();
    }
}
