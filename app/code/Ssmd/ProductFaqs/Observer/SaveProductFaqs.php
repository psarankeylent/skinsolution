<?php
/**
 * Copyright © 2017 BORN . All rights reserved.
 */
namespace Ssmd\Productfaqs\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;

class SaveProductFaqs implements ObserverInterface
{
    const PRODUCT_FAQS_ATTRIBUTE_CODE = 'product_faqs';

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

        $data = isset($post[self::PRODUCT_FAQS_ATTRIBUTE_CODE]) ? $post[self::PRODUCT_FAQS_ATTRIBUTE_CODE] : '';
        // $product -> setProductFaqs(json_encode($data));

        // Serialize data code start here
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $postedDataObj = $objectManager->get('Magento\Framework\Serialize\SerializerInterface');


        // Faqs
        $additionalDataOfFaqs = $postedDataObj->serialize($data);
        $product->setData('product_faqs', $additionalDataOfFaqs);


        // echo "<pre>";
        //        //print_r($post);
        //exit;

        // For Questions & Answers
        $requiredParams_qa = ['prod_questions','prod_answers'];
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

