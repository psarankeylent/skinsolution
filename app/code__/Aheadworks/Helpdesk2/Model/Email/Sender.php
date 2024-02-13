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
namespace Aheadworks\Helpdesk2\Model\Email;

use Aheadworks\Helpdesk2\Model\Email\Template\TransportBuilder;
use Aheadworks\Helpdesk2\Model\Email\Template\TransportBuilderFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;

/**
 * Class Sender
 *
 * @package Aheadworks\Helpdesk2\Model\Email
 */
class Sender
{
    /**
     * @var TransportBuilderFactory
     */
    private $transportBuilderFactory;

    /**
     * @param TransportBuilderFactory $transportBuilderFactory
     */
    public function __construct(
        TransportBuilderFactory $transportBuilderFactory
    ) {
        $this->transportBuilderFactory = $transportBuilderFactory;
    }

    /**
     * Send email message
     *
     * @param MetadataInterface $emailMetadata
     * @throws LocalizedException
     * @throws MailException
     */
    public function send($emailMetadata)
    {
        //print_r(get_class_methods($emailMetadata));
        //print_r($emailMetadata->getData());
        //echo "this is executed"; die;
        /** @var TransportBuilder $transportBuilder */
        $transportBuilder = $this->transportBuilderFactory->create();

        $transportBuilder
            ->setTemplateModel(Template::class)
            ->setTemplateIdentifier($emailMetadata->getTemplateId())
            ->setTemplateOptions($emailMetadata->getTemplateOptions())
            ->setTemplateVars($emailMetadata->getTemplateVariables())
            ->setFromByScope(
                [
                    'name' => $emailMetadata->getSenderName(),
                    'email' => $emailMetadata->getSenderEmail()
                   // 'email' => 'no-reply@enhance.md'
                ]
            )->addTo(
                $emailMetadata->getRecipientEmail(),
                $emailMetadata->getRecipientName()
            );



        $attachments = $emailMetadata->getAttachments() ? : [];
        foreach ($attachments as $attachment) {
            $transportBuilder->addAttachment($attachment['content'], $attachment['name']);
        }
        if ($emailMetadata->getCcRecipients() && is_array($emailMetadata->getCcRecipients())) {
            foreach ($emailMetadata->getCcRecipients() as $ccRecipient) {
                $transportBuilder->addCc($ccRecipient);
            }
        }
        if ($emailMetadata->getEmailReplyTo()) {
            //$transportBuilder->setReplyTo($emailMetadata->getEmailReplyTo());
            $transportBuilder->setReplyTo('no-reply@enhance.md');
        }
        if ($emailMetadata->getHeaders()) {
            $transportBuilder->addHeaders($emailMetadata->getHeaders());
        
        }

        $transportBuilder->getTransport()->sendMessage();
    }
}
