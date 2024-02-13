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
namespace Aheadworks\Helpdesk2\Controller\Adminhtml\QuickResponse;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Backend\App\Action\Context;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect as ResultRedirect;
use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface;
use Aheadworks\Helpdesk2\Model\Data\CommandInterface;
use Aheadworks\Helpdesk2\Ui\DataProvider\QuickResponse\FormDataProvider;

/**
 * Class Save
 *
 * @package Aheadworks\Helpdesk2\Controller\Adminhtml\QuickResponse
 */
class Save extends Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Aheadworks_Helpdesk2::quick_responses';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var CommandInterface
     */
    private $saveCommand;

    /**
     * @param Context $context
     * @param DataPersistorInterface $dataPersistor
     * @param CommandInterface $saveCommand
     */
    public function __construct(
        Context $context,
        DataPersistorInterface $dataPersistor,
        CommandInterface $saveCommand
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->saveCommand = $saveCommand;
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
                /** @var QuickResponseInterface $quickResponse */
                $quickResponse = $this->saveCommand->execute($data);
                $this->dataPersistor->clear(FormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY);
                $this->messageManager->addSuccessMessage(__('Quick response was successfully saved'));

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [QuickResponseInterface::ID => $quickResponse->getId()]
                    );
                }
                return $resultRedirect->setPath('*/*/');
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving quick response'));
            }

            $this->dataPersistor->set(FormDataProvider::DATA_PERSISTOR_FORM_DATA_KEY, $data);
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
            '_current' => true,
        ];
        $quickResponseId = $data[QuickResponseInterface::ID] ?? null;
        if ($quickResponseId) {
            $path = '*/*/edit';
            $params[QuickResponseInterface::ID] = $quickResponseId;
        }
        return $resultRedirect->setPath($path, $params);
    }
}
