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
namespace Aheadworks\Helpdesk2\Model\Data\Command\Rejection\Message;

use Aheadworks\Helpdesk2\Api\Data\EmailInterfaceFactory;
use Aheadworks\Helpdesk2\Api\Data\RejectedMessageInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Loader as EmailLoader;
use Aheadworks\Helpdesk2\Model\Rejection\Processor\Type\Email as EmailProcessor;
use Aheadworks\Helpdesk2\Model\Rejection\MessageRepository;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Email as EmailResourceModel;
use Aheadworks\Helpdesk2\Model\Source\Gateway\Email\Status as EmailStatus;

/**
 * Class MarkAsUnprocessed
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Rejection\Email
 */
class MarkAsUnprocessed implements CommandInterface
{
    /**
     * @var EmailResourceModel
     */
    private $emailResource;

    /**
     * @var EmailLoader
     */
    private $emailLoader;

    /**
     * @var MessageRepository
     */
    private $rejectedMessageRepository;

    /**
     * @param EmailResourceModel $emailResource
     * @param EmailLoader $emailLoader
     * @param MessageRepository $rejectedMessageRepository
     */
    public function __construct(
        EmailResourceModel $emailResource,
        EmailLoader $emailLoader,
        MessageRepository $rejectedMessageRepository
    ) {
        $this->emailResource = $emailResource;
        $this->emailLoader = $emailLoader;
        $this->rejectedMessageRepository = $rejectedMessageRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        if (!isset($data['item'])) {
            throw new \InvalidArgumentException(
                'Rejected email item param is required'
            );
        }

        /** @var RejectedMessageInterface $rejectedMessage */
        $rejectedMessage = $data['item'];
        $emailId = $rejectedMessage->getMessageData(EmailProcessor::GATEWAY_EMAIL_ID);

        $email = $this->emailLoader->loadById($emailId);
        if ($email->getId()) {
            $email
                ->setStatus(EmailStatus::UNPROCESSED)
                ->setRejectPatternId(null);

            $this->emailResource->save($email);
            $this->rejectedMessageRepository->delete($rejectedMessage);

            return true;
        }

        return false;
    }
}
