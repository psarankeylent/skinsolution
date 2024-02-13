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
namespace Aheadworks\Helpdesk2\Model\Source\Ticket;

use Aheadworks\Helpdesk2\Api\Data\QuickResponseInterface;
use Aheadworks\Helpdesk2\Api\QuickResponseRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrderBuilder;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class QuickResponse
 *
 * @package Aheadworks\Helpdesk2\Model\Source\Ticket
 */
class QuickResponse implements OptionSourceInterface
{
    const EMPTY_VALUE = '';

    /**
     * @var QuickResponseRepositoryInterface
     */
    private $quickResponseRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var SortOrderBuilder
     */
    private $sortOrderBuilder;

    /**
     * @var array
     */
    private $options;

    /**
     * @param QuickResponseRepositoryInterface $quickResponseRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrderBuilder $sortOrderBuilder
     */
    public function __construct(
        QuickResponseRepositoryInterface $quickResponseRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrderBuilder $sortOrderBuilder
    ) {
        $this->quickResponseRepository = $quickResponseRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->sortOrderBuilder = $sortOrderBuilder;
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     */
    public function toOptionArray()
    {
        if (null === $this->options) {
            $this->options[] = [
                'value' => self::EMPTY_VALUE,
                'label' => __('Please select quick response...'),
                'response' => null
            ];

            $this->searchCriteriaBuilder
                ->addFilter(QuickResponseInterface::IS_ACTIVE, true)
                ->addSortOrder(
                    $this->sortOrderBuilder
                        ->setField(QuickResponseInterface::SORT_ORDER)
                        ->setAscendingDirection()
                        ->create()
                );

            $responses = $this->quickResponseRepository->getList($this->searchCriteriaBuilder->create())->getItems();
            foreach ($responses as $response) {
                $this->options[] = [
                    'value' => $response->getId(),
                    'label' => $response->getTitle(),
                    'response' => $response->getCurrentStorefrontLabel()->getContent()
                ];
            }
        }

        return $this->options;
    }
}
