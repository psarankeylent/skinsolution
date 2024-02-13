<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Helpdesk2
 * @version    2.0.6
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Helpdesk2\Model\Department\Option\Config;

use Magento\Framework\Config\ConverterInterface;

/**
 * Class Converter
 *
 * @package Aheadworks\Helpdesk2\Model\Department\Option\Config
 */
class Converter implements ConverterInterface
{
    /**
     * @inheritdoc
     */
    public function convert($source)
    {
        $output = [];

        /** @var $optionNode \DOMNode */
        foreach ($source->getElementsByTagName('option') as $optionNode) {
            $optionName = $this->getAttributeValue($optionNode, 'name');
            $data = [];
            $data['name'] = $optionName;
            $data['label'] = $this->getAttributeValue($optionNode, 'label');

            /** @var $childNode \DOMNode */
            foreach ($optionNode->childNodes as $childNode) {
                if ($childNode->nodeType != XML_ELEMENT_NODE) {
                    continue;
                }
                $inputTypeName = $this->getAttributeValue($childNode, 'name');
                $data['types'][$inputTypeName] = [
                    'name' => $inputTypeName,
                    'label' => $this->getAttributeValue($childNode, 'label')
                ];
            }
            $output[$optionName] = $data;
        }

        return $output;
    }

    /**
     * Get attribute value
     *
     * @param \DOMNode $node
     * @param string $attributeName
     * @param string|null $defaultValue
     * @return null|string
     */
    private function getAttributeValue(\DOMNode $node, $attributeName, $defaultValue = null)
    {
        $attributeNode = $node->attributes->getNamedItem($attributeName);
        $output = $defaultValue;
        if ($attributeNode) {
            $output = $attributeNode->nodeValue;
        }

        return $output;
    }
}
