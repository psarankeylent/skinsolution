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
namespace Aheadworks\Helpdesk2\Model\Automation\Email;

use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Model\Email\MetadataInterface as EmailMetadataInterface;
use Aheadworks\Helpdesk2\Model\Automation\EventDataInterface;

/**
 * Interface ModifierInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email
 */
interface ModifierInterface
{
    /**
     * Add metadata to existing object using event data
     *
     * @param EmailMetadataInterface $emailMetadata
     * @param EventDataInterface $eventData
     * @return EmailMetadataInterface
     * @throws LocalizedException
     */
    public function addMetadata($emailMetadata, $eventData);
}
