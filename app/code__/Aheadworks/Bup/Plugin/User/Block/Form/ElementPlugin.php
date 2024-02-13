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
 * @package    Bup
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Bup\Plugin\User\Block\Form;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class ElementPlugin
 * @package Aheadworks\Bup\Plugin\User\Block\Form
 */
class ElementPlugin
{
    /**
     * Add validation class for element
     *
     * @param AbstractElement $element
     */
    public function beforeGetHtmlAttributes(
        AbstractElement $element
    ) {
        if ($element->getHtmlId() == 'aw_bup_image_loader') {
            $element->addClass('aw_bup-validate-image');
        }
    }
}
