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
namespace Aheadworks\Helpdesk2\Model\StorefrontLabel;

use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\Store;
use Aheadworks\Helpdesk2\Api\Data\StorefrontLabelInterface;

/**
 * Class Resolver
 *
 * @package Aheadworks\Helpdesk2\Model\StorefrontLabel
 */
class Resolver
{
    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @var Converter
     */
    private $converter;

    /**
     * @param DataObjectProcessor $dataObjectProcessor
     * @param Converter $converter
     */
    public function __construct(
        DataObjectProcessor $dataObjectProcessor,
        Converter $converter
    ) {
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->converter = $converter;
    }

    /**
     * Retrieve label on storefront for specific store view
     *
     * @param StorefrontLabelInterface[]|array $labelsData
     * @param int|null $storeId
     * @return StorefrontLabelInterface
     */
    public function getLabelsForStore($labelsData, $storeId)
    {
        $labelRecordForStore = null;
        foreach ($labelsData as $labelsDataRow) {
            $labelsRecord = $this->converter->convertToDataObject($labelsDataRow);
            if ($labelsRecord->getStoreId() == Store::DEFAULT_STORE_ID
                && (!isset($storeId) || !isset($labelRecordForStore))
            ) {
                $labelRecordForStore = $labelsRecord;
            }
            if (isset($storeId) && $labelsRecord->getStoreId() == $storeId) {
                $labelRecordForStore = $labelsRecord;
            }
        }

        return $labelRecordForStore;
    }

    /**
     * Retrieve labels on storefront for specific store view as array
     *
     * @param array $labelsData
     * @param int|null $storeId
     * @return array
     */
    public function getLabelsForStoreAsArray($labelsData, $storeId)
    {
        $storefrontLabel = $this->getLabelsForStore($labelsData, $storeId);
        return $this->dataObjectProcessor->buildOutputDataArray(
            $storefrontLabel,
            StorefrontLabelInterface::class
        );
    }
}
