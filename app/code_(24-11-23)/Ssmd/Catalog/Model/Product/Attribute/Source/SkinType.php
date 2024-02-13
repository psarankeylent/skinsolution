<?php
declare(strict_types=1);

namespace Ssmd\Catalog\Model\Product\Attribute\Source;

class SkinType extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = [
            ['label' => __('Normal'), 'value' => 0],
            ['label' => __('Oily'), 'value' => 1],
            ['label' => __('Dry'), 'value' => 2],
            ['label' => __('Combination'), 'value' => 3],
            ['label' => __('Sensitive'), 'value' => 4],
        ];
        return $this->_options;
    }
}
