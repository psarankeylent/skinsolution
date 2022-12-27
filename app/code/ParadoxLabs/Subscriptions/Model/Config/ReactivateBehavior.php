<?php
/**
 * Paradox Labs, Inc.
 * http://www.paradoxlabs.com
 * 717-431-3330
 *
 * Need help? Open a ticket in our support system:
 *  http://support.paradoxlabs.com
 *
 * @author      Ryan Hoerr <info@paradoxlabs.com>
 * @license     http://store.paradoxlabs.com/license.html
 */

namespace ParadoxLabs\Subscriptions\Model\Config;

/**
 * ReactivateBehavior Class
 */
class ReactivateBehavior implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Keep existing: 1 month subscription due Jan 1st, reactivated Jan 15: Bill Jan 15; next run Feb 1
     * Reset schedule: 1 month subscription due Jan 1st, reactivated Jan 15: Bill Jan 15; next run Feb 15
     * Calculate next: 1 month subscription due Jan 1st, reactivated Jan 15: Next run Feb 1; do not bill until then
     */
    const KEEP_EXISTING = 'keep_existing';
    const RESET = 'reset_schedule';
    const CALCULATE_NEXT = 'calculate_next';

    /**
     * @var array
     */
    protected $options;

    /**
     * Return array of options as value-label pairs
     *
     * @return array Format: array(array('value' => '<value>', 'label' => '<label>'), ...)
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $this->options = [
                [
                    'value' => self::KEEP_EXISTING,
                    'label' => __('Keep existing -- Bill Jan 15; next run Feb 1'),
                ],
                [
                    'value' => self::RESET,
                    'label' => __('Reset schedule -- Bill Jan 15; next run Feb 15'),
                ],
                [
                    'value' => self::CALCULATE_NEXT,
                    'label' => __('Recalculate -- Do not bill Jan 15; next run Feb 1'),
                ],
            ];
        }

        return $this->options;
    }
}
