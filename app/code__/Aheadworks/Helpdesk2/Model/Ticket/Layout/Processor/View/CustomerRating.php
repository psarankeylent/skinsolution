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
namespace Aheadworks\Helpdesk2\Model\Ticket\Layout\Processor\View;

use Aheadworks\Helpdesk2\Model\Config;
use Aheadworks\Helpdesk2\Model\Source\Ticket\CustomerRating as CustomerRatingSource;
use Aheadworks\Helpdesk2\Model\Ticket\CustomerRating\CanRateChecker;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Ticket\Layout\Renderer\ViewRendererInterface;
use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class CustomerRating
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Layout\Processor\View
 */
class CustomerRating implements ProcessorInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var CanRateChecker
     */
    private $checker;

    /**
     * @var CustomerRatingSource
     */
    private $optionSource;

    /**
     * @param ArrayManager $arrayManager
     * @param Config $config
     * @param CanRateChecker $canRateChecker
     * @param CustomerRatingSource $optionSource
     */
    public function __construct(
        ArrayManager $arrayManager,
        Config $config,
        CanRateChecker $canRateChecker,
        CustomerRatingSource $optionSource
    ) {
        $this->arrayManager = $arrayManager;
        $this->config = $config;
        $this->checker = $canRateChecker;
        $this->optionSource = $optionSource;
    }

    /**
     * Prepare customer rating data
     *
     * @param array $jsLayout
     * @param ViewRendererInterface $renderer
     * @return array
     */
    public function process($jsLayout, $renderer)
    {
        if ($this->config->isEnabledCustomerRating()) {
            $formDataProvider = 'components/aw_helpdesk2_config_provider';
            $jsLayout = $this->arrayManager->merge(
                $formDataProvider,
                $jsLayout,
                [
                    'data' => [
                        'is_disabled_customer_rating' => !$this->checker->canRate($renderer->getTicket()),
                        'customer_rating_options' => $this->optionSource->toOptionArray()
                    ]
                ]
            );

            $formDataProvider = 'components/aw_helpdesk2_form_data_provider';
            $jsLayout = $this->arrayManager->merge(
                $formDataProvider,
                $jsLayout,
                [
                    'data' => [
                        'customer_rating' => $renderer->getTicket()->getCustomerRating()
                    ]
                ]
            );
        } else {
            $ratingComponent = 'components/aw_helpdesk2_form/children/top_panel/children/customer_rating';
            $jsLayout = $this->arrayManager->remove(
                $ratingComponent,
                $jsLayout
            );
        }

        return $jsLayout;
    }
}
