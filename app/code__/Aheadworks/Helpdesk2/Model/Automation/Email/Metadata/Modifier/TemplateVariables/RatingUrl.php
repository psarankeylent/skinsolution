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
 * Class RatingUrl
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables
 */
class RatingUrl implements ModifierInterface
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
        $templateVariables[EmailVariables::RATING_URL_STAR_1]
            = $this->urlBuilder->getTicketCustomerRatingLink($externalLink, 1);
        $templateVariables[EmailVariables::RATING_URL_STAR_2]
            = $this->urlBuilder->getTicketCustomerRatingLink($externalLink, 2);
        $templateVariables[EmailVariables::RATING_URL_STAR_3]
            = $this->urlBuilder->getTicketCustomerRatingLink($externalLink, 3);
        $templateVariables[EmailVariables::RATING_URL_STAR_4]
            = $this->urlBuilder->getTicketCustomerRatingLink($externalLink, 4);
        $templateVariables[EmailVariables::RATING_URL_STAR_5]
            = $this->urlBuilder->getTicketCustomerRatingLink($externalLink, 5);
        $emailMetadata->setTemplateVariables($templateVariables);

        return $emailMetadata;
    }
}
