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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Redirect as ResultRedirect;
use Magento\Customer\Model\Session;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;
use Aheadworks\Helpdesk2\Controller\TicketAbstract;

/**
 * Class Reply
 *
 * @package Aheadworks\Helpdesk2\Controller\Support
 */
class Reply extends TicketAbstract
{
    /**
     * @var CommandInterface
     */
    private $replyCommand;

    /**
     * @var ProcessorInterface
     */
    private $postDataProcessor;

    /**
     * @param Context $context
     * @param TicketRepositoryInterface $ticketRepository
     * @param CommandInterface $replyCommand
     * @param ProcessorInterface $postDataProcessor
     * @param Session $customerSession
     */
    public function __construct(
        Context $context,
        TicketRepositoryInterface $ticketRepository,
        CommandInterface $replyCommand,
        ProcessorInterface $postDataProcessor,
        Session $customerSession
    ) {
        parent::__construct($context, $ticketRepository, $customerSession);
        $this->replyCommand = $replyCommand;
        $this->postDataProcessor = $postDataProcessor;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPostValue()) {
            try {
                $ticket = $this->getTicketByExternalLink();
                $data[TicketInterface::ENTITY_ID] = $ticket->getEntityId();
                $data[TicketInterface::CUSTOMER_NAME] = $ticket->getCustomerName();
                $data[TicketInterface::CUSTOMER_EMAIL] = $ticket->getCustomerEmail();
                $ticketData = $this->postDataProcessor->prepareEntityData($data);
                $this->replyCommand->execute($ticketData);
                $this->messageManager->addSuccessMessage(__('Reply successfully added'));

                if ($this->customerSession->authenticate()) {
                    $resultRedirect->setPath('*/*/view', ['id' => $ticket->getEntityId()]);
                } else {
                    $resultRedirect->setPath('*/*/external', ['key' => $ticket->getExternalLink()]);
                }
                return $resultRedirect;
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while adding the reply'));
            }
        }

        return $resultRedirect->setPath('*/*/');
    }
}
