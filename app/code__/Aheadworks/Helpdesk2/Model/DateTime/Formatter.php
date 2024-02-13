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
namespace Aheadworks\Helpdesk2\Model\DateTime;

/**
 * Class Formatter
 *
 * @package Aheadworks\Helpdesk2\Model\DateTime
 */
class Formatter
{
    /**
     * @var string
     */
    private $dateFormat;

    /**
     * @param string $dateFormat
     */
    public function __construct(
        $dateFormat = 'M j, Y h:i:s A'
    ) {
        $this->dateFormat = $dateFormat;
    }

    /**
     * Format date(UTC) to default scope specified
     *
     * @param string $date
     * @return string
     */
    public function formatDate($date)
    {
        //todo add timezone
        $date = new \DateTime($date);
        return $date->format($this->dateFormat);
    }
}
