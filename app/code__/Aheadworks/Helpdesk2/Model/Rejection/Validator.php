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
namespace Aheadworks\Helpdesk2\Model\Rejection;

use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface;
use Aheadworks\Helpdesk2\Model\Rejection\Pattern\Matcher;
use Aheadworks\Helpdesk2\Model\Rejection\Pattern\Search\Builder as PatternSearchBuilder;
use Aheadworks\Helpdesk2\Model\Rejection\Validator\ResultFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Validator
 *
 * @package Aheadworks\Helpdesk2\Model\Rejection
 */
class Validator
{
    /**
     * @var PatternSearchBuilder
     */
    private $patternSearchBuilder;

    /**
     * @var Matcher
     */
    private $matcher;

    /**
     * @var RejectingPatternInterface[]
     */
    private $patterns;

    /**
     * @var ResultFactory
     */
    private $resultFactory;

    /**
     * @param PatternSearchBuilder $patternSearchBuilder
     * @param Matcher $matcher
     * @param ResultFactory $resultFactory
     */
    public function __construct(
        PatternSearchBuilder $patternSearchBuilder,
        Matcher $matcher,
        ResultFactory $resultFactory
    ) {
        $this->patternSearchBuilder = $patternSearchBuilder;
        $this->matcher = $matcher;
        $this->resultFactory = $resultFactory;
    }

    /**
     * Check if rejected
     *
     * @param DataObject $data
     * @return Validator\Result
     * @throws LocalizedException
     */
    public function validate($data)
    {
        $result = $this->resultFactory->create();
        $patterns = $this->getPatterns();
        foreach ($patterns as $pattern) {
            if ($this->matcher->isMatching($pattern, $data)) {
                $result
                    ->setIsRejected(true)
                    ->setPatternId($pattern->getId());
                return $result;
            }
        }

        return $result;
    }

    /**
     * Get patterns
     *
     * @return RejectingPatternInterface[]
     * @throws LocalizedException
     */
    private function getPatterns()
    {
        if ($this->patterns === null) {
            $this->patterns = $this->patternSearchBuilder->addIsActiveFilter()->searchPatterns();
        }

        return $this->patterns;
    }
}
