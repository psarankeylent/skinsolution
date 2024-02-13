<?php

namespace Backend\InlineEdit\Plugin\Component\Listing;
/**
 * {@inheritdoc}
 */
class ColumnFactory {

    /**
     * @var array
     */
    protected $editorMap = [
        'default' => 'text',
        'select' => 'select',
        'boolean' => 'select',
        'multiselect' => 'select',
        'date' => 'dateRange',
    ];

    /**
     * Add Inline Edit for custom Attributes
     * 
     * @param $subject
     * @param $attribute
     * @param $context
     * @param $config
     */
    public function beforeCreate(\Magento\Catalog\Ui\Component\ColumnFactory $subject, $attribute, $context, array $config = [])
    {
        $editorType = $attribute->getFrontendInput();
        if(isset($this->editorMap[$editorType])){
            $editorType = $this->editorMap[$editorType];
        }

        $config['editor'] = ['editorType'=> $editorType];
        return [$attribute, $context, $config];
    }
}