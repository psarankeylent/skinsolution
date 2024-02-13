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
namespace Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket\Frontend;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\StoreManagerInterface;
use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\Config;

/**
 * Class Department
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Processor\Post\Ticket\Frontend
 */
class Department implements ProcessorInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        Config $config,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->storeManager = $storeManager;
    }

    /**
     * @inheritdoc
     *
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function prepareEntityData($data)
    {
        $storeId = $data[TicketInterface::STORE_ID] ?? $this->storeManager->getStore()->getId();

        if (!isset($data[TicketInterface::DEPARTMENT_ID])) {
            $data[TicketInterface::DEPARTMENT_ID] = '';
        }

        if (empty($data[TicketInterface::DEPARTMENT_ID])) {
            $primaryDepartment = $this->config->getPrimaryDepartment($storeId);
            if (!$primaryDepartment) {
                throw new LocalizedException(__('Department is not set up for this store'));
            }

            $data[TicketInterface::DEPARTMENT_ID] = $primaryDepartment;
        }

        return $data;
    }
}
