<?php
declare(strict_types=1);

namespace Ssmd\Catalog\Model\Product\Attribute\Source;

class ProductType extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = [
            ['label' => __('bundle'), 'value' => 0],
            ['label' => __('simple'), 'value' => 1],
        ];

        return $this->_options;
    }
}
