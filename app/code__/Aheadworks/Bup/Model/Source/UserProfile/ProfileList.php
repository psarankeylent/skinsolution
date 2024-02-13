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
namespace Aheadworks\Bup\Model\Source\UserProfile;

use Magento\Framework\Data\OptionSourceInterface;
use Aheadworks\Bup\Model\UserProfile\ListBuilder;

/**
 * Class ProfileList
 *
 * @package Aheadworks\Bup\Model\Source\UserProfile
 */
class ProfileList implements OptionSourceInterface
{
    /**
     * Inactive label
     */
    const INACTIVE_LABEL = '(Inactive)';

    /**
     * @var ListBuilder
     */
    private $listBuilder;

    /**
     * @var array
     */
    private $options;

    /**
     * @param ListBuilder $listBuilder
     */
    public function __construct(
        ListBuilder $listBuilder
    ) {
        $this->listBuilder = $listBuilder;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            $options = [];
            $userProfiles = $this->listBuilder->getProfileList();
            foreach ($userProfiles as $userProfile) {
                $options[] = [
                    'value' => $userProfile->getUserId(),
                    'label' => $userProfile->getStatus()
                        ? $userProfile->getDisplayName()
                        : $userProfile->getDisplayName() . ' ' . __(self::INACTIVE_LABEL)
                ];
            }

            $this->options = $options;
        }

        return $this->options;
    }
}
