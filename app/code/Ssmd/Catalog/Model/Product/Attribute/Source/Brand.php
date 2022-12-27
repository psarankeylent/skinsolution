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
            ['label' => __('-- Please Select a Brand --'), 'value' => ''],
            ['label' => __('Latisse'), 'value' => 0],
            ['label' => __('Upneeq'), 'value' => 1],
            ['label' => __('Obagi'), 'value' => 2],
            ['label' => __('Skinceuticals'), 'value' => 3],
            ['label' => __('Kerastase'), 'value' => 4],
            ['label' => __('L’Oreal Professionnel'), 'value' => 5],
            ['label' => __('Alastin'), 'value' => 6],
            ['label' => __('Skinmedica'), 'value' => 7],
            ['label' => __('Vaniqa'), 'value' => 8],
            ['label' => __('SkinSolutions.MD'), 'value' => 9],

        ];
        return $this->_options;
    }
}
