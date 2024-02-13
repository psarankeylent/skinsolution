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
namespace Aheadworks\Helpdesk2\Model\Automation\Action;

/**
 * Class Pool
 *
 * @package Aheadworks\Helpdesk2\Model\Automation\Action
 */
class Pool
{
    /**
     * @var ActionInterface[]
     */
    private $actions;

    /**
     * @param ActionInterface[] $actions
     */
    public function __construct(
        $actions = []
    ) {
        $this->actions = $actions;
    }

    /**
     * Get action handler
     *
     * @param string $action
     * @return ActionInterface
     */
    public function getActionHandler($action)
    {
        if (isset($this->actions[$action])) {
            if (!$this->actions[$action] instanceof ActionInterface) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Action does not implement required interface: %s.',
                        ActionInterface::class
                    )
                );
            }
            return $this->actions[$action];
        }

        throw new \InvalidArgumentException(
            sprintf('Action handler is not found for action: %s.', $action)
        );
    }
}
