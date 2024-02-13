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
namespace Aheadworks\Helpdesk2\Model\Data\Command\Automation;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Api\DataObjectHelper;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Api\AutomationRepositoryInterface;
use Aheadworks\Helpdesk2\Api\Data\AutomationInterface;
use Aheadworks\Helpdesk2\Api\Data\AutomationInterfaceFactory;

/**
 * Class Save
 *
 * @package Aheadworks\Helpdesk2\Model\Data\Command\Automation
 */
class Save implements CommandInterface
{
    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var AutomationRepositoryInterface
     */
    private $automationRepository;

    /**
     * @var AutomationInterfaceFactory
     */
    private $automationFactory;

    /**
     * @param DataObjectHelper $dataObjectHelper
     * @param AutomationRepositoryInterface $automationRepository
     * @param AutomationInterfaceFactory $automationFactory
     */
    public function __construct(
        DataObjectHelper $dataObjectHelper,
        AutomationRepositoryInterface $automationRepository,
        AutomationInterfaceFactory $automationFactory
    ) {
        $this->dataObjectHelper = $dataObjectHelper;
        $this->automationRepository = $automationRepository;
        $this->automationFactory = $automationFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute($automationData)
    {
        $automation = $this->getAutomationObject($automationData);
        $this->dataObjectHelper->populateWithArray(
            $automation,
            $automationData,
            AutomationInterface::class
        );

        return $this->automationRepository->save($automation);
    }

    /**
     * Get automation object
     *
     * @param array $automationData
     * @return AutomationInterface
     * @throws NoSuchEntityException
     */
    private function getAutomationObject($automationData)
    {
        return isset($gatewayData[AutomationInterface::ID])
            ? $this->automationRepository->get($automationData[AutomationInterface::ID])
            : $this->automationFactory->create();
    }
}
