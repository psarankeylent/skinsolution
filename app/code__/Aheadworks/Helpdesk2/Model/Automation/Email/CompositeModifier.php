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

/**
 * Class CompositeModifier
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email
 */
class CompositeModifier implements ModifierInterface
{
    /**
     * @var ModifierInterface[]
     */
    private $modifierList;

    /**
     * @param ModifierInterface[] $modifierList
     */
    public function __construct(array $modifierList = [])
    {
        $this->modifierList = $modifierList;
    }

    /**
     * @inheritdoc
     */
    public function addMetadata($emailMetadata, $eventData)
    {
        foreach ($this->modifierList as $modifier) {
            if (!$modifier instanceof ModifierInterface) {
                throw new \InvalidArgumentException(
                    __('Email meta data modifier must implement %1', ModifierInterface::class)
                );
            }
            $emailMetadata = $modifier->addMetadata($emailMetadata, $eventData);
        }

        return $emailMetadata;
    }
}
