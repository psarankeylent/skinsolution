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
            ['label' => __('Prescription Only'), 'value' => 0],
            ['label' => __('Dry and Sensitive Skin'), 'value' => 1],
            ['label' => __('Post/Pre Procedure'), 'value' => 2],
            ['label' => __('Discoloration'), 'value' => 3],
            ['label' => __('Sun Protection'), 'value' => 4],
            ['label' => __('Oily Skin Care'), 'value' => 5],
            ['label' => __('Acne Treatment'), 'value' => 6],
            ['label' => __('Anti-Aging'), 'value' => 7],
            ['label' => __('Cleansers'), 'value' => 8],
        ];
        return $this->_options;
    }
}
