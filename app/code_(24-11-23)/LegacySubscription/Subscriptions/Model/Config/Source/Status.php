<?php

namespace LegacySubscription\Subscriptions\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Status implements ArrayInterface
{
    protected $customerSubscriptionFactory;
    protected $request;

    public function __construct(
        \LegacySubscription\Subscriptions\Model\CustomerSubscriptionFactory $customerSubscriptionFactory,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->customerSubscriptionFactory = $customerSubscriptionFactory;
        $this->request = $request;
    }

    public function toOptionArray()
    {
        $result = [];
        foreach ($this->getOptions() as $value => $label) {
            $result[] = [
                 'value' => $value,
                 'label' => $label,
             ];
        }

        return $result;
    }

    public function getOptions()
    {
        $collection = $this->customerSubscriptionFactory->create()->getCollection();
        $collection->addFieldToFilter('id', $this->request->getParam('id'));
        //echo "<pre>"; print_r($collection->getData());

        $statuses = [];
        foreach ($collection as $key => $value) {
            $status = $value->getStatus();
            if($status == 'active')
            {
                $statuses = [
                    'active' => __('Active'),
                    'cancelled' => __('Cancel')
                ];
            }
            else
            {
                $statuses = [
                    $status => __(ucfirst($status)),
                ];
            }
        }
    
        return $statuses;
    }

    
}
