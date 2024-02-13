<?php
/**
 * Copyright © 2017 BORN . All rights reserved.
 */
namespace Ssmd\ProductTopContent\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use \Magento\Framework\Serialize\SerializerInterface;
use \Magento\Framework\App\RequestInterface;
use \Ssmd\ProductTopContent\Model\ContentSectionFactory;

class SaveProductTopContent implements ObserverInterface
{
    const PRODUCT_ATTRIBUTECODE = 'product_topcontent';

    const ATTRIBUTE_FIELDS = ['content_section','content_html'];

    /**
     * @var  \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    protected $productContentSectionFactory;
    /**
     * Constructor
     */
    public function __construct(
        RequestInterface $request,
        SerializerInterface $serializer,
        ContentSectionFactory $productContentSectionFactory
    )
    {
        $this->request = $request;
        $this->serializer = $serializer;
        $this->productContentSectionFactory = $productContentSectionFactory;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $product = $observer->getEvent()->getData('product');
        $post = $this->request->getPost('product');

        $data = $post[self::PRODUCT_ATTRIBUTECODE] ?? [];

        if (is_array($data) && !empty($data)) {
            $data = $this->removeEmptyArray($data, self::ATTRIBUTE_FIELDS);
            $data = $this->setImageProperties($data);
        }

        $data = $this->serializer->serialize($data);

        $product->setData(self::PRODUCT_ATTRIBUTECODE, $data);
    }

    /**
     * Filters empty Rows
     *
     * @param $data
     * @param $fields
     * @return array
     */
    private function removeEmptyArray($data, $fields)
    {
        $fields = array_flip($fields);
        $filterData = [];
        $recordId = 0;

        foreach ($data as $key => $values) {
            $values = array_filter($values);
            $fieldsSetCount = count(array_intersect_key($values, $fields));
            if ($fieldsSetCount > 0) {
                $data[$key]['record_id'] = $recordId++;
                $filterData[] = $data[$key];
            }
        }
        return $filterData;
    }

    /**
     * @param $data
     * @return array
     */
    protected function setImageProperties($data)
    {
        $imageProps = ['name', 'url'];

        foreach ($data as $key => $values) {
            if (isset($data[$key]['content_section'][0]))
            $data[$key]['content_section'][0] = array_intersect_key($data[$key]['content_section'][0], array_flip($imageProps));

        }

        return $data;
    }
}

