<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Ssmd\ProductAdditionalContent\Helper;

use \Ssmd\ProductAdditionalContent\Model\ResourceModel\ContentSection\CollectionFactory;

class Data extends \Magento\Catalog\Helper\Output
{
    protected $productContentSectionCollectionFactory;

    public function __construct(CollectionFactory $productContentSectionCollectionFactory)
    {
        $this->productContentSectionCollectionFactory = $productContentSectionCollectionFactory;
    }

    public function getProductContentSectionOptions()
    {
        $items = $this->productContentSectionCollectionFactory->create();

        $contentSection = [];

        $contentSection[] = [
            'value' => '',
            'label' => '-- Select --'
        ];

        foreach ($items as $item) {
            $contentSection[] = [
                'value' => $item->getContentSection(), //$item->getId(),
                'label' => $item->getContentSection()
            ];
        }

        $contentSection[] = [
            'value' => 'add-new',
            'label' => '-- Add New Section --'
        ];

        return $contentSection;
    }
}
