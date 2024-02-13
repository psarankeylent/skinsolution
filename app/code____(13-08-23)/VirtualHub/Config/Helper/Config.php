<?php

declare(strict_types=1);

namespace VirtualHub\Config\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
    const LOGIN_ENDPOINT_CONFIG = 'configuration/authorization/auth_url';
    const LOGIN_USERNAME_CONFIG = 'configuration/authorization/auth_username';
    const LOGIN_PASSWORD_CONFIG = 'configuration/authorization/auth_password';

    const ORDER_SYNC_API = 'configuration/api_list/order_sync_api';
    const ORDER_CANCELLATION_API = 'configuration/api_list/order_cancellation_api';
    //const ORDER_STATUS_API = 'configuration/api_list/order_status_api';
    //const CONSULTATION_REPORTS_API = 'configuration/api_list/consultation_reports_api';
    const ORDER_ADDRESS_UPDATE_API = 'configuration/api_list/order_address_update_api';
    const CONSULTONLY_STATUS        = 'configuration/api_list/consultonly_status';
    const UPDATE_MEDICAL_HISTORY    = 'configuration/api_list/update_medical_history';
    const UPDATE_PHOTO              = 'configuration/api_list/update_photo';

    // CHATWOOT APIs
    const TICKET_API_ACCESS_KEY        = "configuration/chatwoot_api_list/ticket_api_access_key";
    const CONTACT_SEARCH_API           = "configuration/chatwoot_api_list/contact_search_api";
    const CONTACT_CONVERSATIONS_API    = "configuration/chatwoot_api_list/contact_conversations_api";
    const CONVERSATIONS_VIEW_API       = "configuration/chatwoot_api_list/conversations_view_api";


    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\HTTP\Client\Curl $curl,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Encryption\EncryptorInterface $encryptorInterface,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->curl = $curl;
        $this->encryptorInterface = $encryptorInterface;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    public function getOrderSyncApiUrl(){
        return $this->scopeConfig->getValue(
            SELF::ORDER_SYNC_API
        );
    }

    public function getOrderCancellationApiUrl(){
        return $this->scopeConfig->getValue(
            SELF::ORDER_CANCELLATION_API
        );
    }

    public function getOrderStatusApiUrl(){
        return $this->scopeConfig->getValue(
            SELF::ORDER_STATUS_API
        );
    }
    /*
    public function getConsultationReportsApiUrl(){
        return $this->scopeConfig->getValue(
            SELF::CONSULTATION_REPORTS_API
        );
    }
    */
    public function getOrderAddressUpdateApi(){
        return $this->scopeConfig->getValue(
            SELF::ORDER_ADDRESS_UPDATE_API
        );
    }

    public function getConsultOnlyStatus(){
        return $this->scopeConfig->getValue(
            SELF::CONSULTONLY_STATUS
        );
    }

    public function getUpdateMedicalHistory(){
        return $this->scopeConfig->getValue(
            SELF::UPDATE_MEDICAL_HISTORY
        );
    }

    public function getUpdatePhoto(){
        return $this->scopeConfig->getValue(
            SELF::UPDATE_PHOTO
        );
    }

    public function getVirtualHubBearerToken(){

        $apiLoginEndpointURL =  $this->scopeConfig->getValue(
            SELF::LOGIN_ENDPOINT_CONFIG
        );

        $apiUsername =  $this->scopeConfig->getValue(
            SELF::LOGIN_USERNAME_CONFIG
        );

        $apiPassword =  $this->scopeConfig->getValue(
            SELF::LOGIN_PASSWORD_CONFIG
        );

        $returnResponse = ["success"=>false, "token" => ""];

        if($apiLoginEndpointURL == ""){
            return $returnResponse;
        }

        $apiPassword = $this->encryptorInterface->decrypt($apiPassword);
        $requestData['username'] = $apiUsername;
        $requestData['password'] = $apiPassword;

        $headers = ["Content-Type" => "application/json"];
        $this->curl->setHeaders($headers);
        $this->curl->post($apiLoginEndpointURL, json_encode($requestData));
        $response = $this->curl->getBody();
        $response = json_decode($response, True);

        if(isset($response['success']) && isset($response['data']['token']) && $response['success']){
            $returnResponse['success'] = true;
            $returnResponse['token'] = $response['data']['token'];
        }

        return $returnResponse;
    }
    public function getApiAccessKey()
    {
        return $this->scopeConfig->getValue(
            SELF::TICKET_API_ACCESS_KEY
        );
    }
    public function getContactSearchApiUrl()
    {
        return $this->scopeConfig->getValue(
            SELF::CONTACT_SEARCH_API
        );
    }
    public function getContactConversationsApiUrl()
    {
        return $this->scopeConfig->getValue(
            SELF::CONTACT_CONVERSATIONS_API
        );
    }
    public function getConversationsViewApiUrl()
    {
        return $this->scopeConfig->getValue(
            SELF::CONVERSATIONS_VIEW_API
        );
    }

}



