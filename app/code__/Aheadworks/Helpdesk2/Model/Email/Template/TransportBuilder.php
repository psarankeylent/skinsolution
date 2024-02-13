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
namespace Aheadworks\Helpdesk2\Model\Email\Template;

use Aheadworks\Helpdesk2\Model\Email\Mail\EmailMessage;
use Aheadworks\Helpdesk2\Model\Email\Mail\EmailMessageFactory;
use Aheadworks\Helpdesk2\Model\Email\Mail\Header\HeaderInterface;
use Zend\Mime\Mime;
use Magento\Framework\Mail\Template\FactoryInterface;
use Magento\Framework\Mail\Template\SenderResolverInterface;
use Magento\Framework\Mail\Template\TransportBuilder as FrameworkTransportBuilder;
use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\AddressConverter;
use Magento\Framework\Mail\Exception\InvalidArgumentException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\MessageInterface;
use Magento\Framework\Mail\MimeMessageInterfaceFactory;
use Magento\Framework\Mail\MimePartInterfaceFactory;
use Magento\Framework\Mail\TransportInterfaceFactory;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class TransportBuilder
 *
 * @package Aheadworks\Helpdesk2\Model\Email\Template
 */
class TransportBuilder extends FrameworkTransportBuilder
{
    /**
     * Param that used for storing all message data until it will be used
     *
     * @var array
     */
    protected $messageData = [];

    /**
     * @var array
     */
    protected $messageBodyParts = [];

    /**
     * @var EmailMessageFactory
     */
    protected $emailMessageInterfaceFactory;

    /**
     * @var MimeMessageInterfaceFactory
     */
    protected $mimeMessageInterfaceFactory;

    /**
     * @var MimePartInterfaceFactory
     */
    protected $mimePartInterfaceFactory;

    /**
     * @var AddressConverter
     */
    protected $addressConverter;

    /**
     * @var HeaderInterface[]
     */
    private $headers;

    /**
     * @param FactoryInterface $templateFactory
     * @param MessageInterface $message
     * @param SenderResolverInterface $senderResolver
     * @param ObjectManagerInterface $objectManager
     * @param TransportInterfaceFactory $mailTransportFactory
     */
    public function __construct(
        FactoryInterface $templateFactory,
        MessageInterface $message,
        SenderResolverInterface $senderResolver,
        ObjectManagerInterface $objectManager,
        TransportInterfaceFactory $mailTransportFactory
    ) {
        parent::__construct(
            $templateFactory,
            $message,
            $senderResolver,
            $objectManager,
            $mailTransportFactory
        );
        $this->emailMessageInterfaceFactory = $this->objectManager->get(EmailMessageFactory::class);
        $this->mimeMessageInterfaceFactory = $this->objectManager->get(MimeMessageInterfaceFactory::class);
        $this->mimePartInterfaceFactory = $this->objectManager->get(MimePartInterfaceFactory::class);
        $this->addressConverter = $this->objectManager->get(AddressConverter::class);
    }

    /**
     * @inheritdoc
     */
    public function addCc($address, $name = '')
    {
        $this->addAddressByType('cc', $address, $name);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addTo($address, $name = '')
    {
        $this->addAddressByType('to', $address, $name);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addBcc($address)
    {
        $this->addAddressByType('bcc', $address);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setReplyTo($email, $name = null)
    {
        $this->addAddressByType('replyTo', $email, $name);

        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws MailException
     */
    public function setFrom($from)
    {
        return $this->setFromByScope($from, null);
    }

    /**
     * @inheritdoc
     */
    public function setFromByScope($from, $scopeId = null)
    {
        $result = $this->_senderResolver->resolve($from, $scopeId);
        $this->addAddressByType('from', $result['email'], $result['name']);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addAttachment(
        $body,
        $filename = null,
        $mimeType = Mime::TYPE_OCTETSTREAM,
        $disposition = Mime::DISPOSITION_ATTACHMENT,
        $encoding = Mime::ENCODING_BASE64
    ) {
        $attachmentPart = $this->mimePartInterfaceFactory->create(
            [
                'content' => $body,
                'type' => $mimeType,
                'fileName' => $filename,
                'disposition' => $disposition,
                'encoding' => $encoding,
            ]
        );
        $this->messageBodyParts[] = $attachmentPart;

        return $this;
    }

    /**
     * Add email headers
     *
     * @param HeaderInterface[] $header
     * @return $this
     */
    public function addHeaders($headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function reset()
    {
        $this->messageData = [];
        $this->messageBodyParts = [];
        $this->headers = [];

        return parent::reset();
    }

    /**
     * @inheritdoc
     */
    protected function prepareMessage()
    {
        $template = $this->getTemplate();
        $content = $template->processTemplate();
        $contentPart = $this->mimePartInterfaceFactory->create(['content' => $content]);

        if ($template->getType() != TemplateTypesInterface::TYPE_TEXT
            && $template->getType() != TemplateTypesInterface::TYPE_HTML
        ) {
            throw new LocalizedException(__('Unknown template type'));
        }

        $this->messageBodyParts[] = $contentPart;

        $this->messageData['body'] = $this->mimeMessageInterfaceFactory->create(
            ['parts' => $this->messageBodyParts]
        );

        //phpcs:ignore Magento2.Functions.DiscouragedFunction
        $this->messageData['subject'] = html_entity_decode(
            (string)$template->getSubject(),
            ENT_QUOTES
        );

        /** @var EmailMessage $message */
        $message = $this->emailMessageInterfaceFactory->create($this->messageData);
        if(!empty($this->headers)) {
            foreach ($this->headers as $header) {
                $message->setHeader($header);
            }
        }

        $this->message = $message;

        return $this;
    }

    /**
     * Handles possible incoming types of email (string or array)
     *
     * @param string $addressType
     * @param string|array $email
     * @param string|null $name
     *
     * @return void
     * @throws InvalidArgumentException
     */
    private function addAddressByType(string $addressType, $email, ?string $name = null): void
    {
        if (is_string($email)) {
            $this->messageData[$addressType][] = $this->addressConverter->convert($email, $name);
            return;
        }
        $convertedAddressArray = $this->addressConverter->convertMany($email);
        if (isset($this->messageData[$addressType])) {
            $this->messageData[$addressType] = array_merge(
                $this->messageData[$addressType],
                $convertedAddressArray
            );
        } else {
            $this->messageData[$addressType] = $convertedAddressArray;
        }
    }
}
