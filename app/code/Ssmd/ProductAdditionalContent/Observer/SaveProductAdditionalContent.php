<?php
/**
 * Copyright © 2017 BORN . All rights reserved.
 */
namespace Ssmd\ProductAdditionalContent\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;

class SaveProductAdditionalContent implements ObserverInterface
{
    const PRODUCT_ADDITIONALCONTENT_ATTRIBUTE_CODE = 'product_additionalcontent';

    /**
     * @var  \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * Constructor
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request
    )
    {
        $this->request = $request;
    }

    public function execute(Observer $observer)
    {
        /** @var $product \Magento\Catalog\Model\Product */
        //$product = $observer->getEvent()->getDataObject();
        $product = $observer->getEvent()->getData('product');
        $post = $this->request->getPost();
        $post = $post['product'];

        //echo "<pre>"; print_r($post); exit;

        $data = isset($post[self::PRODUCT_ADDITIONALCONTENT_ATTRIBUTE_CODE]) ? $post[self::PRODUCT_ADDITIONALCONTENT_ATTRIBUTE_CODE] : '';
        // $product -> setProductAdditionalContent(json_encode($data));

        // Serialize data code start here
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $postedDataObj = $objectManager->get('Magento\Framework\Serialize\SerializerInterface');


        // Additional Content
        $additionalDataOfContent = $postedDataObj->serialize($data);
        $product->setData('product_additionalcontent', $additionalDataOfContent);


        // echo "<pre>";
        //        //print_r($post);
        //exit;

        // For Products Additional Content
        $requiredParams_qa = ['content_section','content_html'];
        if (is_array($data)) {
            $data = $this->removeEmptyArray($data, $requiredParams_qa);
        }


    }

    /**
    * Function to remove empty array from the multi dimensional array
    *
    * @return Array
    */
    private function removeEmptyArray($attractionData, $requiredParams){

        $requiredParams = array_combine($requiredParams, $requiredParams);
        $reqCount = count($requiredParams);

        foreach ($attractionData as $key => $values) {
            $values = array_filter($values);
            $inersectCount = count(array_intersect_key($values, $requiredParams));
            if ($reqCount != $inersectCount) {
                unset($attractionData[$key]);
            }
        }
        return $attractionData;
    }
}

