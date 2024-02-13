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
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket;

use Aheadworks\Helpdesk2\Api\Data\MessageInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Source\Ticket\Message\Type as MessageType;
use Aheadworks\Helpdesk2\Model\Ticket\Message\Author\Resolver;

/**
 * Class AdminTypeMessage
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket
 */
class AdminTypeMessage implements ProcessorInterface
{
    /**
     * @var Resolver
     */
    private $authorResolver;

    /**
     * @param Resolver $authorResolver
     */
    public function __construct(
        Resolver $authorResolver
    ) {
        $this->authorResolver = $authorResolver;
    }

    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        try {
            $author = $this->authorResolver->resolveAgent();
            $data[MessageInterface::TYPE] = MessageType::ADMIN;
            $data[MessageInterface::AUTHOR_NAME] = $data[MessageInterface::AUTHOR_NAME] ?? $author->getName();
            $data[MessageInterface::AUTHOR_EMAIL] = $data[MessageInterface::AUTHOR_EMAIL] ?? $author->getEmail();
        } catch (\Exception $exception) {
            return $data;
        }

        return $data;
    }
}
