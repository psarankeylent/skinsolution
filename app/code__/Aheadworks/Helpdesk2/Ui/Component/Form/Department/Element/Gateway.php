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
namespace Aheadworks\Helpdesk2\Ui\Component\Form\Department\Element;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Form\Field;
use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Api\DepartmentRepositoryInterface;
use Aheadworks\Helpdesk2\Api\GatewayRepositoryInterface;

/**
 * Class Gateway
 *
 * @package Aheadworks\Helpdesk2\Ui\Component\Form\Department\Element
 */
class Gateway extends Field
{
    /**
     * @var DepartmentRepositoryInterface
     */
    private $departmentRepository;

    /**
     * @var GatewayRepositoryInterface
     */
    private $gatewayRepository;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param DepartmentRepositoryInterface $departmentRepository
     * @param GatewayRepositoryInterface $gatewayRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        DepartmentRepositoryInterface $departmentRepository,
        GatewayRepositoryInterface $gatewayRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->departmentRepository = $departmentRepository;
        $this->gatewayRepository = $gatewayRepository;
    }

    /**
     * @inheritdoc
     */
    public function prepare()
    {
        parent::prepare();

        $gateway = $this->getCurrentGateway();
        if ($gateway) {
            $config = $this->getData('config');
            $options = &$config['options'];

            $currentGatewayOption = [
                'value' => (string)$gateway->getId(),
                'label' => $gateway->getName()
            ];
            array_splice($options, 1, 0, [$currentGatewayOption]);
            $this->setData('config', $config);
        }
    }

    /**
     * Get current gateway
     *
     * @return GatewayDataInterface|null
     * @throws NoSuchEntityException
     */
    private function getCurrentGateway()
    {
        $gateway = null;
        $departmentId = $this->getContext()->getRequestParam(DepartmentInterface::ID);
        try {
            $department = $this->departmentRepository->get($departmentId);
            $gatewayIds = $department->getGatewayIds();
        } catch (NoSuchEntityException $exception) {
            $gatewayIds = [];
        }

        if (!empty($gatewayIds)) {
            $gatewayId = reset($gatewayIds);
            $gateway = $this->gatewayRepository->get($gatewayId);
        }

        return $gateway;
    }
}
