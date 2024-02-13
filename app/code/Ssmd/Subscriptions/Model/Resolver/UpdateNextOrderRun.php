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
 *  Update NextOrderRun Class
 */
class UpdateNextOrderRun implements \ParadoxLabs\TokenBase\Model\Api\GraphQL\ResolverInterface
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

        //$this->validateInput($args);

        try{

            $subscription = $this->subscriptionRepository->load($args['id']);

            $subscription->setData('next_run', $args['next_run']);
            $subscriptionModel = $this->subscriptionRepository->save($subscription);

            // ===================== Subscription Notification Email Start ======================================//
            $subscriptionEmail = $this->sendChangeNextOrderEmail($subscription, $args['next_run']);
            // ===================== Subscription Notification Email End ======================================= //

            return ['SubscriptionObj' => $subscription->toArray()];

        }catch(Exception $e){
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/graphQL_test.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("Error ". $e->getMessage());
        }

    }

    public function sendChangeNextOrderEmail($subscription, $nextRun)
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

        
        $templateId = 49;
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
                'product_name'    => $productName,
                'today_date'      => date('F d Y'),
                'next_run'        => $nextRun
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
            $dataToSave = array('customer_email' => $customer_email, 'email_sent' => "yes", 'notification_type'=>'Next Order Run Change', 'email_message' => $emailTextMessage);
            $trackLog->setData($dataToSave);
            $trackLog->save();

            $logger->info('Next order run email sent successfully.');

        } catch (\Exception $e) {
            $trackLog = $this->creditCardExpiringModelFactory->create();
            $dataToSave = array('customer_email' => $customer_email, 'email_sent' => "no", 'notification_type'=>'Next Order Run Change','email_message' => $e->getMessage());
            $trackLog->setData($dataToSave);
            $trackLog->save();

            $logger->info('Error while sending next order run change email '.$e->getMessage());
        }
        $this->inlineTranslation->resume();


    }

    /**
     * Validate GraphQL request input.
     *
     * @param array $args
     * @return void
     * @throws \Magento\Framework\GraphQl\Exception\GraphQlInputException
     */
    protected function validateInput(array $args)
    {
        $requiredFields = ['id', 'next_run'];
        foreach ($requiredFields as $v) {
            if (!isset($args[ $v ]) || empty($args[ $v ])) {
                throw new GraphQlInputException(__('"%1" value must be specified', $v));
            }
        }
    }
}

