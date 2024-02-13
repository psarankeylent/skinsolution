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
namespace Aheadworks\Helpdesk2\Model\Service;

use Aheadworks\Helpdesk2\Api\GatewayManagementInterface;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Loader as EmailLoader;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Processor as EmailProcessor;
use Aheadworks\Helpdesk2\Model\ResourceModel\Gateway\Email as EmailResourceModel;

/**
 * Class GatewayService
 *
 * @package Aheadworks\Helpdesk2\Model\Service
 */
class GatewayService implements GatewayManagementInterface
{
    /**
     * @var EmailLoader
     */
    private $emailLoader;

    /**
     * @var EmailResourceModel
     */
    private $emailResource;

    /**
     * @var EmailProcessor
     */
    private $emailProcessor;

    /**
     * @param EmailLoader $emailLoader
     * @param EmailResourceModel $emailResource
     * @param EmailProcessor $emailProcessor
     */
    public function __construct(
        EmailLoader $emailLoader,
        EmailResourceModel $emailResource,
        EmailProcessor $emailProcessor
    ) {
        $this->emailLoader = $emailLoader;
        $this->emailResource = $emailResource;
        $this->emailProcessor = $emailProcessor;
    }

    /**
     * @inheritdoc
     */
    public function processEmails()
    {
        $emails = $this->emailLoader->loadUnprocessedEmails();
        foreach ($emails as $email) {
            $email = $this->emailProcessor->process($email);
            $this->emailResource->save($email);
        }

        return true;
    }
}
