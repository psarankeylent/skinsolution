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
 * @package    Helpdesk2GraphQl
 * @version    1.0.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Helpdesk2GraphQl\Model\Resolver\Argument\Filter;

use Magento\Framework\GraphQl\Query\Resolver\Argument\FieldEntityAttributesInterface;
use Magento\Framework\GraphQl\ConfigInterface;
use Magento\Framework\GraphQl\Config\Element\Type;

/**
 * Class EntityAttributesForAst
 *
 * @package Aheadworks\Helpdesk2GraphQl\Model\Resolver\Argument\Filter
 */
class EntityAttributesForAst implements FieldEntityAttributesInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var string
     */
    private $entityElementName;

    /**
     * @var array
     */
    private $additionalAttributes = [];

    /**
     * @param ConfigInterface $config
     * @param string $entityElementName
     * @param array $additionalAttributes
     */
    public function __construct(
        ConfigInterface $config,
        $entityElementName = null,
        array $additionalAttributes = []
    ) {
        $this->config = $config;
        $this->entityElementName = $entityElementName;
        $this->additionalAttributes = array_merge($this->additionalAttributes, $additionalAttributes);
    }

    /**
     * {@inheritdoc}
     */
    public function getEntityAttributes() : array
    {
        $entityTypeSchema = $this->config->getConfigElement($this->entityElementName);
        if (!$entityTypeSchema instanceof Type) {
            throw new \LogicException(__('%1 type not defined in schema.', $this->entityElementName));
        }

        $fields = [];
        foreach ($entityTypeSchema->getFields() as $field) {
            $fields[$field->getName()] = 'String';
        }

        foreach ($this->additionalAttributes as $attribute) {
            $fields[$attribute] = 'String';
        }

        return $fields;
    }
}
