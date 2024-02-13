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
namespace Aheadworks\Helpdesk2\Model\Data\Validator\Rejection\Pattern;

use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface;
use Magento\Framework\Validator\AbstractValidator;

/**
 * Class Pattern
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Validator\Rejection\Pattern
 */
class Pattern extends AbstractValidator
{
    /**
     * Check if preg_match pattern have valid syntax
     *
     * @param RejectingPatternInterface $pattern
     * @return bool
     * @throws \Exception
     */
    public function isValid($pattern)
    {
        $this->_clearMessages();
        
        try {
            preg_match($pattern->getPattern(), 'some-string');
        } catch (\Exception $exception) {
            $this->_addMessages([__('Rejecting pattern has incorrect syntax')]);
        }

        return empty($this->getMessages());
    }
}
