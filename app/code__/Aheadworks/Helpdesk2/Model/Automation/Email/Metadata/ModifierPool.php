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
namespace Aheadworks\Helpdesk2\Model\Automation\Email\Metadata;

use Aheadworks\Helpdesk2\Model\Automation\Email\ModifierInterface;

/**
 * Class ModifierPool
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Email\Metadata
 */
class ModifierPool
{
    /**
     * @var ModifierInterface[]
     */
    private $modifierList;

    /**
     * @param ModifierInterface[] $modifierList
     */
    public function __construct(
        array $modifierList = []
    ) {
        $this->modifierList = $modifierList;
    }

    /**
     * Retrieve metadata modifier for specific event
     *
     * @param string $eventName
     * @return ModifierInterface
     */
    public function getModifierForEvent($eventName)
    {
        if (!isset($this->modifierList[$eventName])) {
            throw new \InvalidArgumentException(
                __('Unknown email metadata modifier for event action: %1', $eventName)
            );
        }

        return $this->modifierList[$eventName];
    }
}
