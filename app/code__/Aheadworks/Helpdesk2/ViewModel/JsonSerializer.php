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
namespace Aheadworks\Helpdesk2\ViewModel;

use Magento\Framework\Serialize\Serializer\Json as Serializer;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class JsonSerializer
 *
 * @package Aheadworks\Helpdesk2\ViewModel
 */
class JsonSerializer implements ArgumentInterface
{
    /**
     * @var Serializer
     */
    private $jsonSerializer;

    /**
     * @param Serializer $jsonSerializer
     */
    public function __construct(
        Serializer $jsonSerializer
    ) {
        $this->jsonSerializer = $jsonSerializer;
    }

    /**
     * Get verification result
     *
     * @param mixed $data
     * @return string
     */
    public function serialize($data)
    {
        return $this->jsonSerializer->serialize($data);
    }
}
