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
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Ticket;

use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\Result\Json as ResultJson;
use Magento\Framework\Exception\LocalizedException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Data\Validator\Ticket\InlineEditor\Validator;

/**
 * Class InlineEdit
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Ticket
 */
class InlineEdit extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::tickets';

    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var CommandInterface
     */
    private $updateCommand;

    /**
     * @var Validator
     */
    private $gridValidator;

    /**
     * @param Context $context
     * @param JsonFactory $jsonFactory
     * @param CommandInterface $updateCommand
     * @param Validator $gridValidator
     */
    public function __construct(
        Context $context,
        JsonFactory $jsonFactory,
        CommandInterface $updateCommand,
        Validator $gridValidator
    ) {
        parent::__construct($context);
        $this->jsonFactory = $jsonFactory;
        $this->updateCommand = $updateCommand;
        $this->gridValidator = $gridValidator;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var ResultJson $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData(
                [
                    'messages' => [__('Please correct the data sent')],
                    'error' => true,
                ]
            );
        }

        foreach ($postItems as $ticketId => $postItem) {
            try {
                if (!$error) {
                    $this->gridValidator->validateItem($postItem);
                    $this->updateCommand->execute($postItem);
                }
            } catch (LocalizedException $e) {
                $messages[] = $e->getMessage();
                $error = true;
            } catch (\Exception $e) {
                $messages[] =  __('Something went wrong while saving the ticket');
                $error = true;
            }
        }

        return $resultJson->setData(['messages' => $messages, 'error' => $error]);
    }
}
