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
namespace Aheadworks\Helpdesk2\Model\Automation\Action\Handler;

use Psr\Log\LoggerInterface;
use Aheadworks\Helpdesk2\Model\Automation\Action\ActionInterface;
use Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Builder as MetadataBuilder;
use Aheadworks\Helpdesk2\Model\Email\Sender as EmailSender;
use Aheadworks\Helpdesk2\Model\Automation\Action\Handler\SendEmail\CanSendEmailCheckerInterface;

/**
 * Class SendEmail
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Action\Handler
 */
class SendEmail implements ActionInterface
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var MetadataBuilder
     */
    private $metadataBuilder;

    /**
     * @var EmailSender
     */
    private $emailSender;

    /**
     * @var CanSendEmailCheckerInterface
     */
    private $canSendEmailChecker;

    /**
     * @param LoggerInterface $logger
     * @param MetadataBuilder $metadataBuilder
     * @param EmailSender $emailSender
     * @param CanSendEmailCheckerInterface $canSendEmailChecker
     */
    public function __construct(
        LoggerInterface $logger,
        MetadataBuilder $metadataBuilder,
        EmailSender $emailSender,
        CanSendEmailCheckerInterface $canSendEmailChecker
    ) {
        $this->logger = $logger;
        $this->metadataBuilder = $metadataBuilder;
        $this->emailSender = $emailSender;
        $this->canSendEmailChecker = $canSendEmailChecker;
    }

    /**
     * @inheritdoc
     */
    public function run($actionData, $eventData)
    {
        if (!$this->canSendEmailChecker->canSend($actionData, $eventData)) {
            return false;
        }
        $emailMetadata = $this->metadataBuilder->buildForAction($actionData['action'], $eventData);
        $emailMetadata->setTemplateId($actionData['value']);
        try {
            $this->emailSender->send($emailMetadata);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return false;
        }

        return true;
    }
}
