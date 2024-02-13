<?php
declare(strict_types=1);

namespace Ssmd\Catalog\Model\Product\Attribute\Source;

class ApplicationArea extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

    /**
     * getAllOptions
     *
     * @return array
     */
    public function getAllOptions()
    {
        $this->_options = [
            ['label' => __('Face'), 'value' => 0],
            ['label' => __('Body'), 'value' => 1],
            ['label' => __('Eyes'), 'value' => 2],
            ['label' => __('Eyelashes'), 'value' => 3],
            ['label' => __('Eyebrows'), 'value' => 4],
            ['label' => __('Lips'), 'value' => 5],
        ];

        return $this->_options;
    }
}
