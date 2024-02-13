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
namespace Aheadworks\Helpdesk2\Model\Ticket\Detector;

/**
 * Class DetectorComposite
 *
 * @package Aheadworks\Helpdesk2\Model\Ticket\Detector
 */
class DetectorComposite implements DetectorInterface
{
    /**
     * @var DetectorInterface[]
     */
    private $detectors;

    /**
     * @param DetectorInterface[] $detectors
     */
    public function __construct(array $detectors = [])
    {
        $this->detectors = $detectors;
    }

    /**
     * @inheritdoc
     */
    public function detect($dataToDetect)
    {
        foreach ($this->detectors as $detector) {
            $detector->detect($dataToDetect);
        }
    }
}
