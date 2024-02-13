<?php
declare(strict_types=1);

namespace Ssmd\Catalog\Model\Product\Attribute\Source;

class ProductFormat extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = [
            ['label' => __('Cleansers'), 'value' => 0],
            ['label' => __('Toners'), 'value' => 1],
            ['label' => __('Moisturizers'), 'value' => 2],
            ['label' => __('Serums'), 'value' => 3],
            ['label' => __('Brighteners'), 'value' => 4],
            ['label' => __('Sunscreens'), 'value' => 5],
            ['label' => __('Eye Treatments'), 'value' => 6],
            ['label' => __('Body Treatments'), 'value' => 7],
            ['label' => __('Pre & Post Procedure'), 'value' => 8],
            ['label' => __('Kits & Systems'), 'value' => 9],
        ];

        return $this->_options;
    }
}
