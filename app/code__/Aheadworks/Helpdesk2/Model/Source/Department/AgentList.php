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
namespace Aheadworks\Helpdesk2\Model\Source\Department;

use Magento\Framework\Data\OptionSourceInterface;
use Aheadworks\Bup\Model\Source\UserProfile\Area;
use Aheadworks\Bup\Model\UserProfile;
use Aheadworks\Bup\Model\ResourceModel\UserProfile\CollectionExtended;
use Aheadworks\Bup\Model\ResourceModel\UserProfile\CollectionExtendedFactory as UserProfileCollectionExtendedFactory;

/**
 * Class AgentList
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Department
 */
class AgentList implements OptionSourceInterface
{
    const NOT_ASSIGNED_VALUE = '0';

    /**
     * @var UserProfileCollectionExtendedFactory
     */
    private $userProfileCollectionExtendedFactory;

    /**
     * @var array
     */
    private $options;

    /**
     * Retrieve NOT_ASSIGNED label
     *
     * @return \Magento\Framework\Phrase
     */
    static public function getNotAssignedLabel()
    {
        return __('Unassigned');
    }

    /**
     * @param UserProfileCollectionExtendedFactory $userProfileCollectionExtendedFactory
     */
    public function __construct(
        UserProfileCollectionExtendedFactory $userProfileCollectionExtendedFactory
    ) {
        $this->userProfileCollectionExtendedFactory = $userProfileCollectionExtendedFactory;
    }

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        if ($this->options === null) {
            /** @var CollectionExtended $collection */
            $collection = $this->userProfileCollectionExtendedFactory->create();
            $collection->setUserProfileArea(Area::HELPDESK2);
            $collection->reset();

            $this->applySorting($collection);
            $this->options = $this->getOptions($collection);
        }

        return $this->options;
    }

    /**
     * Get option by option ID
     *
     * @param int $optionId
     * @return array|null
     */
    public function getOptionById($optionId)
    {
        foreach ($this->toOptionArray() as $option) {
            if ($option['value'] == $optionId) {
                return $option;
            }
        }

        return null;
    }

    /**
     * Get option array by IDs
     *
     * @param array $agentIds
     * @return array
     */
    public function getOptionArrayByIds($agentIds)
    {
        /** @var CollectionExtended $collection */
        $collection = $this->userProfileCollectionExtendedFactory->create();
        $collection->setUserProfileArea(Area::HELPDESK2);
        $collection->reset();
        $collection->addFieldToFilter('main_table.user_id', ['in' => $agentIds]);

        $this->applySorting($collection);
        return $this->getOptions($collection);
    }

    /**
     * Apply sorting
     *
     * @param CollectionExtended $collection
     */
    private function applySorting($collection)
    {
        $collection
            ->setOrder('main_table.status', $collection::SORT_ORDER_DESC)
            ->setOrder('main_table.sort_order', $collection::SORT_ORDER_ASC);
    }

    /**
     * Get options
     *
     * @param CollectionExtended $collection
     * @return array
     */
    private function getOptions($collection)
    {
        $options[] = [
            'value' => self::NOT_ASSIGNED_VALUE,
            'label' => self::getNotAssignedLabel()
        ];

        /** @var UserProfile $userProfile */
        foreach ($collection as $userProfile) {
            $label = $userProfile->getDisplayName();
            if (!$userProfile->getStatus()) {
                $label .= __(' (Inactive)');
            }

            $options[] = [
                'value' => $userProfile->getId(),
                'label' => $label,
            ];
        }

        return $options;
    }
}
