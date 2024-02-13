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
namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Processor;

/**
 * Class EmailParser
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email\Processor
 */
class EmailParser
{
    /**
     * Parse customer email from mail subject
     *
     * @param string $fromSubject
     * @return bool|string
     */
    public function parseFromSubject($fromSubject)
    {
        if (preg_match("/([a-z0-9.\-_]+@[a-z0-9.\-_]+\.[a-z0-9.\-_]+)/i", $fromSubject, $matches)) {
            if (isset($matches[1])) {
                return strtolower($matches[1]);
            }
        }

        return false;
    }
}
