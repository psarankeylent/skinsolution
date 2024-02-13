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
namespace Aheadworks\Helpdesk2\Ui\DataProvider\Ticket;

use Aheadworks\Helpdesk2\Api\Data\TicketInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;
use Aheadworks\Helpdesk2\Model\ResourceModel\Department\Collection;
use Aheadworks\Helpdesk2\Model\ResourceModel\Ticket\CollectionFactory;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\Ticket\DefaultDataOnNewTicket;
use Aheadworks\Helpdesk2\Model\Ticket;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class FormDataProvider
 *
 * @package Aheadworks\Helpdesk2\Ui\DataProvider\Ticket
 */
class FormDataProvider extends AbstractDataProvider
{
    /**
     * Key for saving and getting form data from data persistor
     */
    const DATA_PERSISTOR_FORM_DATA_KEY = 'aw_helpdesk2_ticket';

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var ProcessorInterface
     */
    private $formDataProcessor;

    /**
     * @var DefaultDataOnNewTicket
     */
    private $defaultDataProcessor;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param RequestInterface $request
     * @param ProcessorInterface $formDataProcessor
     * @param DefaultDataOnNewTicket $defaultDataProcessor
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        DataPersistorInterface $dataPersistor,
        RequestInterface $request,
        ProcessorInterface $formDataProcessor,
        DefaultDataOnNewTicket $defaultDataProcessor,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->request = $request;
        $this->formDataProcessor = $formDataProcessor;
        $this->defaultDataProcessor = $defaultDataProcessor;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        $preparedData = [];
        $dataFromForm = $this->dataPersistor->get(self::DATA_PERSISTOR_FORM_DATA_KEY);

        if (!empty($dataFromForm) && (is_array($dataFromForm))) {
            $id = $dataFromForm[TicketInterface::ENTITY_ID] ?? null;
            $this->dataPersistor->clear(self::DATA_PERSISTOR_FORM_DATA_KEY);
            $preparedData[$id] = $dataFromForm;
        } else {
            $id = $this->request->getParam($this->getRequestFieldName());
            if ($id) {
                $ticketList = $this->getCollection()->addFieldToFilter(
                    $this->getPrimaryFieldName(),
                    $id
                )->getItems();
                /** @var Ticket $ticket */
                foreach ($ticketList as $ticket) {
                    if ($id == $ticket->getEntityId()) {
                        $preparedData[$id] = $this->formDataProcessor->prepareEntityData($ticket->getData());
                    }
                }
            } else {
                $preparedData[$id] = $this->defaultDataProcessor->getData();
            }
        }

        return $preparedData;
    }

    /**
     * @inheritdoc
     */
    public function getMeta()
    {
        $meta = parent::getMeta();
        $meta = $this->defaultDataProcessor->getMeta($meta);

        return $meta;
    }
}
