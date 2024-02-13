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
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket\Thread;

use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type as MessageTypeSource;
use Magento\Cms\Model\Template\FilterProvider;

/**
 * Class WysiwygDirectives
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket\Thread
 */
class WysiwygDirectives implements ProcessorInterface
{
    /**
     * @var FilterProvider
     */
    protected $filterProvider;

    /**
     * @param FilterProvider $filterProvider
     */
    public function __construct(
        FilterProvider $filterProvider
    ) {
        $this->filterProvider = $filterProvider;
    }

    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        $filter = $this->filterProvider->getPageFilter();
        foreach ($data['items'] as &$item) {
            if ($item[MessageInterface::TYPE] == MessageTypeSource::ADMIN) {
                $item[MessageInterface::CONTENT] = $filter->filter($item[MessageInterface::CONTENT]);
            }
        }

        return $data;
    }

    /**
     * @inheritdoc
     */
    public function prepareMetaData($meta)
    {
        return $meta;
    }
}
