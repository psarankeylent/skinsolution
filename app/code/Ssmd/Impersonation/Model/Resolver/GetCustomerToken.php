<?php

namespace Ssmd\Impersonation\Model\Resolver;

use Magento\Framework\GraphQl\Exception\GraphQlInputException;

/**
 * Get Customer Token Class
 */
class GetCustomerToken implements \Magento\Framework\GraphQl\Query\ResolverInterface
{

    protected $graphQL;
    protected $impersonationFactory;
    protected $authSession;
    protected $userFactory;
    protected $encryptor;
    protected $dataHelper;
    protected $timezoneInterface;

    public function __construct(
        \Ssmd\Impersonation\Model\Resolver\Api\GraphQL $graphQL,
        \Ssmd\Impersonation\Model\ImpersonationFactory $impersonationFactory,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\User\Model\UserFactory $userFactory,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Ssmd\Impersonation\Helper\Data $dataHelper,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface

    ) {
        $this->graphQL = $graphQL;
        $this->impersonationFactory = $impersonationFactory;
        $this->authSession = $authSession;
        $this->userFactory = $userFactory;
        $this->encryptor = $encryptor;
        $this->dataHelper = $dataHelper;
        $this->timezoneInterface = $timezoneInterface;
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
        //echo "called"; exit;
        // $this->graphQL->authenticate($context);

        $this->validateInput($args);

        // Address

        // Logged order details
        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/address_test.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /* $shippingAddress = $objectManager->create('\Magento\Customer\Model\Address')->load(1029);


         $shippingAddress->setData('parent_id',1224);
         $shippingAddress->setData('most_recently_used_shipping', 2);
         //$billingAddress->setMostRecentlyUsedBilling(1);
         $shippingAddress->save();*/


        /*$addressRepository = $objectManager->create('\Magento\Customer\Api\AddressRepositoryInterface');
        $addressData = $objectManager->create('\Magento\Customer\Api\Data\AddressInterface');

        $street = ['street' => '123 mission street'];
        $addressData->setFirstname('Jay')
            ->setLastname('Babariya')
            ->setCountryId('US')
            ->setRegionId(23)
            ->setCity('aaa')
            ->setPostcode('44444')
            ->setCustomerId(1224)
            ->setStreet($street)
            ->setTelephone('444444444')
            ->setMostRecentlyUsedShipping('22')
            ->setId(1029);



        $addressRepository->save($addressData);

        echo $addressData->getMostRecentlyUsedShipping(); exit;


        exit;*/

        try{

            $currentDate = date('Y-m-d H:i:s');

            $collection = $this->impersonationFactory->create()->getCollection();
            $collection->addFieldToFilter('admin_token', $args['admin_token']);
            $collection->addFieldToFilter('customer_email', $args['customer_email']);

            //echo "<pre>"; print_r($collection->getData()); exit;


            $output = [];
            $adminUsername = null;
            $adminRole = null;
            $customerToken = null;
            if(!empty($collection->getData()))
            {
                foreach ($collection as $value) {

                    $customerToken = $value->getData('customer_token');
                    $adminUsername = $value->getData('username');
                    $id = $value->getData('id');

                    $userCollection = $this->userFactory->create()->getCollection();
                    $userCollection->addFieldToFilter('username', $value->getData('username'));
                    $userData = $userCollection->getFirstItem();

                    if(!empty($userData->getData()))
                    {
                        $adminRole = $userData->getData('role_name');
                    }

                    // Admin password
                    $adminPassword = $value->getData('admin_password');
                    if($adminPassword != "")
                    {
                        $adminPassword = $this->encryptor->decrypt($adminPassword);
                        if($adminPassword != $args['admin_password'])
                        {
                            $output = ['admin_username' => $adminUsername, 'admin_role' => $adminRole, 'customer_token' => $customerToken,'error_code' => 400, 'status' => 'unauthorized'];

                            return $output;
                        }

                    }

                    // Authenticate Date With Configuration check
                    $configSetTime = $this->dataHelper->checkAdminTokenTimeout();

                    //$createdDate = strtotime($value->getData('created_date')) + $configSetTime;
                    $tokenValidTime = strtotime($value->getData('created_date')) + $configSetTime;
                    $serverTime = strtotime($this->getTimeAccordingToTimeZone(date('Y-m-d H:i:s')));


                    /*echo "Config seconds: ". $configSetTime."<br>";
                    echo "Token time: ".$tokenValidTime."<br>";
                    echo "Server time: ".$serverTime."<br>";

                    echo "Token time: ".date('Y-m-d H:i:s', $tokenValidTime)."<br>";
                    echo "Server time: ".date('Y-m-d H:i:s',$serverTime)."<br>";
                    exit;*/


                    if($tokenValidTime > $serverTime)
                    {
                        //echo "Token valid";
                    }
                    else
                    {
                        // Update AccessTime & Status
                        $convertedDateTime = $this->converToTz(
                            $currentDate,
                            $this->timezoneInterface->getConfigTimezone(),
                            $this->timezoneInterface->getDefaultTimezone(),
                        );

                        $imperModel = $this->impersonationFactory->create()->load($id);
                        $imperModel->setAccessTime($convertedDateTime)
                            ->setStatus('expired')
                            ->save();

                        // Return Data
                        $output = ['admin_username' => $adminUsername, 'admin_role' => $adminRole, 'customer_token' => $customerToken, 'error_code' => 421, 'status' => 'expired'];

                        return $output;
                    }


                }

            }
            else
            {
                $output = ['admin_username' => $adminUsername, 'admin_role' => $adminRole, 'customer_token' => $customerToken,'error_code' => 400, 'status' => 'unauthorized'];

                return $output;
            }

            // Update AccessTime & Status
            $convertedDateTime = $this->converToTz(
                $currentDate,
                $this->timezoneInterface->getConfigTimezone(),
                $this->timezoneInterface->getDefaultTimezone(),
            );

            $imperModel = $this->impersonationFactory->create()->load($id);
            $imperModel->setAccessTime($convertedDateTime)
                ->setStatus('new')
                ->save();

            $output = ['admin_username' => $adminUsername, 'admin_role' => $adminRole, 'customer_token' => $customerToken,
                'error_code' => 200, 'status' => 'new'];

            return $output;

        }catch(Exception $e){
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/graphQL_test.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info("Error ". $e->getMessage());

            //print_r($e->getMessage());
        }

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
        $requiredFields = ['admin_token', 'admin_password', 'customer_email'];
        foreach ($requiredFields as $v) {
            if (!isset($args[ $v ]) || empty($args[ $v ])) {
                throw new GraphQlInputException(__('"%1" value must be specified', $v));
            }
        }
    }

    public function getTimeAccordingToTimeZone($dateTime)
    {
        // for convert date time according to magento's config time zone
        $dateTimeAsTimeZone = $this->timezoneInterface
            ->date(new \DateTime($dateTime))
            ->format('Y-m-d H:i:s');
        return $dateTimeAsTimeZone;
    }

    /**
     * converToTz convert Datetime from one zone to another
     * @param string $dateTime which we want to convert
     * @param string $toTz timezone in which we want to convert
     * @param string $fromTz timezone from which we want to convert
     */
    public function converToTz($dateTime="", $toTz='', $fromTz='')
    {
        // timezone by php friendly values
        $date = new \DateTime($dateTime, new \DateTimeZone($fromTz));
        $date->setTimezone(new \DateTimeZone($toTz));
        $dateTime = $date->format('Y-m-d H:i:s');
        return $dateTime;
    }


}
