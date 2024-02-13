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
namespace Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType\Google;

use Magento\Backend\Model\UrlInterface;

/**
 * Class Config
 *
 * @package Aheadworks\Helpdesk2\Model\Gateway\Email\Connection\AuthType\Google
 */
class Config
{
    const ACCESS_TYPE = 'offline';

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @param UrlInterface $urlBuilder
     */
    public function __construct(
        UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Get redirect uri for google client
     *
     * @return string
     */
    public function getRedirectUri()
    {
        return $this->urlBuilder->getUrl('aw_helpdesk2/gateway_google/verify', ['key' => '']);
    }

    /**
     * Get access type
     *
     * @return string
     */
    public function getAccessType()
    {
        return self::ACCESS_TYPE;
    }
}
