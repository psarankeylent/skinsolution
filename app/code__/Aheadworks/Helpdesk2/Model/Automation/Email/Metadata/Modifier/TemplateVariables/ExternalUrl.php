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
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables;

use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;
use Aheadworks\Helpdesk2\Model\Source\Email\Variables as EmailVariables;
use Aheadworks\Helpdesk2\Model\UrlBuilder;

/**
 * Class ExternalUrl
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables
 */
class ExternalUrl implements ModifierInterface
{
    /**
     * @var UrlBuilder
     */
    private $urlBuilder;

    /**
     * @param UrlBuilder $urlBuilder
     */
    public function __construct(
        UrlBuilder $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * @inheritdoc
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        $templateVariables = $emailMetadata->getTemplateVariables();
        $externalLink = $eventData->getTicket()->getExternalLink();
        $templateVariables[EmailVariables::EXTERNAL_URL] = $this->urlBuilder->getTicketExternalLink($externalLink);
        $emailMetadata->setTemplateVariables($templateVariables);

        return $emailMetadata;
    }
}
