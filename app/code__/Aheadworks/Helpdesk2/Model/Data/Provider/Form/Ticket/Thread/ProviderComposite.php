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
namespace Aheadworks\Helpdesk2\Model\Data\Provider\Form\Ticket\Thread;

/**
 * Class ProviderInterface
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Provider\Form
 */
class ProviderComposite implements ProviderInterface
{
    /**
     * @var ProviderInterface[]
     */
    private $providers;

    /**
     * @param ProviderInterface[] $providers
     */
    public function __construct(array $providers = [])
    {
        $this->providers = $providers;
    }

    /**
     * Provide data for form
     *
     * @param int $ticketId
     * @return array
     */
    public function getData($ticketId)
    {
        $data = [];
        foreach ($this->providers as $provider) {
            $data = array_merge_recursive($data, $provider->getData($ticketId));
        }

        return $data;
    }
}
