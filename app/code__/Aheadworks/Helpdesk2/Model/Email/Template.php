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
namespace Aheadworks\Helpdesk2\Model\Email;

use Magento\Framework\Mail\TemplateInterface;
use Magento\Email\Model\Template as MagentoEmailTemplate;
use Aheadworks\Helpdesk2\Model\Gateway\Email\Message\Filter as MessageFilter;
use Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables\Marker;

/**
 * Class Template
 *
 * @package Aheadworks\Helpdesk2\Model\Email
 */
class Template extends MagentoEmailTemplate implements TemplateInterface
{
    /**
     * @inheritDoc
     */
    public function load($modelId, $field = null)
    {
        parent::load($modelId, $field);
        $this->setData('is_legacy', true);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function loadDefault($templateId)
    {
        parent::loadDefault($templateId);
        $this->setData('is_legacy', true);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getProcessedTemplate(array $variables = [])
    {
        $processedTemplate = parent::getProcessedTemplate($variables);
        if (isset($variables[Marker::HISTORY_MARKER_FLAG_NAME])
            && $variables[Marker::HISTORY_MARKER_FLAG_NAME]
        ) {
            $processedTemplate = $this->getRepliesHistoryMarker() . $processedTemplate;
        }
        return $processedTemplate;
    }

    /**
     * Get replies history marker
     *
     * @return string
     */
    private function getRepliesHistoryMarker()
    {
        $message = __('Please type your reply above this line.');
        $markerHtml = '<br><div class="aw-helpdesk2-reply-marker">' . $message . '</div><br>';

        return MessageFilter::REPLIES_HISTORY_MARKER . $markerHtml;
    }
}
