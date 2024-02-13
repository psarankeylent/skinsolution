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
namespace Aheadworks\Helpdesk2\Model\Data\Command\Rejection\Pattern;

use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Api\RejectingPatternRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\RejectingPatternInterface as PatternInterface;

/**
 * Class Delete
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Rejection\Pattern
 */
class Delete implements CommandInterface
{
    /**
     * @var RejectingPatternRepositoryInterface
     */
    private $patternRepository;

    /**
     * @param RejectingPatternRepositoryInterface $patternRepository
     */
    public function __construct(
        RejectingPatternRepositoryInterface $patternRepository
    ) {
        $this->patternRepository = $patternRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute($data)
    {
        if (!isset($data[PatternInterface::ID])) {
            throw new \InvalidArgumentException(
                'Pattern ID param is required to remove pattern'
            );
        }

        $this->patternRepository->deleteById($data[PatternInterface::ID]);
        return true;
    }
}
