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
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier;

use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;
use Aheadworks\Helpdesk2\Model\Email\Mail\Header\Header;
use Aheadworks\Helpdesk2\Model\Email\Mail\Header\HeaderInterfaceFactory;

/**
 * Class SuppressAutoResponseHeader
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier
 */
class SuppressAutoResponseHeader implements ModifierInterface
{
    /**
     * @var HeaderInterfaceFactory
     */
    private $headerFactory;

    /**
     * @param HeaderInterfaceFactory $headerFactory
     */
    public function __construct(
        HeaderInterfaceFactory $headerFactory
    ) {
        $this->headerFactory = $headerFactory;
    }

    /**
     * @inheritdoc
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        $ticket = $eventData->getTicket();
        /** @var Header $header */
        $header = $this->headerFactory->create();
        $header
            ->setName('X-Auto-Response-Suppress')
            ->setValue('OOF');
        $emailMetadata->addHeader($header);

        $header = $this->headerFactory->create();
        $header
            ->setName('Auto-Submitted')
            ->setValue('auto-replied');
        $emailMetadata->addHeader($header);

        return $emailMetadata;
    }
}
