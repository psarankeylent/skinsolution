<?php

namespace Ssmd\Subscriptions\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;

// Email notification
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;

/**
 * Soft dependency: Supporting 2.3 GraphQL without breaking <2.3 compatibility.
 * 2.3+ implements \Magento\Framework\GraphQL; lower does not.
 */
if (!interface_exists('\ParadoxLabs\TokenBase\Model\Api\GraphQL\ResolverInterface')) {
    if (interface_exists('\Magento\Framework\GraphQl\Query\ResolverInterface')) {
        class_alias(
            '\Magento\Framework\GraphQl\Query\ResolverInterface',
            '\ParadoxLabs\TokenBase\Model\Api\GraphQL\ResolverInterface'
        );
    } else {
        class_alias(
            '\ParadoxLabs\TokenBase\Model\Api\GraphQL\FauxResolverInterface',
            '\ParadoxLabs\TokenBase\Model\Api\GraphQL\ResolverInterface'
        );
    }
}

/**
 * Change Frequency Class
 */
class ChangeFrequency implements \ParadoxLabs\TokenBase\Model\Api\GraphQL\ResolverInterface
{
    /**
     * @var \ParadoxLabs\Subscriptions\Model\Api\GraphQL
     */
    protected $graphQL;

    /**
     * @var \ParadoxLabs\Subscriptions\Api\CustomerSubscriptionRepositoryInterface
     */
    protected $customerSubscriptionRepository;

    /**
     * @var \ParadoxLabs\Subscriptions\Model\Config
     */
    protected $config;
    protected $subscriptionRepository;

    /**
     * ChangeStatus constructor.
     *
     * @param \ParadoxLabs\Subscriptions\Model\Api\GraphQL $graphQL
     * @param \ParadoxLabs\Subscriptions\Api\CustomerSubscriptionRepositoryInterface $customerSubscriptionRepository
     * @param \ParadoxLabs\Subscriptions\Model\Config $config
     * @param \ParadoxLabs\Subscriptions\Model\Source\Status $statusSource
     */
    public function __construct(
        \ParadoxLabs\Subscriptions\Model\Api\GraphQL $graphQL,
        \ParadoxLabs\Subscriptions\Api\CustomerSubscriptionRepositoryInterface $customerSubscriptionRepository,
        \ParadoxLabs\Subscriptions\Api\SubscriptionRepositoryInterface $subscriptionRepository,
        \ParadoxLabs\Subscriptions\Model\Config $config,
        \ParadoxLabs\Subscriptions\Model\Source\Status $statusSource,
        \ParadoxLabs\Subscriptions\Model\Service\ItemManager $itemManager = null,
        ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        StateInterface $state,
        StoreManagerInterface $storeManager,
        \CreditCard\Expiring\Model\CreditCardExpiringModelFactory $creditCardExpiringModelFactory,
        \ParadoxLabs\TokenBase\Model\CardFactory $cardFactory,
        \Magento\Email\Model\TemplateFactory $templateFactory
    ) {
        $this->graphQL = $graphQL;
        $this->customerSubscriptionRepository = $customerSubscriptionRepository;
        $this->subscriptionRepository = $subscriptionRepository;
        $this->config = $config;
        $this->statusSource = $statusSource;
        $this->itemManager = $itemManager ?: \Magento\Framework\App\ObjectManager::getInstance()->get(
            \ParadoxLabs\Subscriptions\Model\Service\ItemManager::class
        );
        $this->scopeConfig = $scopeConfig;
        $this->inlineTranslation = $state;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->creditCardExpiringModelFactory = $creditCardExpiringModelFactory;
        $this->cardFactory = $cardFactory;
        $this->templateFactory = $templateFactory;
    }

    /**
     * Fetches the data from persistence models and format it according to the GraphQL schema.
     *
     * @param \Magento\Framework\GraphQl\Config\Element\Field $field
     * @param \Magento\Framework\GraphQl\Query\Resolver\ContextInterface $context
     * @param \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info
     * @param array|null $value
     * @param array|null $args
     * @throws \Exception
     * @return mixed|\Magento\Framework\GraphQl\Query\Resolver\Value
     */
    public function resolve(
        \Magento\Framework\GraphQl\Config\Element\Field $field,
                                                        $context,
        \Magento\Framework\GraphQl\Schema\Type\ResolveInfo $info,
        array $value = null,
        array $args = null
    ) {

        $this->graphQL->authenticate($context);

        try{

            $subscription = $this->subscriptionRepository->load($args['id']);
            // code added by hitesh for sending email - 04 dec 2023
            $oldFrequency   = $subscription->getData('frequency_count');
            $oldFrequencyUnit = $subscription->getData('frequency_unit');
            // code end 

            //  =============== Changed code(5-06-23) Start ===================

            if (!empty($args['interval']) && $args['interval'] !== '0') {
                $this->updateInterval($subscription, (int)$args['interval']);

                // ========= Change frequency Subscription Notification Email Start =========//
                $subscriptionEmail = $this->sendChangeFrequencyEmail($subscription, $oldFrequency, $oldFrequencyUnit, $args['interval']);
                // ===================== Subscription Notification Email End ======================================= //
            }

            //  =============== Changed code(5-06-23) End ===================

            return ['SubscriptionObj' => $subscription->toArray()];

        }catch(Exception $e){
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/graphQL_test.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("Error ". $e->getMessage());
        }
    }


    /**
     * Update subscription frequency/pricing based on the given interval, if valid and changed
     *
     * @param \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription
     * @param int $optionValueId
     * @return void
     */
    protected function updateInterval(
        \ParadoxLabs\Subscriptions\Api\Data\SubscriptionInterface $subscription,
        int $optionValueId
    ) {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $subscription->getQuote();
        $item  = current($quote->getAllVisibleItems());

        $this->itemManager->updateInterval($subscription, $item, $optionValueId);

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote        = $subscription->getQuote();

        $subscription->addRelatedObject($quote, true);
        $this->subscriptionRepository->save($subscription);

    }


    public function sendChangeFrequencyEmail($subscription, $oldFrequency, $oldFrequencyUnit, $optionValueId)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/subscriptions_custom_email.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customer = $objectManager->get('Magento\Customer\Model\CustomerFactory')->create()->load($subscription->getData('customer_id'));
        $quote = $objectManager->get('Magento\Quote\Model\QuoteFactory')->create()->load($subscription->getData('quote_id'));

        $productName = "";
        foreach ($quote->getAllItems() as $item) {
            $productName = $item->getName();
        }

        if($oldFrequency == 1)
        {
            $oldFrequency = $oldFrequency.' '.$oldFrequencyUnit;
        }
        else
        {
            $oldFrequency = $oldFrequency.' '.$oldFrequencyUnit.'s';
        }

        // New Frequency
        $intervalCollection = $objectManager->get('ParadoxLabs\Subscriptions\Model\IntervalFactory')->create()->getCollection();
        $intervalCollection->addFieldToFilter('value_id', $optionValueId);

        $frequency=null;
        foreach ($intervalCollection as $inter) {
            $frequency = $inter->getFrequencyCount();
        }
        if($frequency == 1)
        {
            $frequency = $frequency.' '.$inter->getFrequencyUnit();
        }
        else
        {
            $frequency = $frequency.' '.$inter->getFrequencyUnit().'s';
        }

        $templateId = 48;
        $name = $customer->getFirstname().' '.$customer->getLastname();
        $customer_email = $customer->getEmail();


        // ================ Send email code start ===============
        $this->inlineTranslation->suspend();
        $sender = [
            'name'  => $this->scopeConfig->getValue('trans_email/ident_support/name',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
            'email' => $this->scopeConfig->getValue('trans_email/ident_support/email',\Magento\Store\Model\ScopeInterface::SCOPE_STORE),
        ];

        $transport = $this->transportBuilder
            ->setTemplateIdentifier($templateId)
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )
            ->setTemplateVars([
                'customer_name'   => $name,
                'subscription_id' => $subscription->getData('increment_id'),
                'last_run'        => date('F d Y',strtotime($subscription->getData('last_run'))),
                'next_run'        => date('F d Y',strtotime($subscription->getData('next_run'))),
                'product_name'    => $productName,
                'old_frequency'   => $oldFrequency,
                'new_frequency'   => $frequency
            ])
            ->setFrom($sender)
            ->addTo($customer_email)
            ->getTransport();

        
        try {
            $transport->sendMessage();
            
            // Text Message getting
            $templateObject    = $this->templateFactory->create()->load($templateId);
            $emailTextMessage  = $templateObject->getTemplateText();

            $trackLog = $this->creditCardExpiringModelFactory->create();
            $dataToSave = array('customer_email' => $customer_email, 'email_sent' => "yes", 'notification_type'=>'Frequency Change', 'email_message' => $emailTextMessage);
            $trackLog->setData($dataToSave);
            $trackLog->save();

            $logger->info('Frequency change email sent successfully.');

        } catch (\Exception $e) {
            $trackLog = $this->creditCardExpiringModelFactory->create();
            $dataToSave = array('customer_email' => $customer_email, 'email_sent' => "no", 'notification_type'=>'Frequency Change','email_message' => $e->getMessage());
            $trackLog->setData($dataToSave);
            $trackLog->save();

            $logger->info('Error while sending frequency change email '.$e->getMessage());
        }
        $this->inlineTranslation->resume();
           
    }

}


