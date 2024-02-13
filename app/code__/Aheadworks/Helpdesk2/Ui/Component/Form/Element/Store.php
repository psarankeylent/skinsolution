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
namespace Aheadworks\Helpdesk2\Ui\Component\Form\Element;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\Element\Select;

/**
 * Class Store
 *
 * @package Aheadworks\Helpdesk2\Ui\Component\Form\Element
 */
class Store extends Select
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @param ContextInterface $context
     * @param StoreManagerInterface $storeManager
     * @param array|null $options
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        StoreManagerInterface $storeManager,
        $options = null,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $options, $components, $data);
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        $config = $this->getData('config');
        if ($this->storeManager->hasSingleStore()) {
            $config['visible'] = false;
            $this->setData('config', $config);
        }
        parent::prepare();
    }
}
