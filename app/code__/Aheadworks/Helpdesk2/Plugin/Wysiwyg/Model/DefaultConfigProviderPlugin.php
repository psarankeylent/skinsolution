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
namespace Aheadworks\Helpdesk2\Plugin\Wysiwyg\Model;

use Magento\Cms\Model\Wysiwyg\DefaultConfigProvider;
use Magento\Framework\DataObject;

/**
 * Class DefaultConfigProviderPlugin
 *
 * @package Aheadworks\Helpdesk2\Plugin\Wysiwyg\Model
 */
class DefaultConfigProviderPlugin
{
    const TINY_FIELD = 'tinymce4';

    /**
     * Modify tiny config provider behaviour. Implement value setting based on merge.
     *
     * @param DefaultConfigProvider $subject
     * @param callable $proceed
     * @param DataObject $config
     * @return DataObject
     */
    public function aroundGetConfig(DefaultConfigProvider $subject, callable $proceed, DataObject $config)
    {
        $configFromXml = $this->getData($config);
        $config = $proceed($config);
        $defaultConfig = $this->getData($config);

        if (!empty($configFromXml)) {
            $mergedConfig = array_replace_recursive($defaultConfig, $configFromXml);
            $config->setData(self::TINY_FIELD, $mergedConfig);
        }

        return $config;
    }

    /**
     * Retrieve tinyMce config
     *
     * @param DataObject $config
     * @return array|mixed
     */
    private function getData(DataObject $config)
    {
        return $config->hasData(self::TINY_FIELD)
            ? $config->getData(self::TINY_FIELD)
            : [];
    }
}
