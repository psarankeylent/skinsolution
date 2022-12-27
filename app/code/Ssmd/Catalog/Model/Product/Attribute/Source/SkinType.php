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
            ['label' => __('Skin Type 1'), 'value' => 0],
            ['label' => __('Skin Type 2'), 'value' => 1],
            ['label' => __('Skin Type 3'), 'value' => 2],
            ['label' => __('Skin Type 4'), 'value' => 3],
        ];
        return $this->_options;
    }
}
