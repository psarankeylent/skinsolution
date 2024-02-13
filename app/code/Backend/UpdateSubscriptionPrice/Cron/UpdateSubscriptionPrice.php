<?php

declare(strict_types=1);

namespace Backend\UpdateSubscriptionPrice\Cron;

class UpdateSubscriptionPrice
{

    protected $logger;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \ParadoxLabs\Subscriptions\Model\SubscriptionFactory $subscriptionFactory,
        \Backend\UpdateSubscriptionPrice\Model\UpdateSubscriptionFrequencyPriceFactory $frequencyPriceFactory,
        \Psr\Log\LoggerInterface $logger
    )
    {
        $this->quoteRepository      = $quoteRepository;
        $this->quoteFactory         = $quoteFactory;
        $this->subscriptionFactory  = $subscriptionFactory;
        $this->frequencyPriceFactory  = $frequencyPriceFactory;
        $this->logger               = $logger;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        //$this->logger->addInfo("Cronjob UpdateSubscriptionPrice is executed.");
        $frequencyPriceFactory = $this->frequencyPriceFactory->create()
            ->getCollection()
            ->addFieldToFilter("status", 0)
            ->getFirstItem();

        if($frequencyPriceFactory->getData('id')){
            $frequencyPrice_id  = $frequencyPriceFactory->getData('id');
            $subscription_id    = $frequencyPriceFactory->getData('subscription_id');
            $quote_id           = $frequencyPriceFactory->getData('quote_id');
            $new_price          = $frequencyPriceFactory->getData('new_price');

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $quote = $objectManager->get('Magento\Quote\Api\CartRepositoryInterface')->get($quote_id);
            $items = $quote->getAllItems();

            foreach ($items as $quoteItem){
                $quoteItem->setCustomPrice($new_price);
                $quoteItem->setOriginalCustomPrice($new_price);
                $quoteItem->save();
            }

            $this->quoteRepository->get($quote_id);
            $this->quoteRepository->save($quote->collectTotals());

            $new_subtotal   = array('subtotal' => $new_price);
            $subscriptionFactory = $this->subscriptionFactory->create()->load($subscription_id);
            $subscriptionFactory->addData($new_subtotal);
            $subscriptionFactory->save();

            $new_status   = array('status' => 1);
            $updateFrequencyPriceFactory = $this->frequencyPriceFactory->create()->load($frequencyPrice_id);
            $updateFrequencyPriceFactory->addData($new_status);
            $updateFrequencyPriceFactory->save();

        }

    }
}


