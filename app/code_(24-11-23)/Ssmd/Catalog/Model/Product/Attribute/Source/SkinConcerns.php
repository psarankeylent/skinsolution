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
            ['label' => __('Anti-Aging'), 'value' => 0],
            ['label' => __('Acne'), 'value' => 1],
            ['label' => __('Moisturizing'), 'value' => 2],
            ['label' => __('Brightening'), 'value' => 3],
            ['label' => __('Tightening'), 'value' => 4],
            ['label' => __('Texture & Tone'), 'value' => 5],
            ['label' => __('Sun Protection'), 'value' => 6],
            ['label' => __('RX'), 'value' => 7],
            ['label' => __('Tired Eyes'), 'value' => 8],
            ['label' => __('Eyelash Growth'), 'value' => 9],
            ['label' => __('Eyebrow Growth'), 'value' => 10],
        ];
        return $this->_options;
    }
}
