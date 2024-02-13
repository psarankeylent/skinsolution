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
namespace Aheadworks\Helpdesk2\Model\Rejection\Processor\Type;

use Aheadworks\Helpdesk2\Model\Gateway\Email\Loader as EmailLoader;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Processor as EmailProcessor;
use Aheadworks\Helpdesk2\Model\Rejection\Processor\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Email as EmailResourceModel;

/**
 * Class Email
 *
 * @package Aheadworks\Helpdesk2\Model\Rejection\Processor\Type
 */
class Email implements ProcessorInterface
{
    const GATEWAY_EMAIL_ID = 'gateway_email_id';

    /**
     * @var EmailResourceModel
     */
    private $emailResource;

    /**
     * @var EmailLoader
     */
    private $emailLoader;

    /**
     * @var EmailProcessor
     */
    private $emailProcessor;

    /**
     * @param EmailResourceModel $emailResource
     * @param EmailLoader $emailLoader
     * @param EmailProcessor $emailProcessor
     */
    public function __construct(
        EmailResourceModel $emailResource,
        EmailLoader $emailLoader,
        EmailProcessor $emailProcessor
    ) {
        $this->emailResource = $emailResource;
        $this->emailLoader = $emailLoader;
        $this->emailProcessor = $emailProcessor;
    }

    /**
     * @inheritDoc
     */
    public function process($rejectedMessage)
    {
        $emailId = $rejectedMessage->getMessageData(self::GATEWAY_EMAIL_ID);
        $email = $this->emailLoader->loadById($emailId);
        if ($email->getId()) {
            $this->emailProcessor->parseMail($email);
            $this->emailResource->save($email);

            return true;
        }

        return false;
    }
}
