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
namespace Aheadworks\Helpdesk2\Model\Rejection\Validator;

/**
 * Class Result
 *
 * @package Aheadworks\Helpdesk2\Model\Rejection\Validator
 */
class Result
{
    /**
     * @var bool
     */
    private $isRejected = false;

    /**
     * @var int
     */
    private $patternId;

    /**
     * @return bool
     */
    public function isRejected()
    {
        return $this->isRejected;
    }

    /**
     * @param bool $isRejected
     * @return Result
     */
    public function setIsRejected(bool $isRejected)
    {
        $this->isRejected = $isRejected;
        return $this;
    }

    /**
     * @return int
     */
    public function getPatternId()
    {
        return $this->patternId;
    }

    /**
     * @param int $patternId
     * @return Result
     */
    public function setPatternId(int $patternId)
    {
        $this->patternId = $patternId;
        return $this;
    }
}
