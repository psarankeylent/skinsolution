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
namespace Aheadworks\Helpdesk2\Model\Gateway\Processor;

use Aheadworks\Helpdesk2\Api\Data\EmailInterface;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\Management as ConnectionManagement;
use Aheadworks\Helpdesk2\Model\Gateway\Email\ConnectionProvider;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Factory as EmailFactory;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Email as GatewayEmailResourceModel;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;
use Zend\Mail\Protocol\Exception\RuntimeException;
use Zend\Mail\Storage\Message as MailMessage;

/**
 * Class EmailProcessor
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Processor
 */
class EmailProcessor implements ProcessorInterface
{
    /**
     * @var object
     */
    private $connection;

    /**
     * @var GatewayDataInterface
     */
    private $gateway;

    /**
     * @var ConnectionProvider
     */
    private $connectionProvider;

    /**
     * @var ConnectionManagement
     */
    private $connectionManagement;

    /**
     * @var GatewayEmailResourceModel
     */
    private $emailResourceModel;

    /**
     * @var EmailFactory
     */
    private $emailFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var int
     */
    private $limit;

    /**
     * @param ConnectionProvider $connectionProvider
     * @param ConnectionManagement $connectionManagement
     * @param GatewayEmailResourceModel $emailResourceModel
     * @param EmailFactory $emailFactory
     * @param LoggerInterface $logger
     * @param int $limit
     */
    public function __construct(
        ConnectionProvider $connectionProvider,
        ConnectionManagement $connectionManagement,
        GatewayEmailResourceModel $emailResourceModel,
        EmailFactory $emailFactory,
        LoggerInterface $logger,
        $limit = 50
    ) {
        $this->connectionProvider = $connectionProvider;
        $this->connectionManagement = $connectionManagement;
        $this->emailResourceModel = $emailResourceModel;
        $this->emailFactory = $emailFactory;
        $this->logger = $logger;
        $this->limit = $limit;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function process($gateway)
    {
        $this->gateway = $gateway;
        $connection = $this->connectionProvider->getConnection($gateway);
        $this->connection = $connection;

        $newMailUIDs = $this->getNewMailUIDs();
        foreach ($newMailUIDs as $mailUid) {
            try {
                $message = $this->getMessageByMailUid($mailUid);
                $this->saveMessage($message, $mailUid);
            } catch (\Exception $e) {
                $this->logger->error(
                    __('An error occurred in gateway: "%1", message: %2', $gateway->getName(), $e->getMessage())
                );
            }
        }
    }

    /**
     * Prepare new mail UIDs
     *
     * @return array
     * @throws LocalizedException
     */
    private function getNewMailUIDs()
    {
        $allGatewayMailUIDs = $this->retrieveGatewayMailUIDs();
        $lastSavedMailUID = $this->emailResourceModel->getLastSavedMailUIDByGateway(
            $this->gateway->getId(),
            $this->gateway->getEmail()
        );

        if ($lastSavedMailUID) {
            $newMailUIDs = array_filter($allGatewayMailUIDs, function($uid) use ($lastSavedMailUID) {
                return $uid > $lastSavedMailUID;
            });
        } else {
            $newMailUIDs = $allGatewayMailUIDs;
        }

        $newMailUIDs = array_slice($newMailUIDs, 0, $this->limit);

        return $newMailUIDs;
    }

    /**
     * Retrieve mail UIDs from gateway
     *
     * @return array
     */
    private function retrieveGatewayMailUIDs()
    {
        try {
            $mailUIDs = $this->connectionManagement->getMailUIDs($this->connection);
        } catch (\Exception $e) {
            $mailUIDs = [];
        }

        return $mailUIDs;
    }

    /**
     * Get message by mail UID
     *
     * @param string $mailUid
     * @return MailMessage
     * @throws RuntimeException
     */
    private function getMessageByMailUid($mailUid)
    {
        $messageNumber = $this->connectionManagement->getMessageNumberByMailUid($this->connection, $mailUid);
        $message = $this->connectionManagement->getMessageByNumber($this->connection, $messageNumber);

        return $message;
    }

    /**
     * Save message
     *
     * @param MailMessage $message
     * @param string $mailUid
     * @throws \Exception
     */
    private function saveMessage($message, $mailUid)
    {
        $messageNumber = $this->connectionManagement->getMessageNumberByMailUid($this->connection, $mailUid);

        /** @var EmailInterface $mail */
        $email = $this->emailFactory->create($message);
        $email
            ->setUid($mailUid . $this->gateway->getEmail())
            ->setGatewayId($this->gateway->getId())
            ->setHeaders($this->connectionManagement->getMessageHeadersByNumber($this->connection, $messageNumber));

        $this->emailResourceModel->save($email);

        if ($this->gateway->getIsDeleteFromHost()) {
            $this->connectionManagement->removeMessageFromServerByNumber($this->connection, $messageNumber);
        }
    }
}
