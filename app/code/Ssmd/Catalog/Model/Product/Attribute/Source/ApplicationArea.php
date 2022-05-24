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
            ['label' => __('Application Area 1'), 'value' => 0],
            ['label' => __('Application Area 2'), 'value' => 1],
            ['label' => __('Application Area 3'), 'value' => 2],
            ['label' => __('Application Area 4'), 'value' => 3],
        ];

        return $this->_options;
    }
}
