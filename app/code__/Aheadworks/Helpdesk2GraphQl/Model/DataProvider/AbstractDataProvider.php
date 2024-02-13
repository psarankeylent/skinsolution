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
namespace Aheadworks\Helpdesk2GraphQl\Model\DataProvider;

use Aheadworks\Helpdesk2GraphQl\Model\ObjectConverter;
use Magento\Framework\Api\SearchResultsInterface;

/**
 * Class AbstractDataProvider
 *
 * @package Aheadworks\Helpdesk2GraphQl\Model\DataProvider
 */
abstract class AbstractDataProvider implements DataProviderInterface
{
    /**
     * @var ObjectConverter
     */
    private $objectConverter;

    /**
     * @param ObjectConverter $objectConverter
     */
    public function __construct(
        ObjectConverter $objectConverter
    ) {
        $this->objectConverter = $objectConverter;
    }

    /**
     * Use class reflection on given data interface to build output data array
     *
     * @param SearchResultsInterface $searchResult
     * @param string $objectType
     */
    protected function convertResultItemsToDataArray($searchResult, string $objectType)
    {
        $itemsAsArray = [];
        foreach ($searchResult->getItems() as $item) {
            $itemsAsArray[] = $this->objectConverter->convertToArray($item, $objectType);
        }

        $searchResult->setItems($itemsAsArray);
    }
}
