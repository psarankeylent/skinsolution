<?php

declare(strict_types=1);

namespace FreeGift\CartRulesFreeGift\Plugin\Rule\Metadata;

use Magento\Framework\App\RequestInterface;
use Magento\SalesRule\Model\ResourceModel\Rule;

class ValueProviderPlugin
{
    public function afterGetMetadataValues(
        \Magento\SalesRule\Model\Rule\Metadata\ValueProvider $subject,
        $result
    ) {
        $applyOptions = [            
                    'label' => 'Add a Gift',
                    'value' => 'add_gift'
        ];
        
        array_push($result['actions']['children']['simple_action']['arguments']['data']['config']['options'], $applyOptions);
        return $result;
    }

}