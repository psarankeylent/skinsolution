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

namespace ParadoxLabs\Subscriptions\Ui\Component\MassAction\Status;

use Magento\Framework\UrlInterface;
use ParadoxLabs\Subscriptions\Model\Source\Status;

/**
 * Options for mass-changing subscription status from grid
 */
class Options implements \JsonSerializable
{
    /**
     * @var Status
     */
    protected $statusSource;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * Additional options params
     *
     * @var array
     */
    protected $data;

    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor
     *
     * @param Status $statusSource
     * @param UrlInterface $urlBuilder
     * @param array $data
     */
    public function __construct(
        Status $statusSource,
        UrlInterface $urlBuilder,
        array $data = []
    ) {
        $this->statusSource = $statusSource;
        $this->urlBuilder   = $urlBuilder;
        $this->data         = $data;
    }

    /**
     * Get action options
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        if ($this->options === null) {
            foreach ($this->statusSource->toOptionArray() as $option) {
                $this->options[ $option['value'] ] = [
                    'type' => 'set_status_' . $option['value'],
                    'label' => __($option['label']),
                    '__disableTmpl' => true,
                    'url' => $this->urlBuilder->getUrl(
                        'subscriptions/index/massStatus',
                        ['status' => $option['value']]
                    ),
                    'confirm' => [
                        'title' => __('Please Confirm'),
                        'message' => __(
                            "Change subscription status to '%1'?",
                            $option['label']
                        ),
                    ],
                ];
            }

            $this->options = array_values($this->options);
        }

        return $this->options;
    }
}
