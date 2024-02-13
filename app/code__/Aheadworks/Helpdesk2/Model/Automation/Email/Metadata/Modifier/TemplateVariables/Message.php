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

use Magento\Cms\Model\Template\FilterProvider;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;
use Aheadworks\Helpdesk2\Model\Source\Email\Variables as EmailVariables;

/**
 * Class Message
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata\Modifier\TemplateVariables
 */
class Message implements ModifierInterface
{
    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * @param FilterProvider $filterProvider
     */
    public function __construct(FilterProvider $filterProvider)
    {
        $this->filterProvider = $filterProvider;
    }

    /**
     * @inheritdoc
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        if (!$eventData->getMessage()) {
            throw new LocalizedException(__('Message must be present in event data'));
        }
        $filter = $this->filterProvider->getPageFilter();
        $message = $eventData->getMessage();
        $message->setContent($filter->filter($message->getContent()));

        $templateVariables = $emailMetadata->getTemplateVariables();
        $templateVariables[EmailVariables::MESSAGE] = $message;
        $emailMetadata->setTemplateVariables($templateVariables);

        return $emailMetadata;
    }
}
