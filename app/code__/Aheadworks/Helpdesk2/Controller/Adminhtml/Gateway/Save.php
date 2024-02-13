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
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\Gateway;

use Aheadworks\Helpdesk2\Api\Data\DepartmentInterface;
use Aheadworks\Helpdesk2\Model\Source\Gateway\Type as GatewayTypeSource;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect as ResultRedirect;
use Aheadworks\Helpdesk2\Api\Data\GatewayDataInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Ui\DataProvider\Gateway\FormDataProvider as GatewayFormDataProvider;
use Aheadworks\Helpdesk2\Model\Data\Processor\Post\ProcessorInterface;

/**
 * Class Save
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\Gateway
 */
class Save extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::gateways';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var CommandInterface
     */
    private $saveCommand;

    /**
     * @var ProcessorInterface
     */
    private $postDataProcessor;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param CommandInterface $saveCommand
     * @param ProcessorInterface $postDataProcessor
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        CommandInterface $saveCommand,
        ProcessorInterface $postDataProcessor
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->saveCommand = $saveCommand;
        $this->postDataProcessor = $postDataProcessor;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        /** @var ResultRedirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data = $this->getRequest()->getPostValue()) {
            try {
                $gatewayData = $this->postDataProcessor->prepareEntityData($data);
                /** @var GatewayDataInterface $gateway */
                $gateway = $this->saveCommand->execute($gatewayData);
                $this->dataPersistor->clear(GatewayFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY);
                $this->messageManager->addSuccessMessage(__('Gateway was successfully saved'));

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [GatewayDataInterface::ID => $gateway->getId()]
                    );
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the gateway'));
            }

            $this->dataPersistor->set(GatewayFormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY, $data);
            return $this->getPreparedRedirectOnError($resultRedirect, $data);
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Retrieve prepared redirect to the entity form on error
     *
     * @param ResultRedirect $resultRedirect
     * @param array $data
     * @return ResultRedirect
     */
    private function getPreparedRedirectOnError($resultRedirect, $data)
    {
        $path = '*/*/new';
        $params = [
            GatewayDataInterface::TYPE => $data[GatewayDataInterface::TYPE] ?? GatewayTypeSource::DEFAULT_TYPE,
            '_current' => true,
        ];
        $gatewayId = $data[GatewayDataInterface::ID] ?? null;
        if ($gatewayId) {
            $path = '*/*/edit';
            $params[GatewayDataInterface::ID] = $gatewayId;
        }
        return $resultRedirect->setPath($path, $params);
    }
}
