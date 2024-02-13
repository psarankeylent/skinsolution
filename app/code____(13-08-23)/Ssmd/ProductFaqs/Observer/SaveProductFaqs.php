<?php
/**
 * Copyright © 2017 BORN . All rights reserved.
 */
namespace Ssmd\ProductFaqs\Observer;

use Magento\Framework\App\RequestInterface;
use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\SerializerInterface;

class SaveProductFaqs implements ObserverInterface
{
    const PRODUCT_ATTRIBUTECODE = 'product_faqs';

    const ATTRIBUTE_FIELDS = ['question','answer'];

    /**
     * @var  \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var SerializerInterface
     */
    protected $serializer;

    /**
     * Constructor
     */
    public function __construct(
        RequestInterface $request,
        SerializerInterface $serializer
    )
    {
        $this->request = $request;
        $this->serializer = $serializer;
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
            $data = $this->serializer->serialize($data);
        }

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
}

