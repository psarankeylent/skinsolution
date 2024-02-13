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

use Aheadworks\Helpdesk2\Api\Data\TicketOptionInterface;
use Aheadworks\Helpdesk2\Api\TicketRepositoryInterface;
use Aheadworks\Helpdesk2\Model\Data\Processor\Form\ProcessorInterface;
use Magento\Framework\Api\Filter;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProviderInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class OptionsDataProvider
 *
 * @package Aheadworks\Helpdesk2\Ui\DataProvider\Ticket
 */
class OptionsDataProvider extends AbstractDataProvider implements DataProviderInterface
{
    /**
     * @var TicketRepositoryInterface
     */
    private $ticketRepository;

    /**
     * @var ProcessorInterface
     */
    private $formDataProcessor;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param RequestInterface $request
     * @param ProcessorInterface $formDataProcessor
     * @param TicketRepositoryInterface $ticketRepository
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        RequestInterface $request,
        ProcessorInterface $formDataProcessor,
        TicketRepositoryInterface $ticketRepository,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->request = $request;
        $this->formDataProcessor = $formDataProcessor;
        $this->ticketRepository = $ticketRepository;
    }

    /**
     * @inheritdoc
     */
    public function getData()
    {
        $preparedData = [];
        try {
            $optionsData = [];
            $ticketId = $this->getTicketId();
            $ticket = $this->ticketRepository->getById($ticketId);

            foreach ($ticket->getOptions() as $option) {
                $optionsData['options'][$this->createOptionId($option)] = $option->getValue();
            }
            $optionsData['empty'] = empty($optionsData['options']);

            $preparedData[$ticketId] = $optionsData;
        } catch (\Exception $exception) {
            $preparedData = [];
        }

        return $this->formDataProcessor->prepareEntityData($preparedData);
    }

    /**
     * @inheritdoc
     */
    public function addFilter(Filter $filter)
    {
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getMeta()
    {
        try {
            $meta = parent::getMeta();
            $ticketId = $data['ticket_id'] = $this->getTicketId();
            $ticket = $this->ticketRepository->getById($ticketId);

            foreach ($ticket->getOptions() as $option) {
                $meta['general']['children'][] = $this->prepareFieldMeta($option);
            }
        } catch (\Exception $exception) {
            $meta = parent::getMeta();
        }

        return $meta;
    }

    /**
     * Retrieve ticket id
     *
     * @return int
     */
    private function getTicketId() {
        return $this->request->getParam($this->getRequestFieldName());
    }

    /**
     * Prepare preview meta
     *
     * @param TicketOptionInterface $option
     * @return array
     */
    private function prepareFieldMeta($option)
    {
        $optionId = $this->createOptionId($option);
        $previewMeta = [
            'attributes' => [
                'class' => \Magento\Ui\Component\Form\Field::class,
                'name' => $optionId,
                'formElement' => 'input'
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => 'field',
                        'formElement' => 'input',
                        'label' => $option->getLabel(),
                        'elementTmpl' => 'ui/form/element/text',
                        'imports' => [
                            'value' => '${ $.provider }:data.options.' . $optionId,
                        ],
                        'dataScope' => 'options.' . $optionId
                    ],
                ],
            ],
        ];

        return $previewMeta;
    }

    /**
     * @param TicketOptionInterface $option
     * @return string
     */
    private function createOptionId($option)
    {
        return 'option_' . $option->getId();
    }
}
