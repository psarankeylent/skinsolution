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
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post\Automation;

use Magento\Store\Model\Store as MagentoStore;
use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Source\Automation\Action;

/**
 * Class StoreConfig
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Post\Automation
 */
class StoreConfig implements ProcessorInterface
{
    /**
     * @inheritdoc
     */
    public function prepareEntityData($data)
    {
        if (isset($data[AutomationInterface::ACTIONS]) && is_array($data[AutomationInterface::ACTIONS])) {
            foreach ($data[AutomationInterface::ACTIONS] as &$action) {
                if (isset($action['config']) && is_array($action['config'])) {
                    $action['config'] = $this->checkActionConfig($action['config']);
                }
            }
        }

        return $data;
    }

    /**
     * Check action config
     *
     * @param array $configData
     * @return array
     */
    private function checkActionConfig($configData)
    {
        foreach ($configData as $configType => &$config) {
            if ($configType == Action::STORE_IDS_TO_SEND_EMAIL) {
                if (in_array(MagentoStore::DEFAULT_STORE_ID, $config)) {
                    $config = [(string)MagentoStore::DEFAULT_STORE_ID];
                }
            }
        }

        return $configData;
    }
}
