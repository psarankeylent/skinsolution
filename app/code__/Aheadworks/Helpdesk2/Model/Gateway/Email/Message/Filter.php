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
namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Message;

/**
 * Class Filter
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email\Message
 */
class Filter
{
    /**
     * RegEx pattern to detect previous replies
     */
    const REPLIES_HISTORY_REGEX = '/(<!--){1}(\sHD2_REPLY_MARKER)[\s\S]*/';
    const REPLIES_HISTORY_MARKER = '<!-- HD2_REPLY_MARKER -->';

    /**
     * Cut history of previous replies
     *
     * @param string $content
     * @return string
     */
    public function cutRepliesHistory($content)
    {
        return preg_replace(self::REPLIES_HISTORY_REGEX, '', $content);
    }
}
