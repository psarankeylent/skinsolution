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
namespace Aheadworks\Bup\Plugin\User\Controller\Adminhtml\User;

use Magento\Backend\Block\Widget\Form;

/**
 * Class EditPlugin
 * @package Aheadworks\Bup\Plugin\User\Controller\Adminhtml\User
 */
class EditPlugin
{
    /**
     * Set enctype attribute to the form
     *
     * @param Form $subject
     */
    public function beforeGetFormHtml(
        Form $subject
    ) {
        $subject->getForm()->setData('enctype', 'multipart/form-data');
    }
}
