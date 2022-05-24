<?php
declare(strict_types=1);

namespace Ssmd\Catalog\Model\Product\Attribute\Source;

class SkinConcerns extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = [
            ['label' => __('Skin Concerns 1'), 'value' => 0],
            ['label' => __('Skin Concerns 2'), 'value' => 1],
            ['label' => __('Skin Concerns 3'), 'value' => 2],
            ['label' => __('Skin Concerns 4'), 'value' => 3],
        ];
        return $this->_options;
    }
}
