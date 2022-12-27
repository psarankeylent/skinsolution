<?php

namespace Ssmd\Catalog\Model\Plugin\Product;

use Closure;

class ValidationRules
{

    /**
     * @param \Magento\Catalog\Ui\DataProvider\CatalogEavValidationRules $rulesObject
     * @param callable $proceed
     * @param \Magento\Catalog\Api\Data\ProductAttributeInterface $attribute,
     * @param array $data
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundBuild(
        \Magento\Catalog\Ui\DataProvider\CatalogEavValidationRules $rulesObject,
        Closure $proceed,
        \Magento\Catalog\Api\Data\ProductAttributeInterface $attribute,
        array $data
    ){
        $rules = $proceed($attribute,$data);
        if($attribute->getAttributeCode() == 'alle_points'){ //custom filter
            $validationClasses = explode(' ', $attribute->getFrontendClass());
            foreach ($validationClasses as $class) {
                $rules[$class] = true;
            }
        }
        return $rules;
    }
}
