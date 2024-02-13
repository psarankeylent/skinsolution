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
namespace Aheadworks\Helpdesk2\Model\Email\Mail;

use Aheadworks\Helpdesk2\Model\Email\Mail\Header\HeaderInterface;

/**
 * Class EmailMessage
 *
 * @package Aheadworks\Helpdesk2\Model\Email\Mail
 */
class EmailMessage extends \Magento\Framework\Mail\EmailMessage
{
    /**
     * Set email header
     *
     * @param HeaderInterface $header
     * @return $this
     */
    public function setHeader($header)
    {
        $headers = $this->zendMessage->getHeaders();
        if ($headers->has($header->getName())) {
            $headers->removeHeader($header->getName());
        }
        $headers->addHeaderLine($header->getName(), $header->getValue());

        return $this;
    }
}
