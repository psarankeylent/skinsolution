<?php
declare(strict_types=1);

namespace Ssmd\Catalog\Model\Product\Attribute\Source;

class Brand extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = [
            ['label' => __('Brand 1'), 'value' => 0],
            ['label' => __('Brand 2'), 'value' => 1],
            ['label' => __('Brand 3'), 'value' => 2],
            ['label' => __('Brand 4'), 'value' => 3],
        ];
        return $this->_options;
    }
}
