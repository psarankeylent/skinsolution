<?php

declare(strict_types=1);

namespace FreeGift\CartRulesFreeGift\Plugin;

use Magento\Framework\App\RequestInterface;
use Magento\SalesRule\Model\ResourceModel\Rule;

class BeforeSaveCartRule
{
    /**
     * @var RequestInterface
     */
    private $request;

    public function __construct(
        RequestInterface $request
    ) {
        $this->request = $request;
    }
    public function beforeSave(Rule $subject, $object) {
        $data = $this->request->getPostValue();

        $giftSku = $data['gift_sku'];
        if($data['gift_sku'] != "" && $data['simple_action'] == 'add_gift')
        {
            $actionArray = $object->getActions()->asArray();
            $actionArray[0] = ['add_gift' => $giftSku];            
        }
        //echo "<pre>"; print_r($object->getActions()->asArray()); exit;

        //$giftSku = $data['simple_action'];
        $object->setAddGift($data['simple_action']);
        $object->setGiftSku($giftSku);

        return null;
    }
}