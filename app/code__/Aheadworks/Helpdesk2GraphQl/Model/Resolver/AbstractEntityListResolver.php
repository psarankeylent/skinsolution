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
 * @package    Helpdesk2GraphQl
 * @version    1.0.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Helpdesk2GraphQl\Model\Resolver;

use Aheadworks\Helpdesk2GraphQl\Model\DataProvider\DataProviderInterface;
use Aheadworks\Helpdesk2GraphQl\Model\Resolver\Argument\Modifier\ModifierInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SearchResultsInterface;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\Resolver\Argument\SearchCriteria\Builder;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

/**
 * Class AbstractEntityListResolver
 *
 * @package Aheadworks\Helpdesk2GraphQl\Model\Resolver
 */
class AbstractEntityListResolver extends AbstractResolver
{
    /**
     * @var Builder
     */
    private $searchCriteriaBuilder;

    /**
     * @var DataProviderInterface
     */
    private $dataProvider;

    /**
     * @var ModifierInterface
     */
    private $argsModifier;

    /**
     * @param Builder $searchCriteriaBuilder
     * @param DataProviderInterface $dataProvider
     * @param ModifierInterface|null $argsModifier
     */
    public function __construct(
        Builder $searchCriteriaBuilder,
        DataProviderInterface $dataProvider,
        ModifierInterface $argsModifier = null
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->dataProvider = $dataProvider;
        $this->argsModifier = $argsModifier;
    }

    /**
     * {@inheritdoc}
     */
    public function performResolve(
        Field $field,
        $context,
        ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {
        if ($this->argsModifier) {
            $args = $this->argsModifier->modifyArgs($field, $context, $info,$args);
        }

        $searchCriteria = $this->searchCriteriaBuilder->build($field->getName(), $args);
        $searchCriteria->setCurrentPage($args['currentPage']);
        $searchCriteria->setPageSize($args['pageSize']);

        $storeId = isset($args['storeId']) ? $args['storeId'] : null;
        $searchResult = $this->dataProvider->getListData($searchCriteria, $storeId);

        $data = [
            'total_count' => $searchResult->getTotalCount(),
            'items' => $searchResult->getItems(),
            'page_info' => [
                'page_size' => $searchCriteria->getPageSize(),
                'current_page' => $this->resolveCurrentPage($searchCriteria, $searchResult)
            ]
        ];

        return $data;
    }

    /**
     * Validate arguments
     *
     * @param array $args
     * @throws GraphQlInputException
     */
    protected function validateArgs($args)
    {
        if (isset($args['currentPage']) && $args['currentPage'] < 1) {
            throw new GraphQlInputException(__('`currentPage` value must be greater than 0.'));
        }

        if (isset($args['pageSize']) && $args['pageSize'] < 1) {
            throw new GraphQlInputException(__('`pageSize` value must be greater than 0.'));
        }
    }

    /**
     * Resolve current page
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param SearchResultsInterface $searchResult
     * @return GraphQlInputException
     */
    private function resolveCurrentPage($searchCriteria, $searchResult)
    {
        $maxPages = $searchCriteria->getPageSize()
            ? ceil($searchResult->getTotalCount() / $searchCriteria->getPageSize())
            : 0;

        $currentPage = $searchCriteria->getCurrentPage();
        if ($searchCriteria->getCurrentPage() > $maxPages && $searchResult->getTotalCount() > 0) {
            $currentPage = new GraphQlInputException(
                __('currentPage value %1 specified is greater than the number of pages available.', $maxPages)
            );
        }
        return $currentPage;
    }
}
